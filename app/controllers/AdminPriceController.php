<?php
class AdminPriceController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Hiển thị giao diện Quản lý giá
    public function index() {
        $productModel = $this->model('ProductModel');
        
        // Lấy danh sách tất cả sản phẩm đang bán
        $data['products'] = $productModel->getFilteredProducts(['status' => '1,2'], 0, 1000); 
        $data['title'] = "Cập nhật Giá Bán & Lợi nhuận";
        
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
}