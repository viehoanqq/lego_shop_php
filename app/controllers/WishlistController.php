<?php
class WishlistController extends Controller {
    public function index() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        // 1. Kiểm tra đăng nhập (Bảo mật)
        if (!isset($_SESSION['user_account_id'])) {
            header("Location: /lego_shop_php/account/login");
            exit;
        }

        // 2. Lấy danh sách từ Model
        $wishlistModel = $this->model('WishlistModel');
        $products = $wishlistModel->getUserWishlist($_SESSION['user_account_id']);

        // 3. Mẹo: Vì đây là trang Wishlist, tất cả sản phẩm hiện ra 
        // ĐỀU PHẢI có tim đỏ. Ta gán cứng is_liked = 1 cho mảng này.
        if (!empty($products)) {
            foreach ($products as &$p) {
                $p['is_liked'] = 1;
            }
        }

        // 4. Đổ ra View (Tận dụng lại view user/product hoặc tạo view riêng)
        // Ở đây mình tạo view riêng là 'user/wishlist' cho chuyên nghiệp nhé
        $this->view('user/wishlist', [
            'title' => 'Sản phẩm yêu thích',
            'products' => $products
        ]);
    }
    public function toggle() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_account_id'])) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập để yêu thích!']);
            exit;
        }

        $product_id = $_POST['product_id'] ?? null;
        if ($product_id) {
            $wishlistModel = $this->model('WishlistModel');
            $result = $wishlistModel->toggleWishlist($_SESSION['user_account_id'], $product_id);
            echo json_encode($result);
        }
        exit;
    }
}