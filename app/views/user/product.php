<?php require __DIR__ . '/../components/breadcrumb.php'; ?>

<div class="product-page-wrapper" style="background-color: #f8f9fa; padding-bottom: 50px;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 15px; display: grid; grid-template-columns: 260px 1fr; gap: 30px;">
        
        <div class="left-column">
            <?php require __DIR__ . '/../components/sidebar_filter.php'; ?>
        </div>

        <div class="right-column">
            
            <div class="category-header-bar" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #f0f0f0; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
                
                <div class="category-info">
                    <h1 style="color: #a4161a; font-size: 22px; font-weight: 700; margin: 0 0 5px 0; display: flex; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-box-open"></i> <?= isset($category_name) ? $category_name : 'Tất cả sản phẩm LEGO' ?>
                    </h1>
                    <p style="color: #666; font-size: 14px; margin: 0;">
                        Hiển thị <strong><?= $total_products ?? 0 ?></strong> sản phẩm
                    </p>
                </div>

                <div class="category-sort">
                    <label style="font-weight: 600; color: #444; margin-right: 10px; font-size: 14px;"><i class="fa-solid fa-arrow-down-a-z"></i> Sắp xếp:</label>
                    <select style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; outline: none; cursor: pointer;">
                        <option value="newest">Mới nhất</option>
                        <option value="price_asc">Giá: Thấp đến cao</option>
                        <option value="price_desc">Giá: Cao đến thấp</option>
                        <option value="name_asc">Tên: A-Z</option>
                    </select>
                </div>
            </div>

            <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php require __DIR__ . '/../components/product_card.php'; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; grid-column: 1 / -1; color: #666; padding: 40px 0; background: #fff; border-radius: 12px; border: 1px solid #f0f0f0;">Không tìm thấy sản phẩm nào phù hợp!</p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
/* CSS Responsive cho trang Product */
@media (max-width: 992px) {
    .product-page-wrapper .container { grid-template-columns: 1fr; } /* Màn hình nhỏ thì Sidebar đẩy lên trên */
    .left-column { margin-bottom: 20px; }
}
@media (max-width: 576px) {
    .category-header-bar { flex-direction: column; align-items: flex-start; gap: 15px; }
    .category-sort select { width: 100%; margin-top: 10px; }
}
</style>