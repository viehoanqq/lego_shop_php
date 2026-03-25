<?php
// app/controllers/ProfileController.php
class ProfileController extends Controller {
    
    // Hàm chạy đầu tiên để kiểm tra xem đã đăng nhập chưa
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_account_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

    // 1. Trang thông tin cá nhân
    public function index() {
        // Gọi Model để lấy thông tin user từ DB truyền ra View (bạn tự viết hàm lấy User nhé)
        // $userModel = $this->model('UserModel');
        // $user_info = $userModel->getUserById($_SESSION['user_account_id']);

        $this->view('user/profile/info', [
            'title' => 'Thông tin cá nhân',
            'active_tab' => 'info' // Truyền biến này để Sidebar biết đang ở đâu
            // 'user_info' => $user_info
        ]);
    }

    // 2. Trang Đơn hàng
    public function orders() {
        $this->view('user/profile/orders', [
            'title' => 'Đơn hàng của tôi',
            'active_tab' => 'orders'
        ]);
    }

    // 3. Trang Đổi mật khẩu
    public function password() {
        $this->view('user/profile/password', [
            'title' => 'Đổi mật khẩu',
            'active_tab' => 'password'
        ]);
    }
}