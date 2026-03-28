<style>
    /* ... (CSS của bạn giữ nguyên 100%) ... */
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
    
    /* MỚI THÊM CHO NÚT SỬA */
    .btn-edit { background: #f59e0b; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: 0.3s; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.4); margin-right: 10px;}
    .btn-edit:hover { background: #d97706; transform: translateY(-2px); }

    .product-cell { display: flex; align-items: center; gap: 15px; }
    .img-product { width: 60px; height: 60px; min-width: 60px; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0; background: #fff; }

    /* // In ấn: Ẩn các phần không cần thiết khi in */
    /* --- CSS DÀNH RIÊNG CHO BẢN IN --- */
    @media print {
        /* Giấu menu bên trái, phần header admin và các nút bấm */
        .side-bar, .admin-header, .action-buttons { 
            display: none !important; 
        }
        /* Mở rộng toàn bộ nội dung chiếm 100% tờ giấy A4 */
        .main-content { 
            margin-left: 0 !important; 
            width: 100% !important; 
            padding: 0 !important; 
            background: #fff !important;
        }
        /* Bỏ đổ bóng, bỏ viền của các khối container để tiết kiệm mực in */
        .table-container, .summary-box { 
            box-shadow: none !important; 
            border: 1px solid #000 !important; 
            margin: 0 !important; 
            padding: 15px !important;
        }
        /* Hiện phần Header riêng của hóa đơn (logo, tên cửa hàng...) */
        .print-invoice-header { display: block !important; }
        body { background: #fff !important; }
    }
    
    /* Ẩn header hóa đơn khi đang xem trên màn hình web */
    .print-invoice-header { display: none; }
</style>

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'completed'): ?>
    <div style="padding: 15px; border-radius: 8px; background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check"></i> Đã hoàn tất phiếu nhập! Hệ thống đã tính lại giá vốn (WAC), giá bán và cập nhật số lượng tồn kho.
    </div>
<?php endif; ?>
<?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
    <div style="padding: 15px; border-radius: 8px; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; margin-bottom: 20px; font-weight: 600;">
        <i class="fa-solid fa-circle-check"></i> Đã cập nhật bản nháp thành công!
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

   <div class="action-buttons" style="display: flex; gap: 15px; align-items: center; margin-bottom: 25px; padding: 15px; background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05);">
    
    <a href="/lego_shop_php/adminimport" style="background: #f1f5f9; color: #475569; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; border: 1px solid #cbd5e1;">
        <i class="fa-solid fa-arrow-left"></i> Quay lại
    </a>

    <?php if($receipt['status'] === 'draft'): ?>
        
        <a href="/lego_shop_php/adminimport/edit/<?= $receipt['id'] ?>" 
           style="background: #f59e0b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; transition: 0.2s;">
            <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa phiếu
        </a>

        <form action="/lego_shop_php/adminimport/complete/<?= $receipt['id'] ?>" method="POST" style="margin: 0;" onsubmit="return confirm('XÁC NHẬN HOÀN TẤT?\n\nSau khi hoàn tất, kho hàng sẽ được cộng thêm và bạn KHÔNG THỂ chỉnh sửa phiếu này nữa.');">
            <button type="submit" style="background: #10b981; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: 0.2s;">
                <i class="fa-solid fa-check-double"></i> Hoàn tất phiếu nhập
            </button>
        </form>
        
    <?php else: ?>
        <a href="/lego_shop_php/adminimport/print/<?= $receipt['id'] ?>" target="_blank" 
           style="display: inline-block; background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; transition: 0.2s;">
            <i class="fa-solid fa-print"></i> In phiếu nhập
        </a>
        
        <span style="color: #10b981; font-weight: 700; margin-left: auto;">
            <i class="fa-solid fa-circle-check"></i> Phiếu nhập đã hoàn tất (Không thể sửa)
        </span>
    <?php endif; ?>

</div>

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