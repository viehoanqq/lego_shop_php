<?php
class AdminInventoryController extends Controller {
    private $InventoryModel;
    private $productModel;
    private $limit = 10; 

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id'])) { header("Location: /lego_shop_php/admin/login"); exit; }
        $this->InventoryModel = $this->model('InventoryModel');
        $this->productModel = $this->model('ProductModel');
    }

    public function index() {
        $type = $_GET['type'] ?? 'all';
        $keyword = $_GET['keyword'] ?? ''; 
        $custom_threshold = $_GET['custom_threshold'] ?? ''; // Lọc theo số lượng
        
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $products = $this->InventoryModel->getLowStockProducts($offset, $this->limit, $type, $keyword, $custom_threshold);
        $totalItems = $this->InventoryModel->countLowStockProducts($type, $keyword, $custom_threshold);
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
            'custom_threshold' => $custom_threshold
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
        $pid = $_GET['product_id'] ?? 0;
        echo json_encode(['success' => true, 'data' => $this->InventoryModel->getStockCard($pid)]);
        exit;
    }

    // API Điều chỉnh kho
    public function adjustStockAjax() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            $data = json_decode(file_get_contents('php://input'), true);
            $pid = $data['product_id'];
            $real = $data['real_stock'];
            $reason = $data['reason'];
            
            if($this->InventoryModel->adjustStock($pid, $_SESSION['admin_id'], $real, $reason)){
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi CSDL']);
            }
            exit;
        }
    }

    public function updateBulkMinStock() {
        // Chỉ xử lý nếu là yêu cầu POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            
            // Đọc dữ liệu JSON gửi từ Fetch API hoặc Ajax
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            // Kiểm tra dữ liệu đầu vào
            if (!$data || empty($data['items'])) {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu gửi lên không hợp lệ hoặc trống.']);
                return;
            }
            
            $db = $this->InventoryModel->getConnection();
            $db->begin_transaction(); // Sử dụng Transaction để đảm bảo tính toàn vẹn dữ liệu

            try {
                foreach ($data['items'] as $item) {
                    $product_id = intval($item['product_id']);
                    $min_stock = intval($item['min_stock']);
                    
                    // Chỉ cập nhật nếu ID hợp lệ và số lượng không âm
                    if ($product_id > 0 && $min_stock >= 0) {
                        $this->InventoryModel->updateSingleMinStock($product_id, $min_stock);
                    }
                }
                
                $db->commit();
                echo json_encode(['success' => true, 'message' => 'Cập nhật ngưỡng tồn kho thành công!']);
            } catch (Exception $e) {
                $db->rollback();
                echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
            }
            exit;
        }
    }
}
?>