<?php require __DIR__ . '/../components/breadcrumb.php'; ?>

<?php $is_logged_in = isset($_SESSION['user_id']) ? 'true' : 'false'; ?>

<div class="product-detail-wrapper" style="background-color: #fff; padding-bottom: 50px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px 15px;">
        
        <div class="product-detail-grid">
            <div class="product-gallery">
                <div class="main-image-box">
                    <?php $main_image = !empty($images) ? $images[0]['image_url'] : 'default-lego.jpg'; ?>
                    <img id="mainImage" src="/lego_shop_php/public/assets/images/<?= $main_image ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                
                <div class="thumbnail-list">
                    <?php if(!empty($images)): ?>
                        <?php foreach($images as $img): ?>
                            <div class="thumb-item" onclick="changeMainImage('<?= $img['image_url'] ?>')">
                                <img src="/lego_shop_php/public/assets/images/<?= $img['image_url'] ?>" alt="Thumbnail">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-info-box">
                <div class="product-category"><?= mb_strtoupper($product['category_name'] ?? 'LEGO') ?></div>
                <h1 class="product-title"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="product-meta">
                    <span class="sku">Mã: <?= htmlspecialchars($product['sku']) ?></span>
                    <span class="divider">|</span>
                    
                    <span class="rating">
                        <?php 
                            $avg = $rating_info['avg_rating'] ?? 0;
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= floor($avg)) {
                                    echo '<i class="fa-solid fa-star"></i>';
                                } elseif ($i - 0.5 <= $avg) {
                                    echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                } else {
                                    echo '<i class="fa-regular fa-star" style="color: #ccc;"></i>';
                                }
                            }
                        ?>
                        <span style="color: #555; margin-left: 5px; font-weight: normal;">
                            (<?= $avg ?>/5 - <?= $rating_info['total_reviews'] ?? 0 ?> đánh giá)
                        </span>
                    </span>
                </div>

                <div class="product-price-box">
                    <span class="current-price"><?= number_format($product['selling_price'], 0, ',', '.') ?>đ</span>
                </div>

                <div class="quick-specs">
                    <h3 class="specs-title"><i class="fa-solid fa-circle-info"></i> Thông số sản phẩm</h3>
                    <ul>
                        <li><span>Số mảnh ghép:</span> <strong>
                            <?= (!empty($product['pieces']) && $product['pieces'] > 0) ? number_format($product['pieces'], 0, ',', '.') . ' mảnh' : 'Đang cập nhật' ?>
                        </strong></li>
                        <li><span>Series:</span> <strong><?= htmlspecialchars($product['category_name'] ?? 'LEGO') ?></strong></li>
                        <li><span>Tình trạng:</span> 
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <strong style="color: #28a745;">Còn hàng (<?= $product['stock_quantity'] ?> sản phẩm)</strong>
                            <?php else: ?>
                                <strong style="color: #dc3545;">Hết hàng</strong>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>

                <div class="action-buttons">
                    <?php if ($product['stock_quantity'] > 0): ?>
                        <form id="addToCartForm" style="display: flex; gap: 15px; width: 100%; flex-wrap: wrap;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                                <input type="number" id="qtyInput" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" readonly>
                                <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                            </div>
                            
                            <button type="button" class="btn-add-to-cart" onclick="handleCartAction('add')">
                                <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                            </button>
                            
                            <button type="button" class="btn-buy-now" onclick="handleCartAction('buy_now')">
                                <i class="fa-solid fa-bolt"></i> MUA NGAY
                            </button>
                        </form>
                    <?php else: ?>
                        <div style="width: 100%;">
                            <button type="button" disabled class="btn-out-of-stock">
                                <i class="fa-solid fa-box-open"></i> SẢN PHẨM TẠM HẾT HÀNG
                            </button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="store-policies">
                    <div class="policy-item"><i class="fa-solid fa-shield-halved"></i> Bảo hành chính hãng 12 tháng</div>
                    <div class="policy-item"><i class="fa-solid fa-truck-fast"></i> Miễn phí vận chuyển toàn quốc</div>
                    <div class="policy-item"><i class="fa-solid fa-rotate-left"></i> Đổi trả trong 7 ngày</div>
                    <div class="policy-item"><i class="fa-solid fa-headset"></i> Hỗ trợ 24/7: 1900 1208</div>
                </div>

            </div>
        </div>

        <div class="product-tabs-section">
            <ul class="tabs-nav">
                <li class="tab-link active" onclick="openTab(event, 'desc')">Mô tả sản phẩm</li>
                <li class="tab-link" onclick="openTab(event, 'specs')">Thông số kỹ thuật</li>
                <li class="tab-link" onclick="openTab(event, 'reviews')">Đánh giá (<?= $rating_info['total_reviews'] ?? 0 ?>)</li>
            </ul>
            
            <div id="desc" class="tab-content active">
                <h3 style="color: #a4161a; margin-bottom: 15px;">Giới thiệu sản phẩm</h3>
                <div class="description-text">
                    <?= !empty($product['description']) ? nl2br($product['description']) : 'Đang cập nhật nội dung mô tả cho sản phẩm này...' ?>
                </div>
            </div>
            
            <div id="specs" class="tab-content">
                <h3 style="color: #a4161a; margin-bottom: 15px;">Chi tiết thông số kỹ thuật</h3>
                <table class="specs-table">
                    <tr><th>Mã sản phẩm (SKU)</th><td><?= htmlspecialchars($product['sku']) ?></td></tr>
                    <tr><th>Thương hiệu</th><td><?= htmlspecialchars($product['category_name']) ?></td></tr>
                    <tr><th>Nhà sản xuất</th><td><?= htmlspecialchars($product['manufacturer'] ?? 'Tập đoàn LEGO') ?></td></tr>
                    <tr><th>Xuất xứ</th><td><?= htmlspecialchars($product['country_of_origin'] ?? 'Đan Mạch') ?></td></tr>
                    <tr><th>Độ tuổi</th><td><?= htmlspecialchars($product['age_range'] ?? 'Đang cập nhật') ?></td></tr>
                    <tr><th>Số mảnh ghép</th><td><?= isset($product['pieces']) ? number_format($product['pieces'], 0, ',', '.') . ' mảnh' : 'Đang cập nhật' ?></td></tr>
                    <tr><th>Kích thước</th><td><?= htmlspecialchars($product['dimensions'] ?? 'Đang cập nhật') ?></td></tr>
                    <tr><th>Chất liệu</th><td><?= htmlspecialchars($product['material'] ?? 'Nhựa ABS cao cấp') ?></td></tr>
                    <tr><th>Năm sản xuất</th><td><?= htmlspecialchars($product['release_year'] ?? 'Đang cập nhật') ?></td></tr>
                </table>
            </div>
            
            <div id="reviews" class="tab-content">
                <h3 style="color: #a4161a; margin-bottom: 15px;">Đánh giá từ khách hàng</h3>
                <?php if(!empty($reviews)): ?>
                    <div class="review-list">
                        <?php foreach($reviews as $rev): ?>
                            <div class="review-item">
                                <div class="reviewer-avatar"><i class="fa-solid fa-user"></i></div>
                                <div class="review-body">
                                    <div class="reviewer-name"><?= htmlspecialchars($rev['fullname']) ?></div>
                                    <div class="review-stars">
                                        <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo ($i <= $rev['rating']) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star" style="color: #ccc;"></i>';
                                            }
                                        ?>
                                        <span class="review-date"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                    <div class="review-text"><?= nl2br(htmlspecialchars($rev['comment'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-reviews">
                        <i class="fa-regular fa-comment-dots"></i>
                        <p>Chưa có đánh giá nào. Hãy là người đầu tiên!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
/* --- Grid & Layout --- */
.product-detail-grid { display: grid; grid-template-columns: 45% 55%; gap: 40px; margin-bottom: 50px; }
.main-image-box { border: 1px solid #eee; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 15px; background: #fff; }
.main-image-box img { max-width: 100%; height: auto; max-height: 400px; object-fit: contain; }
.thumbnail-list { display: flex; gap: 10px; flex-wrap: wrap; }
.thumb-item { width: 80px; height: 80px; border: 1px solid #ddd; border-radius: 8px; padding: 5px; cursor: pointer; transition: 0.2s; }
.thumb-item:hover { border-color: #a4161a; }
.thumb-item img { width: 100%; height: 100%; object-fit: contain; }

.product-category { color: #666; font-weight: 600; font-size: 14px; margin-bottom: 8px; }
.product-title { color: #a4161a; font-size: 28px; font-weight: 800; line-height: 1.3; margin-bottom: 15px; }
.product-meta { color: #777; font-size: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.rating i { color: #ffc107; }
.product-price-box { margin-bottom: 25px; }
.current-price { color: #a4161a; font-size: 32px; font-weight: 800; }

.quick-specs { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
.specs-title { color: #a4161a; font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; }
.quick-specs ul { list-style: none; padding: 0; }
.quick-specs li { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; border-bottom: 1px dashed #eee; padding-bottom: 8px; }

/* --- Buttons & Inputs --- */
.quantity-selector { display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden; height: 48px; background: #fff; }
.qty-btn { background: #f4f4f4; border: none; width: 45px; height: 100%; cursor: pointer; font-size: 20px; font-weight: bold; }
.qtyInput { width: 50px; border: none; text-align: center; font-weight: 700; font-size: 16px; }

.btn-add-to-cart { flex: 1; background: #ffc107; color: #333; border: none; border-radius: 8px; font-weight: 800; height: 48px; cursor: pointer; transition: 0.3s; min-width: 180px; }
.btn-buy-now { flex: 1; background: #a4161a; color: #fff; border: none; border-radius: 8px; font-weight: 800; height: 48px; cursor: pointer; transition: 0.3s; min-width: 180px; }
.btn-out-of-stock { width: 100%; height: 50px; background: #eee; color: #888; border: none; border-radius: 8px; font-weight: 800; cursor: not-allowed; }

.store-policies { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee; }
.policy-item { font-size: 13px; color: #666; display: flex; align-items: center; gap: 8px; }
.policy-item i { color: #a4161a; }

/* --- Tabs & Tables --- */
.tabs-nav { display: flex; border-bottom: 1px solid #ddd; margin-top: 50px; margin-bottom: 30px; list-style: none; padding: 0; }
.tab-link { padding: 15px 30px; cursor: pointer; font-weight: 700; color: #777; border-bottom: 3px solid transparent; }
.tab-link.active { color: #a4161a; border-bottom-color: #a4161a; }
.tab-content { display: none; line-height: 1.8; animation: fadeIn 0.3s ease; }
.tab-content.active { display: block; }
.specs-table { width: 100%; border-collapse: collapse; }
.specs-table th { background: #f9f9f9; text-align: left; width: 30%; padding: 12px; border: 1px solid #eee; }
.specs-table td { padding: 12px; border: 1px solid #eee; }

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Responsive */
@media(max-width: 900px) { .product-detail-grid { grid-template-columns: 1fr; } }
</style>

<script>
function changeMainImage(imgUrl) {
    document.getElementById('mainImage').src = "/lego_shop_php/public/assets/images/" + imgUrl;
}

function openTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function updateQty(change) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + change;
    let max = parseInt(input.max);
    if (val >= 1 && val <= max) input.value = val;
    else if (val > max) showToast(`Chỉ còn ${max} sản phẩm trong kho!`, "error");
}

function handleCartAction(action) {
    const isLoggedIn = <?= $is_logged_in ?>;
    
    if (!isLoggedIn) {
        showToast("Vui lòng đăng nhập để mua hàng!", "error");
        return;
    }

    const form = document.getElementById('addToCartForm');
    const formData = new FormData(form);
    const btn = event.currentTarget;
    const oldText = btn.innerHTML;

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>...';
    btn.style.pointerEvents = 'none';

    fetch('/lego_shop_php/cart/addAjax', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        btn.innerHTML = oldText;
        btn.style.pointerEvents = 'auto';

        if(data.success) {
            if (action === 'buy_now') {
                window.location.href = '/lego_shop_php/cart';
            } else {
                showToast("Đã thêm vào giỏ hàng!", "success");
                if(typeof updateCartBadge === 'function') updateCartBadge(data.cart_count);
            }
        } else {
            showToast(data.message, "error");
        }
    })
    .catch(() => {
        btn.innerHTML = oldText;
        btn.style.pointerEvents = 'auto';
        showToast("Lỗi hệ thống!", "error");
    });
}
</script>