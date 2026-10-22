<?php
require_once __DIR__ . '/../models/VoucherModel.php';
require_once __DIR__ . '/../models/PaymentModel.php';
require_once __DIR__ . '/../models/RefundModel.php';

class CheckoutController extends Controller {
    // Các phương thức thanh toán khách được chọn khi đặt hàng
    private $allowed_methods = ['cash', 'vietqr'];

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function calcSubtotal($cart_items) {
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += ($item['selling_price'] * $item['quantity']);
        }
        return (int)$subtotal;
    }

    public function index() {
        $cartModel = $this->model('CartModel');
        $userModel = $this->model('UserModel');
        $voucherModel = $this->model('VoucherModel');
        $paymentModel = $this->model('PaymentModel');

        // Giải phóng tồn kho của các đơn VietQR quá hạn trước khi tính toán
        $paymentModel->expireUnpaidOrders();

        $cart_items = $cartModel->getCartItems($_SESSION['user_id']);
        if (empty($cart_items)) {
            header("Location: /lego_shop_php/cart");
            exit;
        }

        $addresses = $userModel->getUserAddresses($_SESSION['user_id']);
        $total_price = $this->calcSubtotal($cart_items);

        // Kiểm tra lại voucher đã áp dụng trước đó (giỏ hàng có thể đã thay đổi)
        $applied_voucher = null;
        $discount = 0;
        if (!empty($_SESSION['voucher_code'])) {
            $check = $voucherModel->validate($_SESSION['voucher_code'], $_SESSION['user_id'], $total_price);
            if ($check['ok']) {
                $applied_voucher = $check['voucher'];
                $discount = $check['discount'];
            } else {
                unset($_SESSION['voucher_code']);
            }
        }

        $this->view('/user/cart/checkout', [
            'title' => 'Thanh toán đơn hàng',
            'cart_items' => $cart_items,
            'addresses' => $addresses,
            'total_price' => $total_price,
            'discount' => $discount,
            'applied_voucher' => $applied_voucher,
            'vouchers' => $voucherModel->getAvailableVouchers($_SESSION['user_id']),
            'settings' => $paymentModel->getSettings()
        ]);
    }

    // API: Áp dụng mã giảm giá
    public function applyVoucher() {
        $input = json_decode(file_get_contents('php://input'), true);
        $code = strtoupper(trim($input['code'] ?? ''));

        $cartModel = $this->model('CartModel');
        $cart_items = $cartModel->getCartItems($_SESSION['user_id']);
        if (empty($cart_items)) {
            $this->jsonResponse(['success' => false, 'message' => 'Giỏ hàng của bạn đang trống!']);
        }
        $subtotal = $this->calcSubtotal($cart_items);

        $voucherModel = $this->model('VoucherModel');
        $check = $voucherModel->validate($code, $_SESSION['user_id'], $subtotal);

        if (!$check['ok']) {
            $this->jsonResponse(['success' => false, 'message' => $check['message']]);
        }

        $_SESSION['voucher_code'] = $check['voucher']['code'];
        $this->jsonResponse([
            'success' => true,
            'message' => $check['message'],
            'code' => $check['voucher']['code'],
            'description' => VoucherModel::describe($check['voucher']),
            'subtotal' => $subtotal,
            'discount' => $check['discount'],
            'total' => $subtotal - $check['discount']
        ]);
    }

    // API: Bỏ mã giảm giá
    public function removeVoucher() {
        unset($_SESSION['voucher_code']);
        $cartModel = $this->model('CartModel');
        $subtotal = $this->calcSubtotal($cartModel->getCartItems($_SESSION['user_id']));
        $this->jsonResponse(['success' => true, 'subtotal' => $subtotal, 'discount' => 0, 'total' => $subtotal]);
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /lego_shop_php/checkout");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $address_id = $_POST['address_id'] ?? null;
        $payment_method = $_POST['payment_method'] ?? 'cash';

        if (!in_array($payment_method, $this->allowed_methods, true)) {
            set_flash_message('dialog', 'Phương thức thanh toán không hợp lệ!');
            header("Location: /lego_shop_php/checkout");
            exit;
        }

        if (empty($address_id) || $address_id === 'new') {
            set_flash_message('dialog', 'Vui lòng chọn hoặc lưu địa chỉ giao hàng!');
            header("Location: /lego_shop_php/checkout");
            exit;
        }

        $cartModel = $this->model('CartModel');
        $cart_items = $cartModel->getCartItems($user_id);
        if (empty($cart_items)) {
            header("Location: /lego_shop_php/cart");
            exit;
        }

        // Lấy thông tin địa chỉ chi tiết
        $userModel = $this->model('UserModel');
        $address = $userModel->getAddressById($address_id, $user_id);
        if (!$address) {
            set_flash_message('dialog', 'Địa chỉ không hợp lệ!');
            header("Location: /lego_shop_php/checkout");
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $voucherModel = $this->model('VoucherModel');
        $db = $orderModel->getConnection();

        $db->begin_transaction();
        try {
            // =========================================================
            // 1. TRẠM GÁC: KIỂM TRA TỒN KHO KHẢ DỤNG (đã trừ hàng đang giữ cho đơn khác)
            // =========================================================
            foreach ($cart_items as $item) {
                // Khóa dòng sản phẩm để 2 khách không cùng lúc mua vượt tồn kho
                $db->query("SELECT id FROM products WHERE id = " . (int)$item['product_id'] . " FOR UPDATE");
                $available = $cartModel->getAvailableStock($item['product_id']);
                if ($available < $item['quantity']) {
                    throw new RuntimeException("Sản phẩm \"{$item['name']}\" chỉ còn {$available} sản phẩm trong kho!");
                }
            }

            // =========================================================
            // 2. TÍNH TIỀN + ÁP MÃ GIẢM GIÁ (kiểm tra lại phía server)
            // =========================================================
            $subtotal = $this->calcSubtotal($cart_items);
            $discount = 0;
            $voucher = null;
            if (!empty($_SESSION['voucher_code'])) {
                $check = $voucherModel->validate($_SESSION['voucher_code'], $user_id, $subtotal, true);
                if (!$check['ok']) {
                    unset($_SESSION['voucher_code']);
                    throw new RuntimeException('Mã giảm giá: ' . $check['message']);
                }
                $voucher = $check['voucher'];
                $discount = $check['discount'];
            }
            $total_amount = $subtotal - $discount;

            // =========================================================
            // 3. TẠO ĐƠN HÀNG
            // =========================================================
            $order_id = $orderModel->createOrder([
                'user_id' => $user_id,
                'payment_method' => $payment_method,
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'voucher_id' => $voucher['id'] ?? null,
                'voucher_code' => $voucher['code'] ?? null,
                'total_amount' => $total_amount,
                'shipping_fullname' => $address['receiver_name'],
                'shipping_phone' => $address['receiver_phone'],
                'shipping_street' => $address['street'],
                'shipping_ward' => $address['ward'],
                'shipping_district' => $address['district'],
                'shipping_city' => $address['city'],
            ]);

            foreach ($cart_items as $item) {
                $orderModel->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['selling_price']);
            }

            if ($voucher) {
                $voucherModel->recordUsage($voucher['id'], $user_id, $order_id, $discount);
            }

            if ($payment_method === 'vietqr') {
                $orderModel->setPaymentCode($order_id, PaymentModel::makePaymentCode($order_id));
            }

            // Voucher giảm hết tiền -> không cần thanh toán
            if ($total_amount <= 0) {
                $db->query("UPDATE orders SET payment_status = 'paid', paid_at = NOW() WHERE id = " . (int)$order_id);
                $orderModel->addHistory($order_id, 'paid', 'Đơn 0đ sau khi áp dụng mã giảm giá');
            }

            $cartModel->clearCart($user_id);
            $db->commit();
        } catch (RuntimeException $e) {
            $db->rollback();
            set_flash_message('dialog', $e->getMessage());
            header("Location: /lego_shop_php/checkout");
            exit;
        } catch (Exception $e) {
            $db->rollback();
            error_log('Checkout error: ' . $e->getMessage());
            set_flash_message('dialog', 'Lỗi hệ thống khi tạo đơn hàng!');
            header("Location: /lego_shop_php/checkout");
            exit;
        }

        unset($_SESSION['voucher_code']);

        if ($payment_method === 'vietqr' && $total_amount > 0) {
            header("Location: /lego_shop_php/checkout/payment?order_id=" . $order_id);
        } else {
            header("Location: /lego_shop_php/checkout/success?order_id=" . $order_id);
        }
        exit;
    }

    // 4. Hiển thị trang Thành công (Bước 4)
    public function success() {
        $order_id = (int)($_GET['order_id'] ?? 0);
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header("Location: /lego_shop_php/profile/orders");
            exit;
        }

        $this->view('/user/cart/success', [
            'title' => 'Đặt hàng thành công',
            'order_id' => $order_id,
            'order' => $order
        ]);
    }

    // Trang thanh toán VietQR (Bước 3)
    public function payment() {
        $order_id = (int)($_GET['order_id'] ?? 0);

        $paymentModel = $this->model('PaymentModel');
        $paymentModel->expireUnpaidOrders($_SESSION['user_id']);

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);

        if (!$order || $order['user_id'] != $_SESSION['user_id'] || $order['payment_method'] !== 'vietqr') {
            header("Location: /lego_shop_php/profile/orders");
            exit;
        }

        // Đã thanh toán rồi thì sang trang thành công
        if ($order['payment_status'] !== 'unpaid') {
            header("Location: /lego_shop_php/checkout/success?order_id=" . $order_id);
            exit;
        }

        $settings = $paymentModel->getSettings();

        $this->view('/user/cart/payment', [
            'title' => 'Thanh toán VietQR',
            'order' => $order,
            'settings' => $settings,
            'qr_url' => $paymentModel->buildQrUrl($order, $settings),
            'expire_at' => $paymentModel->getExpireTime($order, $settings),
            'sandbox' => (int)($settings['payment_sandbox'] ?? 0) === 1
        ]);
    }

    // xem chi tiết đơn hàng sau khi đặt
    public function view_order() {
        $order_id = $_GET['order_id'] ?? 0;

        if (!$order_id) {
            header("Location: /lego_shop_php/home");
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            set_flash_message('dialog', 'Không tìm thấy đơn hàng!');
            header("Location: /lego_shop_php/profile/orders");
            exit;
        }

        $order_items = $orderModel->getOrderItems($order_id);
        $refundModel = $this->model('RefundModel');

        $this->view('/user/cart/view_order', [
            'title' => 'Chi tiết đơn hàng #' . $order_id,
            'order' => $order,
            'order_items' => $order_items,
            'refund' => $refundModel->getLatestByOrder($order_id)
        ]);
    }

    // ========================================================================
    // HỦY ĐƠN: hoàn lượt voucher, ghi lịch sử.
    // Nếu đơn đã thanh toán online -> tạo yêu cầu hoàn tiền (cần thông tin tài khoản nhận tiền)
    // ========================================================================
    public function cancelOrderAjax() {
        $input = json_decode(file_get_contents('php://input'), true);
        $order_id = (int)($input['order_id'] ?? 0);
        $reason = trim($input['reason'] ?? '') ?: 'Khách hàng tự hủy';

        if (!$order_id) {
            $this->jsonResponse(['success' => false, 'message' => 'Thiếu thông tin đơn hàng!']);
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);

        // Bảo mật: Đảm bảo đơn hàng tồn tại và thuộc về user đang đăng nhập
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Bạn không có quyền hủy đơn này!']);
        }

        // Chỉ cho hủy nếu trạng thái hợp lệ
        if (!in_array($order['status'], ['pending', 'confirmed'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Đơn hàng ở trạng thái này không thể hủy!']);
        }

        $is_paid = ($order['payment_status'] === 'paid');
        $bank_name = trim($input['bank_name'] ?? '');
        $bank_account_number = preg_replace('/\s+/', '', $input['bank_account_number'] ?? '');
        $bank_account_name = mb_strtoupper(trim($input['bank_account_name'] ?? ''));

        if ($is_paid) {
            $error = RefundModel::validateBankInfo($bank_name, $bank_account_number, $bank_account_name);
            if ($error) {
                $this->jsonResponse(['success' => false, 'message' => $error, 'need_bank' => true]);
            }
        }

        $db = $orderModel->getConnection();
        $db->begin_transaction();
        try {
            // Điều kiện status trong WHERE để chống hủy trùng khi admin đang đổi trạng thái
            $stmt1 = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ? AND status IN ('pending','confirmed')");
            $stmt1->bind_param("i", $order_id);
            $stmt1->execute();
            if ($stmt1->affected_rows === 0) {
                throw new RuntimeException('Đơn hàng vừa được cập nhật, vui lòng tải lại trang!');
            }

            $orderModel->addHistory($order_id, 'cancelled', $reason);

            $voucherModel = $this->model('VoucherModel');
            $voucherModel->releaseUsage($order_id);

            if ($is_paid) {
                $refundModel = $this->model('RefundModel');
                $refundModel->create([
                    'order_id' => $order_id,
                    'user_id' => $_SESSION['user_id'],
                    'type' => 'cancel',
                    'amount' => (int)$order['total_amount'],
                    'reason' => $reason,
                    'evidence_image' => null,
                    'bank_name' => $bank_name,
                    'bank_account_number' => $bank_account_number,
                    'bank_account_name' => $bank_account_name,
                ]);
                $db->query("UPDATE orders SET payment_status = 'refund_pending' WHERE id = " . $order_id);
                $orderModel->addHistory($order_id, 'refund_requested', 'Yêu cầu hoàn ' . number_format($order['total_amount'], 0, ',', '.') . 'đ về ' . $bank_name);
            }

            $db->commit();
            $this->jsonResponse([
                'success' => true,
                'message' => $is_paid ? 'Hủy đơn thành công! Yêu cầu hoàn tiền đã được gửi.' : 'Hủy đơn thành công'
            ]);
        } catch (RuntimeException $e) {
            $db->rollback();
            $this->jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        } catch (Exception $e) {
            $db->rollback();
            error_log('Cancel order error: ' . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Lỗi hệ thống khi hủy đơn!']);
        }
    }


    // API Lấy dữ liệu đánh giá cũ
    public function getReviewAjax() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id']) || empty($_GET['product_id'])) {
            echo json_encode(['success' => false]);
            exit;
        }

        $prodModel = $this->model('ProductModel');
        $review = $prodModel->getReviewByUserAndProduct($_SESSION['user_id'], intval($_GET['product_id']));

        if ($review) {
            echo json_encode(['success' => true, 'review' => $review]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    // API Gửi/Cập nhật đánh giá
    public function submitReviewAjax() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để đánh giá!']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $user_id = $_SESSION['user_id'];
        $product_id = intval($input['product_id'] ?? 0);
        $rating = intval($input['rating'] ?? 0);
        $comment = trim($input['comment'] ?? '');

        if ($product_id == 0 || $rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
            exit;
        }

        $prodModel = $this->model('ProductModel');
        // Gọi hàm mới (Tự động Update nếu đã có, Insert nếu chưa)
        $result = $prodModel->saveProductReview($user_id, $product_id, $rating, $comment);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Lưu đánh giá thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, không thể lưu đánh giá!']);
        }
        exit;
    }

}
