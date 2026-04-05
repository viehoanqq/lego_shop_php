<?php
class AdminReviewController extends Controller {
    private $reviewModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
        $this->reviewModel = $this->model('ReviewModel');
    }

    // Hiển thị danh sách review
    public function index() {
        // Nhận dữ liệu từ form tìm kiếm
        $product_id = $_GET['product_id'] ?? null;
        $keyword = $_GET['keyword'] ?? '';
        $rating = $_GET['rating'] ?? '';

        // ===== CẤU HÌNH PHÂN TRANG =====
        $limit = 6; // Số review trên 1 trang
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $offset = ($page - 1) * $limit;

        // Lấy dữ liệu đã phân trang
        $reviews = $this->reviewModel->getReviews($product_id, $keyword, $rating, $offset, $limit);
        
        // Lấy tổng số review để tính số trang
        $totalItems = $this->reviewModel->countReviews($product_id, $keyword, $rating);
        $totalPages = ceil($totalItems / $limit);

        $this->view('admin/review', [
            'reviews' => $reviews,
            'keyword' => $keyword,
            'rating'  => $rating,
            'product_id' => $product_id,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'title'   => 'Quản lý đánh giá',
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