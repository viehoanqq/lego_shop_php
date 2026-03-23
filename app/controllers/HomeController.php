<?php
class HomeController extends Controller {
    public function index() {
        // 1. Gọi Model sản phẩm
        $productModel = $this->model('ProductModel');
        
        // 2. Lấy danh sách sản phẩm từ DB
        $products = $productModel->getAllProducts();

        // 3. Truyền biến $products sang giao diện home.php
        $this->view('user/home', [
            'title' => 'Trang chủ Lego World',
            'products' => $products
        ]);
    }
}