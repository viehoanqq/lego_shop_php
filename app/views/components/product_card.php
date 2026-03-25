<div class="product-card">
    <?php 
        // 1. Xử lý logic Giảm giá
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
            // 2. XỬ LÝ ẢNH (QUAN TRỌNG): Kiểm tra cả 2 trường hợp main_image và image_url
            $img_name = '';
            if (!empty($product['main_image'])) {
                $img_name = $product['main_image']; // Ưu tiên ảnh từ kết quả tìm kiếm
            } elseif (!empty($product['image_url'])) {
                $img_name = $product['image_url']; // Dùng ảnh từ trang danh mục thường
            }

            // 3. Tạo đường dẫn cuối cùng
            $final_image_src = ($img_name !== '') 
                ? "/lego_shop_php/public/assets/images/" . $img_name 
                : "/lego_shop_php/public/assets/images/default-lego.jpg"; 
        ?>
        <img src="<?= htmlspecialchars($final_image_src) ?>" 
             alt="<?= htmlspecialchars($product['name'] ?? 'Sản phẩm LEGO') ?>">
    </a>

    <div class="product-info">
        <div class="product-sku">SKU: <?= htmlspecialchars($product['sku'] ?? 'N/A') ?></div>
        
        <h3 class="product-name">
            <a href="/lego_shop_php/product/detail/<?= $product['id'] ?>"><?= htmlspecialchars($product['name'] ?? 'Sản phẩm') ?></a>
        </h3>

        <div class="product-price">
            <span class="current-price"><?= number_format($selling_price, 0, ',', '.') ?>đ</span>
            <?php if ($has_discount): ?>
                <span class="old-price"><?= number_format($old_price, 0, ',', '.') ?>đ</span>
            <?php endif; ?>
        </div>

      
            <div class="product-actions">
    <button class="btn-add-cart" onclick="addToCart(<?= $product['id'] ?>)">
        <i class="fa-solid fa-cart-shopping"></i> Thêm vào giỏ
    </button>
    
    <?php 
        // Biến $is_liked này bạn cần xử lý ở Controller/Model trước khi truyền sang View
        // Nếu chưa làm logic check, tạm thời để mặc định là fa-regular
        $wishlist_icon = (isset($product['is_liked']) && $product['is_liked']) ? 'fa-solid' : 'fa-regular';
    ?>
    <button class="btn-wishlist" 
            title="Yêu thích" 
            onclick="toggleWishlist(<?= $product['id'] ?>, this)">
        <i class="<?= $wishlist_icon ?> fa-heart"></i>
    </button>
</div>
        </div>
    </div>
