<div class="home-container" style="padding: 30px 0; background-color: #f8f9fa; font-family: 'Inter', sans-serif; min-height: 60vh;">
    
    <div class="banner-section" style="text-align: center; margin-bottom: 40px; padding: 0 15px;">
        <img src="/lego_shop_php/public/assets/images/banner.webp" 
             alt="LEGO World Khuyến Mãi" 
             style="width: 100%; max-width: 1200px; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
    </div>

    <div class="product-section" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="color: #a4161a; font-size: 24px; font-weight: 800; text-transform: uppercase; margin: 0;">
                SẢN PHẨM NỔI BẬT
            </h2>
            <a href="/lego_shop_php/product" style="color: #a4161a; text-decoration: none; font-weight: 600; font-size: 15px; display: flex; align-items: center; gap: 6px; transition: 0.2s;">
                Xem tất cả <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px; padding-bottom: 40px;">
            
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $product): ?>
                    
                    <?php require __DIR__ . '/../components/product_card.php'; ?>

                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 0; background: #fff; border-radius: 12px; border: 1px dashed #ccc;">
                    <i class="fa-solid fa-box-open" style="font-size: 50px; color: #ddd; margin-bottom: 15px;"></i>
                    <p style="color: #666; font-size: 16px; margin: 0;">Hiện chưa có sản phẩm nào được hiển thị!</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
    .product-section a:hover {
        color: #d00000 !important;
        transform: translateX(3px); /* Đẩy mũi tên trượt nhẹ sang phải */
    }
</style>