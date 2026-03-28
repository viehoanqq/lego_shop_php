<style>
    /* ===== CÁC CSS CŨ ===== */
    .header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; background: #fff; padding: 20px 25px; border-radius: 14px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .header-left { flex: 1; display: flex; flex-direction: column; }
    .header-left h2 { margin: 0; font-size: 20px; font-weight: 700; }
    .header-left p { color: #718096; font-size: 14px; margin-top: 4px; }
    .filter-form { display: flex; gap: 12px; align-items: center; margin-top: 15px; }
    .search-wrapper { position: relative; flex: 2; }
    .search-wrapper input { width: 100%; height: 42px; padding: 0 110px 0 38px; border-radius: 10px; border: 1px solid #e2e8f0; outline: none; transition: 0.2s; }
    .search-wrapper input:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.15); }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0; }
    .btn-search-inside { position: absolute; right: 6px; top: 50%; transform: translateY(-50%); height: 30px; padding: 0 14px; border-radius: 6px; border: none; background: #3182ce; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-search-inside:hover { background: #2b6cb0; }
    .form-control { height: 42px; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0 12px; background: #fff; transition: 0.2s; }
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.15); }
    .btn-filter-action { height: 42px; padding: 0 16px; border-radius: 10px; border: none; background: #3182ce; color: #fff; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; }
    .btn-filter-action:hover { background: #2b6cb0; }
    
    .table-container { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .lego-table { width: 100%; border-collapse: collapse; }
    .lego-table thead { background: #f8fafc; }
    .lego-table th { text-align: left; padding: 15px; color: #4a5568; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #edf2f7; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #edf2f7; vertical-align: middle; }
    .lego-table tbody tr:hover { background: #f9fafb; }
    .product-cell { display: flex; align-items: center; gap: 12px; }
    .img-product { width: 52px; height: 52px; border-radius: 10px; object-fit: cover; border: 1px solid #e2e8f0; }
    .stock-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .stock-low { background: #fffaf0; color: #dd6b20; border: 1px solid #fbd38d; }
    .stock-empty { background: #fff5f5; color: #e53e3e; border: 1px solid #feb2b2; }
    
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
    .page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #4a5568; background: #fff; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #edf2f7; }
    .page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; }

    /* ===== CSS MODAL & COMBO BOX TÌM KIẾM MỚI ===== */
    .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.45); backdrop-filter: blur(2px); }
    .modal-content { background: #fff; padding: 25px; width: 800px; max-width: 90%; border-radius: 14px; margin: 50px auto; box-shadow: 0 10px 40px rgba(0,0,0,0.1); animation: fadeIn 0.25s ease; max-height: 85vh; overflow-y: auto;}
    .modal-content h3 { margin-top: 0; color: #1e293b; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 20px;}
    
    .combo-box-wrapper { position: relative; width: 100%; }
    .combo-search-input { width: 100%; padding: 10px; padding-right: 35px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; background-color: #fff; font-size: 13px; color: #1a202c; }
    .combo-search-input:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }
    .combo-dropdown-icon { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none; transition: transform 0.2s; }
    .combo-dropdown-list { display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; background: #fff; border: 1px solid #cbd5e1; border-radius: 6px; max-height: 250px; overflow-y: auto; z-index: 9999; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); padding: 5px 0; margin: 0; list-style: none; }
    .combo-item { padding: 10px 15px; cursor: pointer; border-bottom: 1px solid #f1f5f9; font-size: 13px; color: #1e293b; display: flex; gap: 12px; align-items: center; }
    .combo-item:last-child { border-bottom: none; }
    .combo-item:hover { background-color: #f8fafc; color: #3182ce; }
    .combo-empty { padding: 10px 15px; color: #94a3b8; text-align: center; font-style: italic; }

    .btn-add-row { background: #fff; color: #3182ce; border: 1px dashed #3182ce; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; margin-top: 15px; display: inline-flex; align-items: center; gap: 5px; }
    .btn-add-row:hover { background: #ebf8ff; }
    
    @keyframes fadeIn { from { transform: translateY(-10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
</style>

<div class="header">
    <div class="header-left">
        <h2>Cảnh báo hết hàng trong kho</h2>
        <p style="color: #718096; font-size: 14px;">Danh sách sản phẩm có số lượng tồn kho chạm mức tối thiểu.</p>

        <form action="/lego_shop_php/adminproduct/lowstock" method="GET" class="filter-form">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="keyword" class="form-control" placeholder="Tìm tên hoặc mã SKU..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                <button type="submit" class="btn-search-inside">Tìm kiếm</button>
            </div>

            <select name="type" class="form-control" onchange="this.form.submit()" style="flex: 1; cursor: pointer;">
                <option value="all" <?= ($currentType == 'all') ? 'selected' : '' ?>>-- Tất cả cảnh báo --</option>
                <option value="out" <?= ($currentType == 'out') ? 'selected' : '' ?>>Đã hết (0)</option>
                <option value="low" <?= ($currentType == 'low') ? 'selected' : '' ?>>Sắp hết hàng (Dưới ngưỡng)</option>
            </select>
            
            <input type="hidden" name="page" value="1">
            <button type="button" class="btn-filter-action" onclick="openSettingModal()">
                <i class="fa-solid fa-pen-to-square"></i> Thay đổi mức cảnh báo
            </button>
        </form>
    </div>
</div>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 35%;">Sản phẩm</th>
                <th>Dòng LEGO</th>
                <th style="text-align: center;">Tồn kho thực tế</th>
                <th style="text-align: center;">Ngưỡng cảnh báo</th>
                <th style="text-align: center;">Trạng thái</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                <tr style="text-align: left;">
                    <td>
                        <div class="product-cell">
                            <img src="/lego_shop_php/public/assets/images/<?= $p['main_image'] ?? 'default.jpg' ?>" class="img-product" onerror="this.src='https://placehold.co/52x52?text=LEGO'">
                            <div>
                                <div style="font-weight: 700; color: #3182ce;"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="font-size: 11px; color: #a0aec0;">SKU: <?= strtoupper($p['sku']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span style="background: #edf2f7; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #4a5568;"><?= htmlspecialchars($p['category_name']) ?></span></td>
                    <td style="text-align: center;">
                        <b style="color: #e53e3e; font-size: 16px;"><?= $p['stock_quantity'] ?></b>
                    </td>
                    <td style="text-align: center;"><span style="color: #718096; font-weight: 600;">≤ <?= $p['min_stock_level'] ?></span></td>
                    <td style="text-align: center;">
                        <span class="stock-badge <?= $p['stock_quantity'] <= 0 ? 'stock-empty' : 'stock-low' ?>">
                            <?= $p['stock_quantity'] <= 0 ? 'Hết hàng' : 'Sắp hết' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" style="text-align: center; padding: 50px;">🎉 Tuyệt vời! Không có sản phẩm nào sắp hết hàng.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage - 1 ?>&type=<?= $currentType ?>&keyword=<?= urlencode($keyword) ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>&type=<?= $currentType ?>&keyword=<?= urlencode($keyword) ?>" class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage + 1 ?>&type=<?= $currentType ?>&keyword=<?= urlencode($keyword) ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div id="settingModal" class="modal">
    <div class="modal-content">
        <h3><i class="fa-solid fa-sliders"></i> Cập nhật mức cảnh báo (Min Stock)</h3>
        
        <table class="lego-table" id="updateTable">
            <thead>
                <tr>
                    <th style="width: 60%; background: #fff;">Chọn sản phẩm</th>
                    <th style="width: 25%; text-align: center; background: #fff;">Mức cảnh báo mới</th>
                    <th style="width: 15%; text-align: center; background: #fff;">Xóa</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>

        <button type="button" class="btn-add-row" onclick="addRow()"><i class="fa-solid fa-plus"></i> Thêm sản phẩm khác</button>

        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: flex-end; border-top: 1px solid #e2e8f0; padding-top: 20px;">
            <button onclick="closeSettingModal()" style="background: #edf2f7; color: #4a5568; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">Hủy bỏ</button>
            <button onclick="submitBulkUpdate()" style="background: #3182ce; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;"><i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi</button>
        </div>
    </div>
</div>

<script>
    // Dữ liệu đổ từ PHP xuống
    const productsData = <?= json_encode($all_products ?? []) ?>;

    function openSettingModal() {
        document.getElementById('settingModal').style.display = 'block';
        document.querySelector('#updateTable tbody').innerHTML = ''; // Làm sạch bảng
        addRow(); // Mở sẵn 1 dòng
    }

    function closeSettingModal() {
        document.getElementById('settingModal').style.display = 'none';
    }

    function addRow() {
        const tbody = document.querySelector('#updateTable tbody');
        const rowId = 'row_' + Date.now();
        
        let listHtml = productsData.map(p => {
            let searchStr = (p.name + " " + p.sku).toLowerCase();
            let safeName = p.name.replace(/'/g, "\\'");
            let safeImg = p.image_url ? p.image_url : 'default.jpg';
            return `
            <li class="combo-item" data-search="${searchStr}" onclick="selectProduct('${rowId}', ${p.id}, '${safeName}', ${p.min_stock_level})">
                <img src="/lego_shop_php/public/assets/images/${safeImg}" style="width:40px; height:40px; border-radius:4px; object-fit:cover; border: 1px solid #cbd5e1;" onerror="this.src='https://placehold.co/40x40?text=LEGO'">
                <div style="flex: 1;">
                    <div style="font-weight: 700;">${p.name}</div>
                    <span class="combo-item-sku">SKU: ${p.sku} | Mức cảnh báo cũ: <b style="color:#dd6b20">${p.min_stock_level}</b></span>
                </div>
            </li>`;
        }).join('');

        const rowHtml = `
            <tr id="${rowId}">
                <td style="padding: 10px 0;">
                    <div class="combo-box-wrapper" id="combo_${rowId}">
                        <input type="text" class="combo-search-input" placeholder="Gõ tên hoặc mã sản phẩm..." onfocus="openDropdown('${rowId}')" onkeyup="filterDropdown('${rowId}', this.value)" autocomplete="off">
                        <i class="fa-solid fa-chevron-down combo-dropdown-icon"></i>
                        <input type="hidden" class="real-product-id">
                        <ul class="combo-dropdown-list">
                            ${listHtml}
                            <li class="combo-empty" style="display:none;">Không tìm thấy sản phẩm...</li>
                        </ul>
                    </div>
                </td>
                <td style="padding: 10px; text-align:center;">
                    <input type="number" class="form-control min-stock-input" value="0" min="0" style="text-align:center; width: 80px; margin: 0 auto;">
                </td>
                <td style="padding: 10px; text-align: center;">
                    <button type="button" onclick="document.getElementById('${rowId}').remove();" style="color: #e53e3e; border:none; background:none; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                </td>
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', rowHtml);
    }

    function openDropdown(rowId) {
        document.querySelectorAll('.combo-dropdown-list').forEach(el => el.style.display = 'none');
        const combo = document.getElementById(`combo_${rowId}`);
        combo.querySelector('.combo-dropdown-list').style.display = 'block';
        filterDropdown(rowId, '');
    }

    function filterDropdown(rowId, keyword) {
        const combo = document.getElementById(`combo_${rowId}`);
        const items = combo.querySelectorAll('.combo-item');
        let hasResult = false;
        keyword = keyword.toLowerCase().trim();

        if(keyword !== '') combo.querySelector('.real-product-id').value = '';

        items.forEach(item => {
            if (item.getAttribute('data-search').includes(keyword)) {
                item.style.display = 'flex'; hasResult = true;
            } else {
                item.style.display = 'none';
            }
        });
        combo.querySelector('.combo-empty').style.display = hasResult ? 'none' : 'block';
    }

    function selectProduct(rowId, productId, productName, oldMin) {
        const combo = document.getElementById(`combo_${rowId}`);
        combo.querySelector('.combo-search-input').value = productName;
        combo.querySelector('.real-product-id').value = productId;
        combo.querySelector('.combo-dropdown-list').style.display = 'none';
        
        // Đổ mức cảnh báo cũ ra input để tiện sửa
        document.getElementById(rowId).querySelector('.min-stock-input').value = oldMin;
    }

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.combo-box-wrapper')) {
            document.querySelectorAll('.combo-dropdown-list').forEach(el => el.style.display = 'none');
        }
    });

    async function submitBulkUpdate() {
        const rows = document.querySelectorAll('#updateTable tbody tr');
        if (rows.length === 0) return alert("Vui lòng thêm ít nhất một sản phẩm!");

        const dataToSend = [];
        let isValid = true;

        rows.forEach(row => {
            const pId = row.querySelector('.real-product-id').value;
            const minStock = row.querySelector('.min-stock-input').value;
            
            if (!pId) {
                isValid = false;
                row.querySelector('.combo-search-input').style.borderColor = '#e53e3e';
            } else {
                row.querySelector('.combo-search-input').style.borderColor = '#e2e8f0';
                dataToSend.push({ product_id: pId, min_stock: minStock });
            }
        });

        if (!isValid) return alert("Có sản phẩm chưa được chọn đúng từ danh sách. Vui lòng kiểm tra lại viền đỏ!");

        try {
            const response = await fetch('/lego_shop_php/adminproduct/updateBulkMinStock', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ items: dataToSend })
            });
            const result = await response.json();
            if(result.success) {
                alert("Cập nhật thành công!");
                location.reload();
            } else {
                alert("Lỗi: " + result.message);
            }
        } catch (err) {
            alert("Lỗi kết nối mạng!");
        }
    }
</script>