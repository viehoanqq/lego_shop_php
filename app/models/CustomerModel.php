<?php
class CustomerModel extends Database {
    
    public function getAllCustomers($search = '', $status = '', $limit = null, $offset = null) {
        $db = $this->getConnection();
        // ĐỔI JOIN THÀNH LEFT JOIN
        $sql = "SELECT a.id, a.phone, a.email, a.status, a.role, a.created_at, u.fullname 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE 1=1";

        if (!empty($search)) {
            $search = $db->real_escape_string($search);
            // Dùng COALESCE để tránh lỗi nếu fullname bị NULL
            $sql .= " AND (COALESCE(u.fullname, '') LIKE '%$search%' OR a.email LIKE '%$search%' OR a.phone LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = $db->real_escape_string($status);
            $sql .= " AND a.status = '$status'";
        }

        $sql .= " ORDER BY a.created_at DESC";
        
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT " . (int)$offset . ", " . (int)$limit;
        }

        $result = $db->query($sql);
        $customers = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $customers[] = $row;
            }
        }
        return $customers;
    }

    // Thêm hàm mới để đếm tổng số khách hàng (phục vụ tính số trang)
    public function countAllCustomers($search = '', $status = '') {
        $db = $this->getConnection();
        // ĐỔI JOIN THÀNH LEFT JOIN
        $sql = "SELECT COUNT(*) as total 
                FROM accounts a 
                LEFT JOIN users u ON a.id = u.account_id 
                WHERE 1=1";

        if (!empty($search)) {
            $search = $db->real_escape_string($search);
            $sql .= " AND (COALESCE(u.fullname, '') LIKE '%$search%' OR a.email LIKE '%$search%' OR a.phone LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = $db->real_escape_string($status);
            $sql .= " AND a.status = '$status'";
        }

        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
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