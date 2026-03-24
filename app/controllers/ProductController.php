<?php
// File: app/controllers/ProductController.php

class ProductController extends Controller {
    
    public function index() {
        // Load model
        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        // Lấy dữ liệu
        $products = $prodModel->getAllProducts();
        $categories = $catModel->getCategoriesWithCount();

        // TRUYỀN RA VIEW ĐÚNG THƯ MỤC USER
        $this->view('user/product', [
            'title' => 'Danh sách sản phẩm LEGO',
            'products' => $products,
            'categories' => $categories,
            'total_products' => count($products)
        ]);
    }
    public function detail($id = 0) {
        if ($id == 0) {
            header("Location: /lego_shop_php/product"); // Nếu không có ID, đá về trang danh sách
            exit;
        }

        $prodModel = $this->model('ProductModel');
        $product = $prodModel->getProductById($id);

        if (!$product) {
            // Xử lý nếu ID sản phẩm không tồn tại (Ví dụ: báo lỗi 404)
            die("Sản phẩm không tồn tại!");
        }

        $images = $prodModel->getProductImages($id);

       // Lấy thông tin đánh giá tổng quát (số sao TB, tổng đánh giá)
        $rating_info = $prodModel->getProductRating($id);
        
        // Lấy danh sách chi tiết các bài đánh giá
        $reviews = $prodModel->getReviewsByProductId($id);

        // Truyền ra View
        $this->view('user/product_detail', [
            'title' => $product['name'],
            'parent_title' => 'Sản phẩm',               // Thêm dòng này
            'parent_link' => '/lego_shop_php/product',  // Thêm dòng này
            'product' => $product,
            'images' => $images,
            'rating_info' => $rating_info,
            'reviews' => $reviews,
            'category_name' => $product['category_name']
        ]);
    }
}