<?php
$fmt = function ($n) { return number_format((int)$n, 0, ',', '.') . 'đ'; };
$ps_lbl = OrderModel::PAYMENT_STATUS[$order['payment_status']][0] ?? $order['payment_status'];
$status_lbl = OrderModel::HISTORY_LABELS[$order['status']][0] ?? $order['status'];
$is_online = in_array($order['payment_method'], ['transfer', 'vietqr']);
$current_status = $order['status'];
?>
<div class="ui-page">
    <div class="ui-toolbar">
        <div style="display: flex; align-items: center; gap: 10px;">
            <strong style="font-size: 18px;">Đơn hàng #DH-<?= $order['id'] ?></strong>
            <span class="ui-badge <?= in_array($current_status, ['cancelled', 'returned']) ? 'muted' : ($current_status === 'pending' ? 'attn' : '') ?>"><?= $status_lbl ?></span>
        </div>
        <a href="/lego_shop_php/admin/order" class="ui-btn">Quay lại</a>
    </div>

    <?php if (isset($_GET['msg'])): ?><div class="ui-flash">Đã cập nhật.</div><?php endif; ?>
    <?php if (!empty($flash_error)): ?><div class="ui-flash error"><?= htmlspecialchars($flash_error) ?></div><?php endif; ?>

    <div class="ui-split">
        <div>
            <div class="ui-card">
                <h3 class="ui-card-title">Giao hàng & thanh toán</h3>
                <dl class="ui-dl">
                    <dt>Khách hàng</dt><dd><?= htmlspecialchars($order['shipping_fullname']) ?> · <?= htmlspecialchars($order['shipping_phone']) ?></dd>
                    <dt>Địa chỉ</dt><dd><?= htmlspecialchars($order['shipping_street']) ?>, <?= htmlspecialchars($order['shipping_ward']) ?>, <?= htmlspecialchars($order['shipping_district']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></dd>
                    <dt>Ngày đặt</dt><dd><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></dd>
                    <dt>Hình thức</dt><dd><?= OrderModel::PAYMENT_METHODS[$order['payment_method']] ?? $order['payment_method'] ?></dd>
                    <dt>Thanh toán</dt><dd><?= $ps_lbl ?><?= !empty($order['paid_at']) ? ', ' . date('d/m/Y H:i', strtotime($order['paid_at'])) : '' ?></dd>
                    <?php if (!empty($order['payment_code'])): ?>
                        <dt>Nội dung CK</dt><dd class="ui-mono"><?= htmlspecialchars($order['payment_code']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>

            <div class="ui-card">
                <h3 class="ui-card-title">Sản phẩm</h3>
                <table class="ui-table">
                    <thead>
                        <tr><th>Sản phẩm</th><th class="ui-center">SL</th><th class="ui-right">Đơn giá</th><th class="ui-right">Thành tiền</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($item['image_url'] ?? 'default-lego.jpg') ?>" width="40" height="40" style="object-fit: contain; border: 1px solid #e2e8f0; border-radius: 4px;" alt="">
                                    <?= htmlspecialchars($item['name']) ?>
                                </div>
                            </td>
                            <td class="ui-center"><?= (int)$item['quantity'] ?></td>
                            <td class="ui-right ui-muted"><?= $fmt($item['price']) ?></td>
                            <td class="ui-right"><?= $fmt($item['price'] * $item['quantity']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <tr><td colspan="3" class="ui-right ui-muted">Tạm tính</td><td class="ui-right"><?= $fmt($order['subtotal_amount']) ?></td></tr>
                        <tr><td colspan="3" class="ui-right ui-muted">Giảm giá (<?= htmlspecialchars($order['voucher_code']) ?>)</td><td class="ui-right">-<?= $fmt($order['discount_amount']) ?></td></tr>
                    <?php endif; ?>
                        <tr><td colspan="3" class="ui-right"><strong>Tổng tiền</strong></td><td class="ui-right"><strong style="font-size: 16px;"><?= $fmt($order['total_amount']) ?></strong></td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <?php if ($is_online):
                $pay_done = $order['payment_status'] !== 'unpaid';
            ?>
                <div class="ui-card ui-card-emphasis">
                    <h3 class="ui-card-title"><i class="fa-solid fa-money-bill-transfer"></i> Thanh toán</h3>
                    <div class="pay-summary <?= $pay_done ? 'done' : ($current_status === 'cancelled' ? 'muted' : 'waiting') ?>">
                        <span class="pay-icon"><i class="fa-solid <?= $pay_done ? 'fa-circle-check' : 'fa-hourglass-half' ?>"></i></span>
                        <div>
                            <div class="pay-status"><?= $pay_done ? $ps_lbl : 'Chưa thanh toán' ?></div>
                            <div class="pay-amount"><?= $fmt($order['total_amount']) ?></div>
                            <?php if (!empty($order['paid_at'])): ?><div class="pay-time">Nhận tiền lúc <?= date('d/m/Y H:i', strtotime($order['paid_at'])) ?></div><?php endif; ?>
                        </div>
                    </div>

                    <?php if (!$pay_done && $current_status !== 'cancelled'): ?>
                        <form action="/lego_shop_php/admin/order/update_payment/<?= $order['id'] ?>" method="POST"
                              data-confirm="Xác nhận đã nhận <?= $fmt($order['total_amount']) ?>?">
                            <input type="hidden" name="payment_status" value="paid">
                            <div class="ui-field"><input type="text" name="payment_note" class="ui-input" placeholder="Ghi chú (mã giao dịch)"></div>
                            <button type="submit" class="ui-btn ui-btn-primary ui-btn-block"><i class="fa-solid fa-check"></i> Xác nhận đã nhận tiền</button>
                        </form>
                    <?php endif; ?>

                    <?php if (!empty($transactions)): ?>
                        <div class="ui-label" style="margin-top: 14px;">Giao dịch ghi nhận</div>
                        <table class="ui-table">
                            <?php foreach ($transactions as $tx): ?>
                                <tr>
                                    <td class="ui-small ui-muted ui-nowrap"><?= date('d/m H:i', strtotime($tx['created_at'])) ?></td>
                                    <td class="ui-right"><strong><?= $fmt($tx['amount']) ?></strong></td>
                                    <td class="ui-right"><?php if ($tx['status'] !== 'success'): ?><span class="ui-badge attn">Cần kiểm tra</span><?php endif; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($refund)):
                $rf_labels = ['pending' => 'Chờ xử lý', 'approved' => 'Chờ chuyển tiền', 'rejected' => 'Từ chối', 'completed' => 'Đã hoàn tiền'];
            ?>
                <div class="ui-card">
                    <h3 class="ui-card-title">Hoàn tiền
                        <a href="/lego_shop_php/admin/refund/detail/<?= $refund['id'] ?>" class="ui-link">#RF-<?= $refund['id'] ?></a>
                    </h3>
                    <dl class="ui-dl" style="grid-template-columns: 80px 1fr;">
                        <dt>Loại</dt><dd><?= $refund['type'] === 'return' ? 'Trả hàng' : 'Hủy đơn đã thanh toán' ?></dd>
                        <dt>Số tiền</dt><dd><?= $fmt($refund['amount']) ?></dd>
                        <dt>Trạng thái</dt><dd><span class="ui-badge <?= in_array($refund['status'], ['pending', 'approved']) ? 'attn' : '' ?>"><?= $rf_labels[$refund['status']] ?></span></dd>
                    </dl>
                </div>
            <?php endif; ?>

            <?php if (in_array($current_status, ['pending', 'confirmed', 'shipping'])): ?>
                <div class="ui-card ui-card-emphasis">
                    <h3 class="ui-card-title"><i class="fa-solid fa-arrows-rotate"></i> Cập nhật đơn hàng</h3>
                    <div class="status-now">Trạng thái hiện tại <span class="ui-badge <?= $current_status === 'pending' ? 'attn' : '' ?>"><?= $status_lbl ?></span></div>
                    <form action="/lego_shop_php/admin/order/update_status/<?= $order['id'] ?>" method="POST" id="statusUpdateForm">
                        <div class="ui-field">
                            <label class="ui-label">Chuyển sang</label>
                            <select name="status" id="order_status_select" class="ui-input">
                                <?php if ($current_status === 'pending'): ?>
                                    <option value="confirmed" selected>Xác nhận đơn</option>
                                <?php elseif ($current_status === 'confirmed'): ?>
                                    <option value="shipping" selected>Đang giao</option>
                                <?php else: ?>
                                    <option value="delivered" selected>Giao thành công</option>
                                <?php endif; ?>
                                <option value="cancelled">Hủy đơn</option>
                            </select>
                        </div>
                        <div class="ui-field">
                            <label class="ui-label">Ghi chú</label>
                            <textarea name="note" class="ui-input" rows="2" placeholder="Lý do hủy, mã vận đơn..."></textarea>
                        </div>

                        <?php if ($order['payment_status'] === 'paid'): ?>
                            <div id="cancelRefundBox" style="display: none;">
                                <label class="ui-label">Tài khoản nhận tiền hoàn của khách</label>
                                <div class="ui-field">
                                    <select name="bank_name" class="ui-input">
                                        <option value="">Chọn ngân hàng</option>
                                        <?php foreach ($banks as $b): ?><option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option><?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="ui-field"><input type="text" name="bank_account_number" class="ui-input" placeholder="Số tài khoản"></div>
                                <div class="ui-field"><input type="text" name="bank_account_name" class="ui-input" placeholder="Chủ tài khoản" style="text-transform: uppercase;"></div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="ui-btn ui-btn-primary ui-btn-block"><i class="fa-solid fa-floppy-disk"></i> Cập nhật trạng thái</button>
                    </form>
                </div>
            <?php endif; ?>

            <div class="ui-card">
                <h3 class="ui-card-title">Lịch sử</h3>
                <ul class="ui-timeline" style="max-height: 320px; overflow-y: auto;">
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
    </div>
</div>

<script>
document.getElementById('statusUpdateForm')?.addEventListener('submit', async function (e) {
    if (this.dataset.confirmed === '1') return;
    const newStatus = document.getElementById('order_status_select').value;
    const isOnline = <?= $is_online ? 'true' : 'false' ?>;
    const paid = <?= $order['payment_status'] === 'paid' ? 'true' : 'false' ?>;

    if (newStatus === 'delivered' && isOnline && !paid) {
        e.preventDefault();
        uiAlert('Đơn chưa được thanh toán.');
        return;
    }
    if (newStatus === 'cancelled') {
        e.preventDefault();
        if (await uiConfirm(paid ? 'Hủy đơn và tạo yêu cầu hoàn tiền cho khách?' : 'Hủy đơn này?', { danger: true, okText: 'Hủy đơn' })) {
            this.dataset.confirmed = '1';
            this.submit();
        }
    }
});

document.getElementById('order_status_select')?.addEventListener('change', function () {
    const box = document.getElementById('cancelRefundBox');
    if (box) box.style.display = this.value === 'cancelled' ? 'block' : 'none';
});
</script>
