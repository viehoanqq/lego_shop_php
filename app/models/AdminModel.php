<?php
class AdminModel extends Database {

    // 1. Lấy thông tin chi tiết của Admin
    public function getAdminById($admin_id) {
        $db = $this->getConnection();
        $admin_id = intval($admin_id);

        $sql = "SELECT a.phone, a.email, u.fullname 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE a.id = $admin_id";
        $result = $db->query($sql);
        
        return $result ? $result->fetch_assoc() : false;
    }

    // 2. Cập nhật thông tin cá nhân Admin
    public function updateAdminProfile($admin_id, $fullname, $phone, $email) {
        $db = $this->getConnection();
        $admin_id = intval($admin_id);

        $fullname = $db->real_escape_string(trim($fullname));
        $phone = $db->real_escape_string(trim($phone));
        $email = $db->real_escape_string(trim($email));

        $db->begin_transaction();
        try {
            $db->query("UPDATE accounts SET phone = '$phone', email = '$email' WHERE id = $admin_id");
            $db->query("UPDATE users SET fullname = '$fullname' WHERE account_id = $admin_id");
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    
    // 3. Kiểm tra mật khẩu cũ (dùng khi đổi mật khẩu Admin)
    public function verifyOldPassword($admin_id, $old_password) {
        $db = $this->getConnection();
        $admin_id = intval($admin_id);
        
        $sql = "SELECT password FROM accounts WHERE id = $admin_id";
        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $account = $result->fetch_assoc();
            if (password_verify($old_password, $account['password']) || $old_password === $account['password']) {
                return true;
            }
        }
        return false;
    }
    // Thêm hàm này vào cuối AdminModel.php
    public function updatePassword($admin_id, $new_password) {
        $db = $this->getConnection();
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE accounts SET password = '$hashed_password' WHERE id = '$admin_id'";
        return $db->query($sql);    
    }
}