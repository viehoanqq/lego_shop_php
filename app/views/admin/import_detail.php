<style>
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); padding: 25px; margin-bottom: 30px; }
    .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 25px; border: 1px solid #e2e8f0; }
    .info-item label { color: #64748b; font-size: 13px; font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 5px; }
    .info-item div { color: #1e293b; font-size: 16px; font-weight: 700; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
    .badge-status { padding: 6px 15px; border-radius: 20px; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px; }
    .badge-draft { background: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    .badge-completed { background: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
    .btn-complete { background: #10b981; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.4); }
    .btn-complete:hover { background: #059669; transform: translateY(-2px); }
    .product-cell { display: flex; align-items: center; gap: 15px; }
    .img-product { width: 60px; height: 60px; min-width: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; }
</style>

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'completed'): ?>
    <div style="padding: 15px; border-radius: 8px; background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check"></i> Đã hoàn tất phiếu nhập! Hệ thống đã tính lại giá vốn (WAC), giá bán và cập nhật số lượng tồn kho.
    </div>
<?php endif; ?>

<div class="table-container">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
        <div>
            <h2 style="margin: 0; color: #1e293b; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-file-invoice" style="color: #3b82f6;"></i> CHI TIẾT PHIẾU NHẬP #PN-<?= $receipt['id'] ?>
            </h2>
        </div>
        <a href="/lego_shop_php/adminimport" style="color: #64748b; text-decoration: none; font-weight: 600; background: #f1f5f9; padding: 8px 15px; border-radius: 6px;">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>

    <?php if($receipt['status'] === 'draft'): ?>
        <div style="background: #fffbeb; border: 1px dashed #f59e0b; padding: 20px; border-radius: 8px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h4 style="color: #b45309; margin: 0 0 5px 0;"><i class="fa-solid fa-triangle-exclamation"></i> Phiếu nhập này đang ở trạng thái Bản nháp!</h4>
                <p style="margin: 0; color: #92400e; font-size: 14px;">Kho hàng và Giá vốn (WAC) <b>chưa</b> được cập nhật. Bạn cần kiểm tra lại thông tin bên dưới và nhấn "Hoàn tất".</p>
            </div>
            <a href="/lego_shop_php/adminimport/complete/<?= $receipt['id'] ?>" class="btn-complete" onclick="return confirm('Sau khi hoàn tất, hệ thống sẽ tự động cập nhật kho hàng và tính lại giá bán. Bạn không thể hoàn tác. Xác nhận?')">
                <i class="fa-solid fa-check-double"></i> XÁC NHẬN HOÀN TẤT
            </a>
        </div>
    <?php endif; ?>

    <div class="info-grid">
        <div class="info-item">
            <label>Thời gian tạo</label>
            <div><?= date('d/m/Y - H:i:s', strtotime($receipt['created_at'])) ?></div>
        </div>
        <div class="info-item">
            <label>Nhà cung cấp</label>
            <div><?= htmlspecialchars($receipt['supplier_name']) ?></div>
        </div>
        <div class="info-item">
            <label>Nhân viên lập phiếu</label>
            <div><?= htmlspecialchars($receipt['admin_name']) ?></div>
        </div>
        <div class="info-item">
            <label>Trạng thái</label>
            <?php if($receipt['status'] === 'completed'): ?>
                <span class="badge-status badge-completed"><i class="fa-solid fa-check"></i> Đã hoàn tất</span>
            <?php else: ?>
                <span class="badge-status badge-draft"><i class="fa-solid fa-pen"></i> Bản nháp</span>
            <?php endif; ?>
        </div>
        <div class="info-item" style="grid-column: span 2;">
            <label>Tổng giá trị đơn hàng</label>
            <div style="color: #e53e3e; font-size: 22px;"><?= number_format($receipt['total_amount'], 0, ',', '.') ?> VNĐ</div>
        </div>
    </div>

    <h3 style="margin: 30px 0 15px 0; color: #334155; font-size: 16px;">DANH SÁCH SẢN PHẨM NHẬP</h3>
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 35%;">Tên sản phẩm</th>
                <th style="text-align: center;">Số lượng nhập</th>
                <th style="text-align: right;">Giá nhập vào (đ)</th>
                <th style="text-align: right;">Thành tiền (đ)</th>
                
                <?php if($receipt['status'] === 'completed'): ?>
                    <th style="text-align: right; background: #f0fdf4; color: #166534;">Giá WAC sau nhập</th>
                    <th style="text-align: right; background: #f0fdf4; color: #166534;">Giá bán mới</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($details as $d): ?>
            <tr>
                <td>
                    <div class="product-cell">
                        <img src="/lego_shop_php/public/assets/images/<?= !empty($d['main_image']) ? $d['main_image'] : 'default.jpg' ?>" 
                             class="img-product" 
                             onerror="this.src='https://placehold.co/60x60?text=LEGO'">
                        <div>
                            <div style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($d['product_name']) ?></div>
                            <div style="font-size: 11px; color: #a0aec0; letter-spacing: 0.5px;">SKU: <?= strtoupper($d['sku']) ?></div>
                        </div>
                    </div>
                </td>
                <td style="text-align: center; font-weight: 700;"><?= number_format($d['quantity']) ?></td>
                <td style="text-align: right; font-weight: 600; color: #334155;"><?= number_format($d['price'], 0, ',', '.') ?>đ</td>
                <td style="text-align: right; font-weight: 700; color: #2b6cb0;">
                    <?= number_format($d['quantity'] * $d['price'], 0, ',', '.') ?>đ
                </td>

                <?php if($receipt['status'] === 'completed'): ?>
                    <td style="text-align: right; font-weight: 600; color: #166534;">
                        <?= number_format($d['calculated_average_price'], 0, ',', '.') ?>đ
                    </td>
                    <td style="text-align: right; font-weight: 800; color: #dc2626;">
                        <?= number_format($d['calculated_selling_price'], 0, ',', '.') ?>đ
                    </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>