<?php
class CustomerModel extends Database {
    
    // --- SỬA 2 HÀM LẤY DANH SÁCH & ĐẾM ĐỂ HỖ TRỢ TRẠNG THÁI DELETED ---
    public function getAllCustomers($search = '', $status = '', $limit = null, $offset = null) {
        $db = $this->getConnection();
        $sql = "SELECT a.id, a.phone, a.email, a.status, a.role, a.created_at, u.fullname 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE 1=1";

        if (!empty($search)) {
            $search = $db->real_escape_string($search);
            $sql .= " AND (COALESCE(u.fullname, '') LIKE '%$search%' OR a.email LIKE '%$search%' OR a.phone LIKE '%$search%')";
        }

        // Ẩn tài khoản đã xóa mềm nếu không chọn lọc cụ thể
        if (empty($status)) {
            $sql .= " AND a.status != 'deleted'";
        } else {
            $status = $db->real_escape_string($status);
            $sql .= " AND a.status = '$status'";
        }

        // ==========================================
        // ĐÃ SỬA: Đẩy tài khoản 'locked' và 'deleted' xuống cuối danh sách
        // ==========================================
        $sql .= " ORDER BY CASE 
                    WHEN a.status = 'active' THEN 0 
                    WHEN a.status = 'locked' THEN 1 
                    ELSE 2 
                  END ASC, a.created_at DESC";

        // Xử lý LIMIT & OFFSET
        if ($limit !== null && $offset !== null) { 
            $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit; 
        }
        
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function countAllCustomers($search = '', $status = '') {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM accounts a LEFT JOIN users u ON a.id = u.account_id WHERE 1=1";

        if (!empty($search)) {
            $search = $db->real_escape_string($search);
            $sql .= " AND (COALESCE(u.fullname, '') LIKE '%$search%' OR a.email LIKE '%$search%' OR a.phone LIKE '%$search%')";
        }

        if (empty($status)) {
            $sql .= " AND a.status != 'deleted'";
        } else {
            $status = $db->real_escape_string($status);
            $sql .= " AND a.status = '$status'";
        }

        $result = $db->query($sql);
        return (int)$result->fetch_assoc()['total'];
    }

    // ==========================================
    // CÁC HÀM XỬ LÝ XÓA / KHÔI PHỤC
    // ==========================================

    // Kiểm tra xem khách hàng đã có đơn hàng nào chưa
    public function hasTransactions($accountId) {
        $db = $this->getConnection();
        $accountId = intval($accountId);
        
        // 1. Kiểm tra xem User này có đơn hàng không (Dành cho Customer)
        $sqlOrder = "SELECT COUNT(*) as total FROM orders o 
                     JOIN users u ON o.user_id = u.id 
                     WHERE u.account_id = $accountId";
        $hasOrder = $db->query($sqlOrder)->fetch_assoc()['total'] > 0;

        // 2. Kiểm tra xem Account này có tạo phiếu nhập nào không (Dành cho Admin)
        $sqlImport = "SELECT COUNT(*) as total FROM import_receipts WHERE admin_id = $accountId";
        $hasImport = $db->query($sqlImport)->fetch_assoc()['total'] > 0;

        return ($hasOrder || $hasImport);
    }

    // Xóa vĩnh viễn (Phải xóa sạch các bảng con liên kết trước)
    public function deleteAccountForever($accountId) {
        $db = $this->getConnection();
        $id = intval($accountId);
        
        // Nhờ DB cài đặt sẵn ON DELETE CASCADE ở các bảng: 
        // users, carts, cart_items, product_reviews, wishlists, user_addresses
        // Chúng ta CHỈ CẦN XÓA ACCOUNTS, MySQL sẽ tự động dọn sạch rác ở các bảng kia!
        try {
            $db->query("DELETE FROM accounts WHERE id = $id");
            return true;
        } catch (Exception $e) {
            error_log("Lỗi Xóa Vĩnh Viễn Account: " . $e->getMessage());
            return false;
        }
    }

    // Xóa mềm (Chuyển status thành deleted)
    public function softDeleteAccount($accountId) {
        $db = $this->getConnection();
        $sql = "UPDATE accounts SET status = 'deleted' WHERE id = " . intval($accountId);
        return $db->query($sql);
    }
    public function updateStatus($id, $newStatus) {
        $db = $this->getConnection();
        $id = intval($id);
        $newStatus = $db->real_escape_string($newStatus);

        $sql = "UPDATE accounts SET status = '$newStatus' WHERE id = $id";
        return $db->query($sql);
    }

    public function getAccountById($id) {
        $db = $this->getConnection();
        $id = intval($id);
        $sql = "SELECT status, role FROM accounts WHERE id = $id";
        $result = $db->query($sql);
        return $result->fetch_assoc();
    }

    public function addCustomer($data) {
        $db = $this->getConnection();
        
        // 1. Kiểm tra xem Email hoặc Phone đã tồn tại chưa (Tránh lỗi Duplicate Entry)
        $checkSql = "SELECT id FROM accounts WHERE email = ? OR phone = ?";
        $stmtCheck = $db->prepare($checkSql);
        $stmtCheck->bind_param("ss", $data['email'], $data['phone']);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            return false; // Email hoặc SĐT đã tồn tại
        }

        $db->begin_transaction();
        try {
            // 2. Chèn vào bảng accounts
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $sqlAcc = "INSERT INTO accounts (email, phone, password, role, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $stmtAcc = $db->prepare($sqlAcc);
            $stmtAcc->bind_param("sssss", $data['email'], $data['phone'], $hashedPassword, $data['role'], $data['status']);
            $stmtAcc->execute();
            
            $accountId = $db->insert_id;

            // 3. Chèn vào bảng users
            $sqlUser = "INSERT INTO users (account_id, fullname) VALUES (?, ?)";
            $stmtUser = $db->prepare($sqlUser);
            $stmtUser->bind_param("is", $accountId, $data['fullname']);
            $stmtUser->execute();

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            error_log("Lỗi chèn Customer: " . $e->getMessage());
            return false;
        }
    }

    public function checkExist($email, $phone, $excludeId = null) {
        $db = $this->getConnection();
        $sql = "SELECT email, phone FROM accounts WHERE (email = ? OR phone = ?)";
        
        // Nếu là đang SỬA (Update), ta cần loại trừ ID hiện tại ra
        if ($excludeId) {
            $sql .= " AND id != " . intval($excludeId);
        }

        $stmt = $db->prepare($sql);
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['email'] === $email) return 'email';
            if ($row['phone'] === $phone) return 'phone';
        }
        return false;
    }


    public function getAccount($id) {
        $db = $this->getConnection();
        $id = intval($id);

        // Truy vấn kết hợp (JOIN) để lấy fullname từ bảng users 
        // và các thông tin khác từ bảng accounts
        $sql = "SELECT a.id, a.email, a.phone, a.status, a.role, u.fullname 
                FROM accounts a 
                JOIN users u ON a.id = u.account_id 
                WHERE a.id = $id 
                LIMIT 1";

        $result = $db->query($sql);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    /**
 * Cập nhật thông tin khách hàng ở cả 2 bảng accounts và users
 */
public function updateCustomer($id, $data) {
    $db = $this->getConnection();
    $id = intval($id);
    
    // Bắt đầu Transaction để đảm bảo dữ liệu đồng nhất ở cả 2 bảng
    $db->begin_transaction();
    
    try {
        // 1. Cập nhật bảng accounts
        $sqlAcc = "UPDATE accounts SET email = ?, phone = ?, role = ?, status = ? ";
        $params = [$data['email'], $data['phone'], $data['role'], $data['status']];
        $types = "ssss";

        // Kiểm tra nếu người dùng có nhập mật khẩu mới thì mới cập nhật password
        if (!empty($data['password'])) {
            $sqlAcc .= ", password = ? ";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            $types .= "s";
        }
        
        $sqlAcc .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmtAcc = $db->prepare($sqlAcc);
        $stmtAcc->bind_param($types, ...$params);
        $stmtAcc->execute();

        // 2. Cập nhật bảng users (fullname)
        $sqlUser = "UPDATE users SET fullname = ? WHERE account_id = ?";
        $stmtUser = $db->prepare($sqlUser);
        $stmtUser->bind_param("si", $data['fullname'], $id);
        $stmtUser->execute();

        // Hoàn tất lưu dữ liệu
        $db->commit();
        return true;
    } catch (Exception $e) {
        // Nếu có lỗi, hoàn tác lại toàn bộ (không sửa gì cả)
        $db->rollback();
        error_log("Lỗi updateCustomer: " . $e->getMessage());
        return false;
    }
}
}