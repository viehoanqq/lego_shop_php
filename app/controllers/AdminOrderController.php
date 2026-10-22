<?php
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/RefundModel.php';
require_once __DIR__ . '/../models/VoucherModel.php';

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
            header("Location: /lego_shop_php/admin/order?error=notfound");
            exit;
        }

        $items = $orderModel->getOrderItems($id);
        $reviews = $orderModel->getOrderReviews($id, $order['user_id']);
        $history = $orderModel->getOrderHistory($id); // LẤY LỊCH SỬ TỪ DB
        $paymentModel = $this->model('PaymentModel');
        $refundModel = $this->model('RefundModel');

        $this->view('admin/order_detail', [
            'title' => 'Chi tiết đơn hàng #DH-' . $id,
            'order' => $order,
            'items' => $items,
            'reviews' => $reviews,
            'history' => $history, // TRUYỀN LỊCH SỬ RA VIEW
            'transactions' => $paymentModel->getTransactionsByOrder($id),
            'refund' => $refundModel->getLatestByOrder($id),
            'banks' => RefundModel::BANKS,
            'flash_error' => get_flash_message('order_error')
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
                header("Location: /lego_shop_php/admin/order?error=notfound");
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
                set_flash_message('order_error', 'Cập nhật trạng thái không hợp lệ theo quy trình!');
                header("Location: /lego_shop_php/admin/order/detail/$id");
                exit;
            }

            // Đơn chuyển khoản / VietQR phải nhận được tiền rồi mới được giao thành công
            if ($new_status === 'delivered' && in_array($order['payment_method'], ['transfer', 'vietqr']) && $order['payment_status'] !== 'paid') {
                set_flash_message('order_error', 'Đơn chưa được thanh toán.');
                header("Location: /lego_shop_php/admin/order/detail/$id"); exit;
            }

            // --- HỦY ĐƠN: hoàn lượt voucher + tạo yêu cầu hoàn tiền nếu khách đã thanh toán ---
            if ($new_status === 'cancelled') {
                $this->cancelByAdmin($order, $note);
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
                    header("Location: /lego_shop_php/admin/order/detail/$id?msg=status_success");
                } catch (Exception $e) {
                    $db->rollback();
                    header("Location: /lego_shop_php/admin/order/detail/$id?error=stock_error");
                }
                exit;
            }   
            // --- KẾT THÚC LOGIC TRỪ KHO ---

            if ($orderModel->updateOrderStatusAdmin($id, $new_status, $note)) {
                header("Location: /lego_shop_php/admin/order/detail/$id?msg=status_success");
            } else {
                header("Location: /lego_shop_php/admin/order/detail/$id?error=1");
            }
            exit;
        }
    }

    private function cancelByAdmin($order, $note) {
        $id = (int)$order['id'];
        $orderModel = $this->model('OrderModel');
        $voucherModel = $this->model('VoucherModel');
        $refundModel = $this->model('RefundModel');
        $is_paid = ($order['payment_status'] === 'paid');

        // Tài khoản nhận tiền hoàn (không bắt buộc - khách có thể tự bổ sung ở trang đơn hàng)
        $bank_name = trim($_POST['bank_name'] ?? '');
        $bank_no = preg_replace('/\s+/', '', $_POST['bank_account_number'] ?? '');
        $bank_holder = mb_strtoupper(trim($_POST['bank_account_name'] ?? ''));
        if ($is_paid && ($bank_name !== '' || $bank_no !== '' || $bank_holder !== '')) {
            $error = RefundModel::validateBankInfo($bank_name, $bank_no, $bank_holder);
            if ($error) {
                set_flash_message('order_error', $error);
                header("Location: /lego_shop_php/admin/order/detail/$id"); exit;
            }
        }

        $db = $orderModel->getConnection();
        $db->begin_transaction();
        try {
            $stmt = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND status = ?");
            $stmt->bind_param("is", $id, $order['status']);
            $stmt->execute();
            if ($stmt->affected_rows === 0) throw new RuntimeException('Đơn hàng vừa được cập nhật bởi người khác!');

            $orderModel->addHistory($id, 'cancelled', $note ?: 'Cửa hàng hủy đơn');
            $voucherModel->releaseUsage($id);

            if ($is_paid) {
                $refundModel->create([
                    'order_id' => $id,
                    'user_id' => $order['user_id'],
                    'type' => 'cancel',
                    'amount' => (int)$order['total_amount'],
                    'reason' => 'Cửa hàng hủy đơn' . ($note ? ": $note" : ''),
                    'evidence_image' => null,
                    'bank_name' => $bank_name,
                    'bank_account_number' => $bank_no,
                    'bank_account_name' => $bank_holder,
                ]);
                $db->query("UPDATE orders SET payment_status = 'refund_pending' WHERE id = $id");
                $orderModel->addHistory($id, 'refund_requested', 'Tạo yêu cầu hoàn ' . number_format($order['total_amount'], 0, ',', '.') . 'đ do cửa hàng hủy đơn');
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollback();
            set_flash_message('order_error', $e instanceof RuntimeException ? $e->getMessage() : 'Lỗi hệ thống khi hủy đơn!');
            header("Location: /lego_shop_php/admin/order/detail/$id"); exit;
        }
        header("Location: /lego_shop_php/admin/order/detail/$id?msg=status_success");
    }

    // Xử lý Cập nhật trạng thái Thanh toán (admin xác nhận đã nhận tiền chuyển khoản / VietQR)
    public function update_payment($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $payment_status = $_POST['payment_status'] ?? 'unpaid';
            $orderModel = $this->model('OrderModel');
            $order = $orderModel->getOrderById($id);

            if ($payment_status !== 'paid' || !$order) {
                header("Location: /lego_shop_php/admin/order/detail/$id"); exit;
            }

            // Ghi qua PaymentModel để có log giao dịch + lịch sử đơn
            $paymentModel = $this->model('PaymentModel');
            $ref = 'MANUAL-' . $id . '-' . time();
            $note = trim($_POST['payment_note'] ?? '');
            $result = $paymentModel->confirmPayment((int)$id, (int)$order['total_amount'], 'manual', $ref, $note, '');

            if ($result === 'paid') {
                header("Location: /lego_shop_php/admin/order/detail/$id?msg=payment_success");
            } else {
                $msgs = ['already_paid' => 'Đơn đã được xác nhận thanh toán trước đó!', 'order_cancelled' => 'Đơn đã hủy, không thể xác nhận thanh toán!'];
                set_flash_message('order_error', $msgs[$result] ?? 'Không thể xác nhận thanh toán!');
                header("Location: /lego_shop_php/admin/order/detail/$id");
            }
            exit;
        }
    }
}