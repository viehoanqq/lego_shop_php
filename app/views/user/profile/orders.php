<?php
class CheckoutController extends Controller {
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        // Bắt buộc đăng nhập mới được vào trang Checkout
        if (!isset($_SESSION['user_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

    // 1. Hiển thị trang Thanh toán (Bước 2)
    public function index() {
        $cartModel = $this->model('CartModel');
        $userModel = $this->model('UserModel');

        $cart_items = $cartModel->getCartItems($_SESSION['user_id']);
        if (empty($cart_items)) {
            header("Location: /lego_shop_php/cart");
            exit;
        }

        $addresses = $userModel->getUserAddresses($_SESSION['user_id']);
        
        $total_price = 0;
        foreach ($cart_items as $item) {
            $total_price += ($item['selling_price'] * $item['quantity']);
        }

        $this->view('/user/cart/checkout', [
            'title' => 'Thanh toán đơn hàng',
            'cart_items' => $cart_items,
            'addresses' => $addresses,
            'total_price' => $total_price
        ]);
    }

    // 2. Xử lý lưu đơn hàng vào Database (Bước đệm)
    public function process() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $address_id = $_POST['address_id'] ?? null;

            // Kiểm tra xem khách đã chọn địa chỉ chưa
            if (empty($address_id) || $address_id === 'new') {
                echo "<script>alert('Vui lòng chọn hoặc lưu địa chỉ giao hàng!'); history.back();</script>";
                exit;
            }

            // Lấy Giỏ hàng
            $cartModel = $this->model('CartModel');
            $cart_items = $cartModel->getCartItems($user_id);
            if (empty($cart_items)) {
                header("Location: /lego_shop_php/cart");
                exit;
            }

            // Tính tổng tiền
            $total_amount = 0;
            foreach ($cart_items as $item) {
                $total_amount += ($item['selling_price'] * $item['quantity']);
            }

            // Lấy thông tin địa chỉ chi tiết
            $userModel = $this->model('UserModel');
            $address = $userModel->getAddressById($address_id, $user_id);
            
            if (!$address) {
                echo "<script>alert('Địa chỉ không hợp lệ!'); history.back();</script>";
                exit;
            }

            // ========================================================
            // PHIÊN DỊCH DỮ LIỆU ĐỂ KHỚP VỚI BẢNG ENUM TRONG DATABASE
            // ========================================================
            $status = 'pending'; // Mặc định đơn mới là chờ xử lý (pending)

            $raw_payment = $_POST['payment_method'] ?? 'cod'; 
            if ($raw_payment === 'banking') {
                $payment_method = 'transfer'; // Chuyển khoản
            } elseif ($raw_payment === 'online') {
                $payment_method = 'online';   // Trực tuyến
            } else {
                $payment_method = 'cash';     // COD (Tiền mặt)
            }

            // Gọi OrderModel để lưu
            $orderModel = $this->model('OrderModel');
            
            $order_id = $orderModel->createOrder(
                $user_id, 
                $status, 
                $payment_method, 
                $total_amount, 
                $address['receiver_name'], 
                $address['receiver_phone'], 
                $address['street'], 
                $address['ward'], 
                $address['district'], 
                $address['city']
            );

            if ($order_id) {
                // Lưu chi tiết sản phẩm
                foreach ($cart_items as $item) {
                    $orderModel->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['selling_price']);
                }

                // Xóa giỏ hàng sau khi đặt thành công
                $cartModel->clearCart($user_id);

                // Chuyển hướng dựa trên phương thức thanh toán
                if ($payment_method === 'transfer') {
                    header("Location: /lego_shop_php/checkout/payment?order_id=" . $order_id);
                } else {
                    header("Location: /lego_shop_php/checkout/success?order_id=" . $order_id);
                }
                exit;
            } else {
                echo "<script>alert('Lỗi hệ thống khi tạo đơn hàng!'); history.back();</script>";
            }
        }
    }

    // 3. Hiển thị trang Quét mã QR (Bước 3)
    public function payment() {
        $order_id = $_GET['order_id'] ?? 0;
        
        // Gọi DB để lấy thông tin số tiền cần chuyển
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id); 
        
        // Bảo mật: Nếu không có đơn hàng hoặc đơn hàng không phải của user này
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            header("Location: /lego_shop_php/home");
            exit;
        }

        $total_price = $order ? $order['total_amount'] : 0;

        $this->view('/user/cart/payment', [
            'title' => 'Thanh toán chuyển khoản',
            'order_id' => $order_id,
            'total_price' => $total_price
        ]);
    }

    // 4. Hiển thị trang Thành công (Bước 4)
    public function success() {
        $order_id = $_GET['order_id'] ?? 0;
        $this->view('/user/cart/success', [
            'title' => 'Đặt hàng thành công',
            'order_id' => $order_id
        ]);
    }

    // 5. Hiển thị chi tiết đơn hàng sau khi đặt (Bước 5)
    public function view_order() {
        $order_id = $_GET['order_id'] ?? 0;
        
        if (!$order_id) {
            header("Location: /lego_shop_php/home");
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);
        
        // BẢO MẬT: Kiểm tra xem đơn hàng có tồn tại và có đúng là của user đang đăng nhập không
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            echo "<script>alert('Bạn không có quyền xem đơn hàng này!'); window.location.href='/lego_shop_php/home';</script>";
            exit;
        }

        // Lấy danh sách sản phẩm
        $order_items = $orderModel->getOrderItems($order_id);

        $this->view('/user/cart/view_order', [
            'title' => 'Chi tiết đơn hàng #' . $order_id,
            'order' => $order,
            'order_items' => $order_items
        ]);
    }
}