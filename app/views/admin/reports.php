<style>
    /* Ép kích thước ảnh chuẩn để không bị vỡ layout */
    .img-report { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; }
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); max-height: 65vh; overflow-y: auto; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { position: sticky; top: 0; background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; z-index: 5; font-weight: 700; letter-spacing: 0.5px; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }

    /* BỐ CỤC FORM LỌC CHIA 2 BÊN */
    .filter-split-layout { display: flex; gap: 40px; flex-wrap: wrap; }
    .filter-col { flex: 1; min-width: 300px; display: flex; flex-direction: column; gap: 15px; }
    .filter-col-right { border-left: 1px dashed #cbd5e1; padding-left: 40px; justify-content: space-between; }
    .filter-row { display: flex; gap: 15px; width: 100%; }
    .filter-group { flex: 1; }
    
    .search-wrapper { position: relative; width: 100%; }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
    .search-wrapper input { width: 100%; height: 42px; padding: 0 15px 0 35px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; transition: 0.2s; font-size: 13px; }
    .search-wrapper input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

    .form-control-ui { width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 8px; padding: 0 12px; font-size: 13px; outline: none; transition: 0.2s; color: #1e293b; background: #fff; }
    .form-control-ui:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .filter-label { font-weight: 700; font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px; display: block; }

    /* CÁC NÚT TÁC VỤ */
    .btn-submit-filter { background: #3b82f6; color: #fff; height: 42px; border: none; padding: 0 25px; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; }
    .btn-submit-filter:hover { background: #2563eb; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2); }
    
    .btn-outline-danger { background: transparent; color: #ef4444; border: 1px solid #ef4444; height: 42px; width: 42px; border-radius: 8px; display: inline-flex; justify-content: center; align-items: center; cursor: pointer; transition: 0.2s; text-decoration: none; }
    .btn-outline-danger:hover { background: #fef2f2; }

    .btn-detail { background: #eff6ff; color: #3b82f6; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 13px; font-weight: 700; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; border: 1px solid transparent; }
    .btn-detail:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.2); transform: translateY(-2px); }

    /* THANH LỌC NHANH BÊN DƯỚI */
    .btn-quick { background: #f8fafc; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; color: #475569; cursor: pointer; transition: 0.2s; }
    .btn-quick:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }
    
    @media(max-width: 900px) {
        .filter-col-right { border-left: none; padding-left: 0; border-top: 1px dashed #cbd5e1; padding-top: 20px; }
    }
</style>



<form method="GET" action="/lego_shop_php/adminreport" id="reportForm" style="background: #fff; padding: 25px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #f1f5f9;">
    
    <div class="filter-split-layout">
        <div class="filter-col">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Tìm kiếm sản phẩm</label>
                    <div class="search-wrapper">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" name="keyword" value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" placeholder="Tên sản phẩm hoặc mã SKU..." onchange="this.form.submit()">
                    </div>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Danh mục LEGO</label>
                    <select name="category_id" class="form-control-ui" style="cursor: pointer;" onchange="this.form.submit()">
                        <option value="all">-- Tất cả danh mục --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= ($filters['category_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="filter-col filter-col-right">
            <div class="filter-row">
                <div class="filter-group">
                    <label class="filter-label">Từ ngày</label>
                    <input type="date" name="start_date" id="start_date" class="form-control-ui" value="<?= $filters['start_date'] ?? '' ?>">
                </div>
                <div class="filter-group">
                    <label class="filter-label">Đến ngày (Mốc chốt)</label>
                    <input type="date" name="end_date" id="end_date" class="form-control-ui" value="<?= $filters['end_date'] ?? '' ?>">
                </div>
            </div>
            
            <div class="filter-row" style="align-items: center; justify-content: space-between; margin-top: 10px;">
                <div style="display: flex; gap: 8px; align-items: center;">
                    <span style="font-size: 11px; color: #94a3b8; font-weight: 700; text-transform: uppercase;"> Chọn nhanh:</span>
                    <button type="button" class="btn-quick" onclick="quickDate('7')">7 ngày trước</button>
                    <button type="button" class="btn-quick" onclick="quickDate('30')">30 ngày trước</button>
                    <button type="button" class="btn-quick" onclick="quickDate('month')">Tháng này</button>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-submit-filter">
                        <i class="fa-solid fa-filter"></i> LỌC
                    </button>
                    <a href="/lego_shop_php/adminreport" class="btn-outline-danger" title="Xóa bộ lọc">
                        <i class="fa-solid fa-rotate-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 35%;">Sản phẩm & Thông tin</th>
                <th style="text-align: center;">Tồn đầu kỳ</th>
                <th style="text-align: center;">Nhập kho (+)</th>
                <th style="text-align: center;">Xuất bán (-)</th>
                <th style="text-align: center; background: #eff6ff; color: #1e40af;">Tồn Cuối Kỳ</th>
                <th style="text-align: center;">Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($reports)): ?>
                <?php foreach($reports as $r): 
                    $opening = $r['stock_at_time'] - $r['period_in'] + $r['period_out'];
                ?>
                <tr style="transition: 0.2s;">
                    <td>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <img src="/lego_shop_php/public/assets/images/<?= !empty($r['main_image']) ? $r['main_image'] : 'default.jpg' ?>" class="img-report" onerror="this.src='https://placehold.co/50x50?text=LEGO'">
                            <div>
                                <div style="font-weight: 700; color: #1e293b; font-size: 14px; margin-bottom: 4px;"><?= htmlspecialchars($r['name']) ?></div>
                                <span style="background: #f1f5f9; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 700; color: #64748b; border: 1px solid #e2e8f0;">SKU: <?= strtoupper($r['sku']) ?></span>
                            </div>
                        </div>
                    </td>
                    <td style="text-align: center; color: #64748b; font-weight: 600; font-size: 15px;"><?= number_format($opening) ?></td>
                    <td style="text-align: center;"><span style="color:#10b981; font-weight:800; font-size: 15px;">+<?= number_format($r['period_in']) ?></span></td>
                    <td style="text-align: center;"><span style="color:#f43f5e; font-weight:800; font-size: 15px;">-<?= number_format($r['period_out']) ?></span></td>
                    <td style="text-align: center; background: #fafafa;"><b style="font-size: 18px; color: #1e40af;"><?= number_format($r['stock_at_time']) ?></b></td>
                    <td style="text-align: center;">
                        <a href="/lego_shop_php/adminreport/productDetail/<?= $r['id'] ?>?start=<?= $filters['start_date'] ?>&end=<?= $filters['end_date'] ?>" class="btn-detail">
                            <i class="fa-solid fa-chart-line"></i> Phân tích
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 60px; color: #94a3b8; font-size: 14px;">
                        <i class="fa-solid fa-folder-open" style="font-size: 32px; margin-bottom: 10px; display: block; opacity: 0.5;"></i>
                        Không có dữ liệu trong khoảng thời gian hoặc điều kiện lọc này.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function quickDate(type) {
    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');
    const today = new Date();
    let sDate = new Date();
    
    if (type === '7') {
        sDate.setDate(today.getDate() - 7);
    } else if (type === '30') {
        sDate.setDate(today.getDate() - 30);
    } else if (type === 'month') {
        sDate = new Date(today.getFullYear(), today.getMonth(), 1);
    }
    
    // Format YYYY-MM-DD
    const formatYYYYMMDD = (d) => {
        return d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2);
    }

    start.value = formatYYYYMMDD(sDate);
    end.value = formatYYYYMMDD(today);
    
    // Tự động submit form khi bấm nút lọc nhanh
    document.getElementById('reportForm').submit();
}
</script>