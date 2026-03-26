<?php
class OrderModel extends Database {
    
    // Lưu thông tin chung của đơn hàng
    public function createOrder($user_id, $status, $payment_method, $total_amount, $fullname, $phone, $street, $ward, $district, $city) {
        $db = $this->getConnection();
        
        $sql = "INSERT INTO orders (user_id, status, payment_method, total_amount, shipping_fullname, shipping_phone, shipping_street, shipping_ward, shipping_district, shipping_city) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ississssss", $user_id, $status, $payment_method, $total_amount, $fullname, $phone, $street, $ward, $district, $city);
        
        if ($stmt->execute()) {
            return $db->insert_id; 
        }
        return false;
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
}