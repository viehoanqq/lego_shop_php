<?php
class ReportModel extends Database {
    public function getInventoryReport($filters = []) {
        $db = $this->getConnection();
        $category_id = $filters['category_id'] ?? 'all';
        $start_date = $filters['start_date'] ?? date('Y-m-01');
        $end_date = $filters['end_date'] ?? date('Y-m-d');
        $keyword = $db->real_escape_string($filters['keyword'] ?? '');

        $start_time = $db->real_escape_string($start_date . ' 00:00:00');
        $end_time = $db->real_escape_string($end_date . ' 23:59:59');

        $where = "p.status IN (1, 2)";
        if ($category_id !== 'all') $where .= " AND p.category_id = " . intval($category_id);
        if (!empty($keyword)) $where .= " AND (p.name LIKE '%$keyword%' OR p.sku LIKE '%$keyword%')";

        $sql = "SELECT p.id, p.name, p.sku, c.name as category_name,
                -- 1. Nhập trong kỳ
                (SELECT COALESCE(SUM(d.quantity), 0) FROM import_receipt_details d 
                 JOIN import_receipts r ON d.receipt_id = r.id 
                 WHERE d.product_id = p.id AND r.status = 'completed' 
                 AND r.created_at BETWEEN '$start_time' AND '$end_time') as period_in,
                -- 2. Xuất trong kỳ
                (SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od 
                 JOIN orders o ON od.order_id = o.id 
                 WHERE od.product_id = p.id AND o.status = 'delivered' 
                 AND o.created_at BETWEEN '$start_time' AND '$end_time') as period_out,
                -- 3. Tồn kho tại thời điểm END_DATE
                ((SELECT COALESCE(SUM(d.quantity), 0) FROM import_receipt_details d 
                  JOIN import_receipts r ON d.receipt_id = r.id 
                  WHERE d.product_id = p.id AND r.status = 'completed' AND r.created_at <= '$end_time')
                 -
                 (SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od 
                  JOIN orders o ON od.order_id = o.id 
                  WHERE od.product_id = p.id AND o.status = 'delivered' AND o.created_at <= '$end_time')
                ) as stock_at_time,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $where ORDER BY period_out DESC";
        return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    public function getInventoryDetail($id, $start, $end) {
    $db = $this->getConnection();
    $s = $db->real_escape_string($start . ' 00:00:00');
    $e = $db->real_escape_string($end . ' 23:59:59');
    
    // Thêm ORDER BY dt DESC để đưa ngày mới nhất lên trên đầu
    $sql = "(SELECT 'Nhập kho' as type, r.id as ref, d.quantity as qty, r.created_at as dt 
             FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
             WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at BETWEEN '$s' AND '$e')
            UNION ALL 
            (SELECT 'Xuất bán' as type, o.id as ref, -od.quantity as qty, o.created_at as dt 
             FROM order_details od JOIN orders o ON od.order_id = o.id 
             WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$s' AND '$e')
            ORDER BY dt DESC"; // Quan trọng: Đảo ngược ở đây
    return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
}
    public function getProductPerformanceStats($id, $start, $end) {
    $db = $this->getConnection();
    $id = intval($id);
    $start_dt = $db->real_escape_string($start . ' 00:00:00');
    $end_dt = $db->real_escape_string($end . ' 23:59:59');

    // 1. Tính tồn đầu kỳ (Tổng nhập - Tổng xuất trước ngày bắt đầu)
    $sqlOpening = "SELECT (
        COALESCE((SELECT SUM(d.quantity) FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at < '$start_dt'), 0)
        -
        COALESCE((SELECT SUM(od.quantity) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at < '$start_dt'), 0)
    ) as opening";
    $opening = $db->query($sqlOpening)->fetch_assoc()['opening'];

    // 2. Tính Nhập, Xuất và Lợi nhuận trong kỳ
    $sqlMain = "SELECT 
                SUM(CASE WHEN type = 'import' THEN qty ELSE 0 END) as total_in,
                SUM(CASE WHEN type = 'export' THEN ABS(qty) ELSE 0 END) as total_out,
                (SELECT SUM((od.price - p.import_price) * od.quantity) 
                 FROM order_details od JOIN orders o ON od.order_id = o.id JOIN products p ON od.product_id = p.id
                 WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_dt' AND '$end_dt') as profit
            FROM (
                SELECT 'import' as type, d.quantity as qty FROM import_receipt_details d 
                JOIN import_receipts r ON d.receipt_id = r.id 
                WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at BETWEEN '$start_dt' AND '$end_dt'
                UNION ALL
                SELECT 'export' as type, -od.quantity as qty FROM order_details od 
                JOIN orders o ON od.order_id = o.id 
                WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_dt' AND '$end_dt'
            ) as combined";
    
    $stats = $db->query($sqlMain)->fetch_assoc();
    $stats['opening_stock'] = $opening;
    $stats['closing_stock'] = $opening + $stats['total_in'] - $stats['total_out'];
    
    return $stats;
}
public function getChartData($id, $start, $end) {
    $db = $this->getConnection();
    // Tạo danh sách các ngày trong khoảng để làm nhãn (Labels)
    $labels = [];
    $current = strtotime($start);
    $last = strtotime($end);
    while($current <= $last) {
        $labels[] = date('d/m', $current);
        $current = strtotime('+1 day', $current);
    }

    // Lấy dữ liệu nhập theo ngày
    $sqlIn = "SELECT DATE(r.created_at) as date, SUM(d.quantity) as qty 
              FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
              WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at BETWEEN '$start' AND '$end'
              GROUP BY DATE(r.created_at)";
    $resIn = $db->query($sqlIn)->fetch_all(MYSQLI_ASSOC);

    // Lấy dữ liệu xuất theo ngày
    $sqlOut = "SELECT DATE(o.created_at) as date, SUM(od.quantity) as qty 
               FROM order_details od JOIN orders o ON od.order_id = o.id 
               WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start' AND '$end'
               GROUP BY DATE(o.created_at)";
    $resOut = $db->query($sqlOut)->fetch_all(MYSQLI_ASSOC);

    // Map dữ liệu vào mảng nhãn
    $dataIn = []; $dataOut = [];
    foreach($labels as $l) {
        $foundIn = 0; $foundOut = 0;
        foreach($resIn as $ri) { if(date('d/m', strtotime($ri['date'])) == $l) $foundIn = $ri['qty']; }
        foreach($resOut as $ro) { if(date('d/m', strtotime($ro['date'])) == $l) $foundOut = $ro['qty']; }
        $dataIn[] = $foundIn;
        $dataOut[] = $foundOut;
    }

    return ['labels' => $labels, 'in' => $dataIn, 'out' => $dataOut];
}

}