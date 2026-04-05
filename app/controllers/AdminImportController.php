<?php
class AdminImportController extends Controller {
    
    private $limit = 6; // SỐ DÒNG HIỂN THỊ 1 TRANG

    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Trang mặc định (Chỉ hiện bảng lịch sử và Bộ lọc có Phân Trang)
    public function index() {
        $importModel = $this->model('ImportModel');
        
        $filters = [
            'keyword'     => $_GET['keyword'] ?? '',
            'supplier_id' => $_GET['supplier_id'] ?? '',
            'status'      => $_GET['status'] ?? '',
            'start_date'  => $_GET['start_date'] ?? '',
            'end_date'    => $_GET['end_date'] ?? ''
        ];
        
        // ===== XỬ LÝ PHÂN TRANG =====
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $totalRecords = $importModel->countAllImports($filters);
        $totalPages = ceil($totalRecords / $this->limit);

        $data['filters'] = $filters; 
        $data['suppliers'] = $importModel->getAllSuppliers(); 
        
        // Truyền thêm offset và limit vào
        $data['imports'] = $importModel->getAllImports($filters, $offset, $this->limit); 
        $data['title'] = "Quản lý Nhập hàng";
        $data['is_form'] = false; 
        
        $data['currentPage'] = $page;
        $data['totalPages'] = $totalPages;
        
        $this->view('admin/imports', $data);
    }

    // Trang tạo mới (Hiện form phía trên, bảng lịch sử phía dưới)
    public function create() {
        $importModel = $this->model('ImportModel');
        $productModel = $this->model('ProductModel'); 
        
        // Ở form tạo mới, bảng lịch sử bên dưới chỉ cần hiện 10 phiếu gần nhất
        $data['imports'] = $importModel->getAllImports([], 0, 6);
        $data['suppliers'] = $importModel->getAllSuppliers();
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
                // Nhận import_date từ JS, nếu trống thì lấy ngày giờ hiện tại
                $import_date = !empty($data['import_date']) 
               ? str_replace('T', ' ', $data['import_date']) // Chuyển chữ T thành khoảng trắng để MySQL hiểu
               : date('Y-m-d H:i:s');

                $importModel = $this->model('ImportModel'); 
                $success = $importModel->createImportTransaction($admin_id, $supplier_id, $products, $status, $import_date);

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

    // --- MỞ TRANG IN PHIẾU NHẬP (TAB MỚI) ---
    public function print($id) {
        $importModel = $this->model('ImportModel');
        
        $receipt = $importModel->getImportById($id);
        if (!$receipt || $receipt['status'] !== 'completed') {
            echo "<script>alert('Lỗi: Chỉ có thể in phiếu nhập đã hoàn tất!'); window.close();</script>";
            exit;
        }

        $data['receipt'] = $receipt;
        $data['receipt_details'] = $importModel->getImportDetails($id);
        
        
        // 1. Giải nén mảng $data thành các biến độc lập ($receipt, $receipt_details)
        extract($data); 
        
        // 2. Gọi thẳng file View HTML thuần túy (Bypass toàn bộ Sidebar/Header)
        // (Lưu ý: Nếu hệ thống báo lỗi không tìm thấy file, hãy thử đổi thành '../app/views/admin/import_print.php')
        require_once 'app/views/admin/import_print.php'; 
        
        // 3. Ngắt luôn PHP tại đây để hệ thống không tự động nạp thêm Footer
        exit; 
    }

    // ==================================================
    // 1. HIỂN THỊ FORM SỬA BẢN NHÁP
    // ==================================================
    public function edit($id) {
        $importModel = $this->model('ImportModel');
        $productModel = $this->model('ProductModel');
        
        $receipt = $importModel->getImportById($id);
        // Chặn nếu không tìm thấy phiếu, hoặc phiếu KHÔNG PHẢI BẢN NHÁP
        if (!$receipt || $receipt['status'] !== 'draft') {
            header("Location: /lego_shop_php/adminimport");
            exit;
        }

        $data['receipt'] = $receipt;
        $data['receipt_details'] = $importModel->getImportDetails($id); 
        $data['suppliers'] = $importModel->getAllSuppliers();
        $data['products'] = $productModel->getFilteredProducts(['status' => '1,2'], 0, 1000); 
        
        $data['title'] = "Tiếp tục lập phiếu nhập (#PN-" . $id . ")";
        $data['is_form'] = true; 
        
        // Gọi ra file View tạo phiếu nhập để load lại dữ liệu cũ
        $this->view('admin/imports', $data); 
    }

    // ==================================================
    // 2. API NHẬN DỮ LIỆU CẬP NHẬT TỪ AJAX (KHI BẤM LƯU)
    // ==================================================
    public function updateDraft($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_clean();
            header('Content-Type: application/json');
            
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data || empty($data['products'])) throw new Exception("Dữ liệu không hợp lệ");

                $supplier_id = intval($data['supplier_id']);
                $import_date = !empty($data['import_date']) 
               ? str_replace('T', ' ', $data['import_date']) // Chuyển chữ T thành khoảng trắng để MySQL hiểu
               : date('Y-m-d H:i:s');
                $products = $data['products'];

                $importModel = $this->model('ImportModel');
                // Gọi hàm Update Draft trong Model
                $success = $importModel->updateDraftTransaction($id, $supplier_id, $products, $import_date);

                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("Lỗi cập nhật CSDL");
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }
}
