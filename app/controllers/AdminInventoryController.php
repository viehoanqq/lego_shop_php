<?php
class AdminInventoryController extends Controller {
    private $InventoryModel;
    private $productModel;
    private $limit = 6; 

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id'])) { header("Location: /lego_shop_php/admin/login"); exit; }
        $this->InventoryModel = $this->model('InventoryModel');
        $this->productModel = $this->model('ProductModel');
    }

    public function index() {
        $type = $_GET['type'] ?? 'all';
        $keyword = $_GET['keyword'] ?? ''; 
        
        // BƯỚC 1: XỬ LÝ LƯU NGƯỠNG CẢNH BÁO MỚI (NẾU CÓ)
        if (isset($_GET['threshold']) && is_numeric($_GET['threshold'])) {
            $new_threshold = intval($_GET['threshold']);
            $this->InventoryModel->updateAllMinStock($new_threshold);
            
            // Xóa tham số threshold trên URL để tránh update lại khi F5
            header("Location: /lego_shop_php/admininventory?tab=alerts&keyword=" . urlencode($keyword));
            exit;
        }

        // BƯỚC 2: LẤY NGƯỠNG CẢNH BÁO HIỆN TẠI TỪ DB
        $current_threshold = $this->InventoryModel->getGlobalMinStock();

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        // BƯỚC 3: LẤY DỮ LIỆU
        $products = $this->InventoryModel->getLowStockProducts($offset, $this->limit, $type, $keyword);
        $totalItems = $this->InventoryModel->countLowStockProducts($type, $keyword);
        $totalPages = ceil($totalItems / $this->limit);

        $all_products = $this->productModel->getAllProductsForDropdown();

        $this->view('admin/inventory', [
            'products'     => $products,
            'all_products' => $all_products,
            'totalItems'   => $totalItems,
            'totalPages'   => $totalPages,
            'currentPage'  => $page,
            'currentType'  => $type,
            'keyword'      => $keyword,
            'current_threshold' => $current_threshold, // Truyền biến này ra View
            'title'   => 'Quản lý tồn kho',
        ]);
    }

    

    // API Tra cứu tồn kho theo ngày
    public function getSnapshotAjax() {
        header('Content-Type: application/json');
        $date = $_GET['date'] ?? date('Y-m-d');
        $data = $this->InventoryModel->getInventorySnapshot($date);
        echo json_encode(['success' => true, 'data' => $data]);
        exit;
    }

    // API Lấy Thẻ Kho (Lịch sử)
    public function getStockCardAjax() {
        header('Content-Type: application/json');
        $product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
        $target_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d'); // Mặc định là hôm nay

        if ($product_id <= 0) {
            echo json_encode(['success' => false]);
            exit;
        }

        $inventoryModel = $this->model('InventoryModel'); // Thay bằng tên Model thực tế của bạn
        
        // Bạn cần viết thêm các hàm này trong Model để tính toán
        $opening_stock = $inventoryModel->calculateStockAtTime($product_id, $target_date . ' 00:00:00');
        $closing_stock = $inventoryModel->calculateStockAtTime($product_id, $target_date . ' 23:59:59');
        $transactions = $inventoryModel->getTransactionsByDate($product_id, $target_date);

        echo json_encode([
            'success' => true,
            'data' => [
                'opening_stock' => $opening_stock,
                'closing_stock' => $closing_stock,
                'transactions' => $transactions
            ]
        ]);
        exit;
    }

       
}
?>