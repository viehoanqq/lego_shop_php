<?php
class CategoryModel extends Database {
    public function getCategoryById($id) {
        $db = $this->getConnection();
        $sql = "SELECT * FROM categories WHERE id = " . intval($id);
        $result = $db->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : false;
    }
    // Lấy tất cả danh mục đang hoạt động
    public function getAllCategories() {
        $db = $this->getConnection();
        $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY ordering ASC";
        $result = $db->query($sql);
        
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }
    public function getCategoriesWithCount() {
        $db = $this->getConnection();
        // Nối bảng categories và products để đếm số sản phẩm (chỉ đếm sản phẩm đang active)
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 1 
                WHERE c.status = 'active' 
                GROUP BY c.id 
                ORDER BY c.ordering ASC";
                
        $result = $db->query($sql);
        $categories = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
        return $categories;
    }
    // Nếu bạn muốn đếm số lượng trong giỏ hàng luôn thì viết thêm hàm ở đây
    // public function getCartCount($user_id) { ... }

    public function insert($data) {
        $db = $this->getConnection();
        $name = $db->real_escape_string($data['name']);
        $desc = $db->real_escape_string($data['description']);
        $img = $db->real_escape_string($data['image_url']);
        
        $sql = "INSERT INTO categories (name, description, image_url, status) 
                VALUES ('$name', '$desc', '$img', 'active')";
        return $db->query($sql);
    }

    public function update($id, $data) {
        $db = $this->getConnection();
        $id = intval($id);
        $name = $db->real_escape_string($data['name']);
        $desc = $db->real_escape_string($data['description']);
        $img = $db->real_escape_string($data['image_url']);
        
        $sql = "UPDATE categories SET name='$name', description='$desc', image_url='$img' WHERE id=$id";
        return $db->query($sql);
    }

    public function updateStatus($id, $status) {
        $db = $this->getConnection();
        $id = intval($id);
        $status = $db->real_escape_string($status);
        $sql = "UPDATE categories SET status = '$status' WHERE id = $id";
        return $db->query($sql);
    }

    // Lấy tất cả danh mục cho trang Admin (Bao gồm cả active và locked)
    // Thêm tham số $limit và $offset vào cuối
    // 1. CHẶN HIỂN THỊ DANH MỤC ĐÃ XÓA MỀM
    public function getAdminCategoriesWithCount($keyword = '', $status = 'all', $limit = 6, $offset = 0) {
        $db = $this->getConnection();
        // Đổi WHERE 1=1 thành WHERE c.status != 'deleted'
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id AND p.status = 1
                WHERE c.status != 'deleted'"; 

        if (!empty($keyword)) {
            $keyword = $db->real_escape_string($keyword);
            $sql .= " AND c.name LIKE '%$keyword%'";
        }
        if ($status !== 'all') {
            $status = $db->real_escape_string($status);
            $sql .= " AND c.status = '$status'";
        }

        $sql .= " GROUP BY c.id ORDER BY c.id DESC LIMIT $offset, $limit";
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Hàm đếm tổng số bản ghi
    public function countAdminCategories($keyword = '', $status = 'all') {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM categories WHERE status != 'deleted'"; 
        
        if (!empty($keyword)) {
            $keyword = $db->real_escape_string($keyword);
            $sql .= " AND name LIKE '%$keyword%'";
        }
        if ($status !== 'all') {
            $status = $db->real_escape_string($status);
            $sql .= " AND status = '$status'";
        }
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    // 3. THÊM HÀM XÓA MỀM VÀO CUỐI FILE
    public function softDeleteCategory($id) {
        $db = $this->getConnection();
        $id = intval($id);
        
        $db->begin_transaction();
        try {
            // Chuyển trạng thái danh mục thành 'deleted' (Ẩn khỏi bảng)
            $db->query("UPDATE categories SET status = 'deleted' WHERE id = $id");
            // Tự động Khóa (status = 2) tất cả sản phẩm thuộc danh mục này để không bán nhầm
            $db->query("UPDATE products SET status = 2 WHERE category_id = $id");
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }

    // Cập nhật trạng thái danh mục và ẨN/HIỆN toàn bộ sản phẩm thuộc danh mục đó
    public function updateStatusAdmin($id, $status) {
        $db = $this->getConnection();
        $id = intval($id);
        
        $db->begin_transaction();
        try {
            // 1. Luôn cập nhật trạng thái của Danh mục
            $catStatus = ($status == 'active') ? 'active' : 'locked';
            $sqlCat = "UPDATE categories SET status = '$catStatus' WHERE id = $id";
            $db->query($sqlCat);

            // 2. Chỉ khi KHÓA (locked) thì mới cập nhật sản phẩm thành 0 (Ẩn)
            // Nếu là 'active', chúng ta KHÔNG làm gì bảng products cả
            if ($status == 'locked') {
                $sqlProd = "UPDATE products SET status = 2 WHERE category_id = $id";
                $db->query($sqlProd);
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
            return false;
        }
    }
    
}