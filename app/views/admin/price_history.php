<style>
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 25px; margin-bottom: 30px; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 20px; }
    .lego-table th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
    .product-header { display: flex; gap: 20px; align-items: center; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
</style>

<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #1e293b; font-weight: 800;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #3b82f6; margin-right: 10px;"></i> TRA CỨU GIÁ THEO LÔ HÀNG
        </h2>
        <a href="/lego_shop_php/adminprice" style="color: #64748b; text-decoration: none; font-weight: 600; background: #f1f5f9; padding: 8px 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <div class="product-header">
        <img src="/lego_shop_php/public/assets/images/<?= !empty($product['main_image']) ? $product['main_image'] : 'default.jpg' ?>" 
             style="width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 1px solid #cbd5e1;" 
             onerror="this.src='https://placehold.co/80x80?text=LEGO'">
        <div>
            <h3 style="margin: 0 0 5px 0; color: #1e293b;"><?= htmlspecialchars($product['name']) ?></h3>
            <div style="color: #64748b; font-size: 14px; font-weight: 600;">Mã SKU: <span style="color: #3b82f6;"><?= strtoupper($product['sku']) ?></span></div>
        </div>
    </div>

    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 120px; text-align: center;">Mã Lô (Phiếu)</th>
                <th>Thời gian nhập</th>
                <th style="text-align: center;">SL Nhập</th>
                <th style="text-align: right;">Giá nhập của Lô</th>
                <th style="text-align: right; background: #f0fdf4; color: #166534;">Giá Vốn WAC (Tích lũy)</th>
                <th style="text-align: center; background: #eff6ff; color: #1e40af;">% Lợi nhuận áp dụng</th>
                <th style="text-align: right; background: #fffbeb; color: #b45309;">Giá Bán Thiết Lập</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($history)): ?>
                <?php foreach($history as $h): 
                    // Tính ngược % lợi nhuận tại thời điểm đó: ((Giá bán - Giá vốn) / Giá vốn) * 100
                    $margin = 0;
                    if ($h['wac_price'] > 0) {
                        $margin = (($h['selling_price'] - $h['wac_price']) / $h['wac_price']) * 100;
                    }
                ?>
                <tr class="table-row-hover">
                    <td style="text-align: center;">
                        <a href="/lego_shop_php/adminimport/detail/<?= $h['receipt_id'] ?>" style="background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 4px; font-weight: 700; font-family: monospace; text-decoration: none;">
                            #PN-<?= $h['receipt_id'] ?>
                        </a>
                    </td>
                    <td style="color: #475569; font-size: 14px; font-weight: 500;">
                        <?= date('d/m/Y - H:i', strtotime($h['import_date'])) ?>
                    </td>
                    <td style="text-align: center; font-weight: 700; color: #334155;">
                        +<?= number_format($h['import_qty']) ?>
                    </td>
                    <td style="text-align: right; font-weight: 600; color: #64748b;">
                        <?= number_format($h['batch_import_price'], 0, ',', '.') ?>đ
                    </td>
                    
                    <td style="text-align: right; font-weight: 700; color: #166534; background: #f0fdf4;">
                        <?= number_format($h['wac_price'], 0, ',', '.') ?>đ
                    </td>
                    <td style="text-align: center; font-weight: 800; color: #1e40af; background: #eff6ff;">
                        <?= number_format($margin, 1) ?>%
                    </td>
                    <td style="text-align: right; font-weight: 800; color: #dc2626; background: #fffbeb;">
                        <?= number_format($h['selling_price'], 0, ',', '.') ?>đ
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 500;">
                        Sản phẩm này chưa có lịch sử nhập lô hàng nào.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>