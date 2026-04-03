
<style>
    /* CSS cho trang Chi tiết đơn hàng */
    .order-detail-box { background: #fff; border-radius: 12px; padding: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; margin-bottom: 30px; width: 80%; margin: 0 auto 30px auto; }
    
    .order-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 25px; }
    .order-header h2 { margin: 0; color: #a4161a; font-size: 24px; font-weight: 800; }
    .order-status { background: #fff5f5; color: #e03131; padding: 8px 15px; border-radius: 6px; font-weight: 700; border: 1px solid #ffc9c9; }

    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
    .info-card { background: #fdfdfd; padding: 20px; border-radius: 8px; border: 1px dashed #ccc; }
    .info-card h3 { margin-top: 0; font-size: 16px; color: #333; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
    .info-card p { margin: 5px 0; color: #555; font-size: 15px; line-height: 1.6; }
    .info-card strong { color: #222; }

    /* Bảng sản phẩm */
    .table-items { width: 100%; border-collapse: collapse; margin-top: 10px; }
    .table-items th { background: #f8f9fa; padding: 15px; text-align: left; font-weight: 700; color: #444; border-bottom: 2px solid #eee; }
    .table-items td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    .item-product { display: flex; align-items: center; gap: 15px; }
    .item-product img { width: 60px; height: 60px; object-fit: contain; border: 1px solid #eee; border-radius: 6px; background: #fff; }
    .item-name { font-weight: 600; color: #333; font-size: 15px; }

    .total-section { width: 350px; margin-left: auto; margin-top: 20px; }
    .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 15px; color: #555; }
    .total-row.final { border-top: 2px solid #eee; padding-top: 15px; margin-top: 5px; font-size: 20px; font-weight: 800; color: #a4161a; }

    .btn-wrap { text-align: center; margin-top: 40px; }
    .btn-continue { display: inline-block; background: #e03131; color: #fff; padding: 15px 35px; margin: 0 15px; border-radius: 8px; font-size: 16px; font-weight: 700; text-decoration: none; transition: 0.3s; }
    .btn-continue:hover { background: #c92a2a; transform: translateY(-2px); }

    @media (max-width: 768px) { .info-grid { grid-template-columns: 1fr; } .total-section { width: 100%; } }
    .btn-cancel-order { display: inline-block; padding: 13px 25px; border-radius: 8px; font-size: 16px; font-weight: 700; text-decoration: none; transition: 0.3s; border: 2px solid #e03131; color: #e03131; background: #fff; cursor: pointer; margin-right: 15px; }
    .btn-cancel-order:hover:not(:disabled) { background: #fff5f5; }
    .btn-cancel-order:disabled, .btn-cancel-order.disabled { border-color: #ccc; color: #999; background: #f5f5f5; cursor: not-allowed; }
</style>

<div class="checkout-page-wrapper">
    <div class="checkout-container">
        <?php $current_step = 5; require __DIR__ . '/../../components/checkout_progress.php'; ?>

        <div class="order-detail-box">
            <div class="order-header">
                <h2>Chi tiết đơn hàng #<?= htmlspecialchars($order['id']) ?></h2>
                <div class="order-status">
                    <?php 
                                if($order['status'] == 'pending') echo 'Đang chờ xử lý';
                                elseif($order['status'] == 'confirmed') echo 'Đã xác nhận';
                                elseif($order['status'] == 'delivered') echo 'Đã giao hàng';
                                elseif($order['status'] == 'cancelled') echo 'Đã hủy';
                                else echo 'Không xác định';
                            ?>
                </div>
                
            </div>

            <div class="info-grid">
                <div class="info-card">
                    <h3><i class="fa-solid fa-location-dot" style="color:#e03131;"></i> Thông tin người nhận</h3>
                    <p><strong><?= htmlspecialchars($order['shipping_fullname']) ?></strong></p>
                    <p>Điện thoại: <?= htmlspecialchars($order['shipping_phone']) ?></p>
                    <p>Địa chỉ: <?= htmlspecialchars($order['shipping_street']) ?>, <?= htmlspecialchars($order['shipping_ward']) ?>, <?= htmlspecialchars($order['shipping_district']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></p>
                </div>
                
                <div class="info-card">
                    <h3><i class="fa-solid fa-credit-card" style="color:#e03131;"></i> Hình thức thanh toán</h3>
                    <p>Phương thức: 
                        <strong>
                            <?php 
                                if($order['payment_method'] == 'cash') echo 'Thanh toán khi nhận hàng (COD)';
                                elseif($order['payment_method'] == 'transfer') echo 'Chuyển khoản ngân hàng';
                                else echo 'Thanh toán trực tuyến';
                            ?>
                        </strong>
                    </p>
                    <p>Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                </div>
            </div>

            <h3 style="font-size: 18px; margin-bottom: 15px; color: #333;">Sản phẩm đã mua</h3>
            <div style="overflow-x: auto;">
                <table class="table-items">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">Đơn giá</th>
                            <th style="text-align: center;">Số lượng</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($order_items as $item): ?>
                            <tr>
                                <td>
                                    <div class="item-product">
                                        <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($item['image_url']) ?>" alt="IMG">
                                        <span class="item-name"><?= htmlspecialchars($item['name']) ?></span>
                                    </div>
                                </td>
                                <td style="text-align: center; color: #666;"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td style="text-align: center; font-weight: 600;">x<?= $item['quantity'] ?></td>
                                <td style="text-align: right; font-weight: 700; color: #333;"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="total-section">
                <div class="total-row">
                    <span>Tạm tính</span>
                    <span><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                </div>
                <div class="total-row">
                    <span>Phí vận chuyển</span>
                    <span style="color: #2f9e44; font-weight: 600;">Miễn phí</span>
                </div>
                <div class="total-row final">
                    <span>Tổng cộng</span>
                    <span><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                </div>
            </div>
            
            <div class="btn-wrap">
                                <a href="/lego_shop_php/home" class="btn-continue"><i class="fa-solid fa-shopping-bag"></i>   Tiếp tục mua sắm</a>
                <a href="/lego_shop_php/profile/orders" class="btn-continue"><i class="fa-solid fa-shopping-cart"></i>   Danh sách đơn hàng</a>
            </div>
        </div>
    </div>
</div>