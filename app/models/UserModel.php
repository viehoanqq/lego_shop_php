<?php
class UserModel extends Database {

    // Lấy thông tin chi tiết của Khách hàng
    public function getUserProfile($account_id) {
        $db = $this->getConnection();
        $account_id = intval($account_id);
        
        $sql = "SELECT a.phone, a.email, u.fullname 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE a.id = $account_id AND a.status = 'active'";
                
        $result = $db->query($sql);
        return $result ? $result->fetch_assoc() : false;
    }

    // [MỚI THÊM] - Kiểm tra xem SĐT mới có bị trùng với người khác không
    public function checkPhoneExists($phone, $current_account_id) {
        $db = $this->getConnection();
        $phone = $db->real_escape_string(trim($phone));
        $current_account_id = intval($current_account_id);

        // Lấy tài khoản có SĐT này, nhưng BỎ QUA tài khoản hiện tại của mình
        $sql = "SELECT id FROM accounts WHERE phone = '$phone' AND id != $current_account_id";
        $result = $db->query($sql);
        
        return ($result && $result->num_rows > 0); // Trả về true nếu bị trùng
    }

    // 2. Cập nhật thông tin Khách hàng
    public function updateUserProfile($account_id, $fullname, $phone) {
        $db = $this->getConnection();
        $account_id = intval($account_id);
        
        $fullname = $db->real_escape_string(trim($fullname));
        $phone = $db->real_escape_string(trim($phone));

        $db->begin_transaction();
        try {
            $db->query("UPDATE accounts SET phone = '$phone' WHERE id = $account_id");
            $db->query("UPDATE users SET fullname = '$fullname' WHERE account_id = $account_id");
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
}