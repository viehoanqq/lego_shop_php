<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box">
                <div class="address-header">
                    <h2 class="section-title" style="margin: 0;">Lịch sử mua hàng</h2>
                </div>
                <p class="section-desc" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Quản lý và theo dõi trạng thái các đơn hàng của bạn</p>

                <div class="order-list">
                    <?php if (empty($orders)): ?>
                        <div style="text-align: center; padding: 40px; color: #888;">
                            <i class="fa-solid fa-box-open" style="font-size: 45px; margin-bottom: 15px; color: #ddd;"></i>
                            <p style="font-size: 16px;">Bạn chưa có đơn hàng nào.</p>
                            <a href="/lego_shop_php/home" class="btn-submit-modal" style="display: inline-block; margin-top: 15px; text-decoration: none;">Mua sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <?php 
                            // Từ điển dịch trạng thái ENUM trong DB sang tiếng Việt & CSS Class
                            $status_map = [
                                'pending' => ['label' => 'Chờ xử lý', 'class' => 'badge-pending'],
                                'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'badge-confirmed'],
                                'delivered' => ['label' => 'Giao thành công', 'class' => 'badge-delivered'],
                                'cancelled' => ['label' => 'Đã hủy', 'class' => 'badge-cancelled']
                            ];
                        ?>

                        <?php foreach($orders as $order): 
                            $st = $order['status'] ?? 'pending';
                            $status_label = $status_map[$st]['label'] ?? 'Không xác định';
                            $status_class = $status_map[$st]['class'] ?? 'badge-pending';
                        ?>
                            <div class="order-card">
                                <div class="order-header">
                                    <div class="order-id-date">
                                        <span class="order-id">Mã đơn: #<?= htmlspecialchars($order['id']) ?></span>
                                        <span class="order-date"><i class="fa-regular fa-clock"></i> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                                    </div>
                                    <div class="order-status-badge <?= $status_class ?>">
                                        <?= $status_label ?>
                                    </div>
                                </div>
                                
                                <div class="order-body">
                                    <div class="order-info">
                                        <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['shipping_fullname']) ?> - <?= htmlspecialchars($order['shipping_phone']) ?></p>
                                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_street']) ?>, <?= htmlspecialchars($order['shipping_ward']) ?>, <?= htmlspecialchars($order['shipping_district']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></p>
                                        <p><strong>Thanh toán:</strong> 
                                            <?php 
                                                if($order['payment_method'] == 'cash') echo 'Thanh toán khi nhận hàng (COD)';
                                                elseif($order['payment_method'] == 'transfer') echo 'Chuyển khoản ngân hàng';
                                                else echo 'Thanh toán trực tuyến';
                                            ?>
                                        </p>
                                    </div>
                                    <div class="order-price-wrap">
                                        <span class="price-label">Tổng tiền</span>
                                        <span class="price-value"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                                    </div>
                                </div>

                                <div class="order-footer">
                                    <a href="/lego_shop_php/checkout/view_order?order_id=<?= $order['id'] ?>" class="btn-outline-red">Xem chi tiết</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </section>

    </div>
</div>

<style>
    /* CSS RIÊNG CHO LỊCH SỬ ĐƠN HÀNG */
    .order-list { display: flex; flex-direction: column; gap: 20px; }
    
    .order-card {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s ease;
    }
    .order-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border-color: #ddd;
    }

    /* Header của Card */
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px dashed #eee;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .order-id { font-weight: 800; color: #333; font-size: 16px; margin-right: 15px; }
    .order-date { color: #888; font-size: 14px; }
    
    /* Nhãn trạng thái */
    .order-status-badge {
        padding: 6px 14px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
    }
    .badge-pending { background: #fff3cd; color: #f08c00; border: 1px solid #ffe066; }
    .badge-confirmed { background: #e7f5ff; color: #1971c2; border: 1px solid #a5d8ff; }
    .badge-delivered { background: #ebfbee; color: #2f9e44; border: 1px solid #b2f2bb; }
    .badge-cancelled { background: #fff5f5; color: #e03131; border: 1px solid #ffc9c9; }

    /* Body của Card */
    .order-body {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 20px;
    }
    .order-info { flex: 1; padding-right: 20px; }
    .order-info p { margin: 5px 0; color: #555; font-size: 14.5px; line-height: 1.5; }
    
    .order-price-wrap {
        text-align: right;
        min-width: 150px;
    }
    .price-label { display: block; color: #888; font-size: 13px; margin-bottom: 3px; }
    .price-value { font-size: 20px; font-weight: 800; color: #e03131; }

    /* Footer của Card (Nút bấm) */
    .order-footer {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-outline-red {
        display: inline-block;
        padding: 8px 20px;
        border: 1px solid #e03131;
        color: #e03131;
        background: transparent;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-outline-red:hover {
        background: #fff5f5;
    }
    
    /* Tái sử dụng class nút đỏ từ giao diện trước */
    .btn-submit-modal { 
        background: #a4161a; color: white; border: none; padding: 10px 24px; 
        border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; 
    }
    .btn-submit-modal:hover { background: #800f13; }

    @media (max-width: 768px) {
        .order-body { flex-direction: column; align-items: flex-start; gap: 15px; }
        .order-price-wrap { text-align: left; }
    }
</style>