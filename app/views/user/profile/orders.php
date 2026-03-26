<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box" style="border: 2px solid #a4161a; border-radius: 12px; padding: 30px; background: #fff;">
                <div class="address-header" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
                    <h2 class="section-title" style="margin: 0; color: #a4161a; font-size: 22px;">Đơn hàng của tôi</h2>
                    <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Quản lý và theo dõi trạng thái các đơn hàng</p>
                </div>

                <div class="order-list">
                    <?php if (empty($orders)): ?>
                        <div style="text-align: center; padding: 40px; color: #888;">
                            <i class="fa-solid fa-box-open" style="font-size: 45px; margin-bottom: 15px; color: #ddd;"></i>
                            <p style="font-size: 16px;">Bạn chưa có đơn hàng nào.</p>
                            <a href="/lego_shop_php/home" class="btn-submit-modal" style="display: inline-block; margin-top: 15px; text-decoration: none;">Mua sắm ngay</a>
                        </div>
                    <?php else: ?>
                        <?php 
                            $status_map = [
                                'pending' => ['label' => 'Chờ xử lý', 'class' => 'badge-pending'],
                                'confirmed' => ['label' => 'Đã xác nhận', 'class' => 'badge-confirmed'],
                                'delivered' => ['label' => 'Giao thành công', 'class' => 'badge-delivered'],
                                'cancelled' => ['label' => 'Đã hủy', 'class' => 'badge-cancelled'],
                                'shipping' => ['label' => 'Đang giao hàng', 'class' => 'badge-confirmed']
                            ];
                        ?>

                        <?php foreach($orders as $order): 
                            $st = $order['status'] ?? 'pending';
                            $status_label = $status_map[$st]['label'] ?? 'Không xác định';
                            $status_class = $status_map[$st]['class'] ?? 'badge-pending';
                        ?>
                            <div class="order-card" style="border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 15px; background: #fafafa;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                                    <div>
                                        <span style="font-weight: 800; color: #333; font-size: 15px;">ĐƠN HÀNG #<?= htmlspecialchars($order['id']) ?></span>
                                        <span style="color: #ccc; margin: 0 8px;">|</span>
                                        <span style="color: #666; font-size: 14px;"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></span>
                                        <span class="order-status-badge <?= $status_class ?>" style="margin-left: 10px;"><?= $status_label ?></span>
                                    </div>
                                    
                                    <button class="btn-view-order" data-id="<?= $order['id'] ?>" style="background: none; border: none; color: #0056b3; font-weight: 600; font-size: 14px; cursor: pointer;">
                                        Xem chi tiết
                                    </button>
                                </div>
                                
                                <div style="color: #555; font-size: 14px; line-height: 1.6;">
                                    <p style="margin: 0;"><strong>Người nhận:</strong> <?= htmlspecialchars($order['shipping_fullname']) ?> (<?= htmlspecialchars($order['shipping_phone']) ?>)</p>
                                    <p style="margin: 0;"><strong>Tổng tiền:</strong> <span style="color: #a4161a; font-weight: 700;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </div>
</div>

<div class="modal-overlay" id="orderDetailModal">
    <div class="modal-box" style="max-width: 700px; padding: 0; overflow: hidden;">
        <div class="modal-header" style="background: #a4161a; color: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalOrderTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: white;">Chi tiết đơn hàng</h3>
            <button type="button" class="btn-close-modal" id="closeOrderModalBtn" style="color: white; opacity: 0.8;">&times;</button>
        </div>
        
        <div class="order-modal-content" style="padding: 25px;">
            <div style="display: flex; gap: 20px; margin-bottom: 20px; font-size: 14px;">
                <div style="flex: 1; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; background: #fafafa;">
                    <strong style="color: #333;"><i class="fa-solid fa-location-dot text-red" style="color: #a4161a;"></i> Giao hàng đến</strong>
                    <p id="md_fullname" style="margin: 5px 0 2px 0; font-weight: 600;"></p>
                    <p id="md_phone" style="margin: 0 0 5px 0; color: #666;"></p>
                    <p id="md_address" style="margin: 0; color: #666;"></p>
                </div>
                <div style="flex: 1; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; background: #fafafa;">
                    <strong style="color: #333;"><i class="fa-solid fa-credit-card text-red" style="color: #a4161a;"></i> Thanh toán & Trạng thái</strong>
                    <p id="md_payment" style="margin: 5px 0 10px 0; color: #666;"></p>
                    <div id="md_status" style="display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold;"></div>
                </div>
            </div>

            <div style="max-height: 250px; overflow-y: auto; border: 1px solid #eee; border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                    <thead style="background: #f9f9f9; position: sticky; top: 0;">
                        <tr>
                            <th style="padding: 12px 15px; color: #333;">Sản phẩm</th>
                            <th style="padding: 12px 15px; text-align: center; color: #333;">Đơn giá</th>
                            <th style="padding: 12px 15px; text-align: center; color: #333;">SL</th>
                            <th style="padding: 12px 15px; text-align: right; color: #333;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="md_items_body">
                        </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                <div>
                    <a href="#" id="md_cancel_btn" class="btn-cancel-order" style="display: none;">Hủy đơn hàng này</a>
                </div>
                <div style="font-size: 16px; color: #333;">
                    Tổng cộng: <strong id="md_total_amount" style="color: #a4161a; font-size: 22px;">0đ</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Nhãn trạng thái thu nhỏ cho gọn */
    .order-status-badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-pending { background: #fff3cd; color: #f08c00; border: 1px solid #ffe066; }
    .badge-confirmed { background: #e7f5ff; color: #1971c2; border: 1px solid #a5d8ff; }
    .badge-delivered { background: #ebfbee; color: #2f9e44; border: 1px solid #b2f2bb; }
    .badge-cancelled { background: #fff5f5; color: #e03131; border: 1px solid #ffc9c9; }

    /* Nút xem chi tiết hover */
    .btn-view-order:hover { text-decoration: underline; }

    /* Style cho Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.5); display: none; justify-content: center; align-items: center;
        z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-box { transform: translateY(-20px); transition: transform 0.3s ease; background: #fff; border-radius: 10px; }
    .modal-overlay.show .modal-box { transform: translateY(0); }
    .btn-close-modal { background: none; border: none; font-size: 24px; cursor: pointer; }
    .btn-close-modal:hover { opacity: 1 !important; }

    /* CSS Nút Hủy Đơn trong Modal */
    .btn-cancel-order {
        display: inline-block; padding: 10px 20px; border: 1px solid #e03131;
        color: #e03131; background: #fff; border-radius: 6px;
        font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.2s; cursor: pointer;
    }
    .btn-cancel-order:hover:not(.disabled) { background: #fff5f5; }
    .btn-cancel-order.disabled {
        border-color: #ddd; color: #aaa; background: #f5f5f5; cursor: not-allowed;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('orderDetailModal');
    const closeBtn = document.getElementById('closeOrderModalBtn');
    const cancelBtn = document.getElementById('md_cancel_btn');
    
    const closeModal = () => modal.classList.remove('show');
    closeBtn.addEventListener('click', closeModal);
    window.addEventListener('click', (e) => { if (e.target == modal) closeModal(); });

    const formatVND = (num) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);

    const statusDict = {
        'pending': 'Chờ xử lý', 'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao hàng', 'delivered': 'Giao thành công', 'cancelled': 'Đã hủy'
    };
    const paymentDict = {
        'cash': 'Thanh toán khi nhận hàng (COD)', 'transfer': 'Chuyển khoản ngân hàng', 'online': 'Thanh toán trực tuyến'
    };

    document.querySelectorAll('.btn-view-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            const originalText = this.innerText;
            
            this.innerText = '...';
            this.style.pointerEvents = 'none';

            fetch('/lego_shop_php/profile/getOrderDetailsAjax', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            })
            .then(res => res.json())
            .then(data => {
                this.innerText = originalText;
                this.style.pointerEvents = 'auto';

                if(data.success) {
                    const order = data.order;
                    const items = data.items;

                    // Đổ text thông tin chung
                    document.getElementById('modalOrderTitle').innerText = 'Chi tiết đơn hàng #' + order.id;
                    document.getElementById('md_fullname').innerText = order.shipping_fullname;
                    document.getElementById('md_phone').innerText = order.shipping_phone;
                    document.getElementById('md_address').innerText = `${order.shipping_street}, ${order.shipping_ward}, ${order.shipping_district}, ${order.shipping_city}`;
                    
                    document.getElementById('md_payment').innerText = paymentDict[order.payment_method] || order.payment_method;
                    
                    // Xử lý màu sắc Badge trạng thái trong Modal
                    const stBox = document.getElementById('md_status');
                    stBox.innerText = statusDict[order.status] || order.status;
                    if(order.status === 'cancelled') { stBox.style.background = '#fff5f5'; stBox.style.color = '#e03131'; stBox.style.border = '1px solid #ffc9c9'; }
                    else if(order.status === 'delivered') { stBox.style.background = '#ebfbee'; stBox.style.color = '#2f9e44'; stBox.style.border = '1px solid #b2f2bb'; }
                    else if(order.status === 'confirmed' || order.status === 'shipping') { stBox.style.background = '#e7f5ff'; stBox.style.color = '#1971c2'; stBox.style.border = '1px solid #a5d8ff'; }
                    else { stBox.style.background = '#fff3cd'; stBox.style.color = '#f08c00'; stBox.style.border = '1px solid #ffe066'; }

                    document.getElementById('md_total_amount').innerText = formatVND(order.total_amount);

                    // Xử lý LOGIC NÚT HỦY ĐƠN
                    if (order.status === 'pending' || order.status === 'confirmed') {
                        cancelBtn.style.display = 'inline-block';
                        cancelBtn.className = 'btn-cancel-order';
                        cancelBtn.href = `/lego_shop_php/checkout/cancel_order?order_id=${order.id}`;
                        cancelBtn.onclick = function() { return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.'); };
                        cancelBtn.innerText = 'Hủy đơn hàng này';
                    } else if (order.status === 'shipping' || order.status === 'delivered') {
                        cancelBtn.style.display = 'inline-block';
                        cancelBtn.className = 'btn-cancel-order disabled';
                        cancelBtn.href = 'javascript:void(0)';
                        cancelBtn.onclick = function() { alert('Đơn hàng đang giao hoặc đã giao, không thể hủy.'); return false; };
                        cancelBtn.innerText = 'Không thể hủy (Đang giao)';
                    } else {
                        // Trạng thái đã hủy thì ẩn nút đi
                        cancelBtn.style.display = 'none';
                    }

                    // Đổ list sản phẩm
                    const tbody = document.getElementById('md_items_body');
                    tbody.innerHTML = ''; 
                    items.forEach(item => {
                        const imgUrl = item.image_url ? item.image_url : 'default-lego.jpg'; 
                        const tr = `
                            <tr>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; gap: 12px;">
                                    <img src="/lego_shop_php/public/assets/images/${imgUrl}" style="width:45px; height:45px; object-fit:contain; border:1px solid #ddd; border-radius:6px; background:#fff;">
                                    <span style="font-weight: 500; color: #333;">${item.name}</span>
                                </td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: center; color: #666;">${formatVND(item.price)}</td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: center; font-weight: 600; color: #333;">${item.quantity}</td>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: right; color: #a4161a; font-weight: bold;">${formatVND(item.price * item.quantity)}</td>
                            </tr>
                        `;
                        tbody.insertAdjacentHTML('beforeend', tr);
                    });

                    modal.classList.add('show');
                } else {
                    alert(data.message || 'Có lỗi xảy ra khi tải đơn hàng!');
                }
            })
            .catch(err => {
                console.error("Lỗi Fetch: ", err);
                this.innerText = originalText;
                this.style.pointerEvents = 'auto';
                alert("Lỗi kết nối đến máy chủ!");
            });
        });
    });
});
</script>