<?php
class AdminImportController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Trang mặc định (Chỉ hiện bảng lịch sử)
    public function index() {
        $importModel = $this->model('ImportModel');
        
        $data['imports'] = $importModel->getAllImports();
        $data['title'] = "Quản lý Nhập hàng";
        $data['is_form'] = false; // Báo cho view biết KHÔNG hiện form
        
        $this->view('admin/imports', $data);
    }

    // Trang tạo mới (Hiện form phía trên, bảng lịch sử phía dưới)
    public function create() {
        $importModel = $this->model('ImportModel');
        $productModel = $this->model('ProductModel');
        
        $data['imports'] = $importModel->getAllImports(); // Lấy danh sách để hiện ở dưới
        $data['suppliers'] = $importModel->getAllSuppliers();
        $data['products'] = $productModel->getFilteredProducts(['status' => '1,2'], 0, 1000); 
        $data['title'] = "Lập phiếu nhập kho";
        $data['is_form'] = true; // Báo cho view biết PHẢI hiện form
        
        $this->view('admin/imports', $data);
    }

    // API xử lý nhận dữ liệu AJAX
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Xóa buffer để tránh lỗi khi decode JSON ở phía Frontend
            ob_clean(); 
            header('Content-Type: application/json');

            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) throw new Exception("Dữ liệu không hợp lệ");
                
                $supplier_id = intval($data['supplier_id']);
                $products = $data['products']; 
                $status = $data['status'] ?? 'draft'; // Lấy trạng thái từ JS gửi lên
                $admin_id = $_SESSION['admin_id']; 

                $importModel = $this->model('ImportModel'); 
                $success = $importModel->createImportTransaction($admin_id, $supplier_id, $products, $status);

                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi lưu vào CSDL']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // --- Xem chi tiết phiếu nhập ---
    public function detail($id) {
        $importModel = $this->model('ImportModel');
        
        $receipt = $importModel->getImportById($id);
        if (!$receipt) {
            header("Location: /lego_shop_php/adminimport?error=notfound");
            exit;
        }

        $details = $importModel->getImportDetails($id);
        
        $this->view('admin/import_detail', [
            'title' => 'Chi tiết phiếu nhập #PN-' . $id,
            'receipt' => $receipt,
            'details' => $details
        ]);
    }

    // --- Xử lý nút bấm "Hoàn tất phiếu nhập" ---
    public function complete($id) {
        $importModel = $this->model('ImportModel');
        if ($importModel->completeImport($id)) {
            // Hoàn tất thành công, quay lại trang chi tiết kèm thông báo
            header("Location: /lego_shop_php/adminimport/detail/$id?msg=completed");
        } else {
            header("Location: /lego_shop_php/adminimport/detail/$id?error=1");
        }
        exit;
    }
}