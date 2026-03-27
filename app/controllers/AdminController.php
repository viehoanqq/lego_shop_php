<?php
class AdminController extends Controller {
    
    public function index() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }
        header("Location: /lego_shop_php/admindashboard");
        exit;
    }

    public function login() {
        if (isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admindashboard");
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Login Admin thì vẫn dùng AccountModel vì nó là Authentication
            $accountModel = $this->model('AccountModel');
            $adminUser = $accountModel->checkAdminLogin($username, $password);

            if ($adminUser) {
                $_SESSION['admin_id'] = $adminUser['id'];
                $_SESSION['admin_name'] = $adminUser['fullname'];
                $_SESSION['admin_role'] = $adminUser['role'];
                
                header("Location: /lego_shop_php/admindashboard");
                exit;
            } else {
                $error = "Tài khoản hoặc mật khẩu không chính xác, hoặc bạn không có quyền Admin!";
            }
        }

        $this->view('admin/login', [
            'error' => $error
        ]);
    }

    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        
        header("Location: /lego_shop_php/admin/login");
        exit;
    }
    
    public function dashboard() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }
        $this->view('admin/dashboard', [
            'title' => 'Bảng điều khiển tổng quan'
        ]);
    }

    /* ================= HỒ SƠ CÁ NHÂN ADMIN (PROFILE) ================= */
    public function profile() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }

        // ĐÃ SỬA: Gọi AdminModel để lấy thông tin Profile
        $adminModel = $this->model('AdminModel');
        $admin_info = $adminModel->getAdminById($_SESSION['admin_id']);

        $this->view('admin/profile', [
            'title' => 'Hồ sơ cá nhân',
            'admin_info' => $admin_info,
            'old' => $_SESSION['old_profile'] ?? []
        ]);
        
        unset($_SESSION['old_profile']);
    }

    /* ================= XỬ LÝ LƯU THÔNG TIN ADMIN ================= */
    public function actionUpdateProfile() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = trim($_POST['fullname']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);

            if (empty($fullname) || empty($phone) || empty($email)) {
                $_SESSION['toast_msg'] = "Vui lòng nhập đầy đủ thông tin!";
                $_SESSION['toast_type'] = "error";
                $_SESSION['old_profile'] = $_POST;
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['toast_msg'] = "Định dạng Email không hợp lệ!";
                $_SESSION['toast_type'] = "error";
                $_SESSION['old_profile'] = $_POST;
            } else {
                // ĐÃ SỬA: Gọi AdminModel để Update
                $adminModel = $this->model('AdminModel');
                $result = $adminModel->updateAdminProfile($_SESSION['admin_id'], $fullname, $phone, $email);
                
                if ($result) {
                    $_SESSION['admin_name'] = $fullname;
                    $_SESSION['toast_msg'] = "Lưu thông tin thành công!";
                    $_SESSION['toast_type'] = "success";
                } else {
                    $_SESSION['toast_msg'] = "Lỗi! Email hoặc Số điện thoại đã tồn tại.";
                    $_SESSION['toast_type'] = "error";
                    $_SESSION['old_profile'] = $_POST;
                }
            }
            header("Location: /lego_shop_php/admin/profile");
            exit;
        }
    }

    /* ================= XỬ LÝ CẬP NHẬT MẬT KHẨU ADMIN ================= */
    public function actionUpdatePassword() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $old_pass = $_POST['old_password'];
            $new_pass = $_POST['new_password'];
            $confirm_pass = $_POST['confirm_password'];

            if (empty($old_pass) || empty($new_pass) || empty($confirm_pass)) {
                $_SESSION['toast_msg'] = "Vui lòng nhập đầy đủ các ô mật khẩu!";
                $_SESSION['toast_type'] = "error";
            } elseif ($new_pass !== $confirm_pass) {
                $_SESSION['toast_msg'] = "Mật khẩu xác nhận không khớp!";
                $_SESSION['toast_type'] = "error";
            } elseif (strlen($new_pass) < 6) {
                $_SESSION['toast_msg'] = "Mật khẩu mới phải từ 6 ký tự trở lên!";
                $_SESSION['toast_type'] = "error";
            } else {
                // ĐÃ SỬA: Gọi AdminModel 
                $adminModel = $this->model('AdminModel');
                
                if ($adminModel->verifyOldPassword($_SESSION['admin_id'], $old_pass)) {
                    $adminModel->updatePassword($_SESSION['admin_id'], $new_pass);
                    $_SESSION['toast_msg'] = "Đổi mật khẩu thành công!";
                    $_SESSION['toast_type'] = "success";
                } else {
                    $_SESSION['toast_msg'] = "Mật khẩu hiện tại không đúng!";
                    $_SESSION['toast_type'] = "error";
                }
            }
            header("Location: /lego_shop_php/admin/profile");
            exit;
        }
    }

    
}