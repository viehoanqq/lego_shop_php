<link rel="stylesheet" href="/lego_shop_php/public/assets/css/cart.css?v=<?= time() ?>">

<div class="cart-page-wrapper">
    <div class="cart-container">
        
        <?php 
            $current_step = 1; 
            require __DIR__ . '/../../components/checkout_progress.php'; 
        ?>

        <div class="cart-header-title">
            <h2>Giỏ hàng của bạn</h2>
            <p>Kiểm tra sản phẩm trước khi thanh toán.</p>
        </div>

        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <img src="/lego_shop_php/public/assets/images/empty-cart.png" 
                     onerror="this.src='https://cdn-icons-png.flaticon.com/512/11329/11329060.png'" alt="Empty Cart">
                <p>Giỏ hàng của bạn đang trống</p>
                <a href="/lego_shop_php/product" class="btn-continue-shopping">Tiếp tục mua sắm</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                
                <div class="cart-items-col">
                    <div class="cart-header-row">
                        <div style="width: 45%; text-align: left;">Sản phẩm</div>
                        <div style="width: 20%; text-align: center;">Giá</div>
                        <div style="width: 20%; text-align: center;">Số lượng</div>
                        <div style="width: 15%; text-align: center;">Thành tiền</div>
                        <div style="width: 5%; text-align: center;"></div>
                    </div>

                    <div class="cart-items-list">
                        <?php foreach ($cart_items as $item): 
                            $img_src = !empty($item['main_image']) ? $item['main_image'] : 'default-lego.jpg';
                            $item_total = $item['selling_price'] * $item['quantity'];
                            // CHÚ Ý: Bắt buộc model CartModel phải JOIN lấy cột available_quantity nhé
                            $max_stock = $item['available_stock'] ?? 0; 
                        ?>
                            <div class="cart-item" id="item-<?= $item['cart_item_id'] ?>">
                                <div class="item-col item-info" style="width: 45%;">
                                    <a href="/lego_shop_php/product/detail/<?= $item['product_id'] ?>">
                                        <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($img_src) ?>" alt="Product">
                                    </a>
                                    <div class="item-details">
                                        <a href="/lego_shop_php/product/detail/<?= $item['product_id'] ?>" class="item-name"><?= htmlspecialchars($item['name']) ?></a>
                                    </div>
                                </div>
                                
                                <div class="item-col item-price" style="width: 20%; justify-content: center;" data-price="<?= $item['selling_price'] ?>">
                                    <?= number_format($item['selling_price'], 0, ',', '.') ?>đ
                                </div>

                                <div class="item-col item-qty" style="width: 20%; justify-content: center;">
                                    <div class="qty-wrapper">
                                        <button class="qty-btn" onclick="checkAndUpdateCartQty(<?= $item['cart_item_id'] ?>, 'decrease')">-</button>
                                        
                                       <input type="number" class="qty-input input-qty" id="qty-<?= $item['cart_item_id'] ?>" value="<?= $item['quantity'] ?>" min="1" max="<?= $max_stock ?>" readonly>
                                        
                                        <button class="qty-btn" onclick="checkAndUpdateCartQty(<?= $item['cart_item_id'] ?>, 'increase')">+</button>
                                    </div>
                                </div>

                                <div class="item-col item-subtotal text-red font-weight-bold" id="subtotal-<?= $item['cart_item_id'] ?>" style="width: 15%; justify-content: center;">
                                    <?= number_format($item_total, 0, ',', '.') ?>đ
                                </div>

                                <div class="item-col item-action" style="width: 5%; justify-content: center;">
                                    <button class="btn-remove-item" onclick="removeCartItem(<?= $item['cart_item_id'] ?>)">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="cart-summary-col">
                    <div class="summary-box">
                        <h3 class="summary-title">Tổng hóa đơn</h3>
                        <div class="summary-row">
                            <span>Tạm tính:</span>
                            <span id="summary-subtotal"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                        </div>
                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="summary-row total-row">
                            <span class="text-red font-weight-bold">Tổng cộng:</span>
                            <span id="summary-total" class="text-red font-weight-bold" style="font-size: 22px;"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                        </div>
                        <button class="btn-checkout" onclick="window.location.href='/lego_shop_php/checkout'">Thanh toán ngay</button>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* CSS CHO BỘ CHỌN SỐ LƯỢNG (+ / -) CHUẨN ĐẸP */
.qty-wrapper {
    display: inline-flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
    background: #fff;
    height: 38px;
}
.qty-wrapper .qty-btn {
    background: #f8f9fa;
    border: none;
    width: 35px;
    height: 100%;
    font-size: 20px;
    font-weight: 600;
    cursor: pointer;
    color: #333;
    transition: 0.2s;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0;
}
.qty-wrapper .qty-btn:hover { background: #e9ecef; color: #a4161a; }
.qty-wrapper .qty-input {
    width: 45px;
    height: 100%;
    border: none;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    text-align: center;
    font-size: 15px;
    font-weight: 700;
    color: #333;
    outline: none;
    pointer-events: none; /* Khóa không cho nhập tay */
}
.qty-wrapper .qty-input::-webkit-outer-spin-button,
.qty-wrapper .qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

<script>
// Hàm trung gian: Kiểm tra tồn kho trước, nếu hợp lệ mới nhả lệnh cho cart.js
function checkAndUpdateCartQty(cartItemId, action) {
    const input = document.getElementById('qty-' + cartItemId);
    let currentVal = parseInt(input.value);
    let max = parseInt(input.getAttribute('max')) || 0;

    // Logic kiểm tra bấm Tăng
    if (action === 'increase') {
        if (currentVal >= max) {
            // Quá số lượng trong kho -> Chặn & Báo lỗi
            if(typeof showToast === 'function') {
                showToast(`Sản phẩm này chỉ còn ${max} sản phẩm!`, "warning");
            } else {
                alert(`Sản phẩm này chỉ còn ${max} sản phẩm!`);
            }
            return; // Ngưng tại đây, không cho gọi hàm tiếp theo
        }
    } 
    // Logic kiểm tra bấm Giảm (Không cho bé hơn 1)
    else if (action === 'decrease') {
        if (currentVal <= 1) return; // Nếu là 1 thì ko cho giảm nữa
    }

    // NẾU VƯỢT QUA ĐƯỢC BÀI KIỂM TRA BÊN TRÊN -> GỌI HÀM CŨ CỦA CART.JS
    if (typeof updateCartQty === 'function') {
        updateCartQty(cartItemId, action);
    } else {
        console.error("Không tìm thấy hàm updateCartQty trong cart.js");
    }
}

</script>

<script src="/lego_shop_php/public/assets/js/cart.js?v=<?= time() ?>"></script>