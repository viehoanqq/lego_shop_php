<?php
class AdminReviewController extends Controller {
    private $reviewModel;

    public function __construct() {
        $this->reviewModel = $this->model('ReviewModel');
    }

    // Hiển thị danh sách review
    public function index() {
        // Nhận dữ liệu từ form tìm kiếm
        $product_id = $_GET['product_id'] ?? null;
        $keyword = $_GET['keyword'] ?? '';
        $rating = $_GET['rating'] ?? '';

        $reviews = $this->reviewModel->getReviews($product_id, $keyword, $rating);

        $this->view('admin/review', [
            'reviews' => $reviews,
            'keyword' => $keyword,
            'rating'  => $rating,
            'product_id' => $product_id
        ]);
    }

    // Thay đổi trạng thái Duyệt/Ẩn
    public function toggleStatus() {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id = $_GET['id'];
            $currentStatus = $_GET['status'];
            
            // Đảo trạng thái
            $newStatus = ($currentStatus === 'approved') ? 'hidden' : 'approved';
            
            if ($this->reviewModel->updateStatus($id, $newStatus)) {
                $_SESSION['success'] = "Cập nhật trạng thái thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật.";
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']); // Quay lại trang trước đó
        exit();
    }

    // Xóa đánh giá
    public function delete($id) {
        if ($this->reviewModel->deleteReview($id)) {
            $_SESSION['success'] = "Đã xóa đánh giá.";
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}