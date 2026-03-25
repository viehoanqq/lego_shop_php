<?php
class AccountModel extends Database {
    
    // 1. Xử lý Đăng ký
    public function registerFull($data) {
        $db = $this->getConnection();
        
        // Kiểm tra SDT hoặc Email đã tồn tại chưa
        $phone = $db->real_escape_string(trim($data['phone']));
        $email = $db->real_escape_string(trim($data['email']));
        
        $check = $db->query("SELECT id FROM accounts WHERE phone = '$phone' OR email = '$email'");
        if ($check->num_rows > 0) {
            return "Số điện thoại hoặc Email này đã được đăng ký rồi!";
        }

        $db->begin_transaction();
        try {
            // Chèn vào bảng accounts
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlAcc = "INSERT INTO accounts (phone, email, password, role, status) 
                       VALUES ('$phone', '$email', '$password', 'customer', 'active')";
            $db->query($sqlAcc);
            $account_id = $db->insert_id;

            // Chèn vào bảng users
            $fullname = $db->real_escape_string(trim($data['fullname']));
            $sqlUser = "INSERT INTO users (account_id, fullname) 
                        VALUES ('$account_id', '$fullname')";
            $db->query($sqlUser);
            $user_id = $db->insert_id;

            // Chèn vào bảng user_addresses
            $street = $db->real_escape_string(trim($data['street']));
            $ward = $db->real_escape_string(trim($data['ward']));
            $district = $db->real_escape_string(trim($data['district']));
            $city = $db->real_escape_string(trim($data['city']));
            
            $sqlAddr = "INSERT INTO user_addresses (user_id, receiver_name, receiver_phone, street, ward, district, city, is_default) 
                        VALUES ('$user_id', '$fullname', '$phone', '$street', '$ward', '$district', '$city', 1)";
            $db->query($sqlAddr);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return "Lỗi hệ thống: " . $e->getMessage();
        }
    }

    // 2. Xử lý Đăng nhập Khách hàng
    public function login($username, $password) {
        $db = $this->getConnection();
        $username = $db->real_escape_string(trim($username));
        
        $sql = "SELECT a.*, u.fullname, u.id as user_id 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE (a.phone = '$username' OR a.email = '$username') AND a.status = 'active'";
                
        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $account = $result->fetch_assoc();
            if (password_verify($password, $account['password'])) {
                unset($account['password']);
                return $account;
            }
        }
        return false; 
    }
    public function verifyOldPassword($account_id, $old_password) {
        $db = $this->getConnection();
        $account_id = intval($account_id);
        
        $sql = "SELECT password FROM accounts WHERE id = $account_id AND status = 'active'";
        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $account = $result->fetch_assoc();
            // Hàm password_verify tự động so sánh pass nhập vào với pass đã băm trong DB
            if (password_verify($old_password, $account['password'])) {
                return true;
            }
        }
        return false;
    }

    // 3. Xử lý Đăng nhập Admin
    public function checkAdminLogin($username, $password) {
        $db = $this->getConnection();
        $username = $db->real_escape_string(trim($username));
        
        $sql = "SELECT a.*, u.fullname 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE (a.phone = '$username' OR a.email = '$username') 
                AND a.role = 'admin' AND a.status = 'active'";
                
        $result = $db->query($sql);

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // KIỂM TRA MẬT KHẨU (Đã mã hóa)
            if (password_verify($password, $admin['password'])) {
                unset($admin['password']); 
                return $admin;
            }
            
            // KIỂM TRA MẬT KHẨU (Chưa mã hóa - dự phòng)
            if ($password === $admin['password'] && !empty($admin['password'])) {
                unset($admin['password']);
                return $admin;
            }
        }
        return false;
    }

    // 4. Kiểm tra tài khoản tồn tại (Quên mật khẩu)  
    public function checkAccountExists($username) {
        $db = $this->getConnection();
        $username = $db->real_escape_string(trim($username));
        
        $sql = "SELECT id FROM accounts WHERE (phone = '$username' OR email = '$username') AND status = 'active'";
        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    // 5. Cập nhật mật khẩu mới (Reset mật khẩu)
    public function updatePassword($account_id, $new_password) {
        $db = $this->getConnection();
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE accounts SET password = '$hashed_password' WHERE id = '$account_id'";
        return $db->query($sql);
    }
}