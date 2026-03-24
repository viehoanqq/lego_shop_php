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
        <i class="fa-solid fa-box-open"></i> 
        <?php 
            // Ưu tiên dùng $title được truyền từ Controller
            echo isset($title) ? $title : 'Tất cả sản phẩm LEGO';
        ?>
    </h1>
    <p style="color: #666; font-size: 14px; margin: 0;">
    </p>
</div>

                <div class="category-sort">
    <label style="font-weight: 600; color: #444; margin-right: 10px; font-size: 14px;">
        <i class="fa-solid fa-arrow-down-a-z"></i> Sắp xếp:
    </label>
    <select id="sortSelect" onchange="applySort(this.value)" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; outline: none; cursor: pointer;">
        <option value="newest" <?= (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : '' ?>>Mới nhất</option>
        <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá: Thấp đến cao</option>
        <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá: Cao đến thấp</option>
        <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>Tên: A-Z</option>
    </select>
</div>

<script>
function applySort(sortValue) {
    // Lấy tất cả tham số hiện tại trên URL (để giữ lại filter, page...)
    let urlParams = new URLSearchParams(window.location.search);
    urlParams.set('sort', sortValue); // Ghi đè hoặc thêm mới tham số sort
    urlParams.set('page', 1);        // Reset về trang 1 khi đổi cách sắp xếp
    
    // Chuyển hướng trang
    window.location.search = urlParams.toString();
}
</script>
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
            <?php if ($total_pages > 1): ?>
                <div class="pagination-container" style="display: flex; justify-content: center; gap: 10px; margin-top: 40px;">
                    
                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => max(1, $current_page - 1)])) ?>" 
                       class="page-link <?= ($current_page == 1) ? 'disabled' : '' ?>">&laquo;</a>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                           class="page-link <?= ($current_page == $i) ? 'active' : '' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <a href="?<?= http_build_query(array_merge($_GET, ['page' => min($total_pages, $current_page + 1)])) ?>" 
                       class="page-link <?= ($current_page == $total_pages) ? 'disabled' : '' ?>">&raquo;</a>
                    
                </div>
            <?php endif; ?>        
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
.page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 8px;
    background: #fff;
    color: #333;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: 0.3s;
    border: 1px solid #eee;
}

.page-link:hover {
    background: #f8f9fa;
    color: #a4161a;
}

.page-link.active {
    background: #a4161a; /* Màu đỏ giống ảnh của bạn */
    color: #fff;
    border-color: #a4161a;
}

.page-link.disabled {
    pointer-events: none;
    opacity: 0.5;
}
</style>