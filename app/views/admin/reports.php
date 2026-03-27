<style>
    /* CSS Cấu trúc bảng chuẩn của hệ thống */
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-top: 10px; max-height: 65vh; overflow-y: auto; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { position: sticky; top: 0; z-index: 10; background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    
    /* CSS Cấu trúc ảnh sản phẩm */
    .product-cell { display: flex; align-items: center; gap: 15px; }
    .img-product { width: 50px; height: 50px; min-width: 50px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; }
</style>

<div class="admin-header" style="margin-bottom: 20px; padding: 10px;">
    <div>
        <h2 style="margin:0; color: #1a202c;"><i class="fa-solid fa-chart-line" style="color: #3b82f6; margin-right: 10px;"></i> Báo Cáo Nhập - Xuất - Tồn</h2>
        <small style="color: #718096;">Dữ liệu biến động kho theo khoảng thời gian và thời điểm</small>
    </div>
</div>

<div style="background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
    
    <form id="filterForm" action="/lego_shop_php/adminreport" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 200px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Loại sản phẩm</label>
            <select name="category_id" class="form-control" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
                <option value="all">Tất cả sản phẩm</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $filters['category_id'] == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Từ ngày (Khoảng t.gian)</label>
            <input type="date" name="start_date" value="<?= $filters['start_date'] ?>" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
        </div>

        <div style="flex: 1; min-width: 150px;">
            <label style="font-weight: 600; font-size: 13px; color: #475569; display: block; margin-bottom: 5px;">Đến ngày (Mốc tra cứu tồn)</label>
            <input type="date" name="end_date" value="<?= $filters['end_date'] ?>" onchange="this.form.submit()" class="form-control" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; outline: none; cursor: pointer;">
        </div>

        <div>
            <a href="/lego_shop_php/adminreport" style="display: inline-flex; align-items: center; justify-content: center; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; text-decoration: none; padding: 0 20px; border-radius: 6px; font-weight: 600; height: 42px; transition: 0.2s;">
                <i class="fa-solid fa-rotate-right" style="margin-right: 8px;"></i> Xóa lọc
            </a>
        </div>
    </form>
</div>
<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 60px; text-align: center;">Mã</th>
                <th style="width: 350px;">Sản phẩm</th>
                <th style="text-align: center; color: #059669; background: #f0fdf4;">Nhập trong kỳ</th>
                <th style="text-align: center; color: #b45309; background: #fffbeb;">Xuất trong kỳ</th>
                <th style="text-align: center; color: #4338ca; background: #e0e7ff; font-weight: 800;">TỒN KHO THỜI ĐIỂM NÀY</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($reports)): ?>
                <?php foreach($reports as $r): ?>
                <tr class="table-row-hover">
                    <td style="text-align: center; font-weight: 600; color: #64748b;">#<?= $r['id'] ?></td>
                    
                    <td>
                        <div class="product-cell">
                            <img src="/lego_shop_php/public/assets/images/<?= !empty($r['main_image']) ? $r['main_image'] : 'default.jpg' ?>" 
                                 class="img-product" onerror="this.src='https://placehold.co/60x60?text=LEGO'">
                            <div>
                                <div style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($r['name']) ?></div>
                                <div style="font-size: 11px; color: #a0aec0; letter-spacing: 0.5px;">SKU: <?= strtoupper($r['sku']) ?></div>
                            </div>
                        </div>
                    </td>
                    
                    <td style="text-align: center; font-weight: 700; color: #059669; font-size: 15px;">
                        <?= $r['period_in'] > 0 ? '+'.$r['period_in'] : '0' ?>
                    </td>
                    <td style="text-align: center; font-weight: 700; color: #b45309; font-size: 15px;">
                        <?= $r['period_out'] > 0 ? '-'.$r['period_out'] : '0' ?>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: #e0e7ff; color: #4338ca; padding: 6px 15px; border-radius: 20px; font-weight: 800; font-size: 16px;">
                            <?= $r['stock_at_time'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px; color: #64748b;">Không có dữ liệu trong khoảng thời gian này.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>