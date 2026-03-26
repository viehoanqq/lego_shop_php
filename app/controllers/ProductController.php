    <?php
    class ProductController extends Controller {
        private $limit = 6; // Tối đa 6 sản phẩm mỗi trang
        
        // Hàm dùng chung cho Index, Search, Filter
        private function handleProductList($filters = [], $title = 'Danh sách sản phẩm LEGO') {
        // 1. GỌI SESSION VÀ LẤY ID Ở ĐÂY LÀ CHUẨN NHẤT
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $account_id = $_SESSION['user_account_id'] ?? null;

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $prodModel = $this->model('ProductModel');
        $catModel = $this->model('CategoryModel');

        // 2. NHÉT THÊM $account_id VÀO HÀM NÀY:
        $products = $prodModel->getFilteredProducts($filters, $offset, $this->limit, $account_id);
        
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
        public function orders() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) { header("Location: /lego_shop_php/account/login"); exit; }

        $orderModel = $this->model('OrderModel');
        $orders = $orderModel->getOrdersByUserId($_SESSION['user_id']);

        $this->view('/user/profile/orders', [
            'title' => 'Lịch sử mua hàng',
            'orders' => $orders
        ]);
    }

    // Hàm API (AJAX) trả về chi tiết đơn hàng
    public function getOrderDetailsAjax() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');
        
        $input = json_decode(file_get_contents('php://input'), true);
        $order_id = $input['order_id'] ?? 0;
        $user_id = $_SESSION['user_id'] ?? 0;

        if (!$order_id || !$user_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);

        // Bảo mật: Chỉ cho phép chủ đơn hàng xem
        if (!$order || $order['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);
            exit;
        }

        // Lấy danh sách sản phẩm của đơn hàng này
        $items = $orderModel->getOrderItems($order_id);

        echo json_encode([
            'success' => true,
            'order' => $order,
            'items' => $items
        ]);
        exit;
    }
    
    }