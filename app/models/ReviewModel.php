<?php
class ReviewModel extends Database {
    
    // Lấy danh sách review kèm tên khách hàng và tên sản phẩm
    public function getReviews($product_id = null, $keyword = '', $rating = '', $offset = 0, $limit = 6) {
        $db = $this->getConnection();
        
        $where = "WHERE 1=1 ";

        if ($product_id) {
            $where .= " AND r.product_id = " . (int)$product_id;
        }
        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (u.fullname LIKE '%$k%' OR p.name LIKE '%$k%')";
        }
        if (!empty($rating)) {
            $where .= " AND r.rating = " . (int)$rating;
        }

        $sql = "SELECT r.*, u.fullname, p.name as product_name 
                FROM product_reviews r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                $where
                ORDER BY r.created_at DESC
                LIMIT $offset, $limit";
        
        $result = $db->query($sql);
        return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    public function countReviews($product_id = null, $keyword = '', $rating = '') {
        $db = $this->getConnection();
        $where = "WHERE 1=1 ";

        if ($product_id) {
            $where .= " AND r.product_id = " . (int)$product_id;
        }
        if (!empty($keyword)) {
            $k = $db->real_escape_string($keyword);
            $where .= " AND (u.fullname LIKE '%$k%' OR p.name LIKE '%$k%')";
        }
        if (!empty($rating)) {
            $where .= " AND r.rating = " . (int)$rating;
        }

        $sql = "SELECT COUNT(*) as total
                FROM product_reviews r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                $where";
        
        $result = $db->query($sql);
        return $result ? $result->fetch_assoc()['total'] : 0;
    }

    // Cập nhật trạng thái 'approved' hoặc 'hidden'
    public function updateStatus($review_id, $status) {
        $db = $this->getConnection();
        $status = $db->real_escape_string($status);
        $id = (int)$review_id;
        
        $sql = "UPDATE product_reviews SET status = '$status' WHERE id = $id";
        return $db->query($sql);
    }

    // Xóa đánh giá
    public function deleteReview($id) {
        $db = $this->getConnection();
        $id = (int)$id;
        return $db->query("DELETE FROM product_reviews WHERE id = $id");
    }
}