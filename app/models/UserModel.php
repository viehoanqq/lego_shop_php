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
    // Lấy danh sách địa chỉ của User
    public function getUserAddresses($user_id) {
        $db = $this->getConnection();
        $user_id = intval($user_id);
        
        // Lấy danh sách, ưu tiên địa chỉ mặc định (is_default = 1) xếp lên đầu
        $sql = "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY is_default DESC, id DESC";
        $result = $db->query($sql);
        
        $addresses = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $addresses[] = $row;
            }
        }
        return $addresses;
    }

    // [MỚI THÊM] - Thêm địa chỉ mới cho Khách hàng
    public function addUserAddress($user_id, $data) {
        $db = $this->getConnection();
        $user_id = intval($user_id);
        
        $name = $db->real_escape_string(trim($data['receiver_name']));
        $phone = $db->real_escape_string(trim($data['receiver_phone']));
        $street = $db->real_escape_string(trim($data['street']));
        $ward = $db->real_escape_string(trim($data['ward']));
        $district = $db->real_escape_string(trim($data['district']));
        $city = $db->real_escape_string(trim($data['city']));
        
        // Kiểm tra xem khách có tick ô "Đặt làm mặc định" không
        $is_default = isset($data['is_default']) ? 1 : 0;

        $db->begin_transaction();
        try {
            // LOGIC THÔNG MINH 1: Nếu chọn làm mặc định, phải gỡ mặc định của các địa chỉ cũ
            if ($is_default == 1) {
                $db->query("UPDATE user_addresses SET is_default = 0 WHERE user_id = $user_id");
            } else {
                // LOGIC THÔNG MINH 2: Nếu đây là địa chỉ ĐẦU TIÊN của khách, ép nó thành mặc định luôn
                $check = $db->query("SELECT id FROM user_addresses WHERE user_id = $user_id");
                if ($check->num_rows == 0) {
                    $is_default = 1;
                }
            }

            // Chèn địa chỉ mới
            $sql = "INSERT INTO user_addresses (user_id, receiver_name, receiver_phone, street, ward, district, city, is_default) 
                    VALUES ($user_id, '$name', '$phone', '$street', '$ward', '$district', '$city', $is_default)";
            $db->query($sql);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    // [MỚI] - Thiết lập địa chỉ mặc định
    public function setDefaultAddress($address_id, $user_id) {
        $db = $this->getConnection();
        $address_id = intval($address_id);
        $user_id = intval($user_id);

        $db->begin_transaction();
        try {
            // Bước 1: Gỡ bỏ mặc định của tất cả địa chỉ cũ
            $db->query("UPDATE user_addresses SET is_default = 0 WHERE user_id = $user_id");
            // Bước 2: Bật mặc định cho địa chỉ được chọn
            $db->query("UPDATE user_addresses SET is_default = 1 WHERE id = $address_id AND user_id = $user_id");
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // [MỚI] - Cập nhật (Sửa) địa chỉ
    public function updateUserAddress($address_id, $user_id, $data) {
        $db = $this->getConnection();
        $address_id = intval($address_id);
        $user_id = intval($user_id);

        $name = $db->real_escape_string(trim($data['receiver_name']));
        $phone = $db->real_escape_string(trim($data['receiver_phone']));
        $street = $db->real_escape_string(trim($data['street']));
        $ward = $db->real_escape_string(trim($data['ward']));
        $district = $db->real_escape_string(trim($data['district']));
        $city = $db->real_escape_string(trim($data['city']));
        
        $is_default = isset($data['is_default']) ? 1 : 0;

        $db->begin_transaction();
        try {
            if ($is_default == 1) {
                $db->query("UPDATE user_addresses SET is_default = 0 WHERE user_id = $user_id");
            }

            $sql = "UPDATE user_addresses SET 
                    receiver_name = '$name', receiver_phone = '$phone', 
                    street = '$street', ward = '$ward', district = '$district', city = '$city', 
                    is_default = $is_default 
                    WHERE id = $address_id AND user_id = $user_id";
            $db->query($sql);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    // [MỚI] - Xóa địa chỉ
    public function deleteUserAddress($address_id, $user_id) {
        $db = $this->getConnection();
        $address_id = intval($address_id);
        $user_id = intval($user_id);

        // Lớp bảo vệ: Kiểm tra xem địa chỉ này có phải mặc định không
        $check = $db->query("SELECT is_default FROM user_addresses WHERE id = $address_id AND user_id = $user_id");
        if ($check && $check->num_rows > 0) {
            $row = $check->fetch_assoc();
            if ($row['is_default'] == 1) {
                return false; // Đang là mặc định -> Báo lỗi, không cho xóa
            }
        }

        // Nếu an toàn thì Xóa
        $sql = "DELETE FROM user_addresses WHERE id = $address_id AND user_id = $user_id";
        return $db->query($sql);
    }
    // Hàm lưu địa chỉ mới từ trang Checkout (Dùng AJAX)
    public function saveAddress($user_id, $data) {
        $db = $this->getConnection();
        
        $receiver_name = $data['receiver_name'];
        $receiver_phone = $data['receiver_phone'];
        $city = $data['city'];
        $district = $data['district'];
        $ward = $data['ward'];
        $street = $data['street'];
        
        // Mặc định địa chỉ mới thêm từ checkout sẽ không phải là mặc định (is_default = 0)
        // Nếu bạn muốn nó làm mặc định luôn thì đổi số 0 thành 1
        $sql = "INSERT INTO user_addresses (user_id, receiver_name, receiver_phone, city, district, ward, street, is_default) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("issssss", $user_id, $receiver_name, $receiver_phone, $city, $district, $ward, $street);
        
        if ($stmt->execute()) {
            return $db->insert_id; // Trả về ID của dòng vừa chèn để gửi lại cho AJAX
        }
        
        return false;
    }
    public function getAddressById($address_id, $user_id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM user_addresses WHERE id = ? AND user_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $address_id, $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Trả về 1 dòng dữ liệu mảng
    }
}