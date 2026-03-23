<?php
class ProductModel extends Database {
    public function getAllProducts() {
        $db = $this->getConnection();
        // Lấy sản phẩm và ảnh có đánh dấu is_main = 1
        $sql = "SELECT p.*, i.image_url 
                FROM products p 
                LEFT JOIN product_images i ON p.id = i.product_id AND i.is_main = 1 
                WHERE p.status = 1 
                ORDER BY p.created_at DESC";
        
        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}