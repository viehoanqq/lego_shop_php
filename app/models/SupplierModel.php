<?php
class SupplierModel extends Database {
    
    // Lấy danh sách có phân trang và tìm kiếm
    public function getSuppliers($keyword = '', $status = 'all', $limit = 10, $offset = 0) {
        $db = $this->getConnection();
        $where = "WHERE 1=1 AND status != 'deleted'"; // Ẩn những nhà cung cấp đã xóa mềm

        if (!empty($keyword)) {
            $k = $db->real_escape_string(trim($keyword));
            $where .= " AND (name LIKE '%$k%' OR phone LIKE '%$k%' OR email LIKE '%$k%')";
        }

        if ($status !== 'all') {
            $s = $db->real_escape_string($status);
            $where .= " AND status = '$s'";
        }

        $sql = "SELECT * FROM suppliers $where ORDER BY id DESC LIMIT $offset, $limit";
        $result = $db->query($sql);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Đếm tổng số lượng để phân trang
    public function countSuppliers($keyword = '', $status = 'all') {
        $db = $this->getConnection();
        $where = "WHERE 1=1 and status != 'deleted'"; // Ẩn những nhà cung cấp đã xóa mềm

        if (!empty($keyword)) {
            $k = $db->real_escape_string(trim($keyword));
            $where .= " AND (name LIKE '%$k%' OR phone LIKE '%$k%' OR email LIKE '%$k%')";
        }
        if ($status !== 'all') {
            $s = $db->real_escape_string($status);
            $where .= " AND status = '$s'";
        }

        $sql = "SELECT COUNT(*) as total FROM suppliers $where";
        $result = $db->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
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
}
?>