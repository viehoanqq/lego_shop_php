<?php
class CartController extends Controller {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { 
            session_start(); 
        }
    }
    // API Thêm vào giỏ hàng (Gọi bằng Javascript)
    public function add() {
        // Đảm bảo trả về định dạng JSON
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Kiểm tra đăng nhập
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập để mua hàng!']);
                exit;
            }

            $product_id = $_POST['product_id'] ?? 0;
            $quantity = $_POST['quantity'] ?? 1;

            if ($product_id > 0) {
                $cartModel = $this->model('CartModel');
                $result = $cartModel->addProductToCart($_SESSION['user_id'], $product_id, $quantity);
                
                if ($result) {
                    echo json_encode(['status' => 'success', 'msg' => 'Đã thêm sản phẩm vào giỏ hàng!']);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống, không thể thêm!']);
                }
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Sản phẩm không hợp lệ!']);
            }
        }
    }

    // Trang chủ Giỏ hàng 
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }

        $cartModel = $this->model('CartModel');
        $cart_items = $cartModel->getCartItems($_SESSION['user_id']);
        
        // Tính tổng tiền tạm tính
        $total_price = 0;
        foreach ($cart_items as $item) {
            $total_price += ($item['selling_price'] * $item['quantity']);
        }

        $this->view('/user/cart/index', [
            'title' => 'Giỏ hàng của tôi',
            'cart_items' => $cart_items,
            'total_price' => $total_price
        ]);
    }

    // API Cập nhật số lượng (+ / -)
    public function updateQty() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $item_id = $_POST['item_id'] ?? 0;
            $action = $_POST['action'] ?? ''; // 'increase' hoặc 'decrease'
            
            $cartModel = $this->model('CartModel');
            if ($cartModel->updateQuantity($item_id, $_SESSION['user_id'], $action)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Không thể cập nhật!']);
            }
        }
    }

    // API Xóa sản phẩm
    public function remove() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
            $item_id = $_POST['item_id'] ?? 0;
            
            $cartModel = $this->model('CartModel');
            if ($cartModel->removeCartItem($item_id, $_SESSION['user_id'])) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Không thể xóa!']);
            }
        }
    }

    // Thêm vào giỏ hàng ngầm qua AJAX
    public function addAjax() {
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'] ?? 0;
        $quantity = $_POST['quantity'] ?? 1;

        if ($product_id) {
            $cartModel = $this->model('CartModel');
            // Hàm addToCart của bạn: Kiểm tra nếu có rồi thì cộng dồn số lượng, chưa có thì insert mới
            $result = $cartModel->addToCart($user_id, $product_id, $quantity);
            
            if ($result) {
                // Lấy tổng số loại sp trong giỏ để cập nhật Badge (Tuỳ chọn)
                $count = $cartModel->countCartItems($user_id);
                echo json_encode(['success' => true, 'cart_count' => $count]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi DB khi thêm!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi ID sản phẩm!']);
        }
        exit;
    }
    
}