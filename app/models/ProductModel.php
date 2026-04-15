    <?php
    class ProductModel extends Database {

        // Lấy chi tiết 1 sản phẩm kèm thông số kỹ thuật
        public function getProductById($id) {
        $db = $this->getConnection();
        
        // Bổ sung Sub-query lấy image_url làm main_image 
        // VÀ Sub-query tính số lượng hàng khả dụng (trừ đi hàng đang chờ giao)
        $sql = "SELECT p.*, c.name as category_name, pd.*,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image,
                (p.stock_quantity - COALESCE((
                    SELECT SUM(od.quantity) 
                    FROM order_details od 
                    JOIN orders o ON od.order_id = o.id 
                    WHERE od.product_id = p.id AND o.status IN ('pending', 'confirmed', 'shipping')
                ), 0)) as available_stock
                FROM products p 
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
                $where .= " AND (p.name LIKE '%$k%' OR p.sku LIKE '%$k%')";
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
        public function updatePriceAndMargin($product_id, $selling_price, $profit_margin) {
            $db = $this->getConnection();
            
            $product_id = intval($product_id);
            $selling_price = intval($selling_price);
            $profit_margin = floatval($profit_margin);

            $sql = "UPDATE products SET selling_price = ?, profit_margin = ? WHERE id = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("idi", $selling_price, $profit_margin, $product_id);
            
            return $stmt->execute();
        }
        // --- Lấy danh sách sản phẩm để quản lý giá (CÓ TÌM KIẾM & LỌC) ---
        public function getAllProductsWithPrices($filters = [], $offset = null, $limit = null) {
        $db = $this->getConnection();
        $where = ["p.status IN ('1')"];

        if (!empty($filters['keyword'])) {
            $keyword = $db->real_escape_string($filters['keyword']);
            $where[] = "(p.name LIKE '%$keyword%' OR p.sku LIKE '%$keyword%')";
        }
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = " . intval($filters['category_id']);
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT p.*, 
                    (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as main_image
                FROM products p 
                WHERE $whereSql 
                ORDER BY p.created_at DESC";
        
        // Nếu có truyền offset và limit thì thêm LIMIT vào câu SQL
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . intval($offset) . ", " . intval($limit);
        }
        
        $result = $db->query($sql);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // --- Hàm Đếm tổng sản phẩm để chia trang ---
    public function countAllProductsWithPrices($filters = []) {
        $db = $this->getConnection();
        $where = ["p.status IN ('1', '2')"];

        if (!empty($filters['keyword'])) {
            $keyword = $db->real_escape_string($filters['keyword']);
            $where[] = "(p.name LIKE '%$keyword%' OR p.sku LIKE '%$keyword%')";
        }
        if (!empty($filters['category_id'])) {
            $where[] = "p.category_id = " . intval($filters['category_id']);
        }

        $whereSql = implode(' AND ', $where);

        $sql = "SELECT COUNT(*) as total FROM products p WHERE $whereSql";
        $result = $db->query($sql);
        return (int)$result->fetch_assoc()['total'];
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
        
        // Mặc định lấy cả 1 và 2 (1: Đang bán, 2: Tạm ẩn)
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

        // ==================================================
        // ĐÃ SỬA: Thêm CASE WHEN để đẩy status = 2 xuống cuối cùng
        // ==================================================
        $sql .= " ORDER BY CASE WHEN p.status = 2 THEN 1 ELSE 0 END ASC, p.created_at DESC LIMIT " . intval($offset) . ", " . intval($limit);

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
            // Bảng 1: products (Ép cứng Giá = 0, Tồn = 0)
            $sql = "INSERT INTO products (name, sku, category_id, selling_price, stock_quantity, description, status, profit_margin) 
                    VALUES (?, ?, ?, 0, 0, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssisid", $data['name'], $data['sku'], $data['category_id'], $data['description'], $data['status'], $data['profit_margin']);
            $stmt->execute();
            
            // Lấy ID vừa được tạo ra
            $product_id = $db->insert_id;

            // Bảng 2: product_details
            $sqlDetails = "INSERT INTO product_details (product_id, pieces, manufacturer, material, dimensions, age_range, release_year, theme_story) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtDetails = $db->prepare($sqlDetails);
            $stmtDetails->bind_param("iissssis", 
                $product_id, $data['pieces'], $data['manufacturer'], $data['material'], 
                $data['dimensions'], $data['age_range'], $data['release_year'], $data['theme_story']
            );
            $stmtDetails->execute();

            // Bảng 3: product_images (Lưu ảnh đại diện is_main = 1)
            $image_to_save = !empty($data['main_image']) ? $data['main_image'] : 'default.jpg';
            $sqlImg = "INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, 1)";
            $stmtImg = $db->prepare($sqlImg);
            $stmtImg->bind_param("is", $product_id, $image_to_save);
            $stmtImg->execute();

            $db->commit(); 
            
            // [QUAN TRỌNG NHẤT]: Phải trả về ID để Controller lấy đi lưu ảnh phụ
            return $product_id; 
            
        } catch (Exception $e) {
            $db->rollback(); 
            return false;
        }
    }

    // ==========================================
    // 2. HÀM CẬP NHẬT SẢN PHẨM
    // ==========================================
    public function updateProduct($id, $data) {
        $db = $this->getConnection();
        $db->begin_transaction();
        
        try {
            $id = intval($id);
            
            // Bảng 1: products (Không cho phép đổi giá và trạng thái ở đây)
            $sql = "UPDATE products SET name=?, sku=?, category_id=?, description=? WHERE id=?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("ssisi", $data['name'], $data['sku'], $data['category_id'], $data['description'], $id);
            $stmt->execute();

            // Bảng 2: product_details
            $sqlDetails = "UPDATE product_details SET 
                           pieces = ?, manufacturer = ?, material = ?, dimensions = ?, 
                           age_range = ?, release_year = ?, theme_story = ? 
                           WHERE product_id = ?";
            $stmtDetails = $db->prepare($sqlDetails);
            $stmtDetails->bind_param("issssisi", 
                $data['pieces'], $data['manufacturer'], $data['material'], $data['dimensions'], 
                $data['age_range'], $data['release_year'], $data['theme_story'], $id
            );
            $stmtDetails->execute();

            // Bảng 3: product_images (Chỉ xử lý nếu người dùng có up ảnh đại diện mới)
            if (!empty($data['main_image'])) {
                // Tối ưu: Chỉ hạ cấp ảnh chính CŨ (is_main = 1) thành ảnh phụ (is_main = 0)
                $db->query("UPDATE product_images SET is_main = 0 WHERE product_id = $id AND is_main = 1");
                
                // Thêm ảnh chính MỚI vào
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
    public function getGalleryImages($productId) {
        $stmt = $this->conn->prepare("SELECT * FROM product_images WHERE product_id = ? AND is_main = 0");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // 2. Chèn 1 mảng ảnh phụ vào database
    public function insertGalleryImages($productId, $imageUrls) {
        // Tránh gọi prepare trong vòng lặp để tối ưu hiệu suất
        $stmt = $this->conn->prepare("INSERT INTO product_images (product_id, image_url, is_main) VALUES (?, ?, 0)");
        
        foreach ($imageUrls as $url) {
            $stmt->bind_param("is", $productId, $url);
            $stmt->execute();
        }
        return true;
    }

    // 3. Xóa một ảnh phụ
    public function deleteGalleryImage($imageId) {
        $db = $this->getConnection();
        $stmt = $this->conn->prepare("DELETE FROM product_images WHERE id = ?");
        $stmt->bind_param("i", $imageId);
        return $stmt->execute();
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
        public function isNameExists($name, $exclude_id = null) {
            $db = $this->getConnection();
            $name = $db->real_escape_string(trim($name));
            $sql = "SELECT id FROM products WHERE name = '$name'";
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
            
            $db->begin_transaction();
            try {
                // Phải xóa thằng con trước (giỏ hàng, đánh giá, ảnh, chi tiết)
                $db->query("DELETE FROM cart_items WHERE product_id = $id"); // <-- Dòng này giải quyết cái lỗi bạn vừa gửi
                $db->query("DELETE FROM product_reviews WHERE product_id = $id");
                $db->query("DELETE FROM product_images WHERE product_id = $id");
                $db->query("DELETE FROM product_details WHERE product_id = $id");
                
                // Xóa sạch con rồi mới được phép xóa thằng cha (sản phẩm)
                $db->query("DELETE FROM products WHERE id = $id");
                
                $db->commit();
                return true;
            } catch (Exception $e) {
                $db->rollback();
                return false;
            }

        }
        public function getAllProductsForDropdown() {
            $db = $this->getConnection();
            $sql = "SELECT p.id, p.name, p.sku, p.stock_quantity, p.min_stock_level, p.import_price,
                (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image_url 
                FROM products p WHERE p.status IN (1, 2)";
                
            $result = $db->query($sql);
            return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }
        public function getProductsPaginated($offset, $limit, $keyword = '') {
            $db = $this->getConnection();
            
            // Dùng subquery lấy ảnh chính, và loại bỏ sản phẩm có status = 0 (khóa/xóa)
            $sql = "SELECT p.*, 
                           (SELECT image_url FROM product_images WHERE product_id = p.id AND is_main = 1 LIMIT 1) as image_url 
                    FROM products p 
                    WHERE p.status IN (1)";
            
            if (!empty($keyword)) {
                $kw = $db->real_escape_string($keyword);
                $sql .= " AND (p.name LIKE '%$kw%' OR p.sku LIKE '%$kw%')";
            }
            
            $sql .= " ORDER BY p.id DESC LIMIT " . intval($offset) . ", " . intval($limit);
            
            $result = $db->query($sql);
            $data = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { 
                    $data[] = $row; 
                }
            }
            return $data;
        }

        // Đếm tổng số lượng sản phẩm (Cập nhật đồng bộ điều kiện bỏ SP bị khóa)
        public function countProducts($keyword = '') {
            $db = $this->getConnection();
            
            $sql = "SELECT COUNT(*) as total FROM products p WHERE p.status IN (1, 2)";
            
            if (!empty($keyword)) {
                $kw = $db->real_escape_string($keyword);
                $sql .= " AND (p.name LIKE '%$kw%' OR p.sku LIKE '%$kw%')";
            }
            
            $result = $db->query($sql);
            return (int)$result->fetch_assoc()['total'];
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

        // --- Tra cứu lịch sử giá theo lô hàng nhập ---
        public function getProductPriceHistory($product_id) {
            $db = $this->getConnection();
            
            // Kết hợp bảng chi tiết phiếu nhập và phiếu nhập để lấy lịch sử
            $sql = "SELECT d.receipt_id, r.created_at as import_date, 
                        d.quantity as import_qty, 
                        d.price as batch_import_price, 
                        d.calculated_average_price as wac_price, 
                        d.calculated_selling_price as selling_price
                    FROM import_receipt_details d
                    JOIN import_receipts r ON d.receipt_id = r.id
                    WHERE d.product_id = " . intval($product_id) . " AND r.status = 'completed'
                    ORDER BY r.created_at DESC";
                    
            $result = $db->query($sql);
            $data = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            return $data;
        }
    }
