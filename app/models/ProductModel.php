<?php
class ProductModel extends Database {

    // Lấy chi tiết 1 sản phẩm kèm thông số kỹ thuật
    public function getProductById($id, $account_id = null) {
        $db = $this->getConnection();
        $id = intval($id);
        
        $select_liked = "0 as is_liked";
        $join_wishlist = "";

        if ($account_id) {
            $acc_id = intval($account_id);
            $select_liked = "IF(w.id IS NOT NULL, 1, 0) as is_liked";
            $join_wishlist = "LEFT JOIN wishlists w ON p.id = w.product_id AND w.account_id = $acc_id";
        }

        $sql = "SELECT p.*, c.name as category_name, pd.*, $select_liked 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id 
                $join_wishlist
                WHERE p.id = $id";
        
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    // HÀM QUAN TRỌNG NHẤT: Lấy danh sách sản phẩm (Dùng cho tất cả các trang)
    public function getFilteredProducts($filters = [], $offset = 0, $limit = 6, $account_id = null) {
        $db = $this->getConnection();
        
        // --- 1. Xử lý câu SQL JOIN với Wishlist ---
        $select_liked = "0 as is_liked"; // Mặc định là 0 (Chưa thích)
        $join_wishlist = "";

        // Nếu user đã đăng nhập, nối bảng wishlists để tìm xem họ có thích sản phẩm này không
        if ($account_id) {
            $acc_id = intval($account_id);
            // Nếu có kết quả nối bảng (w.id IS NOT NULL) thì is_liked = 1
            $select_liked = "IF(w.id IS NOT NULL, 1, 0) as is_liked";
            $join_wishlist = "LEFT JOIN wishlists w ON p.id = w.product_id AND w.account_id = $acc_id";
        }
        // -------------------------------------------

        $sql = "SELECT p.*, c.name as category_name, pd.pieces, $select_liked,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                LEFT JOIN product_details pd ON p.id = pd.product_id
                $join_wishlist
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
    public function getRandomProducts($limit = 8, $account_id = null) {
        $db = $this->getConnection();
        
        // --- Tương tự như trên ---
        $select_liked = "0 as is_liked";
        $join_wishlist = "";

        if ($account_id) {
            $acc_id = intval($account_id);
            $select_liked = "IF(w.id IS NOT NULL, 1, 0) as is_liked";
            $join_wishlist = "LEFT JOIN wishlists w ON p.id = w.product_id AND w.account_id = $acc_id";
        }
        // -------------------------
        
        $sql = "SELECT p.*, c.name as category_name, $select_liked,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                $join_wishlist
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



    // Thêm sản phẩm mới với Transaction
    public function insertProduct($data) {
        $db = $this->getConnection();
        $db->begin_transaction();
        try {
            // 1. Insert bảng products
            $sql = "INSERT INTO products (name, sku, category_id, selling_price, stock_quantity, description, status) 
                    VALUES (?, ?, ?, ?, 0, ?, 1)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssiis", $data['name'], $data['sku'], $data['category_id'], $data['selling_price'], $data['description']);
            $stmt->execute();
            $product_id = $db->insert_id;

            // 2. Insert bảng product_details
            $sqlDetails = "INSERT INTO product_details (product_id, pieces, age_range) VALUES (?, ?, '12+')";
            $stmtDetails = $db->prepare($sqlDetails);
            $stmtDetails->bind_param("ii", $product_id, $data['pieces']);
            $stmtDetails->execute();

            // 3. Xử lý ảnh nếu có
            if (!empty($data['main_image'])) {
                $sqlImg = "INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, 1)";
                $stmtImg = $db->prepare($sqlImg);
                $stmtImg->bind_param("is", $product_id, $data['main_image']);
                $stmtImg->execute();
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $data) {
        $db = $this->getConnection();
        $db->begin_transaction();
        try {
            $id = intval($id);
            // 1. Update products
            $sql = "UPDATE products SET name=?, sku=?, selling_price=?, category_id=? WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssiii", $data['name'], $data['sku'], $data['selling_price'], $data['category_id'], $id);
            $stmt->execute();

            // 2. Update product_details
            $sqlDetails = "UPDATE product_details SET pieces=? WHERE product_id=?";
            $stmtDetails = $db->prepare($sqlDetails);
            $stmtDetails->bind_param("ii", $data['pieces'], $id);
            $stmtDetails->execute();

            // 3. Update Image nếu có ảnh mới
            if (!empty($data['main_image'])) {
                $db->query("UPDATE product_images SET is_main = 0 WHERE product_id = $id");
                $sqlImg = "INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, 1)";
                $stmtImg = $db->prepare($sqlImg);
                $stmtImg->bind_param("is", $id, $data['main_image']);
                $stmtImg->execute();
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // Kiểm tra SKU tồn tại
    public function isSkuExists($sku, $exclude_id = null) {
        $db = $this->getConnection();
        $sku = $db->real_escape_string($sku);
        $sql = "SELECT id FROM products WHERE sku = '$sku'";
        if ($exclude_id) $sql .= " AND id != " . intval($exclude_id);
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0);
    }

    // Thay đổi trạng thái (Dùng cho cả Ẩn và Xóa mềm)
    public function updateStatus($id, $status) {
        $db = $this->getConnection();
        $id = intval($id);
        $status = intval($status);
        return $db->query("UPDATE products SET status = $status WHERE id = $id");
    }



}