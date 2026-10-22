<?php
$fmt = function ($n) { return number_format((int)$n, 0, ',', '.') . 'đ'; };
$state_labels = [
    'running'   => ['Đang chạy', ''],
    'upcoming'  => ['Sắp diễn ra', ''],
    'expired'   => ['Hết hạn', 'muted'],
    'exhausted' => ['Hết lượt', 'muted'],
    'inactive'  => ['Ngừng áp dụng', 'muted'],
    'ended'     => ['Hết hạn / hết lượt', 'muted'],
];
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
?>
<div class="ui-page">
    <?php if ($session_msg): ?><div class="ui-flash"><?= htmlspecialchars($session_msg) ?></div><?php endif; ?>
    <?php if ($session_error): ?><div class="ui-flash error"><?= htmlspecialchars($session_error) ?></div><?php endif; ?>

<?php if (!$is_form): ?>
    <?php
        $stat_cards = [
            'running'  => ['Đang chạy', 'fa-circle-play'],
            'upcoming' => ['Sắp diễn ra', 'fa-calendar-days'],
            'ended'    => ['Hết hạn / hết lượt', 'fa-hourglass-end'],
            'inactive' => ['Ngừng áp dụng', 'fa-pause'],
        ];
        $vqs = function ($state) use ($filters) { return '?' . http_build_query(['keyword' => $filters['keyword'], 'state' => $filters['state'] === $state ? 'all' : $state]); };
    ?>
    <div class="ui-stats">
        <?php foreach ($stat_cards as $k => [$lbl, $icon]): ?>
            <a href="<?= $vqs($k) ?>" class="ui-stat <?= $filters['state'] === $k ? 'active' : '' ?>">
                <div class="lbl"><?= $lbl ?></div>
                <div class="num"><?= (int)$counts[$k] ?></div>
                <span class="ico"><i class="fa-solid <?= $icon ?>"></i></span>
            </a>
        <?php endforeach; ?>
    </div>

    <form action="/lego_shop_php/admin/voucher" method="GET" class="ui-filter">
        <div class="group wide">
            <label><i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm</label>
            <input type="text" name="keyword" class="control" placeholder="Mã hoặc mô tả" value="<?= htmlspecialchars($filters['keyword']) ?>">
        </div>
        <div class="group">
            <label><i class="fa-solid fa-filter"></i> Trạng thái</label>
            <select name="state" class="control" onchange="this.form.submit()">
                <option value="all">Tất cả trạng thái</option>
                <?php foreach ($state_labels as $k => [$lbl]): ?>
                    <option value="<?= $k ?>" <?= $filters['state'] === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="actions">
            <button type="submit" class="btn-filter"><i class="fa-solid fa-filter"></i> Lọc</button>
            <a href="/lego_shop_php/admin/voucher" class="btn-reset" title="Xóa bộ lọc"><i class="fa-solid fa-rotate-right"></i></a>
            <a href="/lego_shop_php/admin/voucher/add" class="btn-filter"><i class="fa-solid fa-plus"></i> Tạo mã</a>
        </div>
    </form>

    <div class="ui-table-wrap">
        <table class="ui-table">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Mức giảm</th>
                    <th>Đơn tối thiểu</th>
                    <th>Thời hạn</th>
                    <th>Đã dùng</th>
                    <th>Trạng thái</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($vouchers)): ?>
                <tr><td colspan="7" class="ui-empty">Chưa có mã giảm giá nào.</td></tr>
            <?php else: foreach ($vouchers as $v):
                [$lbl, $cls] = $state_labels[VoucherModel::computeState($v)];
            ?>
                <tr>
                    <td>
                        <strong class="ui-mono"><?= htmlspecialchars($v['code']) ?></strong>
                        <?php if (!empty($v['description'])): ?><div class="ui-muted ui-small"><?= htmlspecialchars($v['description']) ?></div><?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(VoucherModel::describe($v)) ?></td>
                    <td><?= $fmt($v['min_order_value']) ?></td>
                    <td class="ui-nowrap ui-small">
                        <?= $v['start_date'] ? date('d/m/Y', strtotime($v['start_date'])) : 'Không giới hạn' ?> -
                        <?= $v['end_date'] ? date('d/m/Y', strtotime($v['end_date'])) : 'Không giới hạn' ?>
                    </td>
                    <td><?= (int)$v['used_count'] ?><?= $v['usage_limit'] ? ' / ' . (int)$v['usage_limit'] : '' ?></td>
                    <td><span class="ui-badge <?= $cls ?>"><?= $lbl ?></span></td>
                    <td class="ui-actions ui-nowrap ui-right">
                        <a href="/lego_shop_php/admin/voucher/edit/<?= $v['id'] ?>">Sửa</a>
                        <a href="/lego_shop_php/admin/voucher/toggle/<?= $v['id'] ?>"
                           data-confirm="<?= $v['status'] === 'active' ? 'Ngừng áp dụng' : 'Kích hoạt lại' ?> mã này?"><?= $v['status'] === 'active' ? 'Ngừng' : 'Bật' ?></a>
                        <a href="/lego_shop_php/admin/voucher/delete/<?= $v['id'] ?>" class="danger"
                           data-confirm="Xóa mã này?">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1):
        $qs = 'keyword=' . urlencode($filters['keyword']) . '&state=' . urlencode($filters['state']);
    ?>
        <div class="ui-pagination">
            <a href="?<?= $qs ?>&page=<?= $currentPage - 1 ?>" class="<?= $currentPage <= 1 ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-left"></i></a>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= $qs ?>&page=<?= $i ?>" class="<?= $currentPage == $i ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?<?= $qs ?>&page=<?= $currentPage + 1 ?>" class="<?= $currentPage >= $totalPages ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-right"></i></a>
        </div>
    <?php endif; ?>

<?php else:
    $f = $form ?? [];
    $dt = function ($v) { return $v ? date('Y-m-d\TH:i', strtotime($v)) : ''; };
    $is_edit = !empty($voucher);
?>
    <div class="ui-card">
        <h3 class="ui-card-title"><?= $is_edit ? 'Sửa mã ' . htmlspecialchars($voucher['code']) : 'Tạo mã giảm giá' ?></h3>

        <form action="/lego_shop_php/admin/voucher/<?= $is_edit ? 'update/' . $voucher['id'] : 'store' ?>" method="POST">
            <div class="ui-grid-3">
                <div class="ui-field">
                    <label class="ui-label">Mã giảm giá *</label>
                    <input type="text" name="code" id="vc_code" class="ui-input ui-mono" required maxlength="30" style="text-transform: uppercase;"
                           value="<?= htmlspecialchars($f['code'] ?? '') ?>" placeholder="VD: SALE50K">
                </div>
                <div class="ui-field" style="grid-column: span 2;">
                    <label class="ui-label">Mô tả</label>
                    <input type="text" name="description" class="ui-input" maxlength="255" value="<?= htmlspecialchars($f['description'] ?? '') ?>">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Kiểu giảm *</label>
                    <select name="discount_type" id="vc_type" class="ui-input">
                        <option value="fixed" <?= ($f['discount_type'] ?? '') !== 'percent' ? 'selected' : '' ?>>Số tiền (đ)</option>
                        <option value="percent" <?= ($f['discount_type'] ?? '') === 'percent' ? 'selected' : '' ?>>Phần trăm (%)</option>
                    </select>
                </div>
                <div class="ui-field">
                    <label class="ui-label">Giá trị giảm *</label>
                    <input type="number" name="discount_value" id="vc_value" class="ui-input" required min="1" value="<?= htmlspecialchars($f['discount_value'] ?? '') ?>">
                </div>
                <div class="ui-field" id="vc_max_group">
                    <label class="ui-label">Giảm tối đa (đ)</label>
                    <input type="number" name="max_discount" class="ui-input" min="0" step="1000" value="<?= htmlspecialchars($f['max_discount'] ?? '') ?>" placeholder="Không giới hạn">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Đơn tối thiểu (đ)</label>
                    <input type="number" name="min_order_value" class="ui-input" min="0" step="1000" value="<?= htmlspecialchars($f['min_order_value'] ?? 0) ?>">
                </div>
                <div class="ui-field">
                    <label class="ui-label">Tổng lượt dùng</label>
                    <input type="number" name="usage_limit" class="ui-input" min="1" value="<?= htmlspecialchars($f['usage_limit'] ?? '') ?>" placeholder="Không giới hạn">
                </div>
                <div class="ui-field">
                    <label class="ui-label">Lượt mỗi khách *</label>
                    <input type="number" name="per_user_limit" class="ui-input" min="1" required value="<?= htmlspecialchars($f['per_user_limit'] ?? 1) ?>">
                </div>

                <div class="ui-field">
                    <label class="ui-label">Bắt đầu</label>
                    <input type="datetime-local" name="start_date" class="ui-input" value="<?= $dt($f['start_date'] ?? null) ?>">
                </div>
                <div class="ui-field">
                    <label class="ui-label">Kết thúc</label>
                    <input type="datetime-local" name="end_date" class="ui-input" value="<?= $dt($f['end_date'] ?? null) ?>">
                </div>
                <div class="ui-field" style="display: flex; align-items: flex-end; padding-bottom: 10px;">
                    <label class="ui-check"><input type="checkbox" name="status" value="1" <?= ($f['status'] ?? 'active') === 'active' ? 'checked' : '' ?>> Đang áp dụng</label>
                </div>
            </div>

            <div style="display: flex; gap: 8px; margin-top: 10px;">
                <button type="submit" class="ui-btn ui-btn-primary">Lưu</button>
                <a href="/lego_shop_php/admin/voucher" class="ui-btn">Hủy</a>
            </div>
        </form>
    </div>

    <?php if ($is_edit): ?>
        <div class="ui-card">
            <h3 class="ui-card-title">Đơn đã dùng mã (<?= count($usages) ?>)</h3>
            <?php if (empty($usages)): ?>
                <p class="ui-muted" style="margin: 0;">Chưa có đơn nào.</p>
            <?php else: ?>
                <table class="ui-table">
                    <thead><tr><th>Đơn hàng</th><th>Khách hàng</th><th>Giảm</th><th>Tổng đơn</th><th>Thời gian</th></tr></thead>
                    <tbody>
                    <?php foreach ($usages as $u): ?>
                        <tr>
                            <td><a href="/lego_shop_php/admin/order/detail/<?= $u['order_id'] ?>" class="ui-link">#DH-<?= $u['order_id'] ?></a></td>
                            <td><?= htmlspecialchars($u['fullname']) ?></td>
                            <td><?= $fmt($u['discount_amount']) ?></td>
                            <td><?= $fmt($u['total_amount']) ?></td>
                            <td class="ui-muted"><?= date('d/m/Y H:i', strtotime($u['used_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <script>
    (function () {
        const type = document.getElementById('vc_type');
        const code = document.getElementById('vc_code');
        const toggleMax = () => { document.getElementById('vc_max_group').style.display = type.value === 'percent' ? '' : 'none'; };
        type.addEventListener('change', toggleMax);
        code.addEventListener('input', () => { code.value = code.value.toUpperCase().replace(/[^A-Z0-9_-]/g, ''); });
        toggleMax();
    })();
    </script>
<?php endif; ?>
</div>
