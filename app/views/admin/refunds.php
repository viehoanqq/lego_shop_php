<?php
$fmt = function ($n) { return number_format((int)$n, 0, ',', '.') . 'đ'; };
$status_ui = [
    'pending'   => ['Chờ xử lý', 'attn'],
    'approved'  => ['Chờ chuyển tiền', 'attn'],
    'rejected'  => ['Từ chối', 'muted'],
    'completed' => ['Đã hoàn tiền', ''],
];
$type_labels = ['cancel' => 'Hủy đơn đã thanh toán', 'return' => 'Trả hàng'];
$qs = function ($over) use ($filters) { return '?' . http_build_query(array_merge($filters, $over)); };
?>
<div class="ui-page">
    <?php $stat_icons = ['pending' => 'fa-hourglass-half', 'approved' => 'fa-money-bill-transfer', 'rejected' => 'fa-ban', 'completed' => 'fa-circle-check']; ?>
    <div class="ui-stats">
        <?php foreach ($status_ui as $k => [$lbl]): ?>
            <a href="<?= $qs(['status' => $filters['status'] === $k ? 'all' : $k, 'page' => 1]) ?>" class="ui-stat <?= $filters['status'] === $k ? 'active' : '' ?>">
                <div class="lbl"><?= $lbl ?></div>
                <div class="num"><?= (int)$counts[$k] ?></div>
                <span class="ico"><i class="fa-solid <?= $stat_icons[$k] ?>"></i></span>
            </a>
        <?php endforeach; ?>
    </div>

    <form action="/lego_shop_php/admin/refund" method="GET" class="ui-filter">
        <div class="group wide">
            <label><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</label>
            <input type="text" name="keyword" class="control" placeholder="Mã đơn, mã yêu cầu, tên, SĐT" value="<?= htmlspecialchars($filters['keyword']) ?>">
        </div>
        <div class="group">
            <label><i class="fa-solid fa-filter"></i> Trạng thái</label>
            <select name="status" class="control" onchange="this.form.submit()">
                <option value="all">Tất cả trạng thái</option>
                <?php foreach ($status_ui as $k => [$lbl]): ?>
                    <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="group">
            <label><i class="fa-solid fa-layer-group"></i> Loại</label>
            <select name="type" class="control" onchange="this.form.submit()">
                <option value="all">Tất cả loại</option>
                <?php foreach ($type_labels as $k => $lbl): ?>
                    <option value="<?= $k ?>" <?= $filters['type'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="actions">
            <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Lọc</button>
            <a href="/lego_shop_php/admin/refund" class="btn-reset" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Loại</th>
                    <th class="ui-right">Số tiền</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($refunds)): ?>
                <tr><td colspan="8" class="ui-empty">Không có yêu cầu nào.</td></tr>
            <?php else: foreach ($refunds as $r): [$lbl, $cls] = $status_ui[$r['status']]; ?>
                <tr>
                    <td>#RF-<?= $r['id'] ?></td>
                    <td><a href="/lego_shop_php/admin/order/detail/<?= $r['order_id'] ?>" class="ui-link">#DH-<?= $r['order_id'] ?></a></td>
                    <td><?= htmlspecialchars($r['fullname']) ?><div class="ui-muted ui-small"><?= htmlspecialchars($r['shipping_phone']) ?></div></td>
                    <td><?= $type_labels[$r['type']] ?></td>
                    <td class="ui-right"><strong><?= $fmt($r['amount']) ?></strong></td>
                    <td class="ui-muted ui-nowrap"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
                    <td><span class="ui-badge <?= $cls ?>"><?= $lbl ?></span></td>
                    <td class="ui-right"><a href="/lego_shop_php/admin/refund/detail/<?= $r['id'] ?>" class="ui-link">Xem</a></td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="ui-pagination">
            <a href="<?= $qs(['page' => $currentPage - 1]) ?>" class="<?= $currentPage <= 1 ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-left"></i></a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= $qs(['page' => $i]) ?>" class="<?= $currentPage == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="<?= $qs(['page' => $currentPage + 1]) ?>" class="<?= $currentPage >= $totalPages ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-right"></i></a>
        </div>
    <?php endif; ?>
</div>
