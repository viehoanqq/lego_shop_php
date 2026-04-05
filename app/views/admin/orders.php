<style>
    /* ===== CSS GIAO DIỆN BẢNG ===== */
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); max-height: 70vh; overflow-y: auto; margin-top: 10px; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { position: sticky; top: 0; z-index: 10; background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    
    .btn-action { text-decoration: none; padding: 6px 12px; border-radius: 6px; transition: 0.2s; font-weight: 600; color: #3182ce; background: transparent; border: 1px solid #3182ce; display: inline-flex; align-items: center; gap: 5px;}
    .btn-action:hover { background: #ebf8ff; }

    /* ===== BỘ LỌC ===== */
    .filter-bar { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; border: 1px solid #edf2f7; }
    .filter-group { display: flex; flex-direction: column; flex: 1; min-width: 160px; }
    .filter-group label { font-size: 12px; font-weight: 700; color: #64748b; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
    
    .search-wrapper { position: relative; display: flex; width: 100%; }
    .filter-control { padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; width: 100%; box-sizing: border-box; transition: 0.2s; background: #f8fafc; }
    .filter-control:focus { border-color: #3182ce; background: #fff; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }

    .search-wrapper .filter-control { padding-right: 40px; }
    .search-btn { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: #3182ce; border: none; width: 32px; height: 32px; border-radius: 6px; cursor: pointer; color: white; transition: 0.2s; display: flex; justify-content: center; align-items: center; }
    .search-btn:hover { background: #2b6cb0; }

    .btn-submit-filter { background: #3182ce; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; height: 40px; display: flex; align-items: center; gap: 8px; }
    .btn-submit-filter:hover { background: #2b6cb0; }

    .btn-reset { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; padding: 10px; border-radius: 8px; text-decoration: none; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; box-sizing: border-box; transition: 0.2s; }
    .btn-reset:hover { background: #fee2e2; color: #ef4444; border-color: #fca5a5; }

    /* ====================================================
       STYLE BADGE TRẠNG THÁI (Phân chia Solid & Outline) 
       ==================================================== */
    .status-badge {
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-width: 130px; /* Đảm bảo bằng nhau */
        gap: 6px;
        letter-spacing: 0.5px;
    }

    /* NHÓM 1: TRẠNG THÁI QUAN TRỌNG CẦN CHÚ Ý (Solid + Shadow) */
    .badge-solid-red {
        background: #e53e3e;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(229, 62, 62, 0.35);
        border: 1px solid transparent;
    }
    .badge-solid-orange {
        background: #dd6b20;
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(221, 107, 32, 0.35);
        border: 1px solid transparent;
    }

    /* NHÓM 2: TRẠNG THÁI BÌNH THƯỜNG / ĐÃ XONG (Outline + Chữ màu) */
    .badge-outline-green {
        background: #ffffff;
        color: #059669;
        border: 1px solid #6ee7b7;
    }
    .badge-outline-blue {
        background: #ffffff;
        color: #3182ce;
        border: 1px solid #90cdf4;
    }
    .badge-outline-teal {
        background: #ffffff;
        color: #319795;
        border: 1px solid #81e6d9;
    }
    /* ===== PAGINATION CSS ===== */
.pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; padding-bottom: 20px;}
.page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #475569; background: #fff; font-weight: 600; transition: 0.2s; }
.page-link:hover { background: #f8fafc; color: #3182ce; border-color: #3182ce; }
.page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; box-shadow: 0 4px 10px rgba(49, 130, 206, 0.2);}
.page-link.disabled { opacity: 0.5; pointer-events: none; background: #f7fafc; color: #cbd5e1; }
</style>

<div class="admin-header1" style="margin-bottom: 20px; padding: 5px;">
    
    
</div>

<form method="GET" action="/lego_shop_php/adminorder" class="filter-bar" id="filterForm">
    <div class="filter-group" style="flex: 2;">
        <label><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm nhanh</label>
        <div class="search-wrapper">
            <input type="text" name="search" class="filter-control" placeholder="Mã đơn, Tên khách hàng..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
            <button type="submit" class="search-btn" title="Tìm kiếm"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>

    <div class="filter-group">
        <label><i class="fa-solid fa-filter"></i> Trạng thái</label>
        <select name="status" class="filter-control" onchange="this.form.submit()">
            <option value="all">Tất cả trạng thái</option>
            <option value="pending" <?= (isset($filters['status']) && $filters['status'] == 'pending') ? 'selected' : '' ?>>Chờ xử lý</option>
            <option value="confirmed" <?= (isset($filters['status']) && $filters['status'] == 'confirmed') ? 'selected' : '' ?>>Đã xác nhận</option>
            <option value="shipping" <?= (isset($filters['status']) && $filters['status'] == 'shipping') ? 'selected' : '' ?>>Đang giao hàng</option>
            <option value="delivered" <?= (isset($filters['status']) && $filters['status'] == 'delivered') ? 'selected' : '' ?>>Giao thành công</option>
            <option value="cancelled" <?= (isset($filters['status']) && $filters['status'] == 'cancelled') ? 'selected' : '' ?>>Đã hủy</option>
        </select>
    </div>

    <div class="filter-group">
        <label><i class="fa-regular fa-calendar"></i> Từ ngày</label>
        <input type="date" name="date_from" class="filter-control" value="<?= htmlspecialchars($filters['date_from'] ?? '') ?>">
    </div>

    <div class="filter-group">
        <label><i class="fa-regular fa-calendar-check"></i> Đến ngày</label>
        <input type="date" name="date_to" class="filter-control" value="<?= htmlspecialchars($filters['date_to'] ?? '') ?>">
    </div>

    <div class="filter-group">
        <label><i class="fa-solid fa-sort"></i> Sắp xếp</label>
        <select name="sort" class="filter-control" onchange="this.form.submit()">
            <option value="date_desc" <?= (isset($filters['sort']) && $filters['sort'] == 'date_desc') ? 'selected' : '' ?>>Mới nhất trước</option>
            <option value="date_asc" <?= (isset($filters['sort']) && $filters['sort'] == 'date_asc') ? 'selected' : '' ?>>Cũ nhất trước</option>
            <option value="price_desc" <?= (isset($filters['sort']) && $filters['sort'] == 'price_desc') ? 'selected' : '' ?>>Giá cao xuống thấp</option>
            <option value="price_asc" <?= (isset($filters['sort']) && $filters['sort'] == 'price_asc') ? 'selected' : '' ?>>Giá thấp lên cao</option>
            
            <option value="ward_asc" <?= (isset($filters['sort']) && $filters['sort'] == 'ward_asc') ? 'selected' : '' ?>>Khu vực giao hàng (A-Z)</option>
        </select>
    </div>

    <div class="filter-group" style="flex: 0; min-width: auto; flex-direction: row; gap: 8px;">
        <button type="submit" class="btn-submit-filter" title="Lọc theo ngày"><i class="fa-solid fa-filter"></i> Lọc</button>
        <a href="/lego_shop_php/adminorder" class="btn-reset" title="Xóa tất cả bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
    </div>
</form>

<?php 
// =========================================================
// LOGIC GOM NHÓM DỮ LIỆU THEO TỈNH THÀNH > QUẬN > PHƯỜNG
// =========================================================
$is_grouped_by_ward = (isset($filters['sort']) && $filters['sort'] == 'ward_asc');
$grouped_orders = [];

if ($is_grouped_by_ward && !empty($orders)) {
    foreach ($orders as $item) {
        $city = !empty($item['shipping_city']) ? $item['shipping_city'] : 'Chưa rõ TP';
        $district = !empty($item['shipping_district']) ? $item['shipping_district'] : 'Chưa rõ Quận';
        $ward = !empty($item['shipping_ward']) ? $item['shipping_ward'] : 'Chưa rõ Phường';
        
        $full_location = $city . ' > ' . $district . ' > ' . $ward;
        $grouped_orders[$full_location][] = $item;
    }
} else {
    $grouped_orders['Tất cả đơn hàng'] = $orders;
}

// Cấu hình hiển thị trạng thái bằng các Class CSS mới
$status_map = [
    // Quan trọng cần xử lý -> Nổi khối
    'pending'   => ['label' => 'Chờ xử lý', 'class' => 'badge-solid-orange', 'icon' => 'fa-hourglass-half'],
    'cancelled' => ['label' => 'Đã hủy', 'class' => 'badge-solid-red', 'icon' => 'fa-ban'],
    
    // Đang tiến hành hoặc đã xong -> Viền mảnh
    'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'badge-outline-teal', 'icon' => 'fa-box'],
    'shipping'  => ['label' => 'Đang giao', 'class' => 'badge-outline-blue', 'icon' => 'fa-truck-fast'],
    'delivered' => ['label' => 'Thành công', 'class' => 'badge-outline-green', 'icon' => 'fa-check-double']
];
$payment_map = [
    'cash'     => 'COD',
    'transfer' => 'Chuyển khoản',
    'online'   => 'Thanh toán Online'
];
?>

<?php if (!empty($orders)): ?>
    <?php foreach ($grouped_orders as $location_name => $ward_orders): ?>
        
        <?php if ($is_grouped_by_ward): ?>
            <h3 style="margin-top: 30px; margin-bottom: 10px; color: #2d3748; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fa-solid fa-map-location-dot" style="color: #e53e3e; margin-right: 8px;"></i> <?= htmlspecialchars($location_name) ?></span>
                <span style="font-size: 14px; background: #edf2f7; padding: 4px 12px; border-radius: 20px; color: #4a5568; font-weight: 600;">
                    <?= count($ward_orders) ?> đơn
                </span>
            </h3>
        <?php endif; ?>

        <div class="table-container" style="margin-bottom: 30px;">
            <table class="lego-table">
                <thead>
                    <tr>
                        <th style="text-align: center; width: 100px;">Mã Đơn</th>
                        <th>Thời gian</th>
                        <th>Khách hàng</th>
                        <th>Thanh toán</th>
                        <th style="text-align: center;">Trạng thái</th>
                        <th style="text-align: right;">Tổng tiền</th>
                        <th style="text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ward_orders as $item): 
                        $st = $item['status'] ?? 'pending';
                        $st_lbl = $status_map[$st]['label'] ?? 'Không xác định';
                        $st_class = $status_map[$st]['class'] ?? 'badge-outline-orange'; // Mặc định class
                        $st_icon = $status_map[$st]['icon'] ?? 'fa-circle-question';
                    ?>
                    <tr>
                        <td style="text-align: center;">
                            <a href="/lego_shop_php/adminorder/detail/<?= $item['id'] ?>" style="background: #f1f5f9; color: #3182ce; padding: 5px 10px; border-radius: 6px; font-weight: 800; font-family: monospace; border: 1px solid #e2e8f0; text-decoration: none; display: inline-block; transition: 0.2s;">
                                #<?= $item['id'] ?>
                            </a>
                        </td>
                        <td style="color: #64748b; font-size: 13px;">
                            <div style="font-weight: 600; color: #334155;"><?= date('d/m/Y', strtotime($item['created_at'])) ?></div>
                            <div><?= date('H:i', strtotime($item['created_at'])) ?></div>
                        </td>
                        <td style="font-weight: 700; color: #1e293b;">
                            <?= htmlspecialchars($item['shipping_fullname']) ?>
                            <?php if (!$is_grouped_by_ward && !empty($item['shipping_city'])): ?>
                                <div style="font-size: 12px; color: #718096; font-weight: normal; margin-top: 4px;">
                                    <i class="fa-solid fa-location-dot" style="font-size: 10px;"></i> <?= htmlspecialchars($item['shipping_district'] . ', ' . $item['shipping_city']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span style="font-size: 13px; font-weight: 600; color: #475569;">
                                <i class="fa-solid fa-credit-card" style="font-size: 11px; opacity: 0.5;"></i> <?= $payment_map[$item['payment_method']] ?? $item['payment_method'] ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-badge <?= $st_class ?>">
                                <i class="fa-solid <?= $st_icon ?>"></i> <?= $st_lbl ?>
                            </span>
                        </td>
                        <td style="text-align: right; font-weight: 800; color: #e53e3e; font-size: 15px;">
                            <?= number_format($item['total_amount'], 0, ',', '.') ?>đ
                        </td>
                        
                        <td style="text-align: center;">
                            <a href="/lego_shop_php/adminorder/detail/<?= $item['id'] ?>" class="btn-action" title="Xem chi tiết đơn hàng này">
                                <i class="fa-solid fa-eye"></i> Chi tiết
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="table-container">
        <table class="lego-table">
            <tr>
                <td style="text-align: center; padding: 80px 20px; color: #94a3b8;">
                    <i class="fa-solid fa-box-open" style="font-size: 50px; color: #e2e8f0; margin-bottom: 15px;"></i><br>
                    <span style="font-size: 16px; font-weight: 600;">Không tìm thấy đơn hàng nào khớp với điều kiện lọc!</span>
                </td>
            </tr>
        </table>
    </div>
<?php endif; ?>
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <?php 
        // Xây dựng chuỗi URL cơ sở để giữ nguyên bộ lọc (Search, Date, Sort...)
        $query_params = [];
        if (!empty($filters['search'])) $query_params['search'] = $filters['search'];
        if (!empty($filters['status'])) $query_params['status'] = $filters['status'];
        if (!empty($filters['date_from'])) $query_params['date_from'] = $filters['date_from'];
        if (!empty($filters['date_to'])) $query_params['date_to'] = $filters['date_to'];
        if (!empty($filters['sort'])) $query_params['sort'] = $filters['sort'];
        
        $base_query = http_build_query($query_params);
        $url_prefix = "/lego_shop_php/adminorder?" . (!empty($base_query) ? $base_query . "&" : "");
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