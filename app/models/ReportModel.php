<?php
class ReportModel extends Database {
    
    // BÁO CÁO NHẬP XUẤT VÀ TỒN KHO TẠI 1 THỜI ĐIỂM
    public function getInventoryReport($category_id = 'all', $start_date = '', $end_date = '') {
        $db = $this->getConnection();
        
        // Mặc định: Từ đầu tháng đến ngày hiện tại
        if(empty($end_date)) $end_date = date('Y-m-d');
        if(empty($start_date)) $start_date = date('Y-m-01'); 

        $end_time = $db->real_escape_string($end_date . ' 23:59:59');
        $start_time = $db->real_escape_string($start_date . ' 00:00:00');

        $where = "p.status IN (1, 2)";
        if ($category_id !== 'all' && !empty($category_id)) {
            $where .= " AND p.category_id = " . intval($category_id);
        }

        // Logic xử lý yêu cầu đồ án:
        $sql = "SELECT p.id, p.name, p.sku, c.name as category_name,
                
                -- Yêu cầu 1: Tính Nhập trong khoảng thời gian
                (SELECT COALESCE(SUM(d.quantity), 0) 
                 FROM import_receipt_details d 
                 JOIN import_receipts r ON d.receipt_id = r.id 
                 WHERE d.product_id = p.id AND r.status = 'completed' 
                 AND r.created_at >= '$start_time' AND r.created_at <= '$end_time') as period_in,
                 
                -- Yêu cầu 1: Tính Xuất trong khoảng thời gian
                (SELECT COALESCE(SUM(od.quantity), 0) 
                 FROM order_details od 
                 JOIN orders o ON od.order_id = o.id 
                 WHERE od.product_id = p.id AND o.status IN ('pending', 'confirmed', 'delivered') 
                 AND o.created_at >= '$start_time' AND o.created_at <= '$end_time') as period_out,
                 
                -- Yêu cầu 2: Tính TỒN KHO TẠI THỜI ĐIỂM = Tổng Nhập đến thời điểm đó - Tổng Xuất đến thời điểm đó
                (
                    (SELECT COALESCE(SUM(d.quantity), 0) 
                     FROM import_receipt_details d 
                     JOIN import_receipts r ON d.receipt_id = r.id 
                     WHERE d.product_id = p.id AND r.status = 'completed' 
                     AND r.created_at <= '$end_time')
                    -
                    (SELECT COALESCE(SUM(od.quantity), 0) 
                     FROM order_details od 
                     JOIN orders o ON od.order_id = o.id 
                     WHERE od.product_id = p.id AND o.status IN ('pending', 'confirmed', 'delivered') 
                     AND o.created_at <= '$end_time')
                ) as stock_at_time,

                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image

                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE $where
                ORDER BY p.id DESC";

        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { $data[] = $row; }
        }
        return $data;
    }
}