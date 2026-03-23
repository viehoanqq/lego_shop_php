<div class="home-container" style="padding: 20px 10%; min-height: 60vh; background-color: #f4f4f4; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <div class="banner" style="text-align: center; margin-bottom: 40px;">
        <img src="/lego_shop_php/public/assets/images/banner.webp" 
             alt="LEGO Banner" 
             style="width: 100%; max-width: 1200px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.15);">
    </div>

    <div class="product-section">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #a4161a; margin-bottom: 30px;">
            <h2 style="color: #a4161a; margin: 0; padding-bottom: 10px;">
                <i class="fa-solid fa-fire"></i> SẢN PHẨM NỔI BẬT
            </h2>
            <a href="#" style="text-decoration: none; color: #666; font-size: 14px;">Xem tất cả ></a>
        </div>
        
        <div class="product-grid" style="display: flex; gap: 25px; flex-wrap: wrap; justify-content: flex-start;">
            <?php if(!empty($products)): ?>
                <?php foreach($products as $item): ?>
                    <div class="product-card" style="width: calc(25% - 20px); min-width: 250px; border-radius: 12px; padding: 15px; text-align: center; background: #fff; transition: transform 0.3s; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border: 1px solid #eee;">
                        
                        <div style="height: 220px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; background: #fbfbfb; border-radius: 8px;">
                            <?php 
                                // Nếu trong DB có image_url thì dùng, không thì hiện logo mặc định
                                $imgName = !empty($item['image_url']) ? $item['image_url'] : 'logo.png'; 
                            ?>
                            <img src="/lego_shop_php/public/assets/images/<?= $imgName ?>" 
                                 alt="<?= $item['name'] ?>" 
                                 style="max-width: 90%; max-height: 90%; object-fit: contain;">
                        </div>

                        <h3 style="font-size: 16px; margin: 10px 0; color: #222; height: 45px; overflow: hidden; line-height: 1.4; font-weight: 600;">
                            <?= $item['name'] ?>
                        </h3>
                        
                        <p style="color: #d90429; font-weight: bold; font-size: 20px; margin: 10px 0;">
                            <?= number_format($item['selling_price'], 0, ',', '.') ?>đ
                        </p>
                        
                        <div style="display: flex; gap: 10px; margin-top: 15px;">
                            <button style="flex: 1; background: #ef233c; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; font-weight: bold;">
                                Mua ngay
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="width: 100%; text-align: center; padding: 100px 0;">
                    <img src="/lego_shop_php/public/assets/images/logo.png" style="opacity: 0.2; width: 100px;">
                    <p style="color: #999; margin-top: 10px;">Kho hàng đang được cập nhật...</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>