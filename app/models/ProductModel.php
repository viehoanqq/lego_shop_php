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
    // ==================================================
    // HÀM TÌM KIẾM SẢN PHẨM (Cho cả Live Search và Search thường)
    // ==================================================
    public function searchProducts($keyword) {
        $db = $this->getConnection();
        
        // Chống SQL Injection (Bảo mật)
        $safe_keyword = $db->real_escape_string($keyword);
        
        // Truy vấn tìm theo Tên sản phẩm, Mã SKU hoặc Tên Danh mục
        $sql = "SELECT p.*, c.name as category_name,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE (p.name LIKE '%$safe_keyword%' 
                   OR p.sku LIKE '%$safe_keyword%' 
                   OR c.name LIKE '%$safe_keyword%')
                AND p.status = 1 
                ORDER BY p.id DESC";
                
        $result = $db->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    // Lấy sản phẩm theo bộ lọc CÓ PHÂN TRANG
    public function getFilteredProducts($filters = [], $offset = 0, $limit = 6) {
        $db = $this->getConnection();
        
        $sql = "SELECT p.*, c.name as category_name, pd.pieces,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id
                WHERE p.status = 1";

        // Áp dụng các điều kiện lọc (Dùng chung logic cho cả hàm count)
        $sql .= $this->_buildFilterWhere($filters);

        $sql .= " ORDER BY p.created_at DESC";
        
        // Thêm giới hạn phân trang
        $sql .= " LIMIT " . intval($offset) . ", " . intval($limit);

        $result = $db->query($sql);
        $products = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
        return $products;
    }

    // Hàm đếm tổng số lượng sản phẩm sau khi lọc (để tính số trang)
    public function countFilteredProducts($filters = []) {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM products p 
                LEFT JOIN product_details pd ON p.id = pd.product_id 
                WHERE p.status = 1";
        
        $sql .= $this->_buildFilterWhere($filters);
        
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    // Hàm phụ để xây dựng câu lệnh WHERE (Tránh viết lặp lại code)
    private function _buildFilterWhere($filters) {
        $db = $this->getConnection();
        $where = "";

        // 1. Lọc theo Danh mục
        if (!empty($filters['category']) && $filters['category'] !== 'all') {
            $cat_id = intval($filters['category']);
            $where .= " AND p.category_id = $cat_id";
        }

        // 2. Lọc theo Khoảng giá
        if (!empty($filters['price_range'])) {
            $range = explode('-', $filters['price_range']);
            $min = intval($range[0]);
            $max = intval($range[1]);
            $where .= " AND p.selling_price BETWEEN $min AND $max";
        } elseif (!empty($filters['min_price']) || !empty($filters['max_price'])) {
            if (!empty($filters['min_price'])) $where .= " AND p.selling_price >= " . intval($filters['min_price']);
            if (!empty($filters['max_price'])) $where .= " AND p.selling_price <= " . intval($filters['max_price']);
        }

        // 3. Lọc theo Số mảnh ghép
        if (!empty($filters['pieces'])) {
            $range = explode('-', $filters['pieces']);
            $min = intval($range[0]);
            $max = intval($range[1]);
            $where .= " AND pd.pieces BETWEEN $min AND $max";
        }

        return $where;
    }
}