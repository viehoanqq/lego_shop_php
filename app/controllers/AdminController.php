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
}