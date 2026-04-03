<?php
class InventoryModel extends Database {
    public function getGlobalMinStock() {
        $db = $this->getConnection();
        // Lấy min_stock_level của 1 sản phẩm bất kỳ làm chuẩn chung
        $res = $db->query("SELECT min_stock_level FROM products LIMIT 1");
        if ($res && $row = $res->fetch_assoc()) {
            return $row['min_stock_level'];
        }
        return 10; // Mặc định nếu db trống
    }

    // Cập nhật ngưỡng cảnh báo cho TẤT CẢ sản phẩm
    public function updateAllMinStock($min_stock) {
        $db = $this->getConnection();
        $ms = intval($min_stock);
        return $db->query("UPDATE products SET min_stock_level = $ms");
    }
    // 1. Lấy danh sách cảnh báo (Đã thêm lọc theo số lượng tùy chỉnh)
    public function getLowStockProducts($offset = 0, $limit = 6, $type = 'all', $keyword = '') {
        $db = $this->getConnection();
        $where = "WHERE p.status IN (1, 2) ";

        if ($type === 'out') { $where .= " AND p.stock_quantity <= 0"; } 
        elseif ($type === 'low') { $where .= " AND p.stock_quantity > 0 AND p.stock_quantity <= p.min_stock_level"; } 
        elseif ($type === 'all') { $where .= " AND p.stock_quantity <= p.min_stock_level"; }

        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (p.name LIKE '%$k%' OR p.sku LIKE '%$k%')";
        }

        $sql = "SELECT p.*, c.name as category_name, 
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                $where ORDER BY p.stock_quantity ASC LIMIT $offset, $limit";

        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countLowStockProducts($type = 'all', $keyword = '') {
        $db = $this->getConnection();
        $where = "WHERE status IN (1, 2) ";
        
        if ($type === 'out') $where .= " AND stock_quantity <= 0";
        elseif ($type === 'low') $where .= " AND stock_quantity > 0 AND stock_quantity <= min_stock_level";
        elseif ($type === 'all') $where .= " AND stock_quantity <= min_stock_level";

        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (name LIKE '%$k%' OR sku LIKE '%$k%')";
        }

        $result = $db->query("SELECT COUNT(*) as total FROM products $where");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // 2. Tính toán tồn kho tại 1 ngày trong quá khứ (Snapshot tab Tổng quan)
    public function getInventorySnapshot($date) {
        $db = $this->getConnection();
        $target_date = $db->real_escape_string($date) . ' 23:59:59';
        
        // CÔNG THỨC: Tồn quá khứ = Tồn thực tế hiện tại - (Lượng nhập ở tương lai) + (Lượng bán ở tương lai)
        $sql = "SELECT p.id, p.name, p.sku, p.import_price,
        (
            p.stock_quantity 
            - 
            COALESCE((SELECT SUM(d.quantity) FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id WHERE d.product_id = p.id AND r.status='completed' AND r.created_at > '$target_date'), 0)
            + 
            COALESCE((SELECT SUM(od.quantity) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = p.id AND o.status = 'delivered' AND o.created_at > '$target_date'), 0)
        ) as historical_stock
        FROM products p WHERE p.status IN (1,2)";
                
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    
    public function updateSingleMinStock($id, $min_stock) {
        $db = $this->getConnection();
        $stmt = $db->prepare("UPDATE products SET min_stock_level = ? WHERE id = ?");
        $ms = intval($min_stock); $pid = intval($id);
        $stmt->bind_param("ii", $ms, $pid);
        return $stmt->execute();
    }

    // =================================================================
    // CÁC HÀM XỬ LÝ THẺ KHO (DÙNG LOGIC GỘP 3 BẢNG CỦA BẠN CHUẨN 100%)
    // =================================================================

    // 4. Tính tồn kho quá khứ (Dành cho Tồn Đầu Ngày / Cuối Ngày)
    public function calculateStockAtTime($product_id, $datetime) {
        $db = $this->getConnection();
        $pid = intval($product_id);
        $dt = $db->real_escape_string($datetime);

        // Lấy tồn thực tế hiện tại
        $stmt1 = $db->query("SELECT stock_quantity FROM products WHERE id = $pid");
        $current_stock = $stmt1->fetch_assoc()['stock_quantity'] ?? 0;

        // Gom tổng các biến động xảy ra SAU thời điểm $datetime
        $sql = "
    SELECT SUM(qty_change) as total_change FROM (
        SELECT d.quantity as qty_change 
        FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
        WHERE d.product_id = $pid AND r.status='completed' AND r.created_at > '$dt'
        
        UNION ALL
        
        SELECT -(od.quantity) as qty_change 
        FROM order_details od JOIN orders o ON od.order_id = o.id 
        WHERE od.product_id = $pid AND o.status = 'delivered' AND o.created_at > '$dt'
    ) as changes
";
        
        $res = $db->query($sql);
        $total_change = $res->fetch_assoc()['total_change'] ?? 0;

        // Tồn quá khứ = Hiện tại - Các biến động đã xảy ra sau đó
        return $current_stock - $total_change;
    }

    // 5. Lấy danh sách giao dịch trong 1 ngày cụ thể (Cho Tab Thẻ Kho)
    public function getTransactionsByDate($product_id, $date) {
        $db = $this->getConnection();
        $pid = intval($product_id);
        
        $start_time = $db->real_escape_string($date) . ' 00:00:00';
        $end_time = $db->real_escape_string($date) . ' 23:59:59';

        $sql = "
    SELECT * FROM (
        SELECT 'import' as type, r.created_at, d.quantity as qty_change, CONCAT('PN-', r.id, ' - Nhập hàng từ NCC') as note 
        FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
        WHERE d.product_id = $pid AND r.status='completed' AND r.created_at >= '$start_time' AND r.created_at <= '$end_time'
        
        UNION ALL
        
        SELECT 'export' as type, o.created_at, -(od.quantity) as qty_change, CONCAT('DH-', o.id, ' - Xuất bán đơn hàng') as note 
        FROM order_details od JOIN orders o ON od.order_id = o.id 
        WHERE od.product_id = $pid AND o.status = 'delivered' AND o.created_at >= '$start_time' AND o.created_at <= '$end_time'
    ) as daily_transactions
    ORDER BY created_at ASC
";
                
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}
?>