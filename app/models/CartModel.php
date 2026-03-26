<?php
class CartModel extends Database {
    
    // Hàm Thêm sản phẩm vào giỏ hàng (Có dùng Transaction để bảo vệ dữ liệu)
    public function addProductToCart($user_id, $product_id, $quantity = 1) {
    $db = $this->getConnection();
    
    // Bật chế độ báo lỗi Exception để try...catch bắt được lỗi SQL
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        $user_id = intval($user_id);
        $product_id = intval($product_id);
        $quantity = intval($quantity);

        $db->begin_transaction();

        // 1. Kiểm tra User đã có giỏ hàng chưa
        // Lưu ý: Kiểm tra tên bảng là 'carts' hay 'cart' nhé
        $cartResult = $db->query("SELECT id FROM carts WHERE user_id = $user_id");
        
        if ($cartResult && $cartResult->num_rows > 0) {
            $cart = $cartResult->fetch_assoc();
            $cart_id = $cart['id'];
        } else {
            $db->query("INSERT INTO carts (user_id) VALUES ($user_id)");
            $cart_id = $db->insert_id;
        }

        // 2. Kiểm tra sản phẩm đã có trong giỏ chưa
        // Lưu ý: Kiểm tra tên bảng là 'cart_items' hay 'cart_details'
        $itemResult = $db->query("SELECT id, quantity FROM cart_items WHERE cart_id = $cart_id AND product_id = $product_id");
        
        if ($itemResult && $itemResult->num_rows > 0) {
            $item = $itemResult->fetch_assoc();
            $new_quantity = $item['quantity'] + $quantity;
            $item_id = $item['id'];
            $db->query("UPDATE cart_items SET quantity = $new_quantity WHERE id = $item_id");
        } else {
            $db->query("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ($cart_id, $product_id, $quantity)");
        }

        $db->commit();
        return true;

    } catch (Exception $e) {
        $db->rollback();
        // Ghi lỗi ra file log để debug hoặc tạm thời die($e->getMessage()) để xem lỗi gì
        error_log("Lỗi Add To Cart: " . $e->getMessage());
        return false; 
    }
}
    // [ĐÃ SỬA] - Lấy danh sách sản phẩm trong giỏ hàng (Kèm ảnh chính từ bảng product_images)
    public function getCartItems($user_id) {
        $db = $this->getConnection();
        $user_id = intval($user_id);
        
        // Dùng LEFT JOIN kết nối với bảng product_images và chỉ lấy ảnh có is_main = 1
        $sql = "SELECT 
                    ci.id as cart_item_id, 
                    ci.quantity, 
                    p.id as product_id, 
                    p.name, 
                    p.selling_price, 
                    pi.image_url as main_image 
                FROM cart_items ci
                JOIN carts c ON ci.cart_id = c.id
                JOIN products p ON ci.product_id = p.id
                LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_main = 1
                WHERE c.user_id = $user_id
                ORDER BY ci.id DESC";
                
        $result = $db->query($sql);
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        return $items;
    }

    // [MỚI] - Cập nhật số lượng (Tăng/Giảm)
    public function updateQuantity($cart_item_id, $user_id, $action) {
        $db = $this->getConnection();
        $cart_item_id = intval($cart_item_id);
        
        // Kiểm tra xem item này có đúng là của user này không (Bảo mật)
        $checkSql = "SELECT ci.quantity FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE ci.id = $cart_item_id AND c.user_id = $user_id";
        $result = $db->query($checkSql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_qty = $row['quantity'];
            
            $new_qty = ($action === 'increase') ? $current_qty + 1 : $current_qty - 1;
            
            if ($new_qty > 0) {
                return $db->query("UPDATE cart_items SET quantity = $new_qty WHERE id = $cart_item_id");
            } else {
                return false; // Không cho giảm xuống dưới 1 (Muốn xóa thì dùng nút Xóa)
            }
        }
        return false;
    }

    // [MỚI] - Xóa sản phẩm khỏi giỏ
    public function removeCartItem($cart_item_id, $user_id) {
        $db = $this->getConnection();
        $cart_item_id = intval($cart_item_id);
        
        // Xóa item có điều kiện phải thuộc về giỏ hàng của user đang đăng nhập
        $sql = "DELETE ci FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE ci.id = $cart_item_id AND c.user_id = $user_id";
        return $db->query($sql);
    }
    public function clearCart($user_id) {
        $db = $this->getConnection();
        
        // Tên bảng giỏ hàng của bạn có thể là 'cart' hoặc 'carts'. Hãy đổi lại nếu tên bảng khác nhé.
        $sql = "DELETE FROM carts WHERE user_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $user_id);
        
        return $stmt->execute();
    }
}