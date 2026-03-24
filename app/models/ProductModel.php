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
    public function getProductById($id) {
        $db = $this->getConnection();
        
        // Dùng LEFT JOIN kéo luôn bảng product_details vào (pd.*)
        $sql = "SELECT p.*, c.name as category_name, pd.* FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id 
                WHERE p.id = " . intval($id);
        
        $result = $db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    // Lấy danh sách tất cả ảnh của 1 sản phẩm
    public function getProductImages($product_id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM product_images WHERE product_id = " . intval($product_id) . " ORDER BY is_main DESC";
        $result = $db->query($sql);
        $images = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
        }
        return $images;
    }
    public function getProductRating($product_id) {
        $db = $this->getConnection();
        $sql = "SELECT 
                    IFNULL(ROUND(AVG(rating), 1), 0) as avg_rating, 
                    COUNT(id) as total_reviews 
                FROM product_reviews 
                WHERE product_id = " . intval($product_id) . " AND status = 'approved'";
                
        $result = $db->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return ['avg_rating' => 0, 'total_reviews' => 0];
    }
    public function getReviewsByProductId($product_id) {
        $db = $this->getConnection();
        // Nối bảng product_reviews với bảng users để lấy fullname
        $sql = "SELECT r.*, u.fullname 
                FROM product_reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = " . intval($product_id) . " AND r.status = 'approved' 
                ORDER BY r.created_at DESC";
                
        $result = $db->query($sql);
        $reviews = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
        }
        return $reviews;
    }
}