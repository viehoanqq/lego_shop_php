<?php
class HomeController extends Controller {
    public function index() {
    $prodModel = $this->model('ProductModel');
    // Lấy 8 sản phẩm mới nhất hiện trang chủ
    $products = $prodModel->getFilteredProducts([], 0, 8); 
    $this->view('user/home', ['title' => 'Trang chủ', 'products' => $products]);
}
}