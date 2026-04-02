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
    // Lấy danh sách sản phẩm trong giỏ hàng
    public function getCartItems($user_id) {
        $db = $this->getConnection();
        $user_id = intval($user_id);
        
        // CHÚ Ý DÒNG SELECT: Đã giữ p.stock_quantity và bổ sung thêm available_stock
        $sql = "SELECT 
                    ci.id as cart_item_id, 
                    ci.quantity, 
                    p.id as product_id, 
                    p.name, 
                    p.selling_price, 
                    p.stock_quantity, 
                    (p.stock_quantity - COALESCE((
                        SELECT SUM(od.quantity) 
                        FROM order_details od 
                        JOIN orders o ON od.order_id = o.id 
                        WHERE od.product_id = p.id AND o.status IN ('pending', 'confirmed', 'shipping')
                    ), 0)) as available_stock,
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
        $user_id = intval($user_id);
        
        // 1. JOIN thêm bảng products để lấy stock_quantity
        $checkSql = "SELECT ci.quantity, p.stock_quantity 
                     FROM cart_items ci 
                     JOIN carts c ON ci.cart_id = c.id 
                     JOIN products p ON ci.product_id = p.id
                     WHERE ci.id = $cart_item_id AND c.user_id = $user_id";
        
        $result = $db->query($checkSql);
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $current_qty = $row['quantity'];
            $max_stock = $row['stock_quantity']; // Lấy số lượng tối đa trong kho
            
            $new_qty = ($action === 'increase') ? $current_qty + 1 : $current_qty - 1;
            
            // 2. Chặn ngay nếu tăng vượt quá kho
            if ($action === 'increase' && $new_qty > $max_stock) {
                return 'out_of_stock'; 
            }
            
            // 3. Nếu số lượng hợp lệ (> 0) thì cho phép cập nhật
            if ($new_qty > 0) {
                $db->query("UPDATE cart_items SET quantity = $new_qty WHERE id = $cart_item_id");
                return true;
            } else {
                return false; // Không cho giảm xuống dưới 1
            }
        }
        return false;
    }
    public function countCartItems($user_id) {
    $db = $this->getConnection();
    $user_id = intval($user_id);
    // Tính tổng số lượng (quantity) hoặc số món (count id) tùy bạn. Ở đây đếm số món:
    $sql = "SELECT SUM(quantity) as total FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE c.user_id = $user_id";
    $result = $db->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }
    return 0;
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