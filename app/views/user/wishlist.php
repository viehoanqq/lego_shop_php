<div class="wishlist-page" style="background-color: #f8f9fa; padding: 50px 0; min-height: 70vh;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px;">
        
        <div class="wishlist-header" style="text-align: center; margin-bottom: 40px;">
            <h1 style="color: #a4161a; font-size: 28px; font-weight: 800; text-transform: uppercase;">
                 Danh sách yêu thích
            </h1>
            <p style="color: #666;">Lưu giữ những bộ LEGO bạn yêu thích nhất tại đây</p>
        </div>

        <?php if (!empty($products)): ?>
            <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 25px;">
                <?php foreach ($products as $product): ?>
                    <div class="wishlist-item-wrapper" id="wishlist-item-<?= $product['id'] ?>">
                        <?php require __DIR__ . '/../components/product_card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-wishlist" style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <i class="fa-regular fa-heart" style="font-size: 80px; color: #ddd; margin-bottom: 20px; display: block;"></i>
                <h2 style="color: #333; margin-bottom: 15px;">Danh sách đang trống!</h2>
                <p style="color: #888; margin-bottom: 30px;">Bạn chưa yêu thích sản phẩm nào. Hãy khám phá các bộ LEGO mới nhất nhé!</p>
                <a href="/lego_shop_php/product" class="btn-view-all" style="background: #a4161a; color: #fff; padding: 12px 30px; border-radius: 25px; text-decoration: none; font-weight: 700; transition: 0.3s;">
                    KHÁM PHÁ NGAY
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    // Ghi đè nhẹ hàm toggleWishlist chỉ riêng cho trang này 
    // Để khi bấm bỏ thích, cái Card đó biến mất ngay lập tức (UX xịn)
    const originalToggle = window.toggleWishlist;
    window.toggleWishlist = function(productId, btnElement) {
        // Gọi lại hàm gốc để xử lý DB và Toast
        // Chúng ta cần xử lý thêm việc xóa Element khỏi màn hình
        const formData = new FormData();
        formData.append('product_id', productId);

        fetch('/lego_shop_php/wishlist/toggle', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed') {
                const item = document.getElementById('wishlist-item-' + productId);
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        item.remove();
                        // Nếu xóa hết thì reload để hiện thông báo trống
                        if (document.querySelectorAll('.wishlist-item-wrapper').length === 0) {
                            location.reload();
                        }
                    }, 400);
                }
                showToast("Đã xóa khỏi danh sách!", "success");
            }
        });
    }
</script>