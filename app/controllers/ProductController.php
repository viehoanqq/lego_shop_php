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
    // ==================================================
    // HÀM XỬ LÝ TRANG KẾT QUẢ TÌM KIẾM (Khi bấm Enter)
    // ==================================================
    public function search() {
        // 1. Lấy từ khóa từ thanh địa chỉ (URL)
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        // 2. Nếu người dùng không nhập gì mà bấm Enter thì đẩy về trang gốc
        if (empty($keyword)) {
            header("Location: /lego_shop_php/product");
            exit;
        }

        // 3. Load Models
        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        // 4. Lấy dữ liệu từ Database
        $products = $prodModel->searchProducts($keyword);
        $categories = $catModel->getCategoriesWithCount();

        // 5. Ném dữ liệu ra View (Dùng lại view user/product)
        $this->view('user/product', [
            'keyword' => $keyword, // Biến này rất quan trọng để View đổi Tiêu đề
            'products' => $products,
            'categories' => $categories,
            'total_products' => count($products),
            'title' => 'Kết quả tìm kiếm cho: ' . htmlspecialchars($keyword), // THÊM DÒNG NÀY ĐỂ SỬA BREADCRUMB
        ]);
    }
    // API Xử lý Live Search (Vừa gõ vừa hiện)
    public function liveSearch() {
        // Khai báo trả về JSON
        header('Content-Type: application/json');
        
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        // Nếu gõ ít hơn 2 ký tự thì trả về mảng rỗng cho nhẹ Server
        if (strlen($keyword) < 2) {
            echo json_encode([]);
            return;
        }

        $prodModel = $this->model('ProductModel');
        $products = $prodModel->searchProducts($keyword);
        
        // Cắt lấy 5 sản phẩm đầu tiên để dropdown không bị quá dài
        $limit_products = array_slice($products, 0, 5);
        
        echo json_encode($limit_products);
    }
}