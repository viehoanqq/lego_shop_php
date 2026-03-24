<?php
class ProductController extends Controller {
    private $limit = 6; // Tối đa 6 sản phẩm mỗi trang

    // Hàm dùng chung cho Index, Search, Filter
    private function handleProductList($filters = [], $title = 'Danh sách sản phẩm LEGO') {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        $products = $prodModel->getFilteredProducts($filters, $offset, $this->limit);
        $total_products = $prodModel->countFilteredProducts($filters);
        $total_pages = ceil($total_products / $this->limit);
        
        $categories = $catModel->getCategoriesWithCount();

        $this->view('user/product', [
            'title'          => $title,
            'products'       => $products,
            'categories'     => $categories,
            'total_products' => $total_products,
            'current_page'   => $page,
            'total_pages'    => $total_pages,
            'keyword'        => $filters['keyword'] ?? null
        ]);
    }

    public function index() {
        $this->handleProductList($_GET);
    }

    public function filter() {
        $this->handleProductList($_GET, 'Kết quả lọc sản phẩm');
    }

    public function search() {
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if (empty($keyword)) { header("Location: /lego_shop_php/product"); exit; }
        
        $filters = $_GET;
        $filters['keyword'] = $keyword;
        $this->handleProductList($filters, 'Kết quả tìm kiếm cho: "' . htmlspecialchars($keyword) . '"');
    }

    public function detail($id = 0) {
        if ($id == 0) { header("Location: /lego_shop_php/product"); exit; }
        $prodModel = $this->model('ProductModel');
        $product = $prodModel->getProductById($id);
        if (!$product) die("Sản phẩm không tồn tại!");

        $this->view('user/product_detail', [
            'title' => $product['name'],
            'product' => $product,
            'images' => $prodModel->getProductImages($id),
            'rating_info' => $prodModel->getProductRating($id),
            'reviews' => $prodModel->getReviewsByProductId($id),
            'category_name' => $product['category_name'],
            'parent_title' => 'Sản phẩm',
            'parent_link' => '/lego_shop_php/product'
        ]);
    }

    public function liveSearch() {
        header('Content-Type: application/json');
        $keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $prodModel = $this->model('ProductModel');
        $products = $prodModel->searchProducts($keyword);
        echo json_encode($products);
        exit;
    }

    public function category($id = 0) {
        if ($id == 0) {
            header("Location: /lego_shop_php/product");
            exit;
        }

        $catModel = $this->model('CategoryModel');
        $category = $catModel->getCategoryById($id); // Bạn cần đảm bảo Model Category có hàm này

        if (!$category) {
            die("Chủ đề không tồn tại!");
        }

        // Tạo mảng filter để truyền vào hàm chung
        $filters = $_GET;
        $filters['category'] = $id;

        $this->handleProductList($filters, 'Chủ đề: ' . $category['name']);
    }
}