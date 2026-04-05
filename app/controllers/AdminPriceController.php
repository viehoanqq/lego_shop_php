<?php
class AdminpriceController extends Controller {
   private $limit = 8; // Đặt số lượng hiển thị 1 trang là 10

    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Hiển thị giao diện Quản lý giá
    public function index() {
        $productModel = $this->model('ProductModel');
        $categoryModel = $this->model('CategoryModel'); 
        
        $filters = [
            'keyword'     => $_GET['keyword'] ?? '',
            'category_id' => $_GET['category_id'] ?? ''
        ];

        // ===== XỬ LÝ PHÂN TRANG =====
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $totalRecords = $productModel->countAllProductsWithPrices($filters);
        $totalPages = ceil($totalRecords / $this->limit);

        // Truyền dữ liệu ra View
        $data['filters'] = $filters;
        $data['categories'] = $categoryModel->getAllCategories();
        
        // Truyền thêm $offset và $limit vào Model
        $data['products'] = $productModel->getAllProductsWithPrices($filters, $offset, $this->limit); 
        $data['title'] = "Cập nhật Giá Bán & Lợi nhuận";
        
        $data['currentPage'] = $page;
        $data['totalPages'] = $totalPages;
        
        $this->view('admin/prices', $data);
    }

    // API nhận dữ liệu lưu từ AJAX
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_clean();
            header('Content-Type: application/json');

            try {
                $data = json_decode(file_get_contents('php://input'), true);
                $product_id = $data['product_id'];
                $selling_price = $data['selling_price'];
                $profit_margin = $data['profit_margin'] / 100; // Đổi từ % (VD: 20) về số thập phân (0.2)

                $productModel = $this->model('ProductModel');
                $success = $productModel->updatePriceAndMargin($product_id, $selling_price, $profit_margin);

                if ($success) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật CSDL']);
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }

    // MỚI: API xem lịch sử giá theo lô hàng
    public function history($id) {
        $productModel = $this->model('ProductModel');
        
        $product = $productModel->getProductFullDetail($id);
        if (!$product) {
            header("Location: /lego_shop_php/adminprice?error=notfound");
            exit;
        }

        $data['product'] = $product;
        $data['history'] = $productModel->getProductPriceHistory($id);
        $data['title'] = "Lịch sử giá: " . $product['name'];
        
        $this->view('admin/price_history', $data);
    }
    // --- HIỂN THỊ FORM SỬA BẢN NHÁP ---
    public function edit($id) {
        $importModel = $this->model('ImportModel');
        $productModel = $this->model('ProductModel');
        
        $receipt = $importModel->getImportById($id);
        // Chặn nếu không phải bản nháp
        if (!$receipt || $receipt['status'] !== 'draft') {
            header("Location: /lego_shop_php/adminimport");
            exit;
        }

        $data['receipt'] = $receipt;
        $data['receipt_details'] = $importModel->getImportDetails($id); // Lấy danh sách SP cũ
        $data['suppliers'] = $importModel->getAllSuppliers();
        $data['products'] = $productModel->getFilteredProducts(['status' => '1,2'], 0, 1000); 
        $data['title'] = "Sửa Phiếu Nhập Kho Nháp (#PN-" . $id . ")";
        $data['is_form'] = true; 
        
        // Truyền ra đúng file View bạn đang dùng để hiển thị form tạo phiếu
        $this->view('admin/imports', $data); 
    }

    // --- API NHẬN DỮ LIỆU CẬP NHẬT TỪ AJAX ---
    public function updateDraft($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            ob_clean();
            header('Content-Type: application/json');
            
            try {
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data || empty($data['products'])) throw new Exception("Dữ liệu không hợp lệ");

                $supplier_id = intval($data['supplier_id']);
                $products = $data['products'];

                $importModel = $this->model('ImportModel');
                $success = $importModel->updateDraftTransaction($id, $supplier_id, $products);

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