<?php
class AdminImportController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Trang mặc định (Chỉ hiện bảng lịch sử)
    // Trang mặc định (Chỉ hiện bảng lịch sử và Bộ lọc)
    public function index() {
        $importModel = $this->model('ImportModel');
        
        // 1. Nhận dữ liệu lọc từ URL (Thêm keyword)
        $filters = [
            'keyword'     => $_GET['keyword'] ?? '',
            'supplier_id' => $_GET['supplier_id'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'start_date'  => $_GET['start_date'] ?? '',
            'end_date'    => $_GET['end_date'] ?? ''
        ];
        
        // 2. Truyền ra view
        $data['filters'] = $filters; 
        $data['suppliers'] = $importModel->getAllSuppliers(); 
        $data['imports'] = $importModel->getAllImports($filters); 
        $data['title'] = "Quản lý Nhập hàng";
        $data['is_form'] = false; 
        
        $this->view('admin/imports', $data);
    }

    // Trang tạo mới (Hiện form phía trên, bảng lịch sử phía dưới)
    public function create() {
        $importModel = $this->model('ImportModel');
        $productModel = $this->model('ProductModel'); // Thêm lại dòng này
        
        $data['imports'] = $importModel->getAllImports();
        $data['suppliers'] = $importModel->getAllSuppliers();
        // THÊM LẠI DÒNG NÀY ĐỂ LOAD DANH SÁCH SẢN PHẨM:
        $data['products'] = $productModel->getFilteredProducts(['status' => '1,2'], 0, 1000); 
        
        $data['title'] = "Lập phiếu nhập kho";
        $data['is_form'] = true; 
        
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
    public function searchProductsAjax() {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            header('Content-Type: application/json');
            ob_clean(); // Xóa các khoảng trắng thừa để JSON không bị lỗi
            
            $keyword = trim($_GET['keyword'] ?? '');
            
            if (strlen($keyword) < 2) {
                echo json_encode(['success' => true, 'data' => []]);
                exit;
            }

            $importModel = $this->model('ImportModel');
            $products = $importModel->searchProductsForImport($keyword);
            
            echo json_encode(['success' => true, 'data' => $products]);
            exit;
        }
    }
    public function edit($id) {
        $importModel = $this->model('ImportModel');
        
        $receipt = $importModel->getImportById($id);
        
        // Chặn người dùng cố tình gõ URL để sửa phiếu đã hoàn tất
        if (!$receipt || $receipt['status'] === 'completed') {
            header("Location: /lego_shop_php/adminimport?error=1");
            exit;
        }

        $data['receipt'] = $receipt;
        $data['details'] = $importModel->getImportDetails($id);
        
        // Load lại danh sách y như form tạo mới
        $data['imports'] = $importModel->getAllImports();
        $data['suppliers'] = $importModel->getAllSuppliers();
        $data['products'] = $importModel->getProductsForImportForm(); 
        
        $data['title'] = "Sửa phiếu nhập kho #PN-" . $id;
        $data['is_form'] = true; 
        $data['is_edit'] = true; // Cờ báo hiệu giao diện biết là đang Sửa
        
        $this->view('admin/imports', $data);
    }
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_clean(); 
            header('Content-Type: application/json');

            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) throw new Exception("Dữ liệu không hợp lệ");
                
                $supplier_id = intval($data['supplier_id']);
                $products = $data['products']; 
                $status = $data['status'] ?? 'draft'; 
                $admin_id = $_SESSION['admin_id']; 

                $importModel = $this->model('ImportModel'); 
                // Gọi hàm update mới viết
                $success = $importModel->updateImportTransaction($id, $admin_id, $supplier_id, $products, $status);

                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật CSDL (Có thể phiếu đã hoàn tất)']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }
}