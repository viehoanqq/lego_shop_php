<?php
class AccountController extends Controller {
    // Trang Đăng nhập
    public function login() {
        $this->view('user/login', ['title' => 'Đăng nhập']);
    }

    // Trang Đăng ký
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
            
            // Validate sơ bộ tại Controller
            if ($data['password'] !== $data['confirm_password']) {
                $_SESSION['register_error'] = "Mật khẩu xác nhận không khớp!";
                $_SESSION['old_data'] = $data;
                header("Location: /lego_shop_php/account/register");
                exit();
            }

            $model = $this->model('AccountModel');
            $result = $model->registerFull($data);

            if ($result === true) {
                $_SESSION['success_msg'] = "Đăng ký thành công!";
                header("Location: /lego_shop_php/account/login");
            } else {
                $_SESSION['register_error'] = $result; 
                $_SESSION['old_data'] = $data;
                header("Location: /lego_shop_php/account/register");
            }
            exit();
        }
    }
}