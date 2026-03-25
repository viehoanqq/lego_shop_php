<?php
class ProfileController extends Controller {
    
    // Yêu cầu đăng nhập trước khi vào trang cá nhân
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_account_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

    // 1. Trang thông tin cá nhân
    public function index() {
        // ĐÃ SỬA: Gọi UserModel để lấy thông tin Khách hàng
        $userModel = $this->model('UserModel');
        $user_info = $userModel->getUserProfile($_SESSION['user_account_id']);

        // Lấy thông báo nếu có lúc update thành công/thất bại
        $msg = $_SESSION['profile_msg'] ?? null;
        $msg_type = $_SESSION['profile_msg_type'] ?? null;
        unset($_SESSION['profile_msg'], $_SESSION['profile_msg_type']);

        $this->view('user/profile/info', [
            'title' => 'Thông tin cá nhân',
            'active_tab' => 'info',
            'user_info' => $user_info, // Truyền data ra view
            'msg' => $msg,
            'msg_type' => $msg_type
        ]);
    }

    // 2. Xử lý Form Cập nhật thông tin Khách hàng
    public function updateInfo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = $_POST['fullname'] ?? '';
            $phone = $_POST['phone'] ?? '';
            
            // ĐÃ SỬA: Gọi UserModel
            $userModel = $this->model('UserModel');
            $result = $userModel->updateUserProfile($_SESSION['user_account_id'], $fullname, $phone);

            if ($result) {
                $_SESSION['user_fullname'] = $fullname; // Đổi tên ở Header
                $_SESSION['profile_msg'] = "Cập nhật thông tin thành công!";
                $_SESSION['profile_msg_type'] = "success";
            } else {
                $_SESSION['profile_msg'] = "Có lỗi xảy ra, vui lòng thử lại!";
                $_SESSION['profile_msg_type'] = "error";
            }
            
            header("Location: /lego_shop_php/profile/index");
            exit;
        }
    }

    // 3. Trang Đơn hàng
    public function orders() {
        $this->view('user/profile/orders', [
            'title' => 'Đơn hàng của tôi',
            'active_tab' => 'orders'
        ]);
    }

    // 4. Trang Đổi mật khẩu
    public function password() {
        $this->view('user/profile/password', [
            'title' => 'Đổi mật khẩu',
            'active_tab' => 'password'
        ]);
    }
}