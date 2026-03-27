<style>
    /* Bố cục Grid 2 cột hiện đại */
    .order-dashboard { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .order-dashboard { grid-template-columns: 1fr; } }

    .admin-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px; border: 1px solid #f1f5f9;}
    .card-header { font-size: 16px; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 15px; padding-bottom: 12px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; }
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; font-size: 14px; }
    .info-item p { margin: 6px 0; color: #475569; display: flex; align-items: center; gap: 8px;}
    .info-item p i { color: #94a3b8; width: 16px; text-align: center;}
    .info-item strong { color: #0f172a; min-width: 110px; display: inline-block;}
    
    .lego-table { width: 100%; border-collapse: collapse; }
    .lego-table th, .lego-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; text-align: left; }
    .lego-table th { background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; font-weight: 700; }
    
    .form-control { padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; width: 100%; font-size: 14px; box-sizing: border-box; background: #fff;}
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    .btn-submit { background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;}
    .btn-submit:hover { background: #2563eb; }

    /* Timeline History CSS */
    .timeline-container { position: relative; margin-top: 15px; padding-left: 25px; }
    .timeline-container::before { content: ''; position: absolute; left: 7px; top: 5px; bottom: 5px; width: 2px; background: #e2e8f0; }
    .timeline-item { position: relative; margin-bottom: 20px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; left: -30px; top: 0; width: 16px; height: 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid #fff; z-index: 2; }
    .timeline-content { background: #f8fafc; padding: 12px; border-radius: 8px; border: 1px solid #f1f5f9; }
    .timeline-time { font-size: 12px; color: #64748b; margin-bottom: 4px; display: block;}
    .timeline-title { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 4px;}
    .timeline-note { font-size: 13px; color: #475569; font-style: italic; background: #fff; padding: 6px; border-radius: 4px; border-left: 3px solid #cbd5e1;}
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h2 style="margin: 0; color: #1e293b; font-size: 24px;"><i class="fa-solid fa-file-invoice" style="color: #3b82f6;"></i> Xử lý Đơn hàng #DH-<?= $order['id'] ?></h2>
    <a href="/lego_shop_php/adminorder" style="color: #64748b; text-decoration: none; font-weight: 600; background: #fff; padding: 8px 16px; border-radius: 8px; border: 1px solid #e2e8f0; transition: 0.2s;"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
</div>

<?php if(isset($_GET['msg'])): ?>
    <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;">
        <i class="fa-solid fa-circle-check"></i> Cập nhật thành công!
    </div>
<?php endif; ?>

<div class="order-dashboard">
    <div class="dashboard-left">
        <div class="admin-card">
            <h3 class="card-header"><i class="fa-solid fa-address-card" style="color:#64748b;"></i> Thông tin Giao hàng & Thanh toán</h3>
            <div class="info-grid">
                <div class="info-item">
                    <p><i class="fa-solid fa-user"></i> <strong>Khách hàng:</strong> <span style="color: #3b82f6; font-weight: 600;"><?= htmlspecialchars($order['shipping_fullname']) ?></span></p>
                    <p><i class="fa-solid fa-phone"></i> <strong>Điện thoại:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></p>
                    <p><i class="fa-solid fa-location-dot"></i> <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_street']) ?>, <?= htmlspecialchars($order['shipping_ward']) ?>, <?= htmlspecialchars($order['shipping_district']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></p>
                </div>
                <div class="info-item">
                    <p><i class="fa-regular fa-calendar-plus"></i> <strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                    <?php 
                        $pay_methods = ['cash' => 'COD (Tiền mặt)', 'transfer' => 'Chuyển khoản', 'online' => 'Online'];
                    ?>
                    <p><i class="fa-solid fa-money-check-dollar"></i> <strong>Hình thức:</strong> <?= $pay_methods[$order['payment_method']] ?? strtoupper($order['payment_method']) ?></p>
                    <p><i class="fa-solid fa-sack-dollar"></i> <strong>Tổng tiền:</strong> <span style="color: #ef4444; font-weight: 800; font-size: 18px;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span></p>
                </div>
            </div>
        </div>

        <div class="admin-card">
            <h3 class="card-header"><i class="fa-solid fa-cubes" style="color:#64748b;"></i> Sản phẩm trong đơn</h3>
            <div style="overflow-x: auto;">
                <table class="lego-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th style="text-align: center;">SL</th>
                            <th style="text-align: right;">Đơn giá</th>
                            <th style="text-align: right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td style="display: flex; align-items: center; gap: 10px;">
                                <img src="/lego_shop_php/public/assets/images/<?= $item['image_url'] ?? 'default-lego.jpg' ?>" width="40" style="border-radius: 6px; border: 1px solid #e2e8f0; padding: 2px;">
                                <span style="font-weight: 600; color: #334155; font-size: 13px;"><?= htmlspecialchars($item['name']) ?></span>
                            </td>
                            <td style="text-align: center; font-weight: 700;"><?= $item['quantity'] ?></td>
                            <td style="text-align: right; color: #64748b;"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                            <td style="text-align: right; font-weight: 700; color: #ef4444;"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="dashboard-right">
        
        <div class="admin-card" style="background: #f0fdf4; border-color: #bbf7d0;">
            <h3 class="card-header" style="color: #15803d; border-color: #bbf7d0;"><i class="fa-solid fa-hand-holding-dollar"></i> Xác nhận Thanh toán</h3>
            <form action="/lego_shop_php/adminorder/update_payment/<?= $order['id'] ?>" method="POST">
                <select name="payment_status" class="form-control" style="margin-bottom: 10px;">
                    <option value="unpaid" <?= ($order['payment_status'] ?? '') == 'unpaid' ? 'selected' : '' ?>>❌ Chưa thanh toán</option>
                    <option value="paid" <?= ($order['payment_status'] ?? '') == 'paid' ? 'selected' : '' ?>>✅ Đã thanh toán</option>
                    <option value="refunded" <?= ($order['payment_status'] ?? '') == 'refunded' ? 'selected' : '' ?>>🔄 Đã hoàn tiền</option>
                </select>
                <button type="submit" class="btn-submit" style="background: #16a34a;"><i class="fa-solid fa-check"></i> Cập nhật TT</button>
            </form>
        </div>

        <div class="admin-card" style="background: #eff6ff; border-color: #bfdbfe;">
            <h3 class="card-header" style="color: #1d4ed8; border-color: #bfdbfe;"><i class="fa-solid fa-arrows-rotate"></i> Cập nhật Đơn hàng</h3>
            <form action="/lego_shop_php/adminorder/update_status/<?= $order['id'] ?>" method="POST">
                <div style="margin-bottom: 12px;">
                    <label style="font-size: 13px; font-weight: 600; color: #1e3a8a; margin-bottom: 5px; display: block;">Chọn trạng thái mới:</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>⏳ Chờ xử lý</option>
                        <option value="confirmed" <?= $order['status'] == 'confirmed' ? 'selected' : '' ?>>📦 Đã xác nhận</option>
                        <option value="shipping" <?= $order['status'] == 'shipping' ? 'selected' : '' ?>>🚚 Đang giao hàng</option>
                        <option value="delivered" <?= $order['status'] == 'delivered' ? 'selected' : '' ?>>✅ Giao thành công</option>
                        <option value="cancelled" <?= $order['status'] == 'cancelled' ? 'selected' : '' ?>>❌ Hủy đơn hàng</option>
                    </select>
                </div>
                
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 13px; font-weight: 600; color: #1e3a8a; margin-bottom: 5px; display: block;">Ghi chú (Tùy chọn):</label>
                    <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Khách hẹn giao sau, Lý do hủy..." style="resize: vertical;"></textarea>
                    <small style="color: #64748b; font-size: 11px;">Ghi chú này khách hàng sẽ nhìn thấy.</small>
                </div>

                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu trạng thái</button>
            </form>
        </div>

        <div class="admin-card">
            <h3 class="card-header"><i class="fa-solid fa-clock-rotate-left" style="color:#64748b;"></i> Lịch sử thao tác</h3>
            <div class="timeline-container">
                
                <div class="timeline-item">
                    <div class="timeline-icon" style="background: #e2e8f0;"><i class="fa-solid fa-plus" style="font-size: 8px; color: #64748b;"></i></div>
                    <div class="timeline-content">
                        <span class="timeline-time"><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></span>
                        <div class="timeline-title">Khách hàng đặt đơn</div>
                    </div>
                </div>

                <?php if(!empty($history)): ?>
                    <?php 
                        $st_dict = ['pending'=>'Chờ xử lý', 'confirmed'=>'Đã xác nhận', 'shipping'=>'Đang giao', 'delivered'=>'Thành công', 'cancelled'=>'Đã hủy'];
                        foreach($history as $h): 
                            $bg = '#3b82f6'; 
                            if($h['status'] == 'delivered') $bg = '#22c55e';
                            if($h['status'] == 'cancelled') $bg = '#ef4444';
                    ?>
                        <div class="timeline-item">
                            <div class="timeline-icon" style="background: <?= $bg ?>;"></div>
                            <div class="timeline-content">
                                <span class="timeline-time"><?= date('d/m/Y H:i:s', strtotime($h['changed_at'])) ?></span>
                                <div class="timeline-title">Chuyển sang: <?= $st_dict[$h['status']] ?? $h['status'] ?></div>
                                <?php if(!empty($h['note'])): ?>
                                    <div class="timeline-note"><i class="fa-solid fa-quote-left" style="color: #94a3b8; font-size: 10px;"></i> <?= htmlspecialchars($h['note']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>