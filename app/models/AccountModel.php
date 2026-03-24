<?php
class AccountModel extends Database {
    
    // Xử lý Đăng ký
    public function registerFull($data) {
        $db = $this->getConnection();
        
        // 1. Kiểm tra SDT hoặc Email đã tồn tại chưa
        $phone = $db->real_escape_string(trim($data['phone']));
        $email = $db->real_escape_string(trim($data['email']));
        
        $check = $db->query("SELECT id FROM accounts WHERE phone = '$phone' OR email = '$email'");
        if ($check->num_rows > 0) {
            return "Số điện thoại hoặc Email này đã được đăng ký rồi!";
        }

        $db->begin_transaction();
        try {
            // 2. Chèn vào bảng accounts
            $password = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlAcc = "INSERT INTO accounts (phone, email, password, role, status) 
                       VALUES ('$phone', '$email', '$password', 'customer', 'active')";
            $db->query($sqlAcc);
            $account_id = $db->insert_id;

            // 3. Chèn vào bảng users
            $fullname = $db->real_escape_string(trim($data['fullname']));
            $sqlUser = "INSERT INTO users (account_id, fullname) 
                        VALUES ('$account_id', '$fullname')";
            $db->query($sqlUser);
            $user_id = $db->insert_id;

            // 4. Chèn vào bảng user_addresses
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

    // Xử lý Đăng nhập
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

    // Kiểm tra tài khoản tồn tại (dùng cho quên mật khẩu)  
    public function checkAccountExists($username) {
        $db = $this->getConnection();
        $username = $db->real_escape_string(trim($username));
        
        $sql = "SELECT id FROM accounts WHERE (phone = '$username' OR email = '$username') AND status = 'active'";
        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc(); // Trả về mảng chứa id tài khoản
        }
        return false;
    }

    // Cập nhật mật khẩu mới
    public function updatePassword($account_id, $new_password) {
        $db = $this->getConnection();
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE accounts SET password = '$hashed_password' WHERE id = '$account_id'";
        return $db->query($sql);
    }

    //admin login
    public function checkAdminLogin($username, $password) {
    $db = $this->getConnection();
    $username = $db->real_escape_string(trim($username));
    
    // 1. Chỉ lấy ra tài khoản có phone hoặc email khớp và là admin
    $sql = "SELECT * FROM accounts WHERE (phone = '$username' OR email = '$username') AND role = 'admin' AND status = 'active'";
    $result = $db->query($sql);

    if ($result && $result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        
        // 2. KIỂM TRA MẬT KHẨU
        // Dùng password_verify để kiểm tra với mật khẩu đã mã hóa trong DB
        if (password_verify($password, $admin['password'])) {
            unset($admin['password']); 
            return $admin;
        }
        
        // 3. (Tạm thời) Nếu bạn chưa kịp mã hóa pass admin trong DB (vẫn để 123456)
        // thì dùng dòng dưới này, nhưng sau đó phải đổi sang hash ngay!
        if ($password === $admin['password'] && !empty($admin['password'])) {
            unset($admin['password']);
            return $admin;
        }
    }
    return false;
}
}