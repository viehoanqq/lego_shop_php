<?php
class ReportModel extends Database {
    
    // 1. LẤY DANH SÁCH BÁO CÁO TỔNG (Trang chủ Báo cáo)
    public function getInventoryReport($filters = []) {
        $db = $this->getConnection();
        
        $category_id = $filters['category_id'] ?? 'all';
        $start_date = $filters['start_date'] ?? date('Y-m-01');
        $end_date = $filters['end_date'] ?? date('Y-m-d');
        $keyword = $db->real_escape_string($filters['keyword'] ?? '');

        $start_time = $db->real_escape_string($start_date . ' 00:00:00');
        $end_time = $db->real_escape_string($end_date . ' 23:59:59');

        $where = "(p.status IN (1, 2) OR (p.status = 3 AND (
                    EXISTS (SELECT 1 FROM order_details od JOIN orders o ON od.order_id = o.id 
                            WHERE od.product_id = p.id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_time' AND '$end_time')
                    OR 
                    EXISTS (SELECT 1 FROM import_receipt_details id JOIN import_receipts ir ON id.receipt_id = ir.id 
                            WHERE id.product_id = p.id AND ir.status = 'completed' AND ir.created_at BETWEEN '$start_time' AND '$end_time')
                  )))";

        if ($category_id !== 'all') {
            $where .= " AND p.category_id = " . intval($category_id);
        }
        if (!empty($keyword)) {
            $where .= " AND (p.name LIKE '%$keyword%' OR p.sku LIKE '%$keyword%')";
        }

        $sql = "SELECT p.id, p.name, p.sku, p.status, 
                -- Số lượng Nhập và Tổng tiền nhập (Vốn)
                (SELECT COALESCE(SUM(d.quantity), 0) FROM import_receipt_details d 
                 JOIN import_receipts r ON d.receipt_id = r.id 
                 WHERE d.product_id = p.id AND r.status = 'completed' 
                 AND r.created_at BETWEEN '$start_time' AND '$end_time') as qty_in,
                 
                (SELECT COALESCE(SUM(d.quantity * d.price), 0) FROM import_receipt_details d 
                 JOIN import_receipts r ON d.receipt_id = r.id 
                 WHERE d.product_id = p.id AND r.status = 'completed' 
                 AND r.created_at BETWEEN '$start_time' AND '$end_time') as total_import_cost,

                -- Số lượng Xuất và Tổng doanh thu
                (SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od 
                 JOIN orders o ON od.order_id = o.id 
                 WHERE od.product_id = p.id AND o.status = 'delivered' 
                 AND o.created_at BETWEEN '$start_time' AND '$end_time') as qty_out,
                 
                (SELECT COALESCE(SUM(od.quantity * od.price), 0) FROM order_details od 
                 JOIN orders o ON od.order_id = o.id 
                 WHERE od.product_id = p.id AND o.status = 'delivered' 
                 AND o.created_at BETWEEN '$start_time' AND '$end_time') as total_revenue,

                -- LỢI NHUẬN GỘP CHUẨN: Tính dựa trên cost_price đã lưu
                (SELECT COALESCE(SUM(od.quantity * (od.price - od.cost_price)), 0) FROM order_details od 
                 JOIN orders o ON od.order_id = o.id 
                 WHERE od.product_id = p.id AND o.status = 'delivered' 
                 AND o.created_at BETWEEN '$start_time' AND '$end_time') as gross_profit,

                -- Tồn cuối kỳ (Tính đến thời điểm end_date)
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
                WHERE $where
                HAVING qty_in > 0 OR qty_out > 0 
                ORDER BY total_revenue DESC"; 

        return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // 2. LẤY LỊCH SỬ GIAO DỊCH CHI TIẾT CỦA 1 SẢN PHẨM
    public function getInventoryDetail($id, $start, $end) {
        $db = $this->getConnection();
        $s = $db->real_escape_string($start . ' 00:00:00');
        $e = $db->real_escape_string($end . ' 23:59:59');
        
        $sql = "(SELECT 'Nhập kho' as type, r.id as ref, d.quantity as qty, r.created_at as dt 
                 FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
                 WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at BETWEEN '$s' AND '$e')
                UNION ALL 
                (SELECT 'Xuất bán' as type, o.id as ref, -od.quantity as qty, o.created_at as dt 
                 FROM order_details od JOIN orders o ON od.order_id = o.id 
                 WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$s' AND '$e')
                ORDER BY dt DESC"; 
        return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // 3. LẤY CÁC CHỈ SỐ THỐNG KÊ (Tồn Đầu, Nhập, Xuất, Tồn Cuối, Lợi Nhuận)
    public function getProductPerformanceStats($id, $start, $end) {
        $db = $this->getConnection();
        $id = intval($id);
        $start_dt = $db->real_escape_string($start . ' 00:00:00');
        $end_dt = $db->real_escape_string($end . ' 23:59:59');

        // 1. Tính tồn đầu kỳ
        $sqlOpening = "SELECT (
            COALESCE((SELECT SUM(d.quantity) FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at < '$start_dt'), 0)
            -
            COALESCE((SELECT SUM(od.quantity) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at < '$start_dt'), 0)
        ) as opening";
        $opening = $db->query($sqlOpening)->fetch_assoc()['opening'];

        // 2. Tính Nhập, Xuất, Doanh Thu và LỢI NHUẬN GỘP CHUẨN
        $sqlMain = "SELECT 
            -- Tổng SL Nhập
            (SELECT COALESCE(SUM(d.quantity), 0) FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at BETWEEN '$start_dt' AND '$end_dt') as total_in,
            
            -- Tổng SL Bán (Chỉ tính Delivered)
            (SELECT COALESCE(SUM(od.quantity), 0) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_dt' AND '$end_dt') as total_out,
            
            -- Tổng Doanh Thu (Giá bán * Số lượng)
            (SELECT COALESCE(SUM(od.quantity * od.price), 0) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_dt' AND '$end_dt') as revenue,
            
            -- LỢI NHUẬN GỘP: SUM(Số lượng bán * (Giá bán thực tế - Giá vốn ĐÃ CHỐT od.cost_price))
            -- Đã xóa JOIN với bảng products vì không cần thiết nữa
            (SELECT COALESCE(SUM(od.quantity * (od.price - od.cost_price)), 0) 
             FROM order_details od 
             JOIN orders o ON od.order_id = o.id 
             WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_dt' AND '$end_dt') as gross_profit";
        
        $stats = $db->query($sqlMain)->fetch_assoc();
        
        // Gán các thông số trả về
        $stats['opening_stock'] = $opening;
        $stats['closing_stock'] = $opening + $stats['total_in'] - $stats['total_out'];
        
        // Gán Lợi nhuận gộp vào key 'profit' để View hiển thị
        $stats['profit'] = $stats['gross_profit']; 
        
        return $stats;
    }

    // 4. LẤY DỮ LIỆU VẼ BIỂU ĐỒ (CHART.JS)
    public function getChartData($id, $start, $end) {
        $db = $this->getConnection();
        $labels = [];
        $current = strtotime($start);
        $last = strtotime($end);
        
        while($current <= $last) {
            $labels[] = date('d/m', $current);
            $current = strtotime('+1 day', $current);
        }

        // FIX Ở ĐÂY: Ép thời gian lấy đến tận 23:59:59 của ngày cuối cùng
        $start_dt = $db->real_escape_string($start . ' 00:00:00');
        $end_dt = $db->real_escape_string($end . ' 23:59:59');

        $sqlIn = "SELECT DATE(r.created_at) as date, SUM(d.quantity) as qty 
                  FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
                  WHERE d.product_id = $id AND r.status = 'completed' AND r.created_at BETWEEN '$start_dt' AND '$end_dt'
                  GROUP BY DATE(r.created_at)";
        $resIn = $db->query($sqlIn)->fetch_all(MYSQLI_ASSOC);

        $sqlOut = "SELECT DATE(o.created_at) as date, SUM(od.quantity) as qty 
                   FROM order_details od JOIN orders o ON od.order_id = o.id 
                   WHERE od.product_id = $id AND o.status = 'delivered' AND o.created_at BETWEEN '$start_dt' AND '$end_dt'
                   GROUP BY DATE(o.created_at)";
        $resOut = $db->query($sqlOut)->fetch_all(MYSQLI_ASSOC);

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

    // 5. LẤY CHI TIẾT LỢI NHUẬN TỪNG ĐƠN HÀNG ĐỂ HIỂN THỊ POPUP
    public function getProfitBreakdown($id, $start, $end) {
        $db = $this->getConnection();
        $id = intval($id);
        $s = $db->real_escape_string($start . ' 00:00:00');
        $e = $db->real_escape_string($end . ' 23:59:59');

        $sql = "SELECT o.id as order_id, o.created_at, od.quantity, od.price, od.cost_price,
                       (od.quantity * (od.price - od.cost_price)) as order_profit
                FROM order_details od
                JOIN orders o ON od.order_id = o.id
                WHERE od.product_id = $id AND o.status = 'delivered'
                AND o.created_at BETWEEN '$s' AND '$e'
                ORDER BY o.created_at DESC";
                
        return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
?>