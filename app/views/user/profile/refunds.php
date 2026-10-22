<?php
$rf_status = [
    'pending'   => 'Chờ xử lý',
    'approved'  => 'Đã duyệt - chờ chuyển tiền',
    'rejected'  => 'Bị từ chối',
    'completed' => 'Đã hoàn tiền',
];
$rf_type = ['cancel' => 'Hủy đơn đã thanh toán', 'return' => 'Trả hàng'];
?>
<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">

        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box" style="border: 2px solid #a4161a; border-radius: 12px; padding: 30px; background: #fff;">
                <div style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
                    <h2 class="section-title" style="margin: 0; color: #a4161a; font-size: 22px;">Hoàn tiền / Trả hàng</h2>
                </div>

                <?php if (empty($refunds)): ?>
                    <div style="text-align: center; padding: 40px; color: #888;"><i class="fa-solid fa-rotate-left" style="font-size: 45px; margin-bottom: 15px; color: #ddd;"></i><p>Bạn chưa có yêu cầu hoàn tiền nào.</p></div>
                <?php else: foreach ($refunds as $r): ?>
                    <div class="refund-card">
                        <div class="refund-head">
                            <div>
                                <strong>Đơn hàng <a href="/lego_shop_php/checkout/view_order?order_id=<?= (int)$r['order_id'] ?>">#<?= (int)$r['order_id'] ?></a></strong>
                                <span class="refund-status <?= in_array($r['status'], ['pending', 'approved']) ? 'open' : '' ?>"><?= $rf_status[$r['status']] ?? $r['status'] ?></span>
                            </div>
                            <div class="refund-right">
                                <span class="refund-date"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></span>
                                <a href="/lego_shop_php/checkout/view_order?order_id=<?= (int)$r['order_id'] ?>" class="refund-view"><i class="fa-solid fa-eye"></i> Xem</a>
                            </div>
                        </div>
                        <p><?= $rf_type[$r['type']] ?? $r['type'] ?> · <strong><?= number_format($r['amount'], 0, ',', '.') ?>đ</strong>
                            <?php if (!empty($r['bank_account_number'])): ?> · <?= htmlspecialchars($r['bank_name']) ?> - <?= htmlspecialchars($r['bank_account_number']) ?><?php endif; ?>
                        </p>
                        <?php if (!empty($r['reason'])): ?><p>Lý do: <?= htmlspecialchars($r['reason']) ?></p><?php endif; ?>
                        <?php if (!empty($r['admin_note'])): ?><p>Phản hồi: <?= htmlspecialchars($r['admin_note']) ?></p><?php endif; ?>
                    </div>
                <?php endforeach; endif; ?>
            </div>
        </section>
    </div>
</div>

<style>
    .refund-card { border: 1px solid #eee; border-radius: 8px; padding: 16px 18px; margin-bottom: 12px; background: #fff; }
    .refund-head { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; color: #333; }
    .refund-head a { color: #333; }
    .refund-status { margin-left: 10px; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 4px; background: #f1f3f5; color: #555; }
    .refund-status.open { background: #fff; color: #a4161a; border: 1px solid #f0c4c6; }
    .refund-date { color: #888; font-size: 13px; }
    .refund-right { display: flex; align-items: center; gap: 12px; }
    .refund-view { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border: 1px solid #a4161a; border-radius: 6px; color: #a4161a; font-size: 13px; font-weight: 600; text-decoration: none; }
    .refund-view:hover { background: #a4161a; color: #fff; }
    .refund-card p { margin: 3px 0; font-size: 14px; color: #555; line-height: 1.6; }
</style>
