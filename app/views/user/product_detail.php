<?php require __DIR__ . '/../components/breadcrumb.php'; ?>

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
                        <li><span>Tình trạng:</span> <strong style="color: <?= ($product['stock_quantity'] > 0) ? '#28a745' : '#dc3545' ?>;">
                            <?= ($product['stock_quantity'] > 0) ? 'Còn hàng' : 'Hết hàng' ?>
                        </strong></li>
                    </ul>
                </div>

                <div class="action-buttons">
                    <form action="/lego_shop_php/cart/add" method="POST" style="display: flex; gap: 15px; width: 100%;">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn" onclick="updateQty(-1)">-</button>
                            <input type="number" id="qtyInput" name="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                            <button type="button" class="qty-btn" onclick="updateQty(1)">+</button>
                        </div>
                        
                        <button type="submit" class="btn-add-to-cart">
                            <i class="fa-solid fa-cart-plus"></i> THÊM VÀO GIỎ HÀNG
                        </button>
                        <button type="button" class="btn-buy-now">
                            <i class="fa-solid fa-bolt"></i> MUA NGAY
                        </button>
                    </form>
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
                <li class="tab-link active" onclick="openTab('desc')">Mô tả sản phẩm</li>
                <li class="tab-link" onclick="openTab('specs')">Thông số kỹ thuật</li>
                <li class="tab-link" onclick="openTab('reviews')">Đánh giá (<?= $rating_info['total_reviews'] ?? 0 ?>)</li>
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
    <tr><th>Xuất xứ thương hiệu</th><td><?= htmlspecialchars($product['country_of_origin'] ?? 'Đan Mạch') ?></td></tr>
    
    <tr><th>Độ tuổi phù hợp</th><td><?= htmlspecialchars($product['age_range'] ?? 'Đang cập nhật') ?></td></tr>
    <tr><th>Số mảnh ghép</th><td><?= isset($product['pieces']) ? number_format($product['pieces'], 0, ',', '.') . ' mảnh' : 'Đang cập nhật' ?></td></tr>
    <tr><th>Kích thước</th><td><?= htmlspecialchars($product['dimensions'] ?? 'Đang cập nhật') ?></td></tr>
    <tr><th>Trọng lượng</th><td><?= isset($product['weight']) && $product['weight'] > 0 ? $product['weight'] . ' kg' : 'Đang cập nhật' ?></td></tr>
    <tr><th>Chất liệu</th><td><?= htmlspecialchars($product['material'] ?? 'Đang cập nhật') ?></td></tr>
    <tr><th>Năm sản xuất</th><td><?= htmlspecialchars($product['release_year'] ?? 'Đang cập nhật') ?></td></tr>
</table>
            </div>
            
            <div id="reviews" class="tab-content">
                <h3 style="color: #a4161a; margin-bottom: 15px;">Đánh giá từ khách hàng</h3>
                
                <?php if(!empty($reviews)): ?>
                    <div class="review-list">
                        <?php foreach($reviews as $rev): ?>
                            <div class="review-item">
                                <div class="reviewer-avatar">
                                    <i class="fa-solid fa-user"></i>
                                </div>
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
                                    <div class="review-text">
                                        <?= nl2br(htmlspecialchars($rev['comment'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 0; color: #666; background: #f9f9f9; border-radius: 8px;">
                        <i class="fa-regular fa-comment-dots" style="font-size: 40px; color: #ddd; margin-bottom: 10px;"></i>
                        <p>Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên đánh giá!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<style>
/* ... (Giữ nguyên phần CSS phía trên của bạn) ... */

/* Grid Layout */
.product-detail-grid { display: grid; grid-template-columns: 45% 55%; gap: 40px; margin-bottom: 50px; }
.main-image-box { border: 1px solid #eee; border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 15px; }
.main-image-box img { max-width: 100%; height: auto; max-height: 400px; object-fit: contain; transition: 0.3s; }
.thumbnail-list { display: flex; gap: 10px; flex-wrap: wrap; }
.thumb-item { width: 80px; height: 80px; border: 1px solid #ddd; border-radius: 8px; padding: 5px; cursor: pointer; transition: 0.2s; }
.thumb-item:hover { border-color: #a4161a; }
.thumb-item img { width: 100%; height: 100%; object-fit: contain; }
.product-category { color: #666; font-weight: 600; font-size: 14px; margin-bottom: 8px; }
.product-title { color: #a4161a; font-size: 26px; font-weight: 800; line-height: 1.3; margin-bottom: 15px; }
.product-meta { color: #777; font-size: 14px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
.rating i { color: #ffc107; } /* Màu vàng chuẩn cho sao */
.product-price-box { display: flex; align-items: center; gap: 15px; margin-bottom: 25px; }
.current-price { color: #a4161a; font-size: 28px; font-weight: 700; }
.quick-specs { background: #f8f9fa; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
.specs-title { color: #a4161a; font-size: 16px; margin-bottom: 15px; border-bottom: 1px solid #e0e0e0; padding-bottom: 10px; }
.quick-specs ul { list-style: none; padding: 0; margin: 0; }
.quick-specs li { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #555; border-bottom: 1px dashed #e0e0e0; padding-bottom: 8px; }
.quick-specs li:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
.quick-specs li strong { color: #333; }
.quantity-selector { display: flex; align-items: center; border: 1px solid #ccc; border-radius: 8px; overflow: hidden; height: 45px; }
.qty-btn { background: #f4f4f4; border: none; width: 40px; height: 100%; cursor: pointer; font-size: 18px; font-weight: bold; color: #555; }
.qty-btn:hover { background: #e0e0e0; }
.quantity-selector input { width: 50px; height: 100%; border: none; text-align: center; font-size: 16px; font-weight: bold; outline: none; }
.btn-add-to-cart { flex: 1; background: #ffc107; color: #333; border: none; border-radius: 8px; font-weight: 800; font-size: 15px; cursor: pointer; transition: 0.2s; height: 45px; }
.btn-add-to-cart:hover { background: #ffb300; }
.btn-buy-now { flex: 1; background: #a4161a; color: #fff; border: none; border-radius: 8px; font-weight: 800; font-size: 15px; cursor: pointer; transition: 0.2s; height: 45px; }
.btn-buy-now:hover { background: #801215; }
.store-policies { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 25px; padding-top: 25px; border-top: 1px solid #eee; }
.policy-item { font-size: 13px; color: #555; display: flex; align-items: center; gap: 8px; }
.policy-item i { color: #a4161a; font-size: 16px; width: 20px; text-align: center; }
.tabs-nav { display: flex; border-bottom: 1px solid #ddd; margin-bottom: 25px; list-style: none; padding: 0; }
.tab-link { padding: 15px 30px; cursor: pointer; font-weight: 600; color: #666; font-size: 15px; border-bottom: 3px solid transparent; transition: 0.3s; }
.tab-link:hover { color: #a4161a; }
.tab-link.active { color: #a4161a; border-bottom-color: #a4161a; }
.tab-content { display: none; font-size: 15px; color: #444; line-height: 1.8; }
.tab-content.active { display: block; animation: fadeIn 0.4s ease; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

/* ==========================================
   CSS MỚI THÊM CHO TAB THÔNG SỐ & ĐÁNH GIÁ
   ========================================== */
/* Bảng thông số kỹ thuật */
.specs-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.specs-table th, .specs-table td { padding: 12px 15px; border: 1px solid #eee; }
.specs-table th { background-color: #f9f9f9; width: 30%; text-align: left; color: #555; font-weight: 600; }
.specs-table td { color: #333; }

/* Danh sách Đánh giá */
.review-list { display: flex; flex-direction: column; gap: 20px; }
.review-item { display: flex; gap: 15px; padding-bottom: 20px; border-bottom: 1px solid #eee; }
.reviewer-avatar { width: 50px; height: 50px; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #999; font-size: 20px; flex-shrink: 0; }
.review-body { flex: 1; }
.reviewer-name { font-weight: 700; color: #333; margin-bottom: 5px; }
.review-stars { margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
.review-stars i { color: #ffc107; font-size: 13px; }
.review-date { font-size: 12px; color: #999; }
.review-text { color: #555; line-height: 1.6; }

@media(max-width: 900px) { .product-detail-grid { grid-template-columns: 1fr; } .store-policies { grid-template-columns: 1fr; } }
@media(max-width: 500px) { .action-buttons form { flex-direction: column; } .tabs-nav { flex-wrap: wrap; } .tab-link { width: 100%; text-align: center; border-bottom: 1px solid #ddd !important; } .tab-link.active { background: #fff5f5; border-left: 3px solid #a4161a; border-bottom: none !important; } .specs-table th { width: 40%; } }
</style>

<script>
function changeMainImage(imgName) {
    document.getElementById('mainImage').src = "/lego_shop_php/public/assets/images/" + imgName;
}

function openTab(tabId) {
    document.querySelectorAll('.tab-link').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    event.currentTarget.classList.add('active');
    document.getElementById(tabId).classList.add('active');
}

function updateQty(change) {
    const input = document.getElementById('qtyInput');
    let currentVal = parseInt(input.value);
    let max = parseInt(input.getAttribute('max'));
    let newVal = currentVal + change;
    if (newVal >= 1 && newVal <= max) { input.value = newVal; }
}
</script>