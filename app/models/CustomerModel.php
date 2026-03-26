<?php
class CustomerModel extends Database {
    
    // Lấy danh sách tất cả khách hàng
    public function getAllCustomers() {
        $db = $this->getConnection();
        $sql = "SELECT a.id, a.phone, a.email, a.status, a.created_at, u.fullname 
                FROM accounts a 
                JOIN users u ON a.id = u.account_id 
                WHERE a.role = 'customer' 
                ORDER BY a.created_at DESC";
        
        $result = $db->query($sql);
        $customers = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        return $customers;
    }

    // Cập nhật trạng thái tài khoản theo ID
    public function updateStatus($id, $newStatus) {
        $db = $this->getConnection();
        $id = intval($id);
        $newStatus = $db->real_escape_string($newStatus); // 'active' hoặc 'locked'

        $sql = "UPDATE accounts SET status = '$newStatus' WHERE id = $id AND role = 'customer'";
        return $db->query($sql);
    }

    // Lấy trạng thái hiện tại của tài khoản
    public function getStatus($id) {
        $db = $this->getConnection();
        $id = intval($id);
        $sql = "SELECT status FROM accounts WHERE id = $id";
        $result = $db->query($sql);
        if ($result && $row = $result->fetch_assoc()) {
            return $row['status'];
        }
        return null;
    }
}