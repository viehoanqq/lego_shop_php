<style>
    /* BẢO VỆ LAYOUT ADMIN */
    .admin-order-wrapper {
        position: relative;
        z-index: 1;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        min-height: calc(100vh - 100px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    /* HEADER PAGE */
    .order-header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e2e8f0;
    }
    .order-header-top h2 { margin: 0; color: #0f172a; font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    .btn-back { background: #fff; color: #475569; border: 1px solid #cbd5e1; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
    .btn-back:hover { background: #f8fafc; color: #0f172a; }

    /* LAYOUT 2 CỘT */
    .order-dashboard { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .order-dashboard { grid-template-columns: 1fr; } }

    /* THẺ CARD TRÁI (THÔNG TIN) */
    .admin-card { background: #fff; border-radius: 8px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
    .card-title { font-size: 14px; font-weight: 700; color: #334155; margin: 0 0 15px 0; display: flex; align-items: center; gap: 8px; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;}
    
    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .info-block { font-size: 13px; color: #475569; line-height: 1.6; }
    .info-line { display: flex; margin-bottom: 8px; align-items: flex-start; gap: 8px; }
    .info-line i { color: #94a3b8; width: 14px; margin-top: 3px; }
    .info-line strong { color: #1e293b; min-width: 80px; }

    /* BẢNG SẢN PHẨM */
    .product-table { width: 100%; border-collapse: collapse; }
    .product-table th { background: #f8fafc; color: #64748b; font-size: 12px; padding: 10px; text-align: left; font-weight: 600; border-bottom: 1px solid #e2e8f0;}
    .product-table td { padding: 12px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; font-size: 13px; }
    .product-img { width: 40px; height: 40px; object-fit: contain; border-radius: 4px; border: 1px solid #e2e8f0; }

    /* THẺ CARD PHẢI (THAO TÁC - MÀU XÁM NHẸ) */
    .card-right { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
    .card-right .card-title { color: #475569; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 12px; font-size: 13px; }

    /* FORM CHUẨN */
    .form-label { display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 5px; }
    .form-control { width: 100%; padding: 8px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; outline: none; transition: 0.2s; box-sizing: border-box; background: #fff; color: #1e293b; }
    .form-control:focus { border-color: #3b82f6; }
    .btn-submit { width: 100%; background: #3b82f6; color: #fff; border: none; padding: 10px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 6px; margin-top: 10px; }
    .btn-submit:hover { background: #2563eb; }

    /* TIMELINE (LỊCH SỬ) SIÊU GỌN */
    .timeline-wrapper { max-height: 250px; overflow-y: auto; padding-right: 5px; margin-top: 10px; }
    .timeline-wrapper::-webkit-scrollbar { width: 4px; }
    .timeline-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .timeline-item { position: relative; padding-left: 15px; padding-bottom: 15px; border-left: 2px solid #e2e8f0; margin-left: 5px; }
    .timeline-item:last-child { border-left-color: transparent; padding-bottom: 0; }
    .timeline-dot { position: absolute; left: -5px; top: 2px; width: 8px; height: 8px; border-radius: 50%; border: 2px solid #f8fafc; box-sizing: content-box; }
    .timeline-content { position: relative; top: -4px; }
    .timeline-time { font-size: 11px; color: #94a3b8; font-weight: 600; display: block; margin-bottom: 2px; }
    .timeline-st { font-size: 13px; font-weight: 600; color: #1e293b; }
    .timeline-note { margin-top: 4px; font-size: 12px; color: #64748b; font-style: italic; background: #fff; padding: 5px 8px; border-radius: 4px; border-left: 2px solid #cbd5e1; border-right: 1px solid #e2e8f0; border-top: 1px solid #e2e8f0; border-bottom: 1px solid #e2e8f0;}
</style>

<div class="admin-order-wrapper">
    
    <div class="order-header-top">
        <h2><i class="fa-solid fa-file-invoice-dollar" style="color: #3b82f6;"></i> Đơn hàng #DH-<?= $order['id'] ?></h2>
        <a href="/lego_shop_php/adminorder" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div style="background: #f0fdf4; color: #15803d; padding: 12px 15px; border-radius: 6px; margin-bottom: 20px; font-size: 13px; font-weight: 600; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 8px;">
            <i class="fa-solid fa-circle-check"></i> Cập nhật dữ liệu thành công!
        </div>
    <?php endif; ?>

    <div class="order-dashboard">
        
        <div class="dashboard-left">
            <div class="admin-card">
                <h3 class="card-title"><i class="fa-regular fa-address-card"></i> Thông tin Giao hàng & Thanh toán</h3>
                <div class="info-grid">
                    <div class="info-block">
                        <div class="info-line"><i class="fa-regular fa-user"></i> <strong>Khách hàng:</strong> <span style="color: #3b82f6; font-weight: 700;"><?= htmlspecialchars($order['shipping_fullname']) ?></span></div>
                        <div class="info-line"><i class="fa-solid fa-phone"></i> <strong>Điện thoại:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></div>
                        <div class="info-line"><i class="fa-solid fa-location-dot"></i> <strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_street']) ?>, <?= htmlspecialchars($order['shipping_ward']) ?>, <?= htmlspecialchars($order['shipping_district']) ?>, <?= htmlspecialchars($order['shipping_city']) ?></div>
                    </div>
                    <div class="info-block">
                        <div class="info-line"><i class="fa-regular fa-calendar"></i> <strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                        <?php $pay_methods = ['cash' => 'COD (Tiền mặt)', 'transfer' => 'Chuyển khoản', 'online' => 'Online']; ?>
                        <div class="info-line"><i class="fa-solid fa-money-check-dollar"></i> <strong>Hình thức:</strong> <?= $pay_methods[$order['payment_method']] ?? strtoupper($order['payment_method']) ?></div>
                        <div class="info-line"><i class="fa-solid fa-sack-dollar"></i> <strong>Tổng tiền:</strong> <span style="color: #ef4444; font-weight: 800; font-size: 15px;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span></div>
                    </div>
                </div>
            </div>

            <div class="admin-card">
                <h3 class="card-title"><i class="fa-solid fa-box-open"></i> Sản phẩm trong đơn</h3>
                <div style="overflow-x: auto;">
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th style="text-align: center;">Số lượng</th>
                                <th style="text-align: right;">Đơn giá</th>
                                <th style="text-align: right;">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td style="display: flex; align-items: center; gap: 10px;">
                                    <img src="/lego_shop_php/public/assets/images/<?= $item['image_url'] ?? 'default-lego.jpg' ?>" width="40" style="border-radius: 4px; border: 1px solid #e2e8f0; padding: 2px;">
                                    <strong><?= htmlspecialchars($item['name']) ?></strong>
                                </td>
                                <td style="text-align: center; font-weight: 600;"><?= $item['quantity'] ?></td>
                                <td style="text-align: right; color: #64748b;"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                                <td style="text-align: right; font-weight: 700; color: #ef4444;"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right; padding-top: 15px; font-weight: 700; color: #475569; border: none;">TỔNG TIỀN :</td>
                                <td style="text-align: right; padding-top: 15px; font-weight: 800; color: #ef4444; font-size: 16px; border: none;">
                                    <?= number_format($order['total_amount'], 0, ',', '.') ?>đ
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="dashboard-right">
            
            <div class="card-right">
                <h3 class="card-title"><i class="fa-solid fa-money-bill-transfer"></i> Xác nhận Thanh toán</h3>
                
                <?php if ($order['payment_method'] === 'transfer'): ?>
                    <?php if (($order['payment_status'] ?? 'unpaid') === 'paid'): ?>
                        <div style="padding: 15px; background: #f0fdf4; color: #15803d; border-radius: 6px; text-align: center; font-weight: 600; border: 1px solid #bbf7d0;">
                            <i class="fa-solid fa-check-double"></i> Đã xác nhận Thanh toán
                        </div>
                    <?php else: ?>
                        <form action="/lego_shop_php/adminorder/update_payment/<?= $order['id'] ?>" method="POST" onsubmit="return confirm('XÁC NHẬN:\nBạn đã nhận được tiền từ khách hàng này chưa?')">
                            <select name="payment_status" class="form-control">
                                <option value="unpaid" selected>Chưa thanh toán</option>
                                <option value="paid">Đã thanh toán (Nhận được tiền)</option>
                            </select>
                            <button type="submit" class="btn-submit"><i class="fa-solid fa-check"></i> Lưu Thanh Toán</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="font-size: 12px; color: #64748b; text-align: center; padding: 10px 0;">
                        <?php if ($order['payment_method'] === 'online'): ?>
                            <i class="fa-solid fa-circle-check" style="color: #22c55e;"></i> Đã thanh toán Online qua VNPay
                        <?php else: ?>
                            <i class="fa-solid fa-truck" style="color: #64748b;"></i> Thanh toán khi nhận hàng (COD)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="card-right">
                <h3 class="card-title"><i class="fa-solid fa-arrows-rotate"></i> Cập nhật Đơn hàng</h3>
                
                <?php 
                $current_status = $order['status'];
                
                // Trạng thái đã chốt (Không cho sửa nữa)
                if ($current_status === 'delivered'): ?>
                    <div style="padding: 15px; background: #f0fdf4; color: #15803d; border-radius: 6px; text-align: center; font-weight: 600; border: 1px solid #bbf7d0;">
                        <i class="fa-solid fa-check-circle"></i> Đơn hàng đã giao thành công.
                    </div>
                <?php elseif ($current_status === 'cancelled'): ?>
                    <div style="padding: 15px; background: #fef2f2; color: #b91c1c; border-radius: 6px; text-align: center; font-weight: 600; border: 1px solid #fecaca;">
                        <i class="fa-solid fa-ban"></i> Đơn hàng đã bị hủy.
                    </div>
                <?php else: ?>
                    <form action="/lego_shop_php/adminorder/update_status/<?= $order['id'] ?>" method="POST" id="statusUpdateForm">
                        
                        <div style="margin-bottom: 12px;">
                            <label class="form-label">Chọn trạng thái mới:</label>
                            <select name="status" id="order_status_select" class="form-control">
                                <?php if ($current_status === 'pending'): ?>
                                    <option value="pending" selected>Chờ xử lý (Hiện tại)</option>
                                    <option value="confirmed">Xác nhận đơn hàng</option>
                                    <option value="cancelled">Hủy đơn hàng</option>
                                
                                <?php elseif ($current_status === 'confirmed'): ?>
                                    <option value="confirmed" selected>Đã xác nhận (Hiện tại)</option>
                                    <option value="shipping">Đang giao hàng</option>
                                    <option value="cancelled">Hủy đơn hàng</option>
                                
                                <?php elseif ($current_status === 'shipping'): ?>
                                    <option value="shipping" selected>Đang giao hàng (Hiện tại)</option>
                                    <option value="delivered">Giao thành công</option>
                                    <option value="cancelled">Hủy đơn</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div style="margin-bottom: 5px;">
                            <label class="form-label">Ghi chú (Note):</label>
                            <textarea name="note" class="form-control" rows="2" placeholder="Nhập lý do hủy hoặc ghi chú..."></textarea>
                        </div>

                        <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu Trạng Thái</button>
                    </form>
                <?php endif; ?>
            </div>

            <div class="card-right">
                <h3 class="card-title"><i class="fa-solid fa-clock-rotate-left"></i> Lịch sử thao tác</h3>
                <div class="timeline-wrapper">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #94a3b8;"></div>
                        <div class="timeline-content">
                            <span class="timeline-time"><?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></span>
                            <div class="timeline-st">Khách hàng đặt đơn</div>
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
                                <div class="timeline-dot" style="background: <?= $bg ?>;"></div>
                                <div class="timeline-content">
                                    <span class="timeline-time"><?= date('d/m/Y H:i:s', strtotime($h['changed_at'])) ?></span>
                                    <div class="timeline-st">Chuyển: <?= $st_dict[$h['status']] ?? $h['status'] ?></div>
                                    <?php if(!empty($h['note'])): ?>
                                        <div class="timeline-note">"<?= htmlspecialchars($h['note']) ?>"</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('statusUpdateForm')?.addEventListener('submit', function(e) {
    const newStatus = document.getElementById('order_status_select').value;
    const paymentMethod = '<?= $order['payment_method'] ?>';
    const currentPaymentStatus = '<?= $order['payment_status'] ?? 'unpaid' ?>';

    // RÀNG BUỘC: Nếu là Chuyển khoản, PHẢI xác nhận "Đã thanh toán" mới được chọn "Thành công"
    if (newStatus === 'delivered' && paymentMethod === 'transfer' && currentPaymentStatus !== 'paid') {
        e.preventDefault(); 
        alert('CẢNH BÁO LỖI LOGIC:\nĐơn hàng này thanh toán qua Chuyển khoản. Bạn phải xác nhận Đã nhận được tiền ở phía trên trước khi có thể đổi trạng thái đơn thành "Giao thành công".');
    }
});
</script>