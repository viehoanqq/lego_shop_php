<?php
class CustomerModel extends Database {
    
    public function getAllCustomers($search = '', $status = '', $limit = null, $offset = null) {
        $db = $this->getConnection();
        $sql = "SELECT a.id, a.phone, a.email, a.status, a.role, a.created_at, u.fullname 
                FROM accounts a 
                JOIN users u ON a.id = u.account_id 
                WHERE 1=1";

        if (!empty($search)) {
            $search = $db->real_escape_string($search);
            $sql .= " AND (u.fullname LIKE '%$search%' OR a.email LIKE '%$search%' OR a.phone LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = $db->real_escape_string($status);
            $sql .= " AND a.status = '$status'";
        }

        $sql .= " ORDER BY a.created_at DESC";
        
        // Thêm phân trang nếu có tham số
        if ($limit !== null && $offset !== null) {
            $sql .= " LIMIT $offset, $limit";
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
        $sql = "SELECT COUNT(*) as total FROM accounts a JOIN users u ON a.id = u.account_id WHERE 1=1";

        if (!empty($search)) {
            $search = $db->real_escape_string($search);
            $sql .= " AND (u.fullname LIKE '%$search%' OR a.email LIKE '%$search%' OR a.phone LIKE '%$search%')";
        }

        if (!empty($status)) {
            $status = $db->real_escape_string($status);
            $sql .= " AND a.status = '$status'";
        }

        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
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
}