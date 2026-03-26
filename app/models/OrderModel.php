<?php
class OrderModel extends Database {
    
    // Lưu thông tin chung của đơn hàng
    public function createOrder($user_id, $status, $payment_method, $total_amount, $fullname, $phone, $street, $ward, $district, $city) {
        $db = $this->getConnection();
        
        // Dùng mysqli_real_escape_string để chống Hack SQL Injection thay cho bind_param
        $user_id = (int)$user_id;
        $total_amount = (float)$total_amount; // Ép kiểu để không bao giờ lỗi
        $status = $db->real_escape_string($status);
        $payment_method = $db->real_escape_string($payment_method);
        $fullname = $db->real_escape_string($fullname);
        $phone = $db->real_escape_string($phone);
        $street = $db->real_escape_string($street);
        $ward = $db->real_escape_string($ward);
        $district = $db->real_escape_string($district);
        $city = $db->real_escape_string($city);

        // Viết thẳng biến vào SQL. Cách này nhìn phát biết ngay biến nào vào cột nào
        $sql = "INSERT INTO orders (user_id, status, payment_method, total_amount, shipping_fullname, shipping_phone, shipping_street, shipping_ward, shipping_district, shipping_city) 
                VALUES ($user_id, '$status', '$payment_method', $total_amount, '$fullname', '$phone', '$street', '$ward', '$district', '$city')";
                
        if ($db->query($sql) === TRUE) {
            return $db->insert_id; 
        }
        
        // Nếu lỗi, in thẳng câu lệnh SQL ra màn hình để biết tại sao
        echo "Lỗi SQL: " . $db->error . "<br>Câu lệnh: " . $sql;
        exit;
    }

    // Lưu chi tiết từng món hàng
    public function addOrderItem($order_id, $product_id, $quantity, $price) {
        $db = $this->getConnection();
        $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
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
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }
    // Lấy chi tiết các sản phẩm trong 1 đơn hàng
    public function getOrderItems($order_id) {
        $db = $this->getConnection();
        // Nối với bảng products để lấy tên và ảnh đại diện
        $sql = "SELECT oi.*, p.name, pi.image_url 
                FROM order_details oi 
                JOIN products p ON oi.product_id = p.id 
                JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE oi.order_id = ?";
                
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
}