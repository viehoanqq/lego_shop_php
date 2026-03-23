<?php
class ProductModel extends Database {
    public function getAllProducts() {
        $db = $this->getConnection();
        // JOIN bảng products với bảng product_images để lấy cột image_url
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