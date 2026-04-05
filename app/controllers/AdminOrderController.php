<?php
class AdminOrderController extends Controller {
    
    private $limit = 5; // SỐ DÒNG HIỂN THỊ TRÊN MỖI TRANG

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Hiển thị danh sách tất cả đơn hàng (CÓ PHÂN TRANG)
    public function index() {
        $orderModel = $this->model('OrderModel');
        $filters = $_GET ?? [];

        // 1. Cấu hình phân trang
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        // 2. Đếm tổng số đơn hàng (theo bộ lọc) để tính số trang
        $totalRecords = $orderModel->countAllOrdersAdmin($filters);
        $totalPages = ceil($totalRecords / $this->limit);

        // 3. Lấy dữ liệu 1 trang (truyền thêm limit và offset)
        $data['orders'] = $orderModel->getAllOrdersAdmin($filters, $this->limit, $offset);
        
        $data['filters'] = $filters; 
        $data['title'] = "Quản lý Đơn hàng";
        $data['currentPage'] = $page;
        $data['totalPages'] = $totalPages;
        
        $this->view('admin/orders', $data);
    }
    
    // (Dự phòng nếu bạn xài link /admin/order)
    public function order() {
        $this->index();
    }

    // Xem chi tiết đơn hàng
    public function detail($id) {
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($id);
        
        if (!$order) {
            header("Location: /lego_shop_php/adminorder?error=notfound");
            exit;
        }

        $items = $orderModel->getOrderItems($id);
        $reviews = $orderModel->getOrderReviews($id, $order['user_id']);
        $history = $orderModel->getOrderHistory($id); // LẤY LỊCH SỬ TỪ DB
        
        $this->view('admin/order_detail', [
            'title' => 'Chi tiết đơn hàng #DH-' . $id,
            'order' => $order,
            'items' => $items,
            'reviews' => $reviews,
            'history' => $history // TRUYỀN LỊCH SỬ RA VIEW
        ]);
    }
    
    // (Dự phòng nếu bạn xài link /admin/order_detail)
    public function order_detail($id) {
        $this->detail($id);
    }

    // Xử lý Cập nhật trạng thái Đơn hàng (Kèm Note)
    public function update_status($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_status = $_POST['status'] ?? '';
            $note = $_POST['note'] ?? ''; 
            
            $orderModel = $this->model('OrderModel');
            $order = $orderModel->getOrderById($id);
            
            if (!$order) {
                header("Location: /lego_shop_php/adminorder?error=notfound");
                exit;
            }

            $current_status = $order['status'];

            // ==========================================
            // BẢO VỆ LOGIC: Máy trạng thái (State Machine)
            // ==========================================
            $is_valid = false;
            
            if ($current_status === 'pending' && in_array($new_status, ['pending', 'confirmed', 'cancelled'])) {
                $is_valid = true;
            } elseif ($current_status === 'confirmed' && in_array($new_status, ['confirmed', 'shipping', 'cancelled'])) {
                $is_valid = true;
            } elseif ($current_status === 'shipping' && in_array($new_status, ['shipping', 'delivered', 'cancelled'])) {
                $is_valid = true;
            }
            
            // Nếu đơn đã hoàn tất/hủy hoặc chọn trạng thái linh tinh -> Đuổi về
            if (!$is_valid || $current_status === 'delivered' || $current_status === 'cancelled') {
                echo "<script>alert('Lỗi: Cập nhật trạng thái không hợp lệ theo quy trình!'); history.back();</script>";
                exit;
            }
            // --- BẮT ĐẦU LOGIC TRỪ KHO KHI THÀNH CÔNG ---
            if ($new_status === 'delivered') {
                // Lấy danh sách sản phẩm trong đơn hàng
                $items = $orderModel->getOrderItems($id);
                $productModel = $this->model('ProductModel');
                $db = $orderModel->getConnection();
                
                $db->begin_transaction();
                try {
                    foreach ($items as $item) {
                        $qty = intval($item['quantity']);
                        $p_id = intval($item['product_id']);
                        
                        // 1. Thực hiện trừ kho vật lý
                        $db->query("UPDATE products SET stock_quantity = stock_quantity - $qty WHERE id = $p_id");
                        
                        
                    }
                    
                    // Cập nhật trạng thái đơn hàng
                    $orderModel->updateOrderStatusAdmin($id, $new_status, $note);
                    
                    $db->commit();
                    header("Location: /lego_shop_php/adminorder/detail/$id?msg=status_success");
                } catch (Exception $e) {
                    $db->rollback();
                    header("Location: /lego_shop_php/adminorder/detail/$id?error=stock_error");
                }
                exit;
            }   
            // --- KẾT THÚC LOGIC TRỪ KHO ---

            if ($orderModel->updateOrderStatusAdmin($id, $new_status, $note)) {
                header("Location: /lego_shop_php/adminorder/detail/$id?msg=status_success");
            } else {
                header("Location: /lego_shop_php/adminorder/detail/$id?error=1");
            }
            exit;
        }
    }

    // Xử lý Cập nhật trạng thái Thanh toán
    public function update_payment($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_status = $_POST['payment_status'] ?? 'unpaid';
            
            $orderModel = $this->model('OrderModel');
            
            if ($orderModel->updatePaymentStatusAdmin($id, $payment_status)) {
                header("Location: /lego_shop_php/adminorder/detail/$id?msg=payment_success");
            } else {
                header("Location: /lego_shop_php/adminorder/detail/$id?error=1");
            }
            exit;
        }
    }
}