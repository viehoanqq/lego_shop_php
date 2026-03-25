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
                                    <div class="qty-control">
                                        <button class="btn-qty" onclick="updateCartQty(<?= $item['cart_item_id'] ?>, 'decrease')"><i class="fa-solid fa-minus"></i></button>
                                        <input type="text" class="input-qty" id="qty-<?= $item['cart_item_id'] ?>" value="<?= $item['quantity'] ?>" readonly>
                                        <button class="btn-qty" onclick="updateCartQty(<?= $item['cart_item_id'] ?>, 'increase')"><i class="fa-solid fa-plus"></i></button>
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

<script src="/lego_shop_php/public/assets/js/cart.js?v=<?= time() ?>"></script>