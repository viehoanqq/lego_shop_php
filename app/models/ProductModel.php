<?php
class ProductModel extends Database {

    // Lấy chi tiết 1 sản phẩm kèm thông số kỹ thuật
    public function getProductById($id) {
        $db = $this->getConnection();
        $sql = "SELECT p.*, c.name as category_name, pd.* FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id 
                WHERE p.id = " . intval($id);
        
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    // HÀM QUAN TRỌNG NHẤT: Lấy danh sách sản phẩm (Dùng cho tất cả các trang)
    public function getFilteredProducts($filters = [], $offset = 0, $limit = 6) {
        $db = $this->getConnection();
        
        $sql = "SELECT p.*, c.name as category_name, pd.pieces,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id
                WHERE p.status = 1";

        $sql .= $this->_buildFilterWhere($filters);

        // Xử lý Sắp xếp
        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':  $sql .= " ORDER BY p.selling_price ASC"; break;
            case 'price_desc': $sql .= " ORDER BY p.selling_price DESC"; break;
            case 'name_asc':   $sql .= " ORDER BY p.name ASC"; break;
            default:           $sql .= " ORDER BY p.created_at DESC"; break;
        }
        
        $sql .= " LIMIT " . intval($offset) . ", " . intval($limit);

        $result = $db->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { $products[] = $row; }
        }
        return $products;
    }

    // Đếm tổng số lượng để phân trang
    public function countFilteredProducts($filters = []) {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id
                LEFT JOIN product_details pd ON p.id = pd.product_id 
                WHERE p.status = 1";
        $sql .= $this->_buildFilterWhere($filters);
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    // Xây dựng điều kiện WHERE dùng chung
    private function _buildFilterWhere($filters) {
        $db = $this->getConnection();
        $where = "";

        if (!empty($filters['keyword'])) {
            $k = $db->real_escape_string($filters['keyword']);
            $where .= " AND (p.name LIKE '%$k%' OR p.sku LIKE '%$k%' OR c.name LIKE '%$k%')";
        }
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $where .= " AND p.category_id = " . intval($filters['category']);
        }
        if (!empty($filters['price_range'])) {
            $range = explode('-', $filters['price_range']);
            if(count($range) == 2) $where .= " AND p.selling_price BETWEEN ".intval($range[0])." AND ".intval($range[1]);
        } elseif (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            if (!empty($filters['min_price'])) $where .= " AND p.selling_price >= " . intval($filters['min_price']);
            if (!empty($filters['max_price'])) $where .= " AND p.selling_price <= " . intval($filters['max_price']);
        }
        if (!empty($filters['pieces'])) {
            $range = explode('-', $filters['pieces']);
            if(count($range) == 2) $where .= " AND pd.pieces BETWEEN ".intval($range[0])." AND ".intval($range[1]);
        }
        return $where;
    }

    // Hàm bổ trợ cho trang chi tiết và Live Search
    public function searchProducts($keyword) {
        return $this->getFilteredProducts(['keyword' => $keyword], 0, 5);
    }

    public function getProductImages($product_id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM product_images WHERE product_id = " . intval($product_id) . " ORDER BY is_main DESC";
        $result = $db->query($sql);
        $images = [];
        if ($result) { while($row = $result->fetch_assoc()) { $images[] = $row; } }
        return $images;
    }

    public function getProductRating($product_id) {
        $db = $this->getConnection();
        $sql = "SELECT IFNULL(ROUND(AVG(rating), 1), 0) as avg_rating, COUNT(id) as total_reviews 
                FROM product_reviews WHERE product_id = ".intval($product_id)." AND status = 'approved'";
        $res = $db->query($sql);
        return ($res) ? $res->fetch_assoc() : ['avg_rating' => 0, 'total_reviews' => 0];
    }

    public function getReviewsByProductId($product_id) {
        $db = $this->getConnection();
        $sql = "SELECT r.*, u.fullname FROM product_reviews r JOIN users u ON r.user_id = u.id 
                WHERE r.product_id = ".intval($product_id)." AND r.status = 'approved' ORDER BY r.created_at DESC";
        $res = $db->query($sql);
        $data = [];
        if ($res) { while($row = $res->fetch_assoc()) { $data[] = $row; } }
        return $data;
    }
    public function getRandomProducts($limit = 8) {
        $db = $this->getConnection();
        
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.status = 1 
                ORDER BY RAND() 
                LIMIT " . intval($limit);
                
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
