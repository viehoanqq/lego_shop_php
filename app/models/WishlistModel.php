<?php
class WishlistModel extends Database {
    
    // 1. Thêm hoặc Xóa (Toggle) sản phẩm yêu thích
    public function toggleWishlist($account_id, $product_id) {
        $db = $this->getConnection();
        
        $account_id = intval($account_id);
        $product_id = intval($product_id);

        // Kiểm tra xem user này đã "thả tim" sản phẩm này chưa
        $sqlCheck = "SELECT id FROM wishlists WHERE account_id = $account_id AND product_id = $product_id";
        $check = $db->query($sqlCheck);
        
        if ($check && $check->num_rows > 0) {
            // Đã có trong danh sách -> Xóa đi (Bỏ thích)
            $db->query("DELETE FROM wishlists WHERE account_id = $account_id AND product_id = $product_id");
            return ['status' => 'removed', 'msg' => 'Đã xóa khỏi danh sách yêu thích'];
        } else {
            // Chưa có -> Thêm mới vào
            $db->query("INSERT INTO wishlists (account_id, product_id) VALUES ($account_id, $product_id)");
            return ['status' => 'added', 'msg' => 'Đã thêm vào danh sách yêu thích'];
        }
    }

    // 2. Lấy danh sách sản phẩm đã yêu thích để hiện ra trang Wishlist
    public function getUserWishlist($account_id) {
        $db = $this->getConnection();
        $account_id = intval($account_id);
        
        // CẬP NHẬT SQL: Thêm Subquery để lấy main_image giống trang chủ
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                JOIN wishlists w ON p.id = w.product_id 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE w.account_id = $account_id 
                ORDER BY w.created_at DESC";
                
        $result = $db->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }
}