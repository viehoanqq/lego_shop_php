<div class="product-card">
    
    <?php 
    // Dùng null coalescing (?? 0) để tránh lỗi nếu key không tồn tại
    $old_price = $product['old_price'] ?? 0; 
    $selling_price = $product['selling_price'] ?? 0;
    
    $has_discount = ($old_price > $selling_price && $old_price > 0);
    if ($has_discount): 
        $discount_percent = round((($old_price - $selling_price) / $old_price) * 100);
?>
    <div class="discount-badge">-<?= $discount_percent ?>%</div>
<?php endif; ?>

    <a href="/lego_shop_php/product/detail/<?= $product['id'] ?? 1 ?>" class="product-image">
        <?php 
            // 1. Kiểm tra xem biến image_url có tồn tại và khác rỗng không
            $image_path = (!empty($product['image_url'])) ? $product['image_url'] : '';
            
            // 2. Nếu có ảnh thật trong DB, ghép thêm đường dẫn localhost. Nếu không, dùng ảnh mặc định.
            // LƯU Ý: Giả sử trong DB bạn chỉ lưu tên file như 'xe-may-bay.jpg'
            // thì bạn phải nối thêm '/lego_shop_php/public/assets/images/products/' vào trước.
            // Nếu trong DB bạn lưu đường dẫn đầy đủ rồi thì giữ nguyên.
            
            $final_image_src = ($image_path !== '') 
                ? "/lego_shop_php/public/assets/images/" . $image_path 
                : "/lego_shop_php/public/assets/images/default-lego.jpg"; 
                // Hãy chuẩn bị sẵn 1 tấm ảnh default-lego.jpg bỏ vào thư mục images nhé.
        ?>
        <img src="<?= htmlspecialchars($final_image_src) ?>" 
             alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm LEGO') ?>">
    </a>

    <div class="product-info">
        <div class="product-sku">SKU: <?= htmlspecialchars($product['sku']) ?></div>
        
        <h3 class="product-name">
            <a href="/lego_shop_php/product/detail/<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></a>
        </h3>

        <div class="product-price">
            <span class="current-price"><?= number_format($product['selling_price'], 0, ',', '.') ?>đ</span>
            <?php if ($has_discount): ?>
                <span class="old-price"><?= number_format($product['old_price'], 0, ',', '.') ?>đ</span>
            <?php endif; ?>
        </div>

        <div class="product-actions">
            <button class="btn-add-cart" onclick="addToCart(<?= $product['id'] ?>)">
                <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ
            </button>
            <button class="btn-wishlist" title="Thêm vào yêu thích">
                <i class="fa-solid fa-heart"></i>
            </button>
        </div>
    </div>
</div>