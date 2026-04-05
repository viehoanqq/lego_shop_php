<?php
class OrderModel extends Database {
    
    // Lưu thông tin chung của đơn hàng
    private function buildAdminOrdersWhere($filters) {
        $db = $this->getConnection();
        $where = ["1=1"]; 
        
        if (!empty($filters['search'])) {
            $s = $db->real_escape_string(trim($filters['search']));
            if (stripos($s, 'DH') !== false || strpos($s, '#') === 0) {
                $clean_id = (int) preg_replace('/[^0-9]/', '', $s);
                $where[] = "id = $clean_id";
            } else {
                $where[] = "(id = '$s' OR shipping_phone LIKE '%$s%' OR shipping_fullname LIKE '%$s%')";
            }
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $st = $db->real_escape_string($filters['status']);
            $where[] = "status = '$st'";
        }
        
        if (!empty($filters['date_from'])) {
            $df = $db->real_escape_string($filters['date_from']) . ' 00:00:00';
            $where[] = "created_at >= '$df'";
        }
        if (!empty($filters['date_to'])) {
            $dt = $db->real_escape_string($filters['date_to']) . ' 23:59:59';
            $where[] = "created_at <= '$dt'";
        }
        return implode(' AND ', $where);
    }
    // Lưu chi tiết từng món hàng
    public function addOrderItem($order_id, $product_id, $quantity, $price) {
        $db = $this->getConnection();
        
        // 1. LẤY GIÁ VỐN (WAC) HIỆN TẠI TỪ KHO (Bảng products)
        $sqlCost = "SELECT import_price FROM products WHERE id = ?";
        $stmtCost = $db->prepare($sqlCost);
        $stmtCost->bind_param("i", $product_id);
        $stmtCost->execute();
        $result = $stmtCost->get_result();
        $row = $result->fetch_assoc();
        
        // Nếu không tìm thấy sản phẩm, gán tạm giá vốn = 0 để không bị lỗi
        $cost_price = $row ? $row['import_price'] : 0; 
        
        // 2. THÊM VÀO CHI TIẾT ĐƠN HÀNG (Lưu cả Giá Bán và Giá Vốn)
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price, cost_price) 
                VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        
        // Giải thích bind_param: 
        // i = integer (order_id, product_id, quantity)
        // d = double/decimal (price, cost_price)
        $stmt->bind_param("iiidd", $order_id, $product_id, $quantity, $price, $cost_price);
        
        return $stmt->execute();
    }
    public function getOrderById($order_id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM orders WHERE id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Trả về 1 mảng chứa dữ liệu đơn hàng
    }
    public function getOrdersByUserId($user_id) {
    $db = $this->getConnection();
    $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) { $orders[] = $row; }
    return $orders;
}
    // Lấy chi tiết các sản phẩm trong 1 đơn hàng
    public function getOrderItems($order_id) {
        $db = $this->getConnection();
        
        // SỬ DỤNG LEFT JOIN + GROUP BY ĐỂ ÉP SQL CHỈ TRẢ VỀ 1 DÒNG DUY NHẤT CHO MỖI SẢN PHẨM
        $sql = "SELECT oi.*, p.name, MAX(pi.image_url) as image_url 
                FROM order_details oi 
                JOIN products p ON oi.product_id = p.id 
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE oi.order_id = ?
                GROUP BY oi.product_id, oi.quantity, oi.price, p.name";
                
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        return $items;
    }
    public function updateOrderStatus($order_id, $status) {
        $db = $this->getConnection();
        
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        // s: string (status), i: integer (id)
        $stmt->bind_param("si", $status, $order_id);
        
        return $stmt->execute();
    }



    //admin


    public function getAllOrdersAdmin($filters = [], $limit = null, $offset = null) {
        $db = $this->getConnection();
        $where = ["1=1"]; 
        
        // 1. Lọc theo mã đơn hàng hoặc tên, SĐT khách hàng
        if (!empty($filters['search'])) {
            $s = $db->real_escape_string(trim($filters['search']));
            if (stripos($s, 'DH') !== false || strpos($s, '#') === 0) {
                $clean_id = (int) preg_replace('/[^0-9]/', '', $s);
                $where[] = "id = $clean_id";
            } else {
                $where[] = "(id = '$s' OR shipping_phone LIKE '%$s%' OR shipping_fullname LIKE '%$s%')";
            }
        }
        
        // 2. Lọc theo trạng thái
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $st = $db->real_escape_string($filters['status']);
            $where[] = "status = '$st'";
        }
        
        // 3. Lọc theo khoảng thời gian
        if (!empty($filters['date_from'])) {
            $df = $db->real_escape_string($filters['date_from']) . ' 00:00:00';
            $where[] = "created_at >= '$df'";
        }
        if (!empty($filters['date_to'])) {
            $dt = $db->real_escape_string($filters['date_to']) . ' 23:59:59';
            $where[] = "created_at <= '$dt'";
        }
        
        // 4. Sắp xếp (Đã gom hết lên đây để MySQL kịp xử lý)
        $orderBy = "created_at DESC"; 
        if (!empty($filters['sort'])) {
            if ($filters['sort'] == 'price_asc') $orderBy = "total_amount ASC";
            if ($filters['sort'] == 'price_desc') $orderBy = "total_amount DESC";
            if ($filters['sort'] == 'date_asc') $orderBy = "created_at ASC";
            if ($filters['sort'] == 'ward_asc') $orderBy = "shipping_city ASC, shipping_district ASC, shipping_ward ASC";
        }
        
        $whereSql = implode(' AND ', $where);
        $sql = "SELECT * FROM orders WHERE $whereSql ORDER BY $orderBy";
        
        // 5. Xử lý Phân trang
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }
        
        $result = $db->query($sql);
        $orders = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) { 
                $orders[] = $row; 
            }
        }
        
        return $orders;
    }

    // ==================================================
    // [ADMIN] ĐẾM TỔNG SỐ ĐƠN HÀNG (ĐỂ TÍNH SỐ TRANG)
    // ==================================================
    public function countAllOrdersAdmin($filters = []) {
        $db = $this->getConnection();
        $where = ["1=1"]; 
        
        // Phải lặp lại logic lọc y hệt hàm trên để đếm cho chuẩn xác
        if (!empty($filters['search'])) {
            $s = $db->real_escape_string(trim($filters['search']));
            if (stripos($s, 'DH') !== false || strpos($s, '#') === 0) {
                $clean_id = (int) preg_replace('/[^0-9]/', '', $s);
                $where[] = "id = $clean_id";
            } else {
                $where[] = "(id = '$s' OR shipping_phone LIKE '%$s%' OR shipping_fullname LIKE '%$s%')";
            }
        }
        
        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $st = $db->real_escape_string($filters['status']);
            $where[] = "status = '$st'";
        }
        
        if (!empty($filters['date_from'])) {
            $df = $db->real_escape_string($filters['date_from']) . ' 00:00:00';
            $where[] = "created_at >= '$df'";
        }
        if (!empty($filters['date_to'])) {
            $dt = $db->real_escape_string($filters['date_to']) . ' 23:59:59';
            $where[] = "created_at <= '$dt'";
        }
        
        $whereSql = implode(' AND ', $where);
        $sql = "SELECT COUNT(*) as total FROM orders WHERE $whereSql";
        
        $result = $db->query($sql);
        return (int)$result->fetch_assoc()['total'];
    }

    // ==================================================
    // CÁC HÀM XỬ LÝ LỊCH SỬ VÀ THANH TOÁN (MỚI THÊM)
    // ==================================================

    // 1. Lấy danh sách lịch sử đơn hàng
    public function getOrderHistory($order_id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM order_history WHERE order_id = ? ORDER BY changed_at ASC";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $history = [];
        while ($row = $result->fetch_assoc()) { 
            $history[] = $row; 
        }
        return $history;
    }

    // 2. Cập nhật trạng thái đơn hàng (Có ghi log History)
    public function updateOrderStatusAdmin($order_id, $status, $note = '') {
        $db = $this->getConnection();
        $db->begin_transaction();

        try {
            // 1. Lấy trạng thái CŨ của đơn hàng để so sánh
            $stmt_check = $db->prepare("SELECT status FROM orders WHERE id = ?");
            $stmt_check->bind_param("i", $order_id);
            $stmt_check->execute();
            $old_status = $stmt_check->get_result()->fetch_assoc()['status'];

            // 2. Cập nhật trạng thái mới
            $stmt1 = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt1->bind_param("si", $status, $order_id);
            $stmt1->execute();

            // 3. Ghi log lịch sử
            $stmt2 = $db->prepare("INSERT INTO order_history (order_id, status, note) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $order_id, $status, $note);
            $stmt2->execute();

            // ==========================================
            // 4. LOGIC HOÀN KHO (TỰ ĐỘNG CỘNG LẠI) KHI ADMIN ĐỔI TRẠNG THÁI TỪ đã giao thành !đã giao
            // ==========================================
            if ($status !== 'delivered' && $old_status === 'delivered') {
                $items = $this->getOrderItems($order_id);
                $processed_products = []; // Rổ đánh dấu sản phẩm đã cộng

                foreach ($items as $item) {
                    $p_id = (int)$item['product_id'];
                    
                    // KIỂM TRA: Nếu sản phẩm này đã được cộng kho rồi thì BỎ QUA NGAY LẬP TỨC
                    if (in_array($p_id, $processed_products)) {
                        continue; 
                    }

                    $qty = (int)$item['quantity'];
                    // Cộng lại vào kho
                    $db->query("UPDATE products SET stock_quantity = stock_quantity + $qty WHERE id = $p_id");
                    
                    // Ghi nhớ là đã cộng sản phẩm này rồi
                    $processed_products[] = $p_id; 
                }
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // 3. Cập nhật trạng thái thanh toán (Paid / Unpaid)
    public function updatePaymentStatusAdmin($order_id, $payment_status) {
        $db = $this->getConnection();
        $sql = "UPDATE orders SET payment_status = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $payment_status, $order_id);
        return $stmt->execute();
    }
    // ==================================================
    // [ADMIN] Lấy đánh giá của khách hàng cho đơn hàng
    // ==================================================
    public function getOrderReviews($order_id, $user_id) {
        $db = $this->getConnection();
        
        // Tìm đánh giá của user_id này đối với các product_id nằm trong đơn hàng này
        $sql = "SELECT pr.*, p.name as product_name 
                FROM product_reviews pr 
                JOIN order_details od ON pr.product_id = od.product_id 
                JOIN products p ON p.id = pr.product_id
                WHERE od.order_id = ? AND pr.user_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $reviews = [];
        while($row = $result->fetch_assoc()) { 
            $reviews[] = $row; 
        }
        return $reviews;
    }

    // Lưu thông tin chung của đơn hàng
    public function createOrder($user_id, $status, $payment_method, $total_amount, $fullname, $phone, $street, $ward, $district, $city) {
        $db = $this->getConnection();
        
        $user_id = (int)$user_id;
        $total_amount = (float)$total_amount; 
        $status = $db->real_escape_string($status);
        $payment_method = $db->real_escape_string($payment_method);
        $fullname = $db->real_escape_string($fullname);
        $phone = $db->real_escape_string($phone);
        $street = $db->real_escape_string($street);
        $ward = $db->real_escape_string($ward);
        $district = $db->real_escape_string($district);
        $city = $db->real_escape_string($city);
        

        // THÊM CỘT created_at VÀO SQL
        $sql = "INSERT INTO orders (user_id, status, payment_method, total_amount, shipping_fullname, shipping_phone, shipping_street, shipping_ward, shipping_district, shipping_city) 
                VALUES ($user_id, '$status', '$payment_method', $total_amount, '$fullname', '$phone', '$street', '$ward', '$district', '$city')";
                
        if ($db->query($sql) === TRUE) {
            return $db->insert_id; 
        }
        
        echo "Lỗi SQL: " . $db->error . "<br>Câu lệnh: " . $sql;
        exit;
    }
}