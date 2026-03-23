<div class="home-container" style="padding: 20px 10%; min-height: 60vh;">
    
    <div class="banner" style="text-align: center; margin-bottom: 40px;">
        <img src="https://placehold.co/1200x350/a4161a/white?text=LEGO+WORLD+STORE+-+SIEU+SALE+THANG+3" alt="Banner Khuyến Mãi" style="width: 100%; max-width: 1200px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    </div>

    <div class="product-section">
        <h2 style="color: #a4161a; border-bottom: 2px solid #a4161a; padding-bottom: 10px; margin-bottom: 25px; display: inline-block;">
            <i class="fa-solid fa-fire"></i> Sản phẩm nổi bật
        </h2>
        
        <div class="product-grid" style="display: flex; gap: 20px; flex-wrap: wrap;">
    <?php if(!empty($products)): ?>
        <?php foreach($products as $item): ?>
            <div class="product-card" style="width: 23%; border: 1px solid #ddd; padding: 10px; border-radius: 8px;">
                <img src="/lego_shop_php/public/assets/images/<?= $item['image'] ?>" style="width: 100%;">
                <h3 style="font-size: 16px;"><?= $item['name'] ?></h3>
                <p style="color: red; font-weight: bold;"><?= number_format($item['price'], 0, ',', '.') ?>đ</p>
                <button style="width: 100%; background: #a4161a; color: #fff; border: none; padding: 5px;">Mua ngay</button>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Chưa có sản phẩm nào trong kho!</p>
    <?php endif; ?>
</div>
    </div>
</div>