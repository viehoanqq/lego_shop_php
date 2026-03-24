public function dashboard() {
    // 1. Kiểm tra đăng nhập (để bảo mật)
    if (!isset($_SESSION['admin_id'])) {
        header("Location: /lego_shop_php/admin/login");
        exit;
    }

    // 2. Gọi view đúng cách để nó tự chèn Sidebar + Header
    // 'admin/dashboard' tương ứng với file: app/views/admin/dashboard.php
    $this->view('admin/dashboard', [
        'title' => 'Bảng điều khiển tổng quan'
    ]);
}