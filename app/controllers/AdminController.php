<?php
class AdminController extends Controller {
    
    public function index() {
        // Mặc định truy cập /admin sẽ kiểm tra đăng nhập, nếu chưa thì đẩy ra trang login
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }
        // Nếu đăng nhập rồi thì vào Dashboard
        header("Location: /lego_shop_php/admin/dashboard");
        exit;
    }

    public function login() {
        // Nếu đã đăng nhập thì không cho vào trang login nữa
        if (isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/dashboard");
            exit;
        }

        $error = null;

        // Xử lý khi form được submit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);

            // Gọi Model để kiểm tra (Giả sử bạn dùng AccountModel)
            $accountModel = $this->model('AccountModel');
            $adminUser = $accountModel->checkAdminLogin($username, $password);

            if ($adminUser) {
                // Đăng nhập thành công -> Lưu Session
                $_SESSION['admin_id'] = $adminUser['id'];
                $_SESSION['admin_name'] = $adminUser['fullname'];
                $_SESSION['admin_role'] = $adminUser['role']; // Ví dụ: 'admin' hoặc 'manager'
                
                header("Location: /lego_shop_php/admin/dashboard");
                exit;
            } else {
                $error = "Tài khoản hoặc mật khẩu không chính xác, hoặc bạn không có quyền Admin!";
            }
        }

        // Hiển thị View Login
        $this->view('admin/login', [
            'error' => $error
        ]);
    }

    public function logout() {
        // Xóa session của admin
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        unset($_SESSION['admin_role']);
        
        header("Location: /lego_shop_php/admin/login");
        exit;
    }
    
    // Hàm Dashboard tạm thời
    public function dashboard() {
    // Kiểm tra quyền admin
    if (!isset($_SESSION['admin_id'])) {
        header("Location: /lego_shop_php/admin/login");
        exit;
    }

    // Gọi view admin/dashboard
    $this->view('admin/dashboard', [
        'title' => 'Bảng điều khiển tổng quan'
    ]);
}

/* ================= HỒ SƠ CÁ NHÂN (PROFILE) ================= */
    public function profile() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }

        // Gọi Model để lấy thông tin Email, SĐT hiện tại của Admin từ DB
        $accountModel = $this->model('AccountModel');
        $admin_info = $accountModel->getAdminById($_SESSION['admin_id']);

        $this->view('admin/profile', [
            'title' => 'Hồ sơ cá nhân',
            'admin_info' => $admin_info, // Đẩy dữ liệu lên View
            'old' => $_SESSION['old_profile'] ?? [] // Giữ lại text nếu nhập lỗi
        ]);
        
        // Xóa session old data sau khi đã hiển thị
        unset($_SESSION['old_profile']);
    }

    /* ================= XỬ LÝ LƯU THÔNG TIN CÁ NHÂN ================= */
    public function actionUpdateProfile() {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: /lego_shop_php/admin/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullname = trim($_POST['fullname']);
            $phone = trim($_POST['phone']);
            $email = trim($_POST['email']);

            // 1. Kiểm tra rỗng
            if (empty($fullname) || empty($phone) || empty($email)) {
                $_SESSION['toast_msg'] = "Vui lòng nhập đầy đủ thông tin!";
                $_SESSION['toast_type'] = "error";
                $_SESSION['old_profile'] = $_POST;
            } 
            // 2. Kiểm tra định dạng Email
            elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['toast_msg'] = "Định dạng Email không hợp lệ!";
                $_SESSION['toast_type'] = "error";
                $_SESSION['old_profile'] = $_POST;
            } 
            // 3. Tiến hành Lưu vào DB
            else {
                $accountModel = $this->model('AccountModel');
                $result = $accountModel->updateAdminProfile($_SESSION['admin_id'], $fullname, $phone, $email);
                
                if ($result) {
                    $_SESSION['admin_name'] = $fullname; // Đổi tên Header ngay lập tức
                    $_SESSION['toast_msg'] = "Lưu thông tin thành công!";
                    $_SESSION['toast_type'] = "success";
                } else {
                    $_SESSION['toast_msg'] = "Lỗi! Email hoặc Số điện thoại đã tồn tại.";
                    $_SESSION['toast_type'] = "error";
                    $_SESSION['old_profile'] = $_POST;
                }
            }
            // Trả về lại trang profile (chớp 1 cái rồi hiện Toast)
            header("Location: /lego_shop_php/admin/profile");
            exit;
        }
    }

    /* ================= XỬ LÝ CẬP NHẬT MẬT KHẨU ================= */
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
                $accountModel = $this->model('AccountModel');
                // Kiểm tra mật khẩu cũ
                if ($accountModel->verifyOldPassword($_SESSION['admin_id'], $old_pass)) {
                    $accountModel->updatePassword($_SESSION['admin_id'], $new_pass);
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
