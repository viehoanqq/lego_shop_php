<?php
$fmt = function ($n) { return number_format($n, 0, ',', '.') . 'đ'; };
$remaining = max(0, $expire_at - time());
?>
<link rel="stylesheet" href="/lego_shop_php/public/assets/css/cart.css?v=<?= time() ?>">
<style>
    .qr-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 900px; margin: 0 auto; }
    .qr-card { background: #fff; border-radius: 10px; padding: 26px; border: 1px solid #eee; }
    .qr-card h3 { font-size: 16px; font-weight: 700; color: #333; margin-bottom: 14px; }
    .qr-row { display: flex; justify-content: space-between; align-items: center; gap: 10px; padding: 11px 0; border-bottom: 1px solid #f3f3f3; font-size: 15px; }
    .qr-row:last-child { border-bottom: none; }
    .qr-row .lbl { color: #777; }
    .qr-row .val { font-weight: 700; color: #333; display: flex; align-items: center; gap: 8px; }
    .btn-copy { background: none; border: 1px solid #ddd; border-radius: 4px; padding: 3px 7px; cursor: pointer; color: #666; }
    .btn-copy:hover { border-color: #a4161a; color: #a4161a; }

    .qr-center { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .qr-img-wrap { position: relative; width: 280px; max-width: 100%; border: 1px solid #eee; border-radius: 10px; padding: 8px; }
    .qr-img-wrap img { width: 100%; display: block; }
    .qr-overlay { position: absolute; inset: 0; background: rgba(255,255,255,.94); border-radius: 10px; display: none; align-items: center; justify-content: center; font-weight: 700; color: #333; }
    .qr-img-wrap.done .qr-overlay { display: flex; }
    .qr-status { margin-top: 14px; font-size: 14px; color: #666; }
    .qr-status strong { color: #333; font-variant-numeric: tabular-nums; }

    .qr-actions { display: flex; flex-direction: column; gap: 8px; width: 100%; max-width: 280px; margin-top: 18px; }
    .qr-actions a, .qr-actions button { padding: 12px; border-radius: 8px; font-weight: 600; font-size: 14px; text-align: center; cursor: pointer; text-decoration: none; border: 1px solid #ddd; background: #fff; color: #333; }
    .qr-actions .primary { background: #a4161a; border-color: #a4161a; color: #fff; }
    @media (max-width: 800px) { .qr-layout { grid-template-columns: 1fr; } }
</style>

<div class="checkout-page-wrapper">
    <div class="checkout-container">
        <?php $current_step = 3; require __DIR__ . '/../../components/checkout_progress.php'; ?>

        <div class="checkout-header-title">
            <h2>Thanh toán đơn hàng #<?= (int)$order['id'] ?></h2>
        </div>

        <div class="qr-layout">
            <div class="qr-card">
                <h3><i class="fa-solid fa-building-columns" style="color: #a4161a;"></i> Thông tin chuyển khoản</h3>
                <div class="qr-row"><span class="lbl">Ngân hàng</span><span class="val"><?= htmlspecialchars($settings['bank_code']) ?></span></div>
                <div class="qr-row"><span class="lbl">Chủ tài khoản</span><span class="val"><?= htmlspecialchars($settings['bank_account_name']) ?></span></div>
                <div class="qr-row">
                    <span class="lbl">Số tài khoản</span>
                    <span class="val"><?= htmlspecialchars($settings['bank_account_no']) ?>
                        <button type="button" class="btn-copy" title="Sao chép" onclick="copyText('<?= htmlspecialchars($settings['bank_account_no']) ?>')"><i class="fa-regular fa-copy"></i></button>
                    </span>
                </div>
                <div class="qr-row">
                    <span class="lbl">Số tiền</span>
                    <span class="val"><?= $fmt($order['total_amount']) ?>
                        <button type="button" class="btn-copy" title="Sao chép" onclick="copyText('<?= (int)$order['total_amount'] ?>')"><i class="fa-regular fa-copy"></i></button>
                    </span>
                </div>
                <div class="qr-row">
                    <span class="lbl">Nội dung</span>
                    <span class="val"><?= htmlspecialchars($order['payment_code']) ?>
                        <button type="button" class="btn-copy" title="Sao chép" onclick="copyText('<?= htmlspecialchars($order['payment_code']) ?>')"><i class="fa-regular fa-copy"></i></button>
                    </span>
                </div>
            </div>

            <div class="qr-card qr-center">
                <div class="qr-img-wrap" id="qrWrap">
                    <img src="<?= htmlspecialchars($qr_url) ?>" alt="Mã VietQR">
                    <div class="qr-overlay" id="qrOverlay"></div>
                </div>

                <div class="qr-status" id="qrStatus">Hết hạn sau <strong id="countdown">--:--</strong></div>

                <div class="qr-actions">
                    <?php if ($sandbox): ?>
                        <button type="button" id="btnSandbox" onclick="simulatePayment()">Giả lập thanh toán</button>
                    <?php endif; ?>
                    <a href="/lego_shop_php/checkout/view_order?order_id=<?= (int)$order['id'] ?>">Xem đơn hàng</a>
                    <a href="/lego_shop_php/profile/orders" class="primary">Thanh toán sau</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const orderId = <?= (int)$order['id'] ?>;
    let remaining = <?= (int)$remaining ?>;
    let finished = false;
    const wrap = document.getElementById('qrWrap');
    const overlay = document.getElementById('qrOverlay');
    const status = document.getElementById('qrStatus');

    function finish(text) {
        finished = true;
        wrap.classList.add('done');
        overlay.innerText = text;
        status.style.display = 'none';
        const sb = document.getElementById('btnSandbox');
        if (sb) sb.style.display = 'none';
    }

    function showPaid() {
        finish('Thanh toán thành công');
        setTimeout(() => location.href = '/lego_shop_php/checkout/success?order_id=' + orderId, 1200);
    }

    function tick() {
        if (finished) return;
        if (remaining <= 0) { checkStatus(); return; }
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        document.getElementById('countdown').innerText = m + ':' + s;
        remaining--;
    }
    tick();
    const timer = setInterval(() => { tick(); if (finished) clearInterval(timer); }, 1000);

    // Hỏi trạng thái thanh toán mỗi 4 giây (webhook ngân hàng cập nhật DB)
    function checkStatus() {
        if (finished) return;
        fetch('/lego_shop_php/payment/status?order_id=' + orderId)
            .then(res => res.json())
            .then(data => {
                if (!data.success) return;
                if (data.payment_status === 'paid') showPaid();
                else if (data.status === 'cancelled') finish('Mã QR đã hết hạn');
            })
            .catch(() => {});
    }
    const poll = setInterval(() => { if (finished) clearInterval(poll); else checkStatus(); }, 4000);

    window.simulatePayment = function () {
        const btn = document.getElementById('btnSandbox');
        btn.disabled = true;
        fetch('/lego_shop_php/payment/simulate', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) showPaid();
            else { btn.disabled = false; showToast(data.message, 'error'); }
        })
        .catch(() => { btn.disabled = false; showToast('Lỗi kết nối máy chủ!', 'error'); });
    };

    window.copyText = function (text) {
        navigator.clipboard.writeText(text).then(() => showToast('Đã sao chép', 'success'));
    };
})();
</script>
