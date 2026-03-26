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
    // --- Cập nhật Giá bán và Tỉ lệ lợi nhuận ---
   // --- Lấy danh sách sản phẩm để quản lý giá ---
    public function getAllProductsWithPrices() {
        $db = $this->getConnection();
        
       
        $sql = "SELECT p.*, 
                       (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                WHERE p.status IN ('1', '2') 
                ORDER BY p.created_at DESC";
        
        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // Thay đổi trạng thái (Dùng cho cả Ẩn và Xóa mềm)
    public function updateStatus($id, $status) {
        $db = $this->getConnection();
        $id = intval($id);
        $status = intval($status);
        return $db->query("UPDATE products SET status = $status WHERE id = $id");
    }

    // Hàm lấy danh sách dành riêng cho Admin (Lấy cả status 1 và 2)
    public function getAdminProducts($filters = [], $offset = 0, $limit = 6) {
        $db = $this->getConnection();
        
        // Mặc định lấy cả 1 và 2
        $where = "WHERE p.status IN (1, 2)"; 

        // Kiểm tra nếu lọc status là số cụ thể (1 hoặc 2)
        if (isset($filters['status']) && is_numeric($filters['status'])) {
            $where = "WHERE p.status = " . intval($filters['status']);
        } 
        // Nếu lọc là một danh sách (ví dụ '1,2')
        elseif (!empty($filters['status']) && $filters['status'] !== 'all' && strpos($filters['status'], ',') !== false) {
            // Làm sạch chuỗi để tránh SQL Injection (ví dụ: "1,2")
            $safe_status = $db->real_escape_string($filters['status']);
            $where = "WHERE p.status IN ($safe_status)";
        }

        $sql = "SELECT p.*, c.name as category_name, pd.pieces,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id 
                $where";

        if (!empty($filters['keyword'])) {
            $k = $db->real_escape_string(trim($filters['keyword']));
            $sql .= " AND (p.name LIKE '%$k%' OR p.sku LIKE '%$k%')";
        }
        
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND p.category_id = " . intval($filters['category']);
        }

        $sql .= " ORDER BY p.created_at DESC LIMIT " . intval($offset) . ", " . intval($limit);

        $result = $db->query($sql);
        $products = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) { $products[] = $row; }
        }
        return $products;
    }

    // Hàm đếm tổng sản phẩm dành riêng cho Admin
    public function countAdminProducts($filters = []) {
        $db = $this->getConnection();
        $where = "WHERE p.status IN (1, 2)";

        if (isset($filters['status']) && is_numeric($filters['status'])) {
            $where = "WHERE p.status = " . intval($filters['status']);
        } elseif (!empty($filters['status']) && $filters['status'] !== 'all' && strpos($filters['status'], ',') !== false) {
            $safe_status = $db->real_escape_string($filters['status']);
            $where = "WHERE p.status IN ($safe_status)";
        }

        $sql = "SELECT COUNT(*) as total FROM products p $where";
        
        if (!empty($filters['keyword'])) {
            $k = $db->real_escape_string(trim($filters['keyword']));
            $sql .= " AND (p.name LIKE '%$k%' OR p.sku LIKE '%$k%')";
        }
        
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $sql .= " AND p.category_id = " . intval($filters['category']);
        }

        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function getProductFullDetail($id) {
        $db = $this->getConnection();
        $id = intval($id);

        $sql = "SELECT p.*, d.*, c.name as category_name,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN product_details d ON p.id = d.product_id 
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    
    }

    // Nếu bạn có dùng hàm updateProductDetails ở Controller thì cũng nên thêm vào đây:
    public function updateProductDetail($id, $data) {
        $db = $this->getConnection();
        $sql = "UPDATE product_details SET 
                manufacturer = ?, 
                material = ?, 
                dimensions = ?, 
                age_range = ?, 
                pieces = ?, 
                theme_story = ? ,
                release_year = ?
                WHERE product_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssssissi", 
            $data['manufacturer'], 
            $data['material'], 
            $data['dimensions'], 
            $data['age_range'], 
            $data['pieces'], 
            $data['theme_story'], 
            $data['release_year'], 
            $id
        );
        
        return $stmt->execute();
    }
    // Lấy đánh giá cũ của user cho 1 sản phẩm
    public function getReviewByUserAndProduct($user_id, $product_id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM product_reviews WHERE user_id = ? AND product_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    // Lưu hoặc Sửa đánh giá (Luôn set status = pending)
    public function saveProductReview($user_id, $product_id, $rating, $comment) {
        $db = $this->getConnection();
        $comment = $db->real_escape_string(trim($comment));

        // Kiểm tra xem đã có chưa
        $existing = $this->getReviewByUserAndProduct($user_id, $product_id);

        if ($existing) {
            // ĐÃ CÓ -> CẬP NHẬT & Set lại thành 'pending'
            $sql = "UPDATE product_reviews SET rating = ?, comment = ?, status = 'pending', created_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("isi", $rating, $comment, $existing['id']);
            return $stmt->execute();
        } else {
            // CHƯA CÓ -> INSERT MỚI là 'pending'
            $sql = "INSERT INTO product_reviews (product_id, user_id, rating, comment, status, created_at) VALUES (?, ?, ?, ?, 'pending', NOW())";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
            return $stmt->execute();
        }
    }

}
