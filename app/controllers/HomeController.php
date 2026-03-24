<?php
class HomeController extends Controller {
    public function index() {
        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');
        
        // Gọi hàm Filtered truyền mảng rỗng (không lọc gì cả), bắt đầu từ 0, lấy 3 sản phẩm
        $new_products = $prodModel->getFilteredProducts([], 0, 3); 
        $header_categories = $catModel->getAllCategories(); // Lấy danh mục cho Header Nav
        $featured_products = $prodModel->getRandomProducts(8);
        $this->view('user/home', [
            'title' => 'Trang chủ - LEGO World Store',
            'new_products' => $new_products,
            'featured_products' => $featured_products, // Truyền biến này ra View
            'header_categories' => $header_categories
        ]);
    }
}