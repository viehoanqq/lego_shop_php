<?php
class ProfileController extends Controller {
    
    // Hàm khởi tạo
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_account_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

    // 1. Trang thông tin cá nhân
    public function index() {
        $userModel = $this->model('UserModel');
        $user_info = $userModel->getUserProfile($_SESSION['user_account_id']);

        $msg = $_SESSION['profile_msg'] ?? null;
        $msg_type = $_SESSION['profile_msg_type'] ?? null;
        unset($_SESSION['profile_msg'], $_SESSION['profile_msg_type']);

        $this->view('user/profile/info', [
            'title' => 'Thông tin cá nhân',
            'active_tab' => 'info',
            'user_info' => $user_info, 
            'msg' => $msg,
            'msg_type' => $msg_type
        ]);
    }

    // 2. Xử lý Cập nhật
    public function updateInfo() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = trim($_POST['fullname'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            
            // --- 1. XÁC THỰC RỖNG & ĐỊNH DẠNG ---
            if (empty($fullname) || empty($phone)) {
                $_SESSION['profile_msg'] = "Vui lòng nhập đầy đủ Họ tên và Số điện thoại!";
                $_SESSION['profile_msg_type'] = "error";
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            if (strlen($fullname) < 2 || strlen($fullname) > 50) {
                $_SESSION['profile_msg'] = "Họ tên phải từ 2 đến 50 ký tự!";
                $_SESSION['profile_msg_type'] = "error";
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            if (!preg_match('/^(0[3|5|7|8|9])+([0-9]{8})$/', $phone)) {
                $_SESSION['profile_msg'] = "Số điện thoại không hợp lệ!";
                $_SESSION['profile_msg_type'] = "error";
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            $userModel = $this->model('UserModel');

            // --- 2. [TÍNH NĂNG MỚI] KIỂM TRA XEM CÓ THAY ĐỔI GÌ KHÔNG ---
            $currentInfo = $userModel->getUserProfile($_SESSION['user_account_id']);
            if ($currentInfo['fullname'] === $fullname && $currentInfo['phone'] === $phone) {
                $_SESSION['profile_msg'] = "Bạn chưa thay đổi thông tin nào!";
                $_SESSION['profile_msg_type'] = "error"; // Sẽ hiển thị Toast màu đỏ
                header("Location: /lego_shop_php/profile/index");
                exit;
            }

            // --- 3. KIỂM TRA TRÙNG SĐT & TIẾN HÀNH LƯU ---
            if ($userModel->checkPhoneExists($phone, $_SESSION['user_account_id'])) {
                $_SESSION['profile_msg'] = "Số điện thoại này đã được sử dụng bởi tài khoản khác!";
                $_SESSION['profile_msg_type'] = "error";
            } else {
                $result = $userModel->updateUserProfile($_SESSION['user_account_id'], $fullname, $phone);

                if ($result) {
                    $_SESSION['user_fullname'] = $fullname; 
                    $_SESSION['profile_msg'] = "Cập nhật thông tin thành công!";
                    $_SESSION['profile_msg_type'] = "success";
                } else {
                    $_SESSION['profile_msg'] = "Hệ thống bận, vui lòng thử lại sau!";
                    $_SESSION['profile_msg_type'] = "error";
                }
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