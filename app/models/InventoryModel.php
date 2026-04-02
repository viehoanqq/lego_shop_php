<?php
class InventoryModel extends Database {
    
    // 1. Lấy danh sách cảnh báo (Đã thêm lọc theo số lượng tùy chỉnh)
    public function getLowStockProducts($offset = 0, $limit = 6, $type = 'all', $keyword = '', $custom_threshold = null) {
        $db = $this->getConnection();
        $where = "WHERE p.status IN (1, 2) ";

        if ($custom_threshold !== null && $custom_threshold !== '') {
            $where .= " AND p.stock_quantity <= " . intval($custom_threshold);
        } else {
            if ($type === 'out') { $where .= " AND p.stock_quantity <= 0"; } 
            elseif ($type === 'low') { $where .= " AND p.stock_quantity > 0 AND p.stock_quantity <= p.min_stock_level"; } 
            elseif ($type === 'all') { $where .= " AND p.stock_quantity <= p.min_stock_level"; }
        }

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

    public function countLowStockProducts($type = 'all', $keyword = '', $custom_threshold = null) {
        $db = $this->getConnection();
        $where = "WHERE status IN (1, 2) ";
        
        if ($custom_threshold !== null && $custom_threshold !== '') {
            $where .= " AND stock_quantity <= " . intval($custom_threshold);
        } else {
            if ($type === 'out') $where .= " AND stock_quantity <= 0";
            elseif ($type === 'low') $where .= " AND stock_quantity > 0 AND stock_quantity <= min_stock_level";
            elseif ($type === 'all') $where .= " AND stock_quantity <= min_stock_level";
        }

        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (name LIKE '%$k%' OR sku LIKE '%$k%')";
        }

        $result = $db->query("SELECT COUNT(*) as total FROM products $where");
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // 2. Tính toán tồn kho tại 1 ngày trong quá khứ (Snapshot)
    public function getInventorySnapshot($date) {
        $db = $this->getConnection();
        $target_date = $db->real_escape_string($date) . ' 23:59:59';
        
        // Công thức: Tồn lịch sử = (Tổng Nhập) - (Tổng Bán) + (Tổng Điều chỉnh) tính đến cuối ngày đó
        $sql = "SELECT p.id, p.name, p.sku, p.import_price,
                (
                    COALESCE((SELECT SUM(d.quantity) FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id WHERE d.product_id = p.id AND r.status='completed' AND r.created_at <= '$target_date'), 0)
                    -
                    COALESCE((SELECT SUM(od.quantity) FROM order_details od JOIN orders o ON od.order_id = o.id WHERE od.product_id = p.id AND o.status != 'cancelled' AND o.created_at <= '$target_date'), 0)
                    +
                    COALESCE((SELECT SUM(a.qty_change) FROM stock_adjustments a WHERE a.product_id = p.id AND a.created_at <= '$target_date'), 0)
                ) as historical_stock
                FROM products p WHERE p.status IN (1,2)";
                
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // 3. Kiểm kho & Điều chỉnh
    public function adjustStock($product_id, $admin_id, $real_stock, $reason) {
        $db = $this->getConnection();
        $product_id = intval($product_id);
        $real_stock = intval($real_stock);
        $reason = $db->real_escape_string($reason);

        $res = $db->query("SELECT stock_quantity FROM products WHERE id = $product_id");
        $old_stock = $res->fetch_assoc()['stock_quantity'];
        $qty_change = $real_stock - $old_stock;

        if ($qty_change == 0) return true; // Không thay đổi gì

        $db->begin_transaction();
        try {
            // Cập nhật kho
            $db->query("UPDATE products SET stock_quantity = $real_stock WHERE id = $product_id");
            // Ghi log
            $db->query("INSERT INTO stock_adjustments (product_id, admin_id, old_stock, new_stock, qty_change, reason) 
                        VALUES ($product_id, $admin_id, $old_stock, $real_stock, $qty_change, '$reason')");
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback(); return false;
        }
    }

    // 4. Thẻ kho (Lịch sử Nhập / Điều chỉnh)
    public function getStockCard($product_id) {
        $db = $this->getConnection();
        $pid = intval($product_id);
        
        // Gộp 3 bảng: Nhập hàng (import), Xuất bán (order), và Điều chỉnh (adjust)
        $sql = "
            SELECT 'import' as type, r.created_at, d.quantity as qty_change, CONCAT('PN-', r.id, ' - Nhập hàng từ NCC') as note 
            FROM import_receipt_details d JOIN import_receipts r ON d.receipt_id = r.id 
            WHERE d.product_id = $pid AND r.status='completed'
            UNION ALL
            SELECT 'export' as type, o.created_at, -(od.quantity) as qty_change, CONCAT('DH-', o.id, ' - Xuất bán đơn hàng') as note 
            FROM order_details od JOIN orders o ON od.order_id = o.id 
            WHERE od.product_id = $pid AND o.status != 'cancelled'
            UNION ALL
            SELECT 'adjust' as type, created_at, qty_change, CONCAT('Điều chỉnh - Kiểm kho: ', reason) as note 
            FROM stock_adjustments WHERE product_id = $pid
            ORDER BY created_at DESC LIMIT 50
        ";
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function updateAllMinStock($min_stock) {
        return $this->getConnection()->query("UPDATE products SET min_stock_level = " . intval($min_stock));
    }
    public function updateSingleMinStock($id, $min_stock) {
        $stmt = $this->getConnection()->prepare("UPDATE products SET min_stock_level = ? WHERE id = ?");
        return $stmt->execute([intval($min_stock), intval($id)]);
    }
}
?>