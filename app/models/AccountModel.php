<?php
class AccountModel extends Database {
    public function registerFull($data) {
        $db = $this->getConnection();
        
        // 1. Kiểm tra SDT hoặc Email đã tồn tại trong bảng accounts chưa
        $phone = $db->real_escape_string($data['phone']);
        $email = $db->real_escape_string($data['email']);
        
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
            $fullname = $db->real_escape_string($data['fullname']);
            $sqlUser = "INSERT INTO users (account_id, fullname) 
                        VALUES ('$account_id', '$fullname')";
            $db->query($sqlUser);
            $user_id = $db->insert_id;

            // 4. Chèn vào bảng user_addresses
            $street = $db->real_escape_string($data['street']);
            $ward = $db->real_escape_string($data['ward']);
            $district = $db->real_escape_string($data['district']);
            $city = $db->real_escape_string($data['city']);
            
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
}