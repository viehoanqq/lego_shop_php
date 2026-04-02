<?php
class CheckoutController extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }
    }

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

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $address_id = $_POST['address_id'] ?? null;
            $payment_method = $_POST['payment_method'] ?? 'cod'; 

            if (empty($address_id) || $address_id === 'new') {
                echo "<script>alert('Vui lòng chọn hoặc lưu địa chỉ giao hàng!'); history.back();</script>";
                exit;
            }

            $cartModel = $this->model('CartModel');
            $cart_items = $cartModel->getCartItems($user_id);
            if (empty($cart_items)) {
                header("Location: /lego_shop_php/cart");
                exit;
            }

            // =========================================================
            // 1. TRẠM GÁC: KIỂM TRA TỒN KHO TRƯỚC KHI TẠO ĐƠN
            // =========================================================
            $productModel = $this->model('ProductModel');
            foreach ($cart_items as $item) {
                $product = $productModel->getProductById($item['product_id']);
                
                // Nếu sản phẩm không tồn tại hoặc số lượng khách mua lớn hơn số lượng kho
                if (!$product || $product['stock_quantity'] < $item['quantity']) {
                    $p_name = $product ? $product['name'] : 'Sản phẩm';
                    // Đá về trang giỏ hàng và báo lỗi hết hàng
                    echo "<script>alert('LỖI: Sản phẩm \"{$p_name}\" không đủ số lượng trong kho!'); window.location.href='/lego_shop_php/cart';</script>";
                    exit;
                }
            }
            // =========================================================

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

            // Gọi OrderModel để lưu
            $orderModel = $this->model('OrderModel');
            $status = 'pending'; 
            
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
                $address['city'],
            );

            if ($order_id) {
                $db = $orderModel->getConnection(); // Lấy DB Connection để chạy trừ kho

                // Lưu chi tiết sản phẩm
                foreach ($cart_items as $item) {
                    $orderModel->addOrderItem($order_id, $item['product_id'], $item['quantity'], $item['selling_price']);
                    
                   
                }

                // Xóa giỏ hàng
                $cartModel->clearCart($user_id);

                // Chuyển hướng
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


    // 4. Hiển thị trang Thành công (Bước 4)
    public function success() {
        $order_id = $_GET['order_id'] ?? 0;
        $this->view('/user/cart/success', [
            'title' => 'Đặt hàng thành công',
            'order_id' => $order_id
        ]);
    }

    public function payment() {
        $order_id = $_GET['order_id'] ?? 0;
        
        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id); 
        
        $total_price = $order ? $order['total_amount'] : 0;

        $this->view('/user/cart/payment', [
            'title' => 'Thanh toán chuyển khoản',
            'order_id' => $order_id,
            'total_price' => $total_price
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
            echo "<script>alert('Bạn không có quyền xem đơn hàng này!'); window.location.href='/lego_shop_php/home';</script>";
            exit;
        }

        $order_items = $orderModel->getOrderItems($order_id);

        $this->view('/user/cart/view_order', [
            'title' => 'Chi tiết đơn hàng #' . $order_id,
            'order' => $order,
            'order_items' => $order_items
        ]);
    }

    // ========================================================================
    // Đã đổi tên hàm thành cancelOrderAjax để phù hợp với JS View đang gọi lên
    // Có kèm Logic HOÀN KHO và GHI LỊCH SỬ
    // ========================================================================
    public function cancelOrderAjax() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập!']);
            exit;
        }

        // Đọc dữ liệu JSON gửi lên
        $input = json_decode(file_get_contents('php://input'), true);
        $order_id = $input['order_id'] ?? 0;
        $reason = $input['reason'] ?? 'Khách hàng tự hủy'; // Lấy reason nếu có

        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng!']);
            exit;
        }

        $orderModel = $this->model('OrderModel');
        $order = $orderModel->getOrderById($order_id);

        // Bảo mật: Đảm bảo đơn hàng tồn tại và thuộc về user đang đăng nhập
        if ($order && $order['user_id'] == $_SESSION['user_id']) {
            
            // Chỉ cho hủy nếu trạng thái hợp lệ
            if (in_array($order['status'], ['pending', 'confirmed'])) {
                
                // Mở transaction để đảm bảo an toàn dữ liệu
                $db = $orderModel->getConnection();
                $db->begin_transaction();

                try {
                    // 1. Đổi trạng thái thành cancelled
                    $stmt1 = $db->prepare("UPDATE orders SET status = 'cancelled' WHERE id = ?");
                    $stmt1->bind_param("i", $order_id);
                    $stmt1->execute();

                    // 2. Ghi lịch sử hủy đơn
                    $stmt2 = $db->prepare("INSERT INTO order_history (order_id, status, note) VALUES (?, 'cancelled', ?)");
                    $stmt2->bind_param("is", $order_id, $reason);
                    $stmt2->execute();

                    
                    // Chốt giao dịch
                    $db->commit();
                    echo json_encode(['success' => true, 'message' => 'Hủy đơn thành công']);

                } catch (Exception $e) {
                    $db->rollback();
                    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống khi hủy đơn!']);
                }

            } else {
                echo json_encode(['success' => false, 'message' => 'Đơn hàng ở trạng thái này không thể hủy!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền hủy đơn này!']);
        }
        exit;
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