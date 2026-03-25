<?php
class HomeController extends Controller {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); } // Bắt buộc start session để lấy id

        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');
        
        // Lấy ID người dùng hiện tại (Nếu họ chưa đăng nhập thì là null)
        $account_id = $_SESSION['user_account_id'] ?? null;
        
        // Gọi hàm và TRUYỀN THÊM $account_id vào cuối cùng
        $new_products = $prodModel->getFilteredProducts([], 0, 3, $account_id); 
        $featured_products = $prodModel->getRandomProducts(8, $account_id);
        
        $header_categories = $catModel->getAllCategories();
        
        $this->view('user/home', [
            'title' => 'Trang chủ - LEGO World Store',
            'new_products' => $new_products,
            'featured_products' => $featured_products,
            'header_categories' => $header_categories
        ]);
    }
}