<aside class="sidebar-filter">
    <form action="/lego_shop_php/product/filter" method="GET" id="filterForm">
        
        <div class="filter-group">
            <h3 class="filter-title"><i class="fa-solid fa-layer-group"></i> Danh mục</h3>
            <ul class="filter-list">
                <li>
                    <label class="filter-label">
                        <input type="radio" name="category" value="all" <?= (!isset($_GET['category']) || $_GET['category'] == 'all') ? 'checked' : '' ?>>
                        Tất cả sản phẩm
                    </label>
                </li>
                <?php if(!empty($categories)): ?>
                    <?php foreach($categories as $cat): ?>
                        <li>
                            <label class="filter-label">
                                <input type="radio" name="category" value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?> 
                                <span class="count">(<?= $cat['product_count'] ?>)</span>
                            </label>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>

        <div class="filter-group">
            <h3 class="filter-title"><i class="fa-solid fa-tags"></i> Khoảng giá</h3>
            <ul class="filter-list">
                <li>
                    <label class="filter-label">
                        <input type="radio" name="price_range" value="0-500000"> Dưới 500.000đ
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="price_range" value="500000-1000000"> 500.000đ - 1.000.000đ
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="price_range" value="1000000-2000000"> 1.000.000đ - 2.000.000đ
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="price_range" value="2000000-5000000"> 2.000.000đ - 5.000.000đ
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="price_range" value="5000000-999999999"> Trên 5.000.000đ
                    </label>
                </li>
            </ul>
            <p class="filter-subtitle">Hoặc tự nhập khoảng giá:</p>
            <div class="price-input-group">
                <input type="number" name="min_price" placeholder="Từ" value="<?= $_GET['min_price'] ?? '' ?>">
                <span>-</span>
                <input type="number" name="max_price" placeholder="Đến" value="<?= $_GET['max_price'] ?? '' ?>">
            </div>
        </div>

        <div class="filter-group">
            <h3 class="filter-title"><i class="fa-solid fa-puzzle-piece"></i> Số mảnh ghép</h3>
            <ul class="filter-list">
                <li>
                    <label class="filter-label">
                        <input type="radio" name="pieces" value="0-100"> Dưới 100 mảnh
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="pieces" value="100-500"> 100 - 500 mảnh
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="pieces" value="500-1000"> 500 - 1000 mảnh
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="pieces" value="1000-2000"> 1000 - 2000 mảnh
                    </label>
                </li>
                <li>
                    <label class="filter-label">
                        <input type="radio" name="pieces" value="2000-99999"> Trên 2000 mảnh
                    </label>
                </li>
            </ul>
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-apply-filter">
                <i class="fa-solid fa-filter"></i> ÁP DỤNG BỘ LỌC
            </button>
            <a href="/lego_shop_php/product" class="btn-clear-filter">
                <i class="fa-solid fa-rotate-left"></i> Xóa bộ lọc
            </a>
        </div>
        
    </form>
</aside>