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
        if (!isset($_GET['id']) || !isset($_GET['status'])) {
            $_SESSION['error'] = 'notfound';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $id = (int) $_GET['id'];
        $currentStatus = $_GET['status'];

        // Validate status hợp lệ
        if (!in_array($currentStatus, ['approved', 'hidden'])) {
            $_SESSION['error'] = 'invalid';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Đảo trạng thái
        $newStatus = ($currentStatus === 'approved') ? 'hidden' : 'approved';

        if ($this->reviewModel->updateStatus($id, $newStatus)) {
            set_flash_message('msg', 'updated');
        } else {
            set_flash_message('error', 'db');
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }


    // ===== DELETE REVIEW =====
    public function delete($id) {
        if (empty($id)) {
            $_SESSION['error'] = 'notfound';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $id = (int) $id;

        if ($this->reviewModel->deleteReview($id)) {
            set_flash_message('msg', 'deleted');
        } else {
            set_flash_message('error', 'db');
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}