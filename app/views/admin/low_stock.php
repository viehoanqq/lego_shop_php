<style>
/* ===== HEADER ===== */
.header { 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-end;
    margin-bottom: 25px; 
    gap: 20px;
    background: #fff;
    padding: 20px 25px;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}

.header-left { 
    flex: 1;
    display: flex;
    flex-direction: column;
}

.header-left h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
}

.header-left p {
    color: #718096;
    font-size: 14px;
    margin-top: 4px;
}

/* ===== FORM ===== */
.filter-form { 
    display: flex; 
    gap: 12px; 
    align-items: center;
    margin-top: 15px;
}

/* ===== SEARCH ===== */
.search-wrapper { 
    position: relative; 
    flex: 2;
}

.search-wrapper input {
    width: 100%;
    height: 42px;
    padding: 0 110px 0 38px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    outline: none;
    transition: 0.2s;
}

.search-wrapper input:focus {
    border-color: #3182ce;
    box-shadow: 0 0 0 2px rgba(49,130,206,0.15);
}

.search-wrapper i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

/* Nút search */
.btn-search-inside {
    position: absolute;
    right: 6px;
    top: 50%;
    transform: translateY(-50%);
    height: 30px;
    padding: 0 14px;
    border-radius: 6px;
    border: none;
    background: #3182ce;
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
}

.btn-search-inside:hover {
    background: #2b6cb0;
}

/* ===== SELECT ===== */
.form-control {
    height: 42px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 0 12px;
    background: #fff;
    transition: 0.2s;
}

.form-control:focus {
    border-color: #3182ce;
    box-shadow: 0 0 0 2px rgba(49,130,206,0.15);
}

/* ===== BUTTON SETTINGS ===== */
.btn-filter-action {
    height: 42px;
    padding: 0 16px;
    border-radius: 10px;
    border: none;
    background: #edf2f7;
    color: #2d3748;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: 0.2s;
}

.btn-filter-action:hover {
    background: #e2e8f0;
}

/* ===== TABLE ===== */
.table-container {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}

.lego-table {
    width: 100%;
    border-collapse: collapse;
}

.lego-table thead {
    background: #f8fafc;
}

.lego-table th {
    text-align: left;
    padding: 15px;
    color: #4a5568;
    font-size: 13px;
    text-transform: uppercase;
    border-bottom: 2px solid #edf2f7;
}

.lego-table td {
    padding: 15px;
    border-bottom: 1px solid #edf2f7;
    vertical-align: middle;
}

/* Hover row */
.lego-table tbody tr:hover {
    background: #f9fafb;
}

/* ===== PRODUCT ===== */
.product-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.img-product {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
}

/* ===== BADGE ===== */
.stock-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
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
}

/* ===== PAGINATION ===== */
.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 25px;
}

.page-link {
    padding: 8px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    color: #4a5568;
    background: #fff;
    font-weight: 600;
    transition: 0.2s;
}

.page-link:hover {
    background: #edf2f7;
}

.page-link.active {
    background: #3182ce;
    color: #fff;
    border-color: #3182ce;
}

/* ===== MODAL ===== */
.modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0; top: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(2px);
}

.modal-content {
    background: #fff;
    padding: 25px;
    width: 360px;
    border-radius: 14px;
    margin: 120px auto;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    animation: fadeIn 0.25s ease;
}

.modal-content h3 {
    margin-top: 0;
}

/* input modal */
.modal-content input {
    width: 100%;
    height: 40px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 0 10px;
    margin-top: 8px;
}

/* button modal */
.modal-content button {
    padding: 8px 14px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
}

.modal-content button:first-child {
    background: #3182ce;
    color: white;
}

.modal-content button:last-child {
    background: #edf2f7;
}

.modal-content button:hover {
    opacity: 0.9;
}

/* animation */
@keyframes fadeIn {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
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
                <button type="submit" class="btn-search-inside">
                    Tìm kiếm
                </button>
            </div>

            <select name="type" class="form-control" onchange="this.form.submit()" 
                    style="flex: 1; cursor: pointer; border-radius: 8px; border: 1px solid #e2e8f0; height: 40px; padding: 0 10px;">
                <option value="all" <?= ($currentType == 'all') ? 'selected' : '' ?>>-- Tất cả cảnh báo --</option>
                <option value="out" <?= ($currentType == 'out') ? 'selected' : '' ?>>Đã hết (0)</option>
                <option value="low" <?= ($currentType == 'low') ? 'selected' : '' ?>>Sắp hết hàng (Dưới ngưỡng)</option>
            </select>
            
            <input type="hidden" name="page" value="1">
            <button type="button" class="btn-filter-action" onclick="openSettingModal()">
                <i class="fa-solid fa-gear"></i> Cài đặt
            </button>
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
                <th style="text-align: center;">Trạng thái</th>
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
                        <b style="color: #e53e3e; text-align: center; font-size: 16px;"><?= $p['stock_quantity'] ?></b> Sản phẩm
                    </td>
                    
                    <td><span style="color: #718096;">≤ <?= $p['min_stock_level'] ?></span></td>
                    
                    <td style="text-align: center;">
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

<div id="settingModal" class="modal">
    <div class="modal-content">
        <h3>⚙️ Cài đặt mức cảnh báo</h3>

        <label>Nhập mức cảnh báo chung:</label>
        <input type="number" id="globalMinStock" min="0" placeholder="Ví dụ: 10">

        <div style="margin-top: 15px;">
            <button onclick="saveMinStock()">Lưu</button>
            <button onclick="closeSettingModal()">Hủy</button>
        </div>
    </div>
</div>
<script>
function openSettingModal() {
    document.getElementById('settingModal').style.display = 'block';
}

function closeSettingModal() {
    document.getElementById('settingModal').style.display = 'none';
}

function saveMinStock() {
    let value = document.getElementById('globalMinStock').value;

    if (value === '' || value < 0) {
        alert('Giá trị không hợp lệ');
        return;
    }

    fetch('/lego_shop_php/adminproduct/updateGlobalMinStock', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'min_stock_level=' + value
    })
    .then(res => res.text())
    .then(() => {
        alert('Đã cập nhật!');
        location.reload();
    });
}
</script>