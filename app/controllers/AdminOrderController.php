<?php
class AdminOrderController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // Hiển thị danh sách tất cả đơn hàng
    public function index() {
        $orderModel = $this->model('OrderModel');
        $data['orders'] = $orderModel->getAllOrdersAdmin($_GET ?? []);
        $data['filters'] = $_GET ?? []; 
        $data['title'] = "Quản lý Đơn hàng";
        
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
            $status = $_POST['status'] ?? '';
            $note = $_POST['note'] ?? ''; // LẤY GHI CHÚ TỪ FORM
            
            $orderModel = $this->model('OrderModel');
            
            if ($orderModel->updateOrderStatusAdmin($id, $status, $note)) {
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