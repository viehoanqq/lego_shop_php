<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">

        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box" style="border: 2px solid #a4161a; border-radius: 12px; padding: 30px; background: #fff;">
                <div style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
                    <h2 class="section-title" style="margin: 0; color: #a4161a; font-size: 22px;">Mã giảm giá</h2>
                </div>

                <?php if (empty($vouchers)): ?>
                    <div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-ticket" style="font-size: 45px; margin-bottom: 15px; color: #ddd;"></i><p>Hiện chưa có mã giảm giá nào.</p></div>
                <?php else: ?>
                    <div class="voucher-grid">
                        <?php foreach ($vouchers as $v): $out = $v['remaining_for_user'] <= 0; ?>
                            <div class="voucher-item <?= $out ? 'used' : '' ?>">
                                <div>
                                    <div class="v-code"><?= htmlspecialchars($v['code']) ?></div>
                                    <div class="v-desc"><?= htmlspecialchars(VoucherModel::describe($v)) ?></div>
                                    <div class="v-cond">
                                        Đơn từ <?= number_format($v['min_order_value'], 0, ',', '.') ?>đ
                                        <?php if (!empty($v['end_date'])): ?> · HSD <?= date('d/m/Y', strtotime($v['end_date'])) ?><?php endif; ?>
                                        · <?= $out ? 'Đã dùng hết lượt' : 'Còn ' . $v['remaining_for_user'] . ' lượt' ?>
                                    </div>
                                </div>
                                <?php if (!$out): ?>
                                    <button type="button" onclick="copyVoucher('<?= htmlspecialchars($v['code']) ?>')">Sao chép</button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</div>

<style>
    .voucher-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(300px, 100%), 1fr)); gap: 12px; }
    .voucher-item { display: flex; justify-content: space-between; align-items: center; gap: 10px; border: 1px solid #e5e5e5; border-radius: 8px; padding: 14px 16px; background: #fff; }
    .voucher-item .v-code { font-weight: 700; color: #333; letter-spacing: .5px; }
    .voucher-item .v-desc { font-size: 14px; color: #333; margin-top: 2px; }
    .voucher-item .v-cond { font-size: 12px; color: #888; margin-top: 4px; }
    .voucher-item button { background: #fff; border: 1px solid #a4161a; color: #a4161a; border-radius: 4px; padding: 6px 12px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap; }
    .voucher-item button:hover { background: #a4161a; color: #fff; }
    .voucher-item.used { opacity: .5; }
</style>

<script>
function copyVoucher(code) {
    navigator.clipboard.writeText(code).then(() => showToast('Đã sao chép mã ' + code, 'success'));
}
</script>
