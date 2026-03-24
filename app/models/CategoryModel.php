<?php
class CategoryModel extends Database {
    
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
}