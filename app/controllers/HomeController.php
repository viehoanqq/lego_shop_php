<?php
class HomeController extends Controller {
    public function index() {
        // Gọi Model
        $productModel = $this->model('ProductModel');
        
        // Lấy dữ liệu
        $products = $productModel->getAllProducts();

        // Truyền dữ liệu sang View
        $this->view('user/home', [
            'title' => 'Lego World Store - Trang chủ',
            'products' => $products
        ]);
    }
}