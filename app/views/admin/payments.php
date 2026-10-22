<?php
$fmt = function ($n) { return number_format((int)$n, 0, ',', '.') . 'đ'; };
$tx_ui = [
    'success'   => ['Khớp đơn', ''],
    'mismatch'  => ['Cần kiểm tra', 'attn'],
    'unmatched' => ['Không rõ đơn', 'attn'],
];
$provider_labels = ['sepay' => 'SePay', 'casso' => 'Casso', 'sandbox' => 'Giả lập', 'manual' => 'Admin xác nhận'];
$qs = function ($over) use ($filters) { return '?' . http_build_query(array_merge($filters, $over)); };
?>
<div class="ui-page">
    <div class="ui-stats">
        <div class="ui-stat">
            <div class="lbl">Đã nhận qua VietQR</div>
            <div class="num"><?= $fmt($stats['received']) ?></div>
            <span class="ico"><i class="fa-solid fa-sack-dollar"></i></span>
        </div>
        <a href="<?= $qs(['status' => $filters['status'] === 'success' ? 'all' : 'success', 'page' => 1]) ?>" class="ui-stat <?= $filters['status'] === 'success' ? 'active' : '' ?>">
            <div class="lbl">Giao dịch khớp đơn</div>
            <div class="num"><?= (int)$stats['success_count'] ?></div>
            <span class="ico"><i class="fa-solid fa-circle-check"></i></span>
        </a>
        <a href="<?= $qs(['status' => $filters['status'] === 'review' ? 'all' : 'review', 'page' => 1]) ?>" class="ui-stat <?= $filters['status'] === 'review' ? 'active' : '' ?>">
            <div class="lbl">Cần kiểm tra</div>
            <div class="num"><?= (int)$stats['review_count'] ?></div>
            <span class="ico"><i class="fa-solid fa-triangle-exclamation"></i></span>
        </a>
    </div>

    <form action="/lego_shop_php/admin/payment" method="GET" class="ui-filter">
        <div class="group wide">
            <label><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</label>
            <input type="text" name="keyword" class="control" placeholder="Mã đơn, nội dung CK, mã giao dịch" value="<?= htmlspecialchars($filters['keyword']) ?>">
        </div>
        <div class="group">
            <label><i class="fa-solid fa-filter"></i> Kết quả</label>
            <select name="status" class="control" onchange="this.form.submit()">
                <option value="all">Tất cả</option>
                <option value="review" <?= $filters['status'] === 'review' ? 'selected' : '' ?>>Cần kiểm tra</option>
                <?php foreach ($tx_ui as $k => [$lbl]): ?>
                    <option value="<?= $k ?>" <?= $filters['status'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="actions">
            <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Lọc</button>
            <a href="/lego_shop_php/admin/payment" class="btn-reset" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
        </div>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr><th>Thời gian</th><th>Nguồn</th><th>Mã GD</th><th>Nội dung CK</th><th class="ui-right">Số tiền</th><th>Đơn hàng</th><th>Kết quả</th></tr>
            </thead>
            <tbody>
            <?php if (empty($transactions)): ?>
                <tr><td colspan="7" class="ui-empty">Chưa có giao dịch nào.</td></tr>
            <?php else: foreach ($transactions as $t): [$lbl, $cls] = $tx_ui[$t['status']]; ?>
                <tr>
                    <td class="ui-muted ui-nowrap"><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                    <td><?= $provider_labels[$t['provider']] ?? htmlspecialchars($t['provider']) ?></td>
                    <td class="ui-mono ui-small"><?= htmlspecialchars($t['transaction_ref'] ?? '') ?></td>
                    <td style="max-width: 260px;"><?= htmlspecialchars($t['content'] ?? '') ?></td>
                    <td class="ui-right"><strong><?= $fmt($t['amount']) ?></strong></td>
                    <td>
                        <?php if ($t['order_id']): ?>
                            <a href="/lego_shop_php/admin/order/detail/<?= $t['order_id'] ?>" class="ui-link">#DH-<?= $t['order_id'] ?></a>
                            <div class="ui-muted ui-small"><?= $fmt($t['order_total']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td><span class="ui-badge <?= $cls ?>"><?= $lbl ?></span></td>
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
