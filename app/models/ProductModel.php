<?php
class ProductModel extends Database {
    public function getAllProducts() {
        $db = $this->getConnection();
        $sql = "SELECT * FROM products"; // Giả sử bảng của bạn tên là products
        $result = $db->query($sql);
        
        $data = [];
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}