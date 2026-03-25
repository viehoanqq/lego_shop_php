<?php
class AccountController extends Controller {

    /* ================= ĐĂNG KÝ ================= */
    public function register() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $error = $_SESSION['register_error'] ?? null;
        $old_data = $_SESSION['old_data'] ?? [];
        
        unset($_SESSION['register_error']);
        unset($_SESSION['old_data']);

        $this->view('user/register', [
            'title' => 'Đăng ký tài khoản',
            'error' => $error,
            'old' => $old_data
        ]);
    }

    public function actionRegister() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $_POST;
            
            // 1. Kiểm tra rỗng từ phía Server
            if (empty(trim($data['phone'])) || empty(trim($data['email'])) || empty(trim($data['password'])) || empty(trim($data['fullname'])) || empty(trim($data['city']))) {
                $_SESSION['register_error'] = "Vui lòng điền đầy đủ các thông tin bắt buộc!";
                $_SESSION['old_data'] = $data;
                header("Location: /lego_shop_php/account/register");
                exit();
            }

            // 2. Kiểm tra check Điều khoản
            if (!isset($data['terms'])) {
                $_SESSION['register_error'] = "Bạn phải đồng ý với Điều khoản dịch vụ!";
                $_SESSION['old_data'] = $data;
                header("Location: /lego_shop_php/account/register");
                exit();
            }

            // 3. Kiểm tra mật khẩu khớp
            if ($data['password'] !== $data['confirm_password']) {
                $_SESSION['register_error'] = "Mật khẩu xác nhận không khớp!";
                $_SESSION['old_data'] = $data;
                header("Location: /lego_shop_php/account/register");
                exit();
            }

            // 4. Gọi Model xử lý (AccountModel lo Authentication)
            $model = $this->model('AccountModel');
            $result = $model->registerFull($data);

            if ($result === true) {
                $_SESSION['success_msg'] = "Đăng ký thành công! Mời bạn đăng nhập.";
                header("Location: /lego_shop_php/account/login");
            } else {
                $_SESSION['register_error'] = $result; 
                $_SESSION['old_data'] = $data;
                header("Location: /lego_shop_php/account/register");
            }
            exit();
        }
    }

    /* ================= ĐĂNG NHẬP ================= */
    public function login() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        $error = $_SESSION['login_error'] ?? null;
        $success = $_SESSION['success_msg'] ?? null; 
        $old_data = $_SESSION['old_login'] ?? [];
        
        unset($_SESSION['login_error']);
        unset($_SESSION['success_msg']);
        unset($_SESSION['old_login']);

        $this->view('user/login', [
            'title' => 'Đăng nhập',
            'error' => $error,
            'success' => $success,
            'old' => $old_data
        ]);
    }

    public function actionLogin() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Xác thực dùng AccountModel
            $model = $this->model('AccountModel');
            $user = $model->login($username, $password);

            if ($user) {
                $_SESSION['user_account_id'] = $user['id'];
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_fullname'] = $user['fullname'];
                $_SESSION['user_role'] = $user['role'];
                
                header("Location: /lego_shop_php/home");
            } else {
                $_SESSION['login_error'] = "Tài khoản hoặc mật khẩu không chính xác!";
                $_SESSION['old_login'] = ['username' => $username];
                header("Location: /lego_shop_php/account/login");
            }
            exit();
        }
    }

    /* ================= ĐĂNG XUẤT ================= */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        session_unset();
        session_destroy();
        header("Location: /lego_shop_php/home");
        exit();
    }

    /* ================= QUÊN MẬT KHẨU (BƯỚC 1) ================= */
    public function forgot() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $error = $_SESSION['forgot_error'] ?? null;
        unset($_SESSION['forgot_error']);

        $this->view('user/forgot', ['title' => 'Quên mật khẩu', 'error' => $error]);
    }

    public function actionForgot() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            
            $model = $this->model('AccountModel');
            $account = $model->checkAccountExists($username);

            if ($account) {
                $_SESSION['reset_account_id'] = $account['id'];
                header("Location: /lego_shop_php/account/reset");
            } else {
                $_SESSION['forgot_error'] = "Không tìm thấy tài khoản với Email/SĐT này!";
                header("Location: /lego_shop_php/account/forgot");
            }
            exit();
        }
    }

    /* ================= ĐẶT LẠI MẬT KHẨU (BƯỚC 2) ================= */
    public function reset() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['reset_account_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit();
        }

        $error = $_SESSION['reset_error'] ?? null;
        unset($_SESSION['reset_error']);

        $this->view('user/reset', ['title' => 'Đặt lại mật khẩu', 'error' => $error]);
    }

    public function actionReset() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $account_id = $_SESSION['reset_account_id'];

            if ($password !== $confirm_password) {
                $_SESSION['reset_error'] = "Mật khẩu xác nhận không khớp!";
                header("Location: /lego_shop_php/account/reset");
                exit();
            }

            $model = $this->model('AccountModel');
            $model->updatePassword($account_id, $password);

            unset($_SESSION['reset_account_id']);
            $_SESSION['success_msg'] = "Đổi mật khẩu thành công! Vui lòng đăng nhập lại.";
            header("Location: /lego_shop_php/account/login");
            exit();
        }
    }
}