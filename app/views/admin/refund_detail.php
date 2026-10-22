<?php
$fmt = function ($n) { return number_format((int)$n, 0, ',', '.') . 'đ'; };
$status_ui = [
    'pending'   => ['Chờ xử lý', 'attn'],
    'approved'  => ['Chờ chuyển tiền', 'attn'],
    'rejected'  => ['Từ chối', 'muted'],
    'completed' => ['Đã hoàn tiền', ''],
];
[$st_lbl, $st_cls] = $status_ui[$refund['status']];
$is_open = in_array($refund['status'], ['pending', 'approved']);
$has_bank = RefundModel::hasBankInfo($refund);
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
?>
<div class="ui-page">
    <?php if ($session_msg): ?><div class="ui-flash"><?= htmlspecialchars($session_msg) ?></div><?php endif; ?>
    <?php if ($session_error): ?><div class="ui-flash error"><?= htmlspecialchars($session_error) ?></div><?php endif; ?>

    <div class="ui-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <strong style="font-size: 18px;">#RF-<?= $refund['id'] ?></strong>
            <span class="ui-badge <?= $st_cls ?>"><?= $st_lbl ?></span>
        </div>
        <a href="/lego_shop_php/admin/refund" class="ui-btn">Quay lại</a>
    </div>

    <div class="ui-split">
        <div>
            <div class="ui-card">
                <h3 class="ui-card-title">Thông tin yêu cầu</h3>
                <dl class="ui-dl">
                    <dt>Loại</dt>
                    <dd><?= $refund['type'] === 'return' ? 'Trả hàng' : 'Hủy đơn đã thanh toán' ?></dd>
                    <dt>Khách hàng</dt>
                    <dd><?= htmlspecialchars($refund['fullname']) ?> · <?= htmlspecialchars($refund['account_phone']) ?> · <?= htmlspecialchars($refund['email']) ?></dd>
                    <dt>Ngày gửi</dt>
                    <dd><?= date('d/m/Y H:i', strtotime($refund['created_at'])) ?></dd>
                    <?php if (!empty($refund['reason'])): ?>
                        <dt>Lý do</dt>
                        <dd><div class="ui-quote"><?= htmlspecialchars($refund['reason']) ?></div></dd>
                    <?php endif; ?>
                    <?php if (!empty($refund['evidence_image'])): ?>
                        <dt>Ảnh</dt>
                        <dd><a href="/lego_shop_php/public/assets/images/<?= htmlspecialchars($refund['evidence_image']) ?>" target="_blank">
                            <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($refund['evidence_image']) ?>" alt="Ảnh minh chứng" style="max-width: 240px; max-height: 240px; border: 1px solid #e2e8f0; border-radius: 6px;">
                        </a></dd>
                    <?php endif; ?>
                    <?php if (!empty($refund['processed_at'])): ?>
                        <dt>Xử lý</dt>
                        <dd><?= htmlspecialchars($refund['admin_name'] ?? 'Admin') ?>, <?= date('d/m/Y H:i', strtotime($refund['processed_at'])) ?></dd>
                    <?php endif; ?>
                    <?php if (!empty($refund['admin_note'])): ?>
                        <dt>Ghi chú</dt>
                        <dd><?= htmlspecialchars($refund['admin_note']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>

            <div class="ui-card">
                <h3 class="ui-card-title">Đơn hàng
                    <a href="/lego_shop_php/admin/order/detail/<?= $order['id'] ?>" class="ui-link">#DH-<?= $order['id'] ?></a>
                </h3>
                <dl class="ui-dl" style="margin-bottom: 12px;">
                    <dt>Trạng thái</dt>
                    <dd><?= OrderModel::HISTORY_LABELS[$order['status']][0] ?? $order['status'] ?></dd>
                    <dt>Thanh toán</dt>
                    <dd><?= OrderModel::PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method'] ?>
                        - <?= OrderModel::PAYMENT_STATUS[$order['payment_status']][0] ?? $order['payment_status'] ?></dd>
                </dl>
                <table class="ui-table">
                    <thead><tr><th>Sản phẩm</th><th class="ui-center">SL</th><th class="ui-right">Thành tiền</th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $it): ?>
                        <tr>
                            <td><?= htmlspecialchars($it['name']) ?></td>
                            <td class="ui-center"><?= (int)$it['quantity'] ?></td>
                            <td class="ui-right"><?= $fmt($it['price'] * $it['quantity']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <tr><td colspan="2" class="ui-right ui-muted">Giảm giá (<?= htmlspecialchars($order['voucher_code']) ?>)</td><td class="ui-right">-<?= $fmt($order['discount_amount']) ?></td></tr>
                    <?php endif; ?>
                    <tr><td colspan="2" class="ui-right"><strong>Khách đã trả</strong></td><td class="ui-right"><strong><?= $fmt($order['total_amount']) ?></strong></td></tr>
                    </tbody>
                </table>
            </div>

            <div class="ui-card">
                <h3 class="ui-card-title">Lịch sử đơn hàng</h3>
                <ul class="ui-timeline">
                    <li><div class="t"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div><div class="s">Đặt hàng</div></li>
                    <?php foreach ($history as $h): ?>
                        <li>
                            <div class="t"><?= date('d/m/Y H:i', strtotime($h['changed_at'])) ?></div>
                            <div class="s"><?= OrderModel::HISTORY_LABELS[$h['status']][0] ?? htmlspecialchars($h['status']) ?></div>
                            <?php if (!empty($h['note'])): ?><div class="n"><?= htmlspecialchars($h['note']) ?></div><?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div>
            <div class="ui-card">
                <h3 class="ui-card-title">Hoàn tiền cho khách</h3>
                <div style="font-size: 26px; font-weight: 700; text-align: center; margin-bottom: 15px;"><?= $fmt($refund['amount']) ?></div>

                <?php if ($has_bank): ?>
                    <?php if ($qr_url && $is_open): ?>
                        <div class="ui-center" style="margin-bottom: 12px;">
                            <img src="<?= htmlspecialchars($qr_url) ?>" alt="QR hoàn tiền" style="width: 220px; max-width: 100%; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px;">
                        </div>
                    <?php endif; ?>
                    <dl class="ui-dl" style="grid-template-columns: 90px 1fr;">
                        <dt>Ngân hàng</dt><dd><?= htmlspecialchars($refund['bank_name']) ?></dd>
                        <dt>Số TK</dt>
                        <dd><span class="ui-mono"><?= htmlspecialchars($refund['bank_account_number']) ?></span>
                            <button type="button" class="ui-btn" style="padding: 2px 7px; margin-left: 4px;" title="Sao chép" onclick="navigator.clipboard.writeText('<?= htmlspecialchars($refund['bank_account_number']) ?>')"><i class="fa-regular fa-copy"></i></button>
                        </dd>
                        <dt>Chủ TK</dt><dd><?= htmlspecialchars($refund['bank_account_name']) ?></dd>
                        <dt>Nội dung</dt><dd>HOAN TIEN DON LEGO<?= (int)$refund['order_id'] ?></dd>
                    </dl>
                <?php else: ?>
                    <p class="ui-muted" style="margin: 0;">Chưa có tài khoản nhận tiền.</p>
                <?php endif; ?>

                <?php if ($is_open): ?>
                    <details style="margin-top: 14px;" <?= $has_bank ? '' : 'open' ?>>
                        <summary class="ui-link" style="cursor: pointer;"><?= $has_bank ? 'Sửa tài khoản' : 'Nhập tài khoản' ?></summary>
                        <form action="/lego_shop_php/admin/refund/updateBank/<?= $refund['id'] ?>" method="POST" style="margin-top: 10px;">
                            <div class="ui-field">
                                <select name="bank_name" class="ui-input" required>
                                    <option value="">Chọn ngân hàng</option>
                                    <?php foreach ($banks as $b): ?>
                                        <option value="<?= htmlspecialchars($b) ?>" <?= $refund['bank_name'] === $b ? 'selected' : '' ?>><?= htmlspecialchars($b) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="ui-field"><input type="text" name="bank_account_number" class="ui-input" placeholder="Số tài khoản" value="<?= htmlspecialchars($refund['bank_account_number']) ?>" required></div>
                            <div class="ui-field"><input type="text" name="bank_account_name" class="ui-input" placeholder="Chủ tài khoản" value="<?= htmlspecialchars($refund['bank_account_name']) ?>" style="text-transform: uppercase;" required></div>
                            <button type="submit" class="ui-btn ui-btn-block">Lưu tài khoản</button>
                        </form>
                    </details>
                <?php endif; ?>
            </div>

            <?php if ($is_open): ?>
                <div class="ui-card">
                    <h3 class="ui-card-title">Xử lý</h3>
                    <form method="POST">
                        <div class="ui-field">
                            <label class="ui-label">Ghi chú gửi khách</label>
                            <textarea name="admin_note" id="admin_note" class="ui-input" rows="3"></textarea>
                        </div>

                        <?php if ($refund['status'] === 'pending'): ?>
                            <button type="submit" class="ui-btn ui-btn-block" formaction="/lego_shop_php/admin/refund/approve/<?= $refund['id'] ?>"
                                    data-confirm="Duyệt yêu cầu này?">Duyệt</button>
                        <?php endif; ?>

                        <button type="submit" class="ui-btn ui-btn-primary ui-btn-block" formaction="/lego_shop_php/admin/refund/complete/<?= $refund['id'] ?>" <?= $has_bank ? '' : 'disabled' ?>
                                data-confirm="Xác nhận đã chuyển <?= $fmt($refund['amount']) ?> cho khách?">Đã chuyển tiền</button>

                        <?php if ($refund['type'] === 'return'): ?>
                            <button type="submit" class="ui-btn ui-btn-danger ui-btn-block" formaction="/lego_shop_php/admin/refund/reject/<?= $refund['id'] ?>"
                                    onclick="if (document.getElementById('admin_note').value.trim().length < 5) { uiAlert('Vui lòng nhập lý do từ chối.'); return false; }" data-confirm="Từ chối yêu cầu này?">Từ chối</button>
                        <?php endif; ?>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
