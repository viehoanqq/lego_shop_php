<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<?php 
    // Lấy ngày hôm nay định dạng YYYY-MM-DD để truyền vào Link
    $today = date('Y-m-d'); 
?>

<style>
    /* ==========================================
       GIAO DIỆN HIỆN ĐẠI (MODERN UI) - TONE XANH ĐỒNG NHẤT
       ========================================== */
    .dashboard-wrapper { 
        font-family: 'Inter', 'Segoe UI', sans-serif; 
        background: transparent; 
        padding: 10px; 
    }
    
    /* CARDS THỐNG KÊ */
    .stat-top { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 30px; }
    
    .stat-card { 
        background: #fff; 
        padding: 24px; 
        border-radius: 16px; 
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.02), 0 8px 10px -6px rgba(0, 0, 0, 0.01); 
        transition: all 0.3s ease; 
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        text-decoration: none; 
        color: inherit; 
    }
    a.stat-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 20px 25px -5px rgba(59, 130, 246, 0.08), 0 8px 10px -6px rgba(59, 130, 246, 0.04); 
        border-color: #3b82f6;
    }
    
    .stat-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
    .stat-icon { 
        width: 54px; height: 54px; 
        border-radius: 14px; 
        display: flex; align-items: center; justify-content: center; font-size: 24px; 
        background: #eff6ff !important; 
        color: #3b82f6 !important; 
    }
    
    .stat-info h3 { font-size: 14px; color: #64748b; font-weight: 600; margin: 0 0 8px 0; text-transform: uppercase; letter-spacing: 0.5px;}
    .stat-value { font-size: 32px; font-weight: 800; color: #444444; line-height: 1.2;} 
    .stat-note { font-size: 13px; font-weight: 600; color: #64748b; display: flex; align-items: center; gap: 6px; margin-top: auto;}

    /* KHU VỰC CHART VÀ STATUS */
    .dashboard-middle { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 30px; }
    .chart-card { 
        background: #fff; padding: 24px; border-radius: 16px; 
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02); 
    }
    .card-title { 
        font-size: 17px; font-weight: 700; margin-bottom: 25px; 
        display: flex; justify-content: space-between; align-items: center; color: #1e293b;
    }

    /* BIỂU ĐỒ CỘT */
    .bar-chart-wrapper { display: flex; align-items: flex-end; justify-content: space-between; height: 200px; padding: 10px 10px 0; border-bottom: 2px solid #f1f5f9; }
    .bar-col { display: flex; flex-direction: column; align-items: center; gap: 10px; width: 40px; height: 100%; justify-content: flex-end; position: relative; }
    .bar-fill { 
        width: 100%; 
        background: linear-gradient(180deg, #3b82f6 0%, #60a5fa 100%); 
        border-radius: 6px 6px 0 0; transition: all 0.5s ease; cursor: pointer; opacity: 0.9;
    }
    .bar-fill:hover { opacity: 1; filter: brightness(1.1); transform: scaleX(1.1); }
    .bar-label { font-size: 12px; font-weight: 700; color: #64748b; margin-top: 5px; }
    .bar-tooltip { 
        position: absolute; top: -35px; font-size: 12px; font-weight: 700; color: #fff; 
        background: #0f172a; padding: 6px 10px; border-radius: 6px; opacity: 0; 
        transition: 0.2s; white-space: nowrap; pointer-events: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .bar-col:hover .bar-tooltip { opacity: 1; top: -40px; }

    /* TRẠNG THÁI GIAO HÀNG */
    .status-bars { width: 100%; height: 16px; display: flex; border-radius: 8px; overflow: hidden; margin: 30px 0 20px; background: #f1f5f9;}
    .bar-delivered { background: #3b82f6; } 
    .bar-shipping { background: #93c5fd; } 
    .bar-pending { background: #e2e8f0; } 
    .bar-cancelled { background: #ffdcdc; } 
    
    .bar-legend { display: flex; flex-direction: column; gap: 16px; }
    .bar-legend-item { display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; color: #475569; align-items: center;}
    .bar-dot { display: inline-block; width: 12px; height: 12px; border-radius: 4px; margin-right: 10px; }

    /* BẢNG ĐƠN HÀNG */
    .recent-orders-card { 
        background: #fff; padding: 24px; border-radius: 16px; 
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
    }
    .mini-table { width: 100%; border-collapse: collapse; }
    .mini-table th { text-align: left; padding: 16px 12px; border-bottom: 2px solid #f1f5f9; color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px;}
    .mini-table tr.clickable-row { cursor: pointer; transition: 0.2s; }
    .mini-table tr.clickable-row:hover { background-color: #f8fafc; }
    .mini-table td { padding: 18px 12px; border-bottom: 1px solid #f8fafc; vertical-align: middle; color: #1e293b; font-size: 14px; font-weight: 600;}

    /* ====================================================
       STYLE BADGE ĐỒNG BỘ TRẠNG THÁI (Solid Shadow vs Outline)
       ==================================================== */
    .badge-status-dashboard { 
        padding: 6px 12px; 
        border-radius: 20px; 
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase; 
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-width: 120px; 
        letter-spacing: 0.5px;
        gap: 6px;
    }

    /* NHÓM QUAN TRỌNG (Solid + Shadow) */
    .badge-solid-red { background: #e53e3e; color: #ffffff; box-shadow: 0 4px 10px rgba(229, 62, 62, 0.3); border: 1px solid transparent; }
    .badge-solid-orange { background: #dd6b20; color: #ffffff; box-shadow: 0 4px 10px rgba(221, 107, 32, 0.3); border: 1px solid transparent; }

    /* NHÓM BÌNH THƯỜNG / ĐÃ XONG (Outline) */
    .badge-outline-green { background: #ffffff; color: #2f855a; border: 1px solid #6ee7b7; }
    .badge-outline-blue { background: #ffffff; color: #3182ce; border: 1px solid #90cdf4; }
    .badge-outline-teal { background: #ffffff; color: #319795; border: 1px solid #81e6d9; }
    
    @media(max-width: 900px) { .dashboard-middle { grid-template-columns: 1fr; } }
</style>

<div class="dashboard-wrapper">
    <div class="stat-top">
        <a href="/lego_shop_php/adminorder?search=&status=all&date_from=<?= $today ?>&date_to=<?= $today ?>&sort=date_desc" class="stat-card" title="Xem tất cả đơn hôm nay">
            <div class="stat-header">
                <div class="stat-info">
                    <h3>Tổng đơn hôm nay</h3>
                    <div class="stat-value"><?= number_format($tong_don_hom_nay) ?> <span style="font-size: 16px; color: #94a3b8; font-weight: 600;">đơn</span></div>
                </div>
                <div class="stat-icon"><i class="fa-solid fa-file-invoice"></i></div>
            </div>
            <div class="stat-note"><i class="fa-solid fa-bolt" style="color: #94a3b8;"></i> Click để xem chi tiết</div>
        </a>
        
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-info">
                    <h3>Doanh thu hôm nay</h3>
                    <div class="stat-value"><?= number_format($tong_thu_nhap, 0, ',', '.') ?>đ</div>
                </div>
            </div>
            <div class="stat-note"><i class="fa-solid fa-check-circle" style="color: #94a3b8;"></i> Từ các đơn đã giao thành công</div>
        </div>

        <a href="/lego_shop_php/adminorder?search=&status=cancelled&date_from=<?= $today ?>&date_to=<?= $today ?>&sort=date_desc" class="stat-card" title="Xem các đơn bị hủy hôm nay">
            <div class="stat-header">
                <div class="stat-info">
                    <h3>Đơn hàng bị hủy</h3>
                    <div class="stat-value"><?= number_format($don_huy_tra) ?> <span style="font-size: 16px; color: #94a3b8; font-weight: 600;">đơn</span></div>
                </div>
            </div>
            <div class="stat-note"><i class="fa-solid fa-triangle-exclamation" style="color: #94a3b8;"></i> Click để kiểm tra</div>
        </a>
    </div>

    <div class="dashboard-middle">
        <div class="chart-card">
            <div class="card-title">
                <span style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-chart-simple" style="color: #94a3b8; padding: 8px; background: #eff6ff; border-radius: 8px;"></i> Hiệu suất doanh thu (7 ngày)</span>
            </div>
            
            <div class="bar-chart-wrapper">
                <?php if (!empty($doanh_thu_7_ngay)): ?>
                    <?php foreach($doanh_thu_7_ngay as $data): ?>
                        <div class="bar-col">
                            <div class="bar-tooltip"><?= number_format($data['amount'], 0, ',', '.') ?>đ</div>
                            <div class="bar-fill" style="height: <?= $data['percent'] ?>%;"></div>
                            <div class="bar-label"><?= $data['day'] ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #94a3b8; width: 100%; text-align: center; margin: auto;">Chưa có dữ liệu doanh thu</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="chart-card">
            <div class="card-title">
                <span style="display: flex; align-items: center; gap: 10px;"><i class="fa-solid fa-truck-fast" style="color: #94a3b8; padding: 8px; background: #eff6ff; border-radius: 8px;"></i> Đơn hàng hôm nay</span>
                <span style="font-size: 12px; background: #eff6ff; padding: 4px 10px; border-radius: 20px; color: #94a3b8; font-weight: 800;">Tổng: <?= $tong_don_trang_thai ?? 0 ?></span>
            </div>
            
            <div class="status-bars">
                <div class="bar-delivered" style="width: <?= $ty_le_da_giao ?? 0 ?>%;" title="Đã giao: <?= $ty_le_da_giao ?? 0 ?>%"></div>
                <div class="bar-shipping" style="width: <?= $ty_le_dang_giao ?? 0 ?>%;" title="Đang giao: <?= $ty_le_dang_giao ?? 0 ?>%"></div>
                <div class="bar-pending" style="width: <?= $ty_le_cho_xu_ly ?? 0 ?>%;" title="Chờ xử lý: <?= $ty_le_cho_xu_ly ?? 0 ?>%"></div>
                <div class="bar-cancelled" style="width: <?= $ty_le_da_huy ?? 0 ?>%;" title="Đã hủy: <?= $ty_le_da_huy ?? 0 ?>%"></div>
            </div>
            
            <div class="bar-legend">
                <div class="bar-legend-item">
                    <span><i class="bar-dot" style="background:#3b82f6"></i>Đã giao thành công</span>
                    <span style="font-weight: 800; color: #1e293b;"><?= $ty_le_da_giao ?? 0 ?>%</span>
                </div>
                <div class="bar-legend-item">
                    <span><i class="bar-dot" style="background:#93c5fd"></i>Đang trên đường giao</span>
                    <span style="font-weight: 800; color: #1e293b;"><?= $ty_le_dang_giao ?? 0 ?>%</span>
                </div>
                <div class="bar-legend-item">
                    <span><i class="bar-dot" style="background:#e2e8f0"></i>Đang chờ xử lý</span>
                    <span style="font-weight: 800; color: #1e293b;"><?= $ty_le_cho_xu_ly ?? 0 ?>%</span>
                </div>
                <div class="bar-legend-item">
                    <span><i class="bar-dot" style="background:#ffdcdc"></i>Đã hủy đơn</span>
                    <span style="font-weight: 800; color: #1e293b;"><?= $ty_le_da_huy ?? 0 ?>%</span>
                </div>  
            </div>
        </div>
    </div>

    <div class="recent-orders-card">
        <div class="card-title" style="margin-bottom: 10px;">
            <span style="display: flex; align-items: center; gap: 10px;"><i class="fa-regular fa-clipboard" style="color: #3b82f6; padding: 8px; background: #eff6ff; border-radius: 8px;"></i> Đơn hàng mới nhất</span>
            <a href="/lego_shop_php/adminorder" style="font-size: 13px; color: #fff; background: #3b82f6; text-decoration: none; font-weight: 600; padding: 6px 14px; border-radius: 8px; transition: 0.2s;">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        
        <div style="overflow-x: auto;">
            <table class="mini-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách hàng</th>
                        <th>Thanh toán</th>
                        <th>Tổng tiền</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: right;">Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $badge_map = [
                            'pending'   => ['lbl' => 'Chờ xử lý',   'class' => 'badge-solid-orange', 'icon' => 'fa-hourglass-half'],
                            'confirmed' => ['lbl' => 'Đã xác nhận', 'class' => 'badge-outline-teal', 'icon' => 'fa-box-open'],
                            'shipping'  => ['lbl' => 'Đang giao',   'class' => 'badge-outline-blue', 'icon' => 'fa-truck-fast'],
                            'delivered' => ['lbl' => 'Thành công',  'class' => 'badge-outline-green', 'icon' => 'fa-check-double'],
                            'cancelled' => ['lbl' => 'Đã hủy',      'class' => 'badge-solid-red', 'icon' => 'fa-ban']
                        ];
                    ?>
                    
                    <?php if(!empty($don_moi_nhat)): ?>
                        <?php foreach($don_moi_nhat as $don): 
                            $st = $don['trang_thai'] ?? 'pending';
                            $badge = $badge_map[$st] ?? $badge_map['pending'];
                            $pm = $don['payment_method'] ?? 'cash';
                        ?>
                        <tr class="clickable-row" onclick="window.location.href='/lego_shop_php/adminorder/detail/<?= $don['id'] ?>'">
                            <td>
                                <span style="font-family: monospace; font-size: 15px; font-weight: 800; color: #3b82f6;">
                                    #<?= $don['id'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($don['khach_hang']) ?></td>
                            <td style="color: #475569; font-weight: 600;">
                                <?= ($pm == 'cash' ? 'Tiền mặt' : 'Chuyển khoản') ?>
                            </td>
                            <td style="font-weight: 800; font-size: 15px; color: #1e293b;"><?= number_format($don['tong_tien'], 0, ',', '.') ?>đ</td>
                            <td style="text-align: center;">
                                <span class="badge-status-dashboard <?= $badge['class'] ?>">
                                    <i class="fa-solid <?= $badge['icon'] ?>"></i> <?= $badge['lbl'] ?>
                                </span>
                            </td>
                            <td style="text-align: right; color: #64748b; font-size: 13px; font-weight: 600;"><i class="fa-regular fa-clock"></i> <?= $don['thoi_gian'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8; font-weight: 600;">Hôm nay chưa có đơn hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>