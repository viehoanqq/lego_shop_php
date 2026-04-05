<style>
    /* CSS ĐỒNG BỘ 100% VỚI BẢNG QUẢN LÝ SẢN PHẨM */
    .table-container { 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
        margin-top: 10px;
        max-height: 70vh; 
        overflow-y: auto; 
    }

    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { 
        position: sticky; 
        top: 0; 
        z-index: 10;
        background: #f8fafc; 
        padding: 15px; 
        text-align: left; 
        color: #64748b; 
        font-size: 13px; 
        text-transform: uppercase; 
        border-bottom: 2px solid #e2e8f0; 
    }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }

    /* FIX LỖI ẢNH TRÀN VIỀN VÀ CĂN CHỈNH TÊN SẢN PHẨM */
    .product-cell { display: flex; align-items: center; gap: 15px; }
    .img-product { width: 60px; height: 60px; min-width: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; }

    /* CSS cho Form Input Giá */
    .form-control { padding: 8px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; transition: 0.2s; }
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    /* ===== PAGINATION CSS ===== */
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; padding-bottom: 20px;}
    .page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #475569; background: #fff; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #f8fafc; color: #3182ce; border-color: #3182ce; }
    .page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; box-shadow: 0 4px 10px rgba(49, 130, 206, 0.2);}
    .page-link.disabled { opacity: 0.5; pointer-events: none; background: #f7fafc; color: #cbd5e1; }
</style>

<div class="admin-header1" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 10px;">
<div style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
    <form id="filterForm" action="/lego_shop_php/adminprice" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 250px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Tìm kiếm sản phẩm</label>
            <div style="position: relative;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></i>
                <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword']) ?>" 
                       placeholder="Nhập tên LEGO hoặc mã SKU..." class="form-control" 
                       style="width: 100%; padding: 10px 10px 10px 35px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none;"
                       onkeypress="if(event.keyCode==13) { this.form.submit(); return false; }">
            </div>
        </div>

        <div style="flex: 1; min-width: 200px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Danh mục LEGO</label>
            <select name="category_id" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
                <option value="">-- Tất cả danh mục --</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <a href="/lego_shop_php/adminprice" style="display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; padding: 0 20px; border-radius: 6px; font-weight: 600; height: 42px; transition: 0.2s;">
                <i class="fa-solid fa-rotate-right" style="margin-right: 5px;"></i> Xóa lọc
            </a>
        </div>

    </form>
</div>    
</div>

<div class="table-container">
    <table class="lego-table" id="priceTable">
        <thead>
            <tr>
                <th style="width: 80px; text-align: center;">Mã</th>
                <th style="width: 350px;">Sản phẩm</th>
                <th style="text-align: right;">Giá vốn WAC (VNĐ)</th>
                <th style="text-align: center; width: 150px;">Lợi nhuận (%)</th>
                <th style="text-align: right; width: 180px;">Giá bán (VNĐ)</th>
                <th style="text-align: center; width: 120px;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $p): 
                $margin_percent = floatval($p['profit_margin'] ?? 0) * 100;
                $import_price = intval($p['import_price'] ?? 0);
                $selling_price = intval($p['selling_price'] ?? 0);
            ?>
            <tr id="row_<?= $p['id'] ?>">
                <td style="text-align: center; font-weight: 600; color: #718096;">#<?= $p['id'] ?></td>
                
                <td>
                    <div class="product-cell">
                        <img src="/lego_shop_php/public/assets/images/<?= !empty($p['main_image']) ? $p['main_image'] : 'default.jpg' ?>" 
                             class="img-product" 
                             onerror="this.src='https://placehold.co/60x60?text=LEGO'">
                        <div>
                            <div style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($p['name']) ?></div>
                            <div style="font-size: 11px; color: #a0aec0; letter-spacing: 0.5px;">SKU: <?= strtoupper($p['sku']) ?></div>
                        </div>
                    </div>
                </td>
                
                <td style="text-align: right; font-weight: 600; color: #4a5568;">
                    <span class="wac-value" data-wac="<?= $import_price ?>">
                        <?= number_format($import_price, 0, ',', '.') ?>đ
                    </span>
                    <?php if($import_price == 0): ?>
                        <div style="font-size: 11px; color: #e53e3e; margin-top: 4px;">Chưa nhập kho</div>
                    <?php endif; ?>
                </td>
                
                <td style="text-align: center;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <input type="number" class="form-control input-margin" value="<?= $margin_percent ?>" 
                               step="0.1" style="width: 70px; text-align: center; font-weight: bold; color: #2d3748;" 
                               oninput="calcFromMargin(<?= $p['id'] ?>)"> 
                        <span style="font-weight: bold; color: #718096;">%</span>
                    </div>
                </td>
                
                <td style="text-align: right;">
                    <input type="text" class="form-control input-sell" value="<?= number_format($selling_price, 0, '', '.') ?>" 
                           style="width: 130px; text-align: right; font-weight: bold; color: #3182ce;" 
                           oninput="formatCurrency(this); calcFromSell(<?= $p['id'] ?>)">
                </td>
                
                <td style="text-align: center;">
                    <div style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                        <button class="btn-save-price" onclick="savePrice(<?= $p['id'] ?>)" 
                                style="background: #38a169; color: white; border: none; padding: 8px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s;">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu
                        </button>
                        
                        <a href="/lego_shop_php/adminprice/history/<?= $p['id'] ?>" 
                           style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; padding: 7px 12px; border-radius: 6px; text-decoration: none; transition: 0.2s;" 
                           title="Tra cứu theo lô hàng">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <?php 
        // Build link để khi chuyển trang vẫn giữ nguyên tìm kiếm & lọc danh mục
        $query_params = [];
        if (!empty($filters['keyword'])) $query_params['keyword'] = $filters['keyword'];
        if (!empty($filters['category_id'])) $query_params['category_id'] = $filters['category_id'];
        
        $base_query = http_build_query($query_params);
        $url_prefix = "/lego_shop_php/adminprice?" . (!empty($base_query) ? $base_query . "&" : "");
    ?>
    
    <div class="pagination">
        <a href="<?= $url_prefix ?>page=<?= $currentPage - 1 ?>" class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <?php 
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $currentPage + 2);
            for ($i = $startPage; $i <= $endPage; $i++): 
        ?>
            <a href="<?= $url_prefix ?>page=<?= $i ?>" class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="<?= $url_prefix ?>page=<?= $currentPage + 1 ?>" class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>
<?php endif; ?>
<script>
// HÀM FORMAT TIỀN TỆ KHI GÕ
function formatCurrency(input) {
    let rawValue = input.value.replace(/[^0-9]/g, '');
    if (rawValue === '') {
        input.value = '';
        return;
    }
    input.value = new Intl.NumberFormat('vi-VN').format(rawValue);
}

function calcFromMargin(id) {
    const row = document.getElementById('row_' + id);
    const wac = parseFloat(row.querySelector('.wac-value').getAttribute('data-wac')) || 0;
    const margin = parseFloat(row.querySelector('.input-margin').value) || 0;
    
    if (wac > 0) {
        // Tính giá bán mới, sau đó format có dấu chấm rồi đẩy vào input
        const newSell = Math.round(wac * (1 + (margin / 100)));
        row.querySelector('.input-sell').value = new Intl.NumberFormat('vi-VN').format(newSell);
    }
}

function calcFromSell(id) {
    const row = document.getElementById('row_' + id);
    const wac = parseFloat(row.querySelector('.wac-value').getAttribute('data-wac')) || 0;
    
    // Lột dấu chấm của Giá bán trước khi đem đi trừ chia WAC
    const rawSellString = row.querySelector('.input-sell').value.replace(/\./g, '');
    const sell = parseFloat(rawSellString) || 0;
    
    if (wac > 0) {
        row.querySelector('.input-margin').value = (((sell - wac) / wac) * 100).toFixed(1);
    }
}

async function savePrice(id) {
    const row = document.getElementById('row_' + id);
    const btn = row.querySelector('.btn-save-price');
    const margin = parseFloat(row.querySelector('.input-margin').value) || 0;
    
    // Lột dấu chấm của Giá bán để gửi về máy chủ bằng số nguyên chuẩn
    const rawSellString = row.querySelector('.input-sell').value.replace(/\./g, '');
    const sell = parseFloat(rawSellString) || 0;

    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    btn.style.background = '#718096';
    
    try {
        const res = await fetch('/lego_shop_php/adminprice/update', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ product_id: id, profit_margin: margin, selling_price: sell })
        });
        if ((await res.json()).success) {
            btn.innerHTML = '<i class="fa-solid fa-check"></i> Đã lưu';
            btn.style.background = '#3182ce';
            setTimeout(() => {
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu';
                btn.style.background = '#38a169';
            }, 1500);
        }
    } catch (e) { 
        alert("Lỗi kết nối máy chủ!"); 
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Lưu';
        btn.style.background = '#38a169';
    }
}
</script>