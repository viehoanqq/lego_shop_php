<div class="home-container" style="padding: 30px 0; background-color: #f8f9fa; font-family: 'Inter', sans-serif; min-height: 60vh;">
    
    <div class="banner-slider-container" style="position: relative; max-width: 1200px; margin: 0 auto 60px; overflow: hidden; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
        <div class="banner-slides" id="bannerSlides">
            <img src="/lego_shop_php/public/assets/images/banner.webp" class="banner-slide active" alt="Banner 1">
            <img src="/lego_shop_php/public/assets/images/banner1.webp" class="banner-slide" alt="Banner 2">
            <img src="/lego_shop_php/public/assets/images/banner2.webp" class="banner-slide" alt="Banner 3" onerror="this.style.display='none'">
        </div>
        
        <button class="slider-btn prev" onclick="moveSlide(-1)"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="slider-btn next" onclick="moveSlide(1)"><i class="fa-solid fa-chevron-right"></i></button>
    </div>

    <div class="category-section" style="max-width: 1200px; margin: 0 auto 80px; padding: 0 15px;">
        <h2 class="section-title" style="text-align: center; margin-bottom: 40px;">CHỦ ĐỀ LEGO</h2>
        <div class="category-grid">
            <?php if (!empty($header_categories)): ?>
                <?php foreach ($header_categories as $cat): ?>
                    <a href="/lego_shop_php/product/category/<?= $cat['id'] ?>" class="category-item">
                        <div class="cat-img-wrapper"> 
                            <?php $cat_img = !empty($cat['image_url']) ? $cat['image_url'] : 'default-cat.png'; ?>
                            <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($cat_img) ?>" alt="<?= htmlspecialchars($cat['name']) ?>">
                        </div>
                        <h3 class="category-name"><?= htmlspecialchars($cat['name']) ?></h3>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%; color: #666;">Đang cập nhật danh mục...</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="new-arrivals-section" style="max-width: 1200px; margin: 0 auto 80px; padding: 0 15px; text-align: center;">
        <h2 class="section-title">Hàng mới về – Bé thích mê</h2>
        <a href="/lego_shop_php/product" class="btn-view-all">Xem tất cả</a>
        
        <div class="new-products-grid">
            <div class="new-promo-banner">
                <img src="/lego_shop_php/public/assets/images/new-product.webp" alt="Hàng Mới Về" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
            </div>
            
            <?php if (!empty($new_products)): ?>
                <?php foreach ($new_products as $product): ?>
                    <div class="product-wrapper">
                        <span class="badge-new">New</span>
                        <?php require __DIR__ . '/../components/product_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: span 3; color: #666; padding-top: 50px;">Chưa có sản phẩm mới nào.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="featured-products-section" style="max-width: 1200px; margin: 0 auto 80px; padding: 0 15px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 class="section-title" style="margin: 0;">SẢN PHẨM NỔI BẬT</h2>
            <a href="/lego_shop_php/product" class="link-view-all" style="color: #a4161a; font-weight: bold; text-decoration: none;">
                Xem tất cả <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <div class="featured-grid">
            <?php if (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): ?>
                    <div class="product-wrapper">
                        <?php require __DIR__ . '/../components/product_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 40px;">Chưa có sản phẩm nổi bật nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    /* CSS Slider */
    .banner-slides { display: flex; width: 100%; transition: transform 0.5s ease-in-out; }
    .banner-slide { min-width: 100%; object-fit: cover; }
    .slider-btn { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.8); border: none; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 18px; color: #333; transition: 0.3s; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
    .slider-btn:hover { background: #fff; color: #a4161a; }
    .slider-btn.prev { left: 15px; }
    .slider-btn.next { right: 15px; }

    /* CSS Typography & Nút bấm */
    .section-title { color: #a4161a; font-size: 24px; font-weight: 800; text-transform: uppercase; margin-bottom: 15px; }
    .btn-view-all { display: inline-block; background-color: #a4161a; color: white; padding: 8px 24px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 14px; margin-bottom: 30px; transition: 0.2s; }
    .btn-view-all:hover { background-color: #800f13; color: white;}

    /* LƯU Ý MỚI: CSS Danh Mục Động (Đã xóa khung, nền trắng và viền) */
    .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 30px; }
    .category-item { display: block; text-align: center; text-decoration: none; transition: transform 0.3s ease; }
    .category-item:hover { transform: translateY(-8px); }
    .cat-img-wrapper { width: 100%; height: 120px; margin: 0 auto 15px; overflow: hidden; display: flex; align-items: center; justify-content: center; transition: transform 0.3s ease; }
    .cat-img-wrapper img { width: 100%; height: 100%; object-fit: contain; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); transition: transform 0.3s ease; }
    .category-item:hover .cat-img-wrapper img { transform: scale(1.1); filter: drop-shadow(0 8px 12px rgba(0,0,0,0.15)); }
    .category-name { color: #333; font-size: 16px; font-weight: 700; margin: 0; line-height: 1.4; transition: color 0.2s; }
    .category-item:hover .category-name { color: #a4161a; }

    /* CSS Grid Sản phẩm mới về */
    .new-products-grid { display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 20px; text-align: left; }
    .product-wrapper { position: relative; display: flex; }
    .product-wrapper > .product-card { width: 100%; margin: 0; } 
    .badge-new { position: absolute; top: 10px; right: 10px; background-color: #a4161a; color: white; padding: 4px 8px; font-size: 12px; font-weight: bold; border-radius: 4px; z-index: 10; }

    /* CSS Sản phẩm nổi bật (Lưới 4 cột) */
    .featured-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: left; }

    /* Responsive Design */
    @media (max-width: 992px) {
        .category-grid { grid-template-columns: repeat(4, 1fr); }
        .new-products-grid { grid-template-columns: 1fr 1fr; }
        .featured-grid { grid-template-columns: repeat(2, 1fr); }
        .new-promo-banner { display: none; } 
    }
    @media (max-width: 576px) {
        .category-grid { grid-template-columns: repeat(2, 1fr); }
        .new-products-grid { grid-template-columns: 1fr; }
        .featured-grid { grid-template-columns: 1fr; }
    }
    /* ==========================================
       CSS DANH MỤC ĐỘNG (Đã sửa Flexbox Canh giữa & Bỏ bóng)
    ========================================== */
    .category-grid { 
        display: flex; 
        flex-wrap: wrap; 
        justify-content: center; /* Bí quyết để luôn canh giữa đây nhé */
        gap: 30px; 
    }
    .category-item { 
        display: block; 
        width: 160px; /* Cố định chiều rộng để các item đều nhau */
        text-align: center; 
        text-decoration: none; 
        transition: transform 0.3s ease; 
    }
    .category-item:hover { 
        transform: translateY(-5px); /* Chỉ giữ lại hiệu ứng nảy nhẹ lên */
    }
    .cat-img-wrapper { 
        width: 100%; 
        height: 120px; 
        margin: 0 auto 15px; 
        overflow: hidden; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
    }
    .cat-img-wrapper img { 
        width: 100%; 
        height: 100%; 
        object-fit: contain; 
        transition: transform 0.3s ease; 
        /* Đã xóa filter: drop-shadow ở đây */
    }
    .category-item:hover .cat-img-wrapper img { 
        transform: scale(1.1); 
        /* Đã xóa filter: drop-shadow lúc hover ở đây */
    }
    .category-name { 
        color: #333; 
        font-size: 15px; 
        font-weight: 700; 
        margin: 0; 
        line-height: 1.3; 
        transition: 0.2s; 
    }
    .category-item:hover .category-name { 
        color: #a4161a; 
    }
</style>

<script>
    let slideIndex = 0;
    const slidesContainer = document.getElementById("bannerSlides");
    const totalSlides = document.querySelectorAll('.banner-slide').length;

    function moveSlide(step) {
        slideIndex += step;
        if (slideIndex >= totalSlides) { slideIndex = 0; }
        else if (slideIndex < 0) { slideIndex = totalSlides - 1; }
        updateSlider();
    }

    function updateSlider() {
        slidesContainer.style.transform = `translateX(-${slideIndex * 100}%)`;
    }

    setInterval(() => { moveSlide(1); }, 5000);
</script>