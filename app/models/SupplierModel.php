<?php
class SupplierModel extends Database {
    
    // Lấy danh sách có phân trang và tìm kiếm
    public function getSuppliers($keyword = '', $status = 'all', $limit = 10, $offset = 0) {
        $db = $this->getConnection();
        $where = "WHERE 1=1";

        // Nếu xem tất cả thì ẩn xóa mềm. Nếu cố tình lọc 'deleted' thì hiện xóa mềm.
        if ($status === 'all') {
            $where .= " AND status != 'deleted'";
        } else {
            $s = $db->real_escape_string($status);
            $where .= " AND status = '$s'";
        }

        if (!empty($keyword)) {
            $k = $db->real_escape_string(trim($keyword));
            $where .= " AND (name LIKE '%$k%' OR phone LIKE '%$k%' OR email LIKE '%$k%')";
        }

        // ==========================================
        // ĐÃ SỬA: Ưu tiên Active lên đầu, đẩy Locked/Inactive và Deleted xuống đáy
        // ==========================================
        $sql = "SELECT * FROM suppliers $where 
                ORDER BY CASE 
                    WHEN status = 'active' THEN 0 
                    WHEN status = 'locked' THEN 1  -- (Hoặc 'inactive' tùy cách bạn đặt tên trong DB)
                    ELSE 2 
                END ASC, id DESC 
                LIMIT $offset, $limit";
                
        $result = $db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    // Đếm tổng số lượng (Cập nhật hỗ trợ lọc deleted)
    public function countSuppliers($keyword = '', $status = 'all') {
        $db = $this->getConnection();
        $where = "WHERE 1=1";

        if ($status === 'all') {
            $where .= " AND status != 'deleted'";
        } else {
            $s = $db->real_escape_string($status);
            $where .= " AND status = '$s'";
        }

        if (!empty($keyword)) {
            $k = $db->real_escape_string(trim($keyword));
            $where .= " AND (name LIKE '%$k%' OR phone LIKE '%$k%' OR email LIKE '%$k%')";
        }

        $sql = "SELECT COUNT(*) as total FROM suppliers $where";
        $result = $db->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    // THÊM MỚI: Hàm khôi phục nhà cung cấp
    public function restoreSupplier($id) {
        $db = $this->getConnection();
        // Khôi phục về trạng thái 'locked' để an toàn, Admin cần thì tự mở lại
        $sql = "UPDATE suppliers SET status = 'locked' WHERE id = " . intval($id);
        return $db->query($sql);
    }

    // Lấy 1 nhà cung cấp theo ID
    public function getSupplierById($id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM suppliers WHERE id = " . intval($id);
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }

    // Thêm mới
    public function insertSupplier($data) {
        $db = $this->getConnection();
        $sql = "INSERT INTO suppliers (name, phone, email, address, status, created_at) 
                VALUES (?, ?, ?, ?, 'active', NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ssss", $data['name'], $data['phone'], $data['email'], $data['address']);
        return $stmt->execute();
    }

    // Cập nhật
    public function updateSupplier($id, $data) {
        $db = $this->getConnection();
        
        // FIX LỖI: Gán biến ra ngoài trước khi bind_param
        $id = intval($id); 
        
        $sql = "UPDATE suppliers SET name = ?, phone = ?, email = ?, address = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        
        // Truyền biến $id vào đây
        $stmt->bind_param("ssssi", $data['name'], $data['phone'], $data['email'], $data['address'], $id);
        
        return $stmt->execute();
    }

    // Khóa / Mở khóa nhà cung cấp
    public function toggleStatus($id, $current_status) {
        $db = $this->getConnection();
        $new_status = ($current_status === 'active') ? 'locked' : 'active';
        $sql = "UPDATE suppliers SET status = '$new_status' WHERE id = " . intval($id);
        return $db->query($sql);
    }
    public function hasImportHistory($id) {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM import_receipts WHERE supplier_id = " . intval($id);
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] > 0;
    }

    // Xóa vĩnh viễn (Hard Delete)
    public function deleteSupplierForever($id) {
        $db = $this->getConnection();
        $sql = "DELETE FROM suppliers WHERE id = " . intval($id);
        return $db->query($sql);
    }

    // Xóa mềm / Ẩn (Soft Delete)
    public function softDeleteSupplier($id) {
        $db = $this->getConnection();
        $sql = "UPDATE suppliers SET status = 'deleted' WHERE id = " . intval($id);
        return $db->query($sql);
    }
    public function isNameExists($name, $exclude_id = null) {
        $db = $this->getConnection();
        $name = $db->real_escape_string(trim($name));
        $sql = "SELECT id FROM suppliers WHERE name = '$name'";
        if ($exclude_id) $sql .= " AND id != " . intval($exclude_id);
        
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0);
    }

    public function isPhoneExists($phone, $exclude_id = null) {
        $db = $this->getConnection();
        $phone = $db->real_escape_string(trim($phone));
        $sql = "SELECT id FROM suppliers WHERE phone = '$phone'";
        if ($exclude_id) $sql .= " AND id != " . intval($exclude_id);
        
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0);
    }

    public function isEmailExists($email, $exclude_id = null) {
        if (empty($email)) return false; // Cho phép email trống
        $db = $this->getConnection();
        $email = $db->real_escape_string(trim($email));
        $sql = "SELECT id FROM suppliers WHERE email = '$email'";
        if ($exclude_id) $sql .= " AND id != " . intval($exclude_id);
        
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0);
    }
}
?>