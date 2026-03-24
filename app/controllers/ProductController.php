<?php
// File: app/controllers/ProductController.php

class ProductController extends Controller {
    
    // Số sản phẩm hiển thị trên 1 trang
    private $limit = 6;

    public function index() {
        // 1. Tính toán phân trang
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $this->limit;

        // 2. Load model
        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        // 3. Lấy dữ liệu (mặc định lấy tất cả nhưng có giới hạn LIMIT)
        $products = $prodModel->getFilteredProducts($_GET, $offset, $this->limit);
        
        // 4. Tính tổng số trang
        $total_products = $prodModel->countFilteredProducts($_GET);
        $total_pages = ceil($total_products / $this->limit);
        
        $categories = $catModel->getCategoriesWithCount();

        // 5. Truyền ra view
        $this->view('user/product', [
            'title'          => 'Danh sách sản phẩm LEGO',
            'products'       => $products,
            'categories'     => $categories,
            'total_products' => $total_products,
            'current_page'   => $page,
            'total_pages'    => $total_pages
        ]);
    }

    public function detail($id = 0) {
        if ($id == 0) {
            header("Location: /lego_shop_php/product");
            exit;
        }

        $prodModel = $this->model('ProductModel');
        $product = $prodModel->getProductById($id);

        if (!$product) {
            die("Sản phẩm không tồn tại!");
        }

        $images = $prodModel->getProductImages($id);
        $rating_info = $prodModel->getProductRating($id);
        $reviews = $prodModel->getReviewsByProductId($id);

        $this->view('user/product_detail', [
            'title'         => $product['name'],
            'parent_title'  => 'Sản phẩm',
            'parent_link'   => '/lego_shop_php/product',
            'product'       => $product,
            'images'        => $images,
            'rating_info'   => $rating_info,
            'reviews'       => $reviews,
            'category_name' => $product['category_name']
        ]);
    }

    public function search() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

        if (empty($keyword)) {
            header("Location: /lego_shop_php/product");
            exit;
        }

        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        // Đối với Search thường ta cũng nên áp dụng phân trang nếu sản phẩm tìm được quá nhiều
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $this->limit;

        // Tận dụng hàm Filter để tìm kiếm (Hàm này bạn cần cập nhật SQL trong Model để hỗ trợ keyword)
        $products = $prodModel->searchProducts($keyword); // Hoặc dùng Filtered nếu Model đã gộp
        $categories = $catModel->getCategoriesWithCount();

        $this->view('user/product', [
            'keyword'        => $keyword,
            'title'          => 'Kết quả tìm kiếm cho: ' . htmlspecialchars($keyword),
            'products'       => $products,
            'categories'     => $categories,
            'total_products' => count($products),
            'total_pages'    => 1 // Tạm thời để 1 cho trang search nếu chưa viết phân trang cho search
        ]);
    }

    public function liveSearch() {
        header('Content-Type: application/json');
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        
        if (strlen($keyword) < 2) {
            echo json_encode([]);
            exit;
        }

        $prodModel = $this->model('ProductModel');
        $products = $prodModel->searchProducts($keyword);
        $limit_products = array_slice($products, 0, 5);
        
        echo json_encode($limit_products);
        exit;
    }

    public function filter() {
        // 1. Phân trang cho bộ lọc
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $this->limit;

        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        $filters = [
            'category'    => $_GET['category'] ?? 'all',
            'price_range' => $_GET['price_range'] ?? null,
            'min_price'   => $_GET['min_price'] ?? null,
            'max_price'   => $_GET['max_price'] ?? null,
            'pieces'      => $_GET['pieces'] ?? null
        ];

        $products = $prodModel->getFilteredProducts($filters, $offset, $this->limit);
        $total_products = $prodModel->countFilteredProducts($filters);
        $total_pages = ceil($total_products / $this->limit);

        $categories = $catModel->getCategoriesWithCount();

        $this->view('user/product', [
            'title'          => 'Kết quả lọc sản phẩm',
            'products'       => $products,
            'categories'     => $categories,
            'total_products' => $total_products,
            'current_page'   => $page,
            'total_pages'    => $total_pages,
            'keyword'        => null // Đảm bảo view không bị nhầm với trang search
        ]);
    }
}