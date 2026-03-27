<style>
/* Container chính */
/* Header Container */
.header { 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-end; /* Căn chỉnh các ô input và nút bấm nằm trên một đường thẳng đáy */
    margin-bottom: 25px; 
    gap: 20px;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

/* Đảm bảo form dàn hàng ngang và chiếm hết chiều rộng của nhóm bên trái */
.filter-form { 
    display: flex; 
    gap: 15px; 
    margin-top: 15px; 
    align-items: center;
    width: 100%; /* Đảm bảo form mở rộng hết mức có thể */
}

/* Thanh tìm kiếm: Chiếm toàn bộ không gian còn trống ở giữa */
.search-wrapper { 
    position: relative; 
    flex-grow: 1; /* Đây là dòng quan trọng để kéo dài thanh search */
}

.search-wrapper input {
    padding-left: 35px;
    width: 100%;
}

/* Ô lọc: Giới hạn chiều rộng lại để không bị quá dài, đẩy nó về bên phải */
.filter-select { 
    width: 250px; 
    flex: none ;
    cursor: pointer;
}

/* Căn chỉnh lại Header để cân đối */
.header-left { 
    flex-grow: 1; 
    display: flex; 
    flex-direction: column;
}

/* Đồng bộ chiều cao cho đẹp */
.form-control {
    height: 42px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}
/* Kiểu dáng Badge trạng thái */
.stock-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.stock-low {
    background: #fffaf0;
    color: #dd6b20;
    border: 1px solid #fbd38d;
}

.stock-empty {
    background: #fff5f5;
    color: #e53e3e;
    border: 1px solid #feb2b2;
    animation: pulse-red 2s infinite; /* Hiệu ứng nháy nhẹ cho hàng đã hết sạch */
}

@keyframes pulse-red {
    0% { box-shadow: 0 0 0 0 rgba(229, 62, 62, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(229, 62, 62, 0); }
    100% { box-shadow: 0 0 0 0 rgba(229, 62, 62, 0); }
}

/* Tùy chỉnh bảng */
.lego-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}

.lego-table thead tr {
    background: #f8fafc;
}

.lego-table th {
    padding: 15px 20px;
    color: #4a5568;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #edf2f7;
}

.lego-table td {
    padding: 15px 20px;
    vertical-align: middle;
    border-bottom: 1px solid #edf2f7;
}

/* Ảnh sản phẩm */
.img-product {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.product-cell {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* Nút quay lại */
.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    background: #718096;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-back:hover {
    background: #4a5568;
    transform: translateX(-3px);
}

</style>
<div class="header">
    <div class="header-left">
        <h2>⚠️ Cảnh báo hết hàng trong kho</h2>
        <p style="color: #718096; font-size: 14px;">Danh sách sản phẩm có số lượng tồn kho chạm mức tối thiểu.</p>

        <form action="/lego_shop_php/adminproduct/lowstock" method="GET" class="filter-form" style="display: flex; gap: 10px; align-items: center;">
            
            <div class="search-wrapper" style="position: relative; flex: 2;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm tên sản phẩm..." 
                       value="<?= $_GET['keyword'] ?? '' ?>"
                       style="padding-left: 35px; width: 100%; border-radius: 8px; border: 1px solid #e2e8f0; height: 40px;">
            </div>

            <select name="type" class="form-control" onchange="this.form.submit()" 
                    style="flex: 1; cursor: pointer; border-radius: 8px; border: 1px solid #e2e8f0; height: 40px; padding: 0 10px;">
                <option value="all" <?= ($currentType == 'all') ? 'selected' : '' ?>>-- Tất cả cảnh báo --</option>
                <option value="out" <?= ($currentType == 'out') ? 'selected' : '' ?>>Đã hết sạch (0)</option>
                <option value="low" <?= ($currentType == 'low') ? 'selected' : '' ?>>Sắp hết hàng (Dưới ngưỡng)</option>
            </select>
            
            <input type="hidden" name="page" value="1">
        </form>
    </div>
    <div style="display: flex; gap: 10px;">
        
        <!-- <a href="/lego_shop_php/adminproduct" class="btn-add-product" style="color: #718096;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại kho tổng
        </a> -->
    </div>
</div>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 35%;">Sản phẩm</th>
                <th>Dòng LEGO</th>
                <th>Tồn kho thực tế</th>
                <th>Mức cảnh báo</th>
                <th>Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                <tr style="text-align: center;">
                    <td>
                        <div class="product-cell">
                            <img src="/lego_shop_php/public/assets/images/<?= $p['main_image'] ?? 'default.jpg' ?>" class="img-product">
                            <div>
                                <div style="font-weight: 700; color: #3182ce;"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="font-size: 11px; color: #a0aec0;">SKU: <?= strtoupper($p['sku']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span style="background: #edf2f7; padding: 3px 8px; border-radius: 4px;"><?= $p['category_name'] ?></span></td>
                    
                    <td>
                        <b style="color: #e53e3e; text-align: center; font-size: 16px;"><?= $p['stock_quantity'] ?></b> mảnh/hộp
                    </td>
                    
                    <td><span style="color: #718096;">≤ <?= $p['min_stock_level'] ?></span></td>
                    
                    <td>
                        <span class="stock-badge <?= $p['stock_quantity'] <= 0 ? 'stock-empty' : 'stock-low' ?>">
                            <?= $p['stock_quantity'] <= 0 ? 'Hết' : 'Sắp hết' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" style="text-align: center; padding: 50px;">🎉 Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if ($totalPages > 1): ?>
    <div class="pagination" style="display: flex; justify-content: center; gap: 8px; margin-top: 25px;">
        
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>&type=<?= $currentType ?>&keyword=<?= urlencode($keyword) ?>" class="page-link">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&type=<?= $currentType ?>&keyword=<?= urlencode($keyword) ?>" 
               class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>&type=<?= $currentType ?>&keyword=<?= urlencode($keyword) ?>" class="page-link">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php endif; ?>
        
    </div>
<?php endif; ?>

<style>
/* CSS cho các nút phân trang đồng bộ với giao diện của bạn */
.page-link {
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    color: #4a5568;
    background: #fff;
    font-weight: 600;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.page-link:hover {
    background: #edf2f7;
    border-color: #cbd5e0;
}

.page-link.active {
    background: #3182ce;
    color: #fff;
    border-color: #3182ce;
    box-shadow: 0 4px 6px rgba(49, 130, 206, 0.2);
}
</style>