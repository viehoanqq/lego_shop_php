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
                                if ($i <= floor($avg)) echo '<i class="fa-solid fa-star"></i>';
                                elseif ($i - 0.5 <= $avg) echo '<i class="fa-solid fa-star-half-stroke"></i>';
                                else echo '<i class="fa-regular fa-star" style="color: #ccc;"></i>';
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
                        <li><span>Số mảnh ghép:</span> <strong><?= (!empty($product['pieces']) && $product['pieces'] > 0) ? number_format($product['pieces'], 0, ',', '.') . ' mảnh' : 'Đang cập nhật' ?></strong></li>
                        <li><span>Series:</span> <strong><?= htmlspecialchars($product['category_name'] ?? 'LEGO') ?></strong></li>
                        <li><span>Tình trạng:</span> 
                            <?php if ($product['available_stock'] > 0): ?>
                                <strong style="color: #28a745;">Còn hàng (<?= $product['available_stock'] ?> sản phẩm)</strong>
                            <?php else: ?>
                                <strong style="color: #dc3545;">Hết hàng</strong>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>

                <div class="action-buttons">
                    <?php if ($product['available_stock'] > 0): ?>
                        <form id="addToCartForm" style="display: flex; flex-direction: column; gap: 20px; width: 100%;">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <span style="font-weight: 600; color: #555; font-size: 15px;">Số lượng:</span>
                                <div class="qty-wrapper">
                                    <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                                    <input type="number" class="qty-input" id="qtyInput" name="quantity" value="1" min="1" max="<?= $product['available_stock'] ?>" readonly>
                                    <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                                </div>
                            </div>
                            
                            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                                <button type="button" class="btn-add-to-cart" onclick="handleCartAction('add')">
                                    <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                                </button>
                                
                                <button type="button" class="btn-buy-now" onclick="handleCartAction('buy_now')">
                                    <i class="fa-solid fa-bolt"></i> MUA NGAY
                                </button>
                            </div>
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
                <h3 style="color: #a4161a; margin-bottom: 25px;">Đánh giá từ khách hàng</h3>
                
                <?php if(!empty($reviews)): ?>
                    <div class="review-list">
                        <?php foreach($reviews as $rev): ?>
                            <div class="review-item">
                                <div class="review-avatar">
                                    <?= strtoupper(substr($rev['fullname'], 0, 1)) ?>
                                </div>
                                
                                <div class="review-main">
                                    <div class="review-header">
                                        <span class="review-name"><?= htmlspecialchars($rev['fullname']) ?></span>
                                        <span class="review-date"><?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?></span>
                                    </div>
                                    
                                    <div class="review-stars">
                                        <?php 
                                            for ($i = 1; $i <= 5; $i++) {
                                                echo ($i <= $rev['rating']) ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                            }
                                        ?>
                                    </div>
                                    
                                    <div class="review-comment">
                                        <?= nl2br(htmlspecialchars($rev['comment'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-reviews" style="text-align: center; padding: 40px 0; background: #f9f9f9; border-radius: 8px; border: 1px dashed #ddd;">
                        <i class="fa-regular fa-comment-dots" style="font-size: 40px; color: #ccc; margin-bottom: 10px;"></i>
                        <p style="color: #666; margin: 0;">Chưa có đánh giá nào. Hãy mua hàng và là người đầu tiên đánh giá sản phẩm này!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
/* ==========================================
   CSS CHO BỘ CHỌN SỐ LƯỢNG (+ / -) MỚI
   ========================================== */
.qty-wrapper {
    display: inline-flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    background: #fff;
    height: 42px;
}
.qty-wrapper .qty-btn {
    background: #f8f9fa;
    border: none;
    width: 42px;
    height: 100%;
    font-size: 20px;
    font-weight: 600;
    cursor: pointer;
    color: #333;
    transition: 0.2s;
    display: flex;
    justify-content: center;
    align-items: center;
}
.qty-wrapper .qty-btn:hover { background: #e9ecef; color: #a4161a; }
.qty-wrapper .qty-input {
    width: 55px;
    height: 100%;
    border: none;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    text-align: center;
    font-size: 16px;
    font-weight: 700;
    color: #333;
    outline: none;
    pointer-events: none; /* Khóa không cho nhập tay, chỉ dùng nút */
}
/* Xóa mũi tên số mặc định của trình duyệt */
.qty-wrapper .qty-input::-webkit-outer-spin-button,
.qty-wrapper .qty-input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* ==========================================
   CSS CHO PHẦN ĐÁNH GIÁ (REVIEWS) XỊN
   ========================================== */
.review-list { display: flex; flex-direction: column; gap: 20px; }
.review-item { 
    display: flex; 
    gap: 20px; 
    padding-bottom: 25px; 
    border-bottom: 1px solid #eee; 
}
.review-item:last-child { border-bottom: none; padding-bottom: 0; }

.review-avatar { 
    width: 50px; height: 50px; 
    background: #ffe3e3; color: #a4161a; 
    font-size: 20px; font-weight: 800; 
    border-radius: 50%; 
    display: flex; align-items: center; justify-content: center; 
    flex-shrink: 0; 
}

.review-main { flex: 1; }
.review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.review-name { font-weight: 700; color: #333; font-size: 15px; }
.review-date { color: #999; font-size: 13px; }
.review-stars { color: #ffc107; font-size: 13px; margin-bottom: 12px; letter-spacing: 2px; }

.review-comment { 
    background: #f8f9fa; 
    padding: 15px; 
    border-radius: 8px; 
    color: #444; 
    font-size: 14.5px; 
    line-height: 1.6; 
    border: 1px solid #f1f1f1; 
    margin: 0; 
}

/* ==========================================
   CÁC CSS BỐ CỤC CHUNG VÀ KHUNG ẢNH
   ========================================== */
.product-detail-grid { display: grid; grid-template-columns: 45% 55%; gap: 40px; margin-bottom: 50px; }

/* FIX CỨNG KHUNG ẢNH CHÍNH (MAGIC HERE) */
.main-image-box { 
    border: 1px solid #eee; 
    border-radius: 12px; 
    padding: 20px; 
    background: #fff; 
    margin-bottom: 15px;
    width: 100%;
    aspect-ratio: 1 / 1; /* Cố định thành khung hình vuông hoàn hảo */
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.main-image-box img { 
    width: 100%; 
    height: 100%; 
    object-fit: contain; /* Ảnh luôn nằm lọt thỏm bên trong khung vuông, không làm giãn layout */
    object-position: center;
}

.thumbnail-list { display: flex; gap: 10px; flex-wrap: wrap; }
.thumb-item { width: 80px; height: 80px; border: 1px solid #ddd; border-radius: 8px; padding: 5px; cursor: pointer; transition: 0.2s; background: #fff; }
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

/* Nút Thêm vào giỏ / Mua ngay */
.btn-add-to-cart { flex: 1; background: #ffc107; color: #333; border: none; border-radius: 8px; font-weight: 800; height: 48px; cursor: pointer; transition: 0.3s; min-width: 180px; font-size: 15px; }
.btn-add-to-cart:hover { background: #ffb300; }
.btn-buy-now { flex: 1; background: #a4161a; color: #fff; border: none; border-radius: 8px; font-weight: 800; height: 48px; cursor: pointer; transition: 0.3s; min-width: 180px; font-size: 15px; }
.btn-buy-now:hover { background: #801215; }
.btn-out-of-stock { width: 100%; height: 50px; background: #eee; color: #888; border: none; border-radius: 8px; font-weight: 800; cursor: not-allowed; font-size: 16px;}

.store-policies { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 30px; padding-top: 25px; border-top: 1px solid #eee; }
.policy-item { font-size: 13px; color: #666; display: flex; align-items: center; gap: 8px; }
.policy-item i { color: #a4161a; font-size: 15px;}

/* Tabs & Tables */
.tabs-nav { display: flex; border-bottom: 1px solid #ddd; margin-top: 50px; margin-bottom: 30px; list-style: none; padding: 0; }
.tab-link { padding: 15px 30px; cursor: pointer; font-weight: 700; color: #777; border-bottom: 3px solid transparent; font-size: 16px; }
.tab-link.active { color: #a4161a; border-bottom-color: #a4161a; }
.tab-content { display: none; line-height: 1.8; animation: fadeIn 0.3s ease; }
.tab-content.active { display: block; }
.specs-table { width: 100%; border-collapse: collapse; }
.specs-table th { background: #f9f9f9; text-align: left; width: 30%; padding: 15px; border: 1px solid #eee; color: #555;}
.specs-table td { padding: 15px; border: 1px solid #eee; font-weight: 500; color: #333;}

@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Responsive */
@media(max-width: 900px) { .product-detail-grid { grid-template-columns: 1fr; } }
@media(max-width: 500px) { .tabs-nav { flex-wrap: wrap; } .tab-link { width: 100%; text-align: center; border-bottom: 1px solid #ddd; } .tab-link.active { border-bottom-color: transparent; border-left: 3px solid #a4161a; background: #fff5f5;} }
</style>

<script>
// Chuyển đổi ảnh chính khi click thumbnail
function changeMainImage(imgUrl) {
    document.getElementById('mainImage').src = "/lego_shop_php/public/assets/images/" + imgUrl;
}

// Chuyển đổi Tabs Mô tả / Thông số / Đánh giá
function openTab(evt, tabId) {
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    evt.currentTarget.classList.add('active');
}

// Hàm cập nhật số lượng
function updateQty(change) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + change;
    let max = parseInt(input.max);
    
    if (val >= 1 && val <= max) {
        input.value = val;
    } else if (val > max) {
        showToast(`Sản phẩm này chỉ còn ${max} cái trong kho!`, "error");
    }
}

// Hàm Xử lý thêm vào giỏ hàng hoặc mua ngay bằng AJAX
function handleCartAction(action) {
    const isLoggedIn = <?= $is_logged_in ?>;
    
    if (!isLoggedIn) {
        showToast("Vui lòng đăng nhập để mua hàng!", "error");
        setTimeout(() => { window.location.href = '/lego_shop_php/account/login'; }, 1500);
        return;
    }

    const form = document.getElementById('addToCartForm');
    const formData = new FormData(form);
    
    const btn = event.currentTarget;
    const oldText = btn.innerHTML;

    // Vô hiệu hóa nút trong lúc chờ mạng
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';
    btn.style.pointerEvents = 'none';

    fetch('/lego_shop_php/cart/addAjax', { 
        method: 'POST', 
        body: formData 
    })
    .then(res => res.json())
    .then(data => {
        // Phục hồi nút
        btn.innerHTML = oldText;
        btn.style.pointerEvents = 'auto';

        if(data.success) {
            if (action === 'buy_now') {
                window.location.href = '/lego_shop_php/cart';
            } else {
                showToast("Đã thêm vào giỏ hàng!", "success");
                // Tự động cập nhật số trên icon giỏ hàng (Nếu bạn có hàm updateCartBadge)
                if(typeof updateCartBadge === 'function') updateCartBadge(data.cart_count);
            }
        } else {
            showToast(data.message || "Lỗi hệ thống khi thêm vào giỏ!", "error");
        }
    })
    .catch((err) => {
        console.error("Fetch Error:", err);
        btn.innerHTML = oldText;
        btn.style.pointerEvents = 'auto';
        showToast("Lỗi kết nối máy chủ!", "error");
    });
}
</script>