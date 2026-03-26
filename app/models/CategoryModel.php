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
    public function getAdminCategoriesWithCount($keyword = '', $status = 'all', $limit = 6, $offset = 0) {
        $db = $this->getConnection();
        $sql = "SELECT c.*, COUNT(p.id) as product_count 
                FROM categories c 
                LEFT JOIN products p ON c.id = p.category_id 
                WHERE 1=1";

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
        $sql = "SELECT COUNT(*) as total FROM categories WHERE 1=1";
        // ... Thêm các điều kiện lọc giống hệt hàm trên ...
        $result = $db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
    
}