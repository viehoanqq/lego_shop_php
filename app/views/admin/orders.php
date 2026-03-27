<style>
    .filter-bar { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; }
    .filter-group { display: flex; flex-direction: column; flex: 1; min-width: 150px; }
    .filter-group label { font-size: 13px; font-weight: 600; color: #4a5568; margin-bottom: 5px; }
    .filter-control { padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; outline: none; }
    .filter-control:focus { border-color: #3182ce; }
    .btn-filter { background: #3182ce; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; height: 41px; }
    .btn-filter:hover { background: #2b6cb0; }
    .btn-reset { background: #edf2f7; color: #4a5568; border: 1px solid #cbd5e0; padding: 10px 20px; border-radius: 6px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: 41px; transition: 0.2s;}
    .btn-reset:hover { background: #e2e8f0; }

    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); max-height: 70vh; overflow-y: auto; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { position: sticky; top: 0; z-index: 10; background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .btn-action { text-decoration: none; padding: 6px 12px; border-radius: 6px; transition: 0.2s; font-weight: 600; color: #3182ce; background: transparent; border: 1px solid #3182ce; display: inline-flex; align-items: center; gap: 5px;}
    .btn-action:hover { background: #ebf8ff; }
</style>

<div class="admin-header" style="margin-bottom: 20px; padding: 5px;">
    <h2 style="margin:0; color: #1a202c;"><i class="fa-solid fa-cart-shopping" style="color: #3182ce;"></i> Quản Lý Đơn Hàng</h2>
    <small style="color: #718096;">Theo dõi, tìm kiếm và xử lý đơn hàng từ khách</small>
</div>

<form method="GET" action="/lego_shop_php/admin/order" class="filter-bar">
    <div class="filter-group" style="flex: 2;">
        <label><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm đơn hàng</label>
        <input type="text" name="search" class="filter-control" placeholder="Nhập Mã đơn, SĐT hoặc Tên KH..." value="<?= htmlspecialchars($filters['search'] ?? '') ?>">
    </div>
    <div class="filter-group">
        <label><i class="fa-solid fa-tags"></i> Trạng thái</label>
        <select name="status" class="filter-control">
            <option value="all">Tất cả trạng thái</option>
            <option value="pending" <?= isset($filters['status']) && $filters['status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
            <option value="confirmed" <?= isset($filters['status']) && $filters['status'] == 'confirmed' ? 'selected' : '' ?>>Đã xác nhận</option>
            <option value="shipping" <?= isset($filters['status']) && $filters['status'] == 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
            <option value="delivered" <?= isset($filters['status']) && $filters['status'] == 'delivered' ? 'selected' : '' ?>>Giao thành công</option>
            <option value="cancelled" <?= isset($filters['status']) && $filters['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
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
        <label><i class="fa-solid fa-arrow-down-a-z"></i> Sắp xếp</label>
        <select name="sort" class="filter-control">
            <option value="date_desc" <?= isset($filters['sort']) && $filters['sort'] == 'date_desc' ? 'selected' : '' ?>>Mới nhất trước</option>
            <option value="date_asc" <?= isset($filters['sort']) && $filters['sort'] == 'date_asc' ? 'selected' : '' ?>>Cũ nhất trước</option>
            <option value="price_desc" <?= isset($filters['sort']) && $filters['sort'] == 'price_desc' ? 'selected' : '' ?>>Giá cao -> thấp</option>
            <option value="price_asc" <?= isset($filters['sort']) && $filters['sort'] == 'price_asc' ? 'selected' : '' ?>>Giá thấp -> cao</option>
        </select>
    </div>
    <div class="filter-group" style="flex: 0; flex-direction: row; gap: 10px;">
        <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Lọc</button>
        <a href="/lego_shop_php/admin/order" class="btn-reset" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
    </div>
</form>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="text-align: center;"><i class="fa-solid fa-hashtag"></i> Mã Đơn</th>
                <th><i class="fa-regular fa-clock"></i> Thời gian</th>
                <th><i class="fa-solid fa-user"></i> Khách hàng</th>
                <th><i class="fa-solid fa-credit-card"></i> Thanh toán</th>
                <th style="text-align: center;"><i class="fa-solid fa-circle-half-stroke"></i> Trạng thái</th>
                <th style="text-align: right;"><i class="fa-solid fa-money-bill-wave"></i> Tổng tiền</th>
                <th style="text-align: center;"><i class="fa-solid fa-gear"></i> Xử lý</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($orders)): ?>
                <?php 
                    $status_map = [
                        'pending'   => ['label' => 'Chờ xử lý', 'bg' => '#fef3c7', 'color' => '#d97706', 'icon' => 'fa-hourglass-half'],
                        'confirmed' => ['label' => 'Đã xác nhận', 'bg' => '#e0e7ff', 'color' => '#3182ce', 'icon' => 'fa-box'],
                        'shipping'  => ['label' => 'Đang giao', 'bg' => '#e0e7ff', 'color' => '#3182ce', 'icon' => 'fa-truck-fast'],
                        'delivered' => ['label' => 'Thành công', 'bg' => '#d1fae5', 'color' => '#059669', 'icon' => 'fa-check-double'],
                        'cancelled' => ['label' => 'Đã hủy', 'bg' => '#fee2e2', 'color' => '#e53e3e', 'icon' => 'fa-ban']
                    ];
                    $payment_map = [
                        'cash'     => 'COD (Tiền mặt)',
                        'transfer' => 'Chuyển khoản',
                        'online'   => 'Thanh toán Online'
                    ];
                ?>
                <?php foreach ($orders as $item): 
                    $st = !empty($item['status']) ? $item['status'] : 'unknown';
                    if (isset($status_map[$st])) {
                        $st_lbl = $status_map[$st]['label'];
                        $st_bg = $status_map[$st]['bg'];
                        $st_color = $status_map[$st]['color'];
                        $st_icon = $status_map[$st]['icon'];
                    } else {
                        $st_lbl = 'Lỗi trạng thái'; $st_bg = '#edf2f7'; $st_color = '#718096'; $st_icon = 'fa-circle-exclamation';
                    }
                ?>
                <tr>
                    <td style="text-align: center;">
                        <span style="background: #edf2f7; color: #4a5568; padding: 4px 8px; border-radius: 4px; font-weight: 700; font-family: monospace;">
                            #DH-<?= $item['id'] ?>
                        </span>
                    </td>
                    <td style="color: #718096; font-size: 13px;">
                        <?= date('d/m/Y', strtotime($item['created_at'])) ?><br>
                        <strong><?= date('H:i', strtotime($item['created_at'])) ?></strong>
                    </td>
                    <td style="font-weight: 600; color: #2d3748;">
                        <?= htmlspecialchars($item['shipping_fullname']) ?><br>
                        <small style="color: #a0aec0; font-weight: normal;"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($item['shipping_phone']) ?></small>
                    </td>
                    <td>
                        <span style="font-size: 13px; font-weight: 600; color: #4a5568;">
                            <?= $payment_map[$item['payment_method']] ?? $item['payment_method'] ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: <?= $st_bg ?>; color: <?= $st_color ?>; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;">
                            <i class="fa-solid <?= $st_icon ?>"></i> <?= $st_lbl ?>
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 800; color: #e53e3e;">
                        <?= number_format($item['total_amount'], 0, ',', '.') ?>đ
                    </td>
                    <td style="text-align: center;">
                        <a href="/lego_shop_php/adminorder/detail/<?= $item['id'] ?>" class="btn-action"><i class="fa-solid fa-eye"></i> Xem</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 60px; color: #a0aec0;">
                        <i class="fa-solid fa-box-open" style="font-size: 40px; color: #cbd5e0; margin-bottom: 15px;"></i><br>
                        Không tìm thấy đơn hàng nào phù hợp với bộ lọc!
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>