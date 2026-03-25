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
    
}