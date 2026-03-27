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
    // Lưu hoặc Sửa đánh giá (Luôn set status = pending)
    public function saveProductReview($user_id, $product_id, $rating, $comment) {
        $db = $this->getConnection();
        $comment = $db->real_escape_string(trim($comment));

        // Kiểm tra xem đã có chưa
        $existing = $this->getReviewByUserAndProduct($user_id, $product_id);

        if ($existing) {
            // ĐÃ CÓ -> CẬP NHẬT & Set lại thành 'pending'
            $sql = "UPDATE product_reviews SET rating = ?, comment = ?, status = 'approved', created_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("isi", $rating, $comment, $existing['id']);
            return $stmt->execute();
        } else {
            // CHƯA CÓ -> INSERT MỚI là 'pending'
            $sql = "INSERT INTO product_reviews (product_id, user_id, rating, comment, status, created_at) VALUES (?, ?, ?, ?, 'approved', NOW())";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
            return $stmt->execute();
        }
    }

    // Hàm lấy sản phẩm sắp hết hàng (tồn kho <= min_stock_level)
    public function getLowStockProducts($offset = 0, $limit = 6, $type = 'all', $keyword = '') {
        $db = $this->getConnection();
        
        $where = "WHERE p.status IN (1, 2) ";

        // Lọc theo loại tồn kho
        if ($type === 'out') {
            $where .= " AND p.stock_quantity <= 0";
        } elseif ($type === 'low') {
            $where .= " AND p.stock_quantity > 0 AND p.stock_quantity <= p.min_stock_level";
        } else {
            $where .= " AND p.stock_quantity <= p.min_stock_level";
        }

        // Lọc theo từ khóa tìm kiếm (Thêm phần này)
        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (p.name LIKE '%$k%' OR p.sku LIKE '%$k%')";
        }

        $sql = "SELECT p.*, c.name as category_name, 
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                $where
                ORDER BY p.stock_quantity ASC 
                LIMIT " . (int)$offset . ", " . (int)$limit;

        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countLowStockProducts($type = 'all', $keyword = '') {
        $db = $this->getConnection();
        
        $where = "WHERE status IN (1, 2) ";
        if ($type === 'out') $where .= " AND stock_quantity <= 0";
        elseif ($type === 'low') $where .= " AND stock_quantity > 0 AND stock_quantity <= min_stock_level";
        else $where .= " AND stock_quantity <= min_stock_level";

        // Lọc theo từ khóa tìm kiếm cho hàm đếm (Thêm phần này)
        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (name LIKE '%$k%' OR sku LIKE '%$k%')";
        }

        $sql = "SELECT COUNT(*) as total FROM products $where";
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    public function updateAllMinStock($min_stock) {
        $db = $this->getConnection();

        $sql = "UPDATE products SET min_stock_level = " . intval($min_stock);
        return $db->query($sql);
    }

    public function updateSingleMinStock($id, $min_stock) {
        $db = $this->getConnection();
        $sql = "UPDATE products SET min_stock_level = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute([intval($min_stock), intval($id)]);
    }


    // Hàm check xóa
    public function canDeleteProduct($id) {
        $db = $this->getConnection(); // Lấy kết nối giống các hàm trên
        $id = intval($id);

        // 1. Kiểm tra trong chi tiết phiếu nhập
        $sqlImport = "SELECT COUNT(*) as total FROM import_receipt_details WHERE product_id = $id";
        $resImport = $db->query($sqlImport);
        $importCheck = $resImport->fetch_assoc();

        // 2. Kiểm tra thêm trong chi tiết đơn hàng
        $sqlOrder = "SELECT COUNT(*) as total FROM order_details WHERE product_id = $id";
        $resOrder = $db->query($sqlOrder);
        $orderCheck = $resOrder->fetch_assoc();

        // Trả về true nếu cả 2 bảng đều không có dữ liệu
        return ($importCheck['total'] == 0 && $orderCheck['total'] == 0);
    }

    public function deleteProduct($id) {
        $db = $this->getConnection();
        $id = intval($id);
        
        // Vì CSDL của bạn có ON DELETE CASCADE nên nó sẽ tự xóa các bảng liên quan
        $sql = "DELETE FROM products WHERE id = $id";
        return $db->query($sql);
    }

    // Hàm 2: Chỉ khóa (ẩn) sản phẩm
    public function hideProduct($id) {
        $db = $this->getConnection();
        $id = intval($id);
        // Giả sử status = 0 là trạng thái bị khóa/ẩn
        $sql = "UPDATE products SET status = 2 WHERE id = $id";
        return $db->query($sql);
    }

    
    //Cập nhật trạng thái sản phẩm có kiểm tra trạng thái Danh mục
    public function updateStatusWithTaskCheck($id, $status) {
        $db = $this->getConnection();
        $id = intval($id);
        $status = intval($status);

        // 1. Lấy thông tin sản phẩm để biết nó thuộc danh mục nào
        $product = $this->getProductById($id);
        if (!$product) return 'notfound';

        // 2. Nếu muốn MỞ sản phẩm (status = 1 hoặc 2), phải kiểm tra Danh mục
        if ($status > 0) {
            $catId = intval($product['category_id']);
            $sqlCat = "SELECT status FROM categories WHERE id = $catId";
            $resCat = $db->query($sqlCat);
            $category = $resCat->fetch_assoc();

            // Kiểm tra nếu danh mục đang bị khóa
            if ($category && strtolower(trim($category['status'])) === 'locked') {
                return 'cat_locked'; // Trả về mã lỗi riêng
            }
        }

        // 3. Nếu kiểm tra ổn hoặc là hành động KHÓA (status = 0) thì tiến hành Update
        $sqlUpdate = "UPDATE products SET status = $status WHERE id = $id";
        if ($db->query($sqlUpdate)) {
            return 'success';
        }

        return 'error';
    }

}
