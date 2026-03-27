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
                                'shipping' => ['label' => 'Đang giao hàng', 'class' => 'badge-shipping']
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
        
        <div class="order-modal-content" style="padding: 25px; max-height: 80vh; overflow-y: auto;">
            
            <div id="md_timeline" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #fff5f5; border: 1px solid #fed7d7;">
                <h4 style="margin: 0 0 10px 0; color: #c53030; font-size: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Tiến trình đơn hàng</h4>
                <div id="md_timeline_content" style="font-size: 13px; color: #4a5568; line-height: 1.6;">
                    </div>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 20px; font-size: 14px;">
                <div style="flex: 1; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; background: #fafafa;">
                    <strong style="color: #333;"><i class="fa-solid fa-location-dot text-red" style="color: #a4161a;"></i> Giao hàng đến</strong>
                    <p id="md_fullname" style="margin: 5px 0 2px 0; font-weight: 600;"></p>
                    <p id="md_phone" style="margin: 0 0 5px 0; color: #666;"></p>
                    <p id="md_address" style="margin: 0; color: #666;"></p>
                </div>
                <div style="flex: 1; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; background: #fafafa;">
                    <strong style="color: #333;"><i class="fa-solid fa-credit-card text-red" style="color: #a4161a;"></i> Thanh toán & Trạng thái</strong>
                    <p id="md_payment" style="margin: 5px 0 5px 0; color: #666;"></p>
                    
                    <div id="md_payment_status" style="margin-bottom: 10px; font-size: 13px;"></div>
                    
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

            <div id="cancel_section" style="display: none; border-top: 1px dashed #ccc; padding-top: 15px; margin-top: 15px;">
                <p style="margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #333;">Lý do hủy đơn hàng:</p>
                <textarea id="cancel_reason_input" placeholder="Vui lòng nhập lý do hủy đơn (không bắt buộc)..." 
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; resize: none; box-sizing: border-box;"></textarea>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" id="confirm_cancel_btn" class="btn-cancel-order" style="flex: 1; background: #e03131; color: #fff;">XÁC NHẬN HỦY ĐƠN</button>
                    <button type="button" onclick="hideCancelInput()" style="flex: 1; border: 1px solid #ccc; background: #fff; border-radius: 6px; cursor: pointer; font-weight: bold;">QUAY LẠI</button>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                <div id="md_cancel_init_box">
                    <a href="javascript:void(0)" id="md_cancel_btn" class="btn-cancel-order" onclick="showCancelInput()" style="display: none;">Hủy đơn hàng này</a>
                </div>
                <div style="font-size: 16px; color: #333;">
                    Tổng cộng: <strong id="md_total_amount" style="color: #a4161a; font-size: 22px;">0đ</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="reviewModal">
    <div class="modal-box" style="max-width: 500px; padding: 25px; border-radius: 12px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; font-size: 18px; color: #333;">Đánh giá sản phẩm</h3>
            <button type="button" class="btn-close-modal" id="closeReviewModalBtn">&times;</button>
        </div>
        
        <p id="reviewProductName" style="color: #a4161a; font-weight: bold; margin-bottom: 15px;"></p>
        
        <form id="reviewForm">
            <input type="hidden" id="rev_product_id" name="product_id">
            <input type="hidden" id="rev_rating" name="rating" value="0">
            
            <div class="star-rating-box" style="text-align: center; margin-bottom: 20px;">
                <p style="margin-bottom: 10px; color: #666; font-size: 14px;">Bạn cảm thấy sản phẩm này như thế nào?</p>
                <div class="stars" id="starContainer">
                    <i class="fa-regular fa-star" data-val="1"></i>
                    <i class="fa-regular fa-star" data-val="2"></i>
                    <i class="fa-regular fa-star" data-val="3"></i>
                    <i class="fa-regular fa-star" data-val="4"></i>
                    <i class="fa-regular fa-star" data-val="5"></i>
                </div>
                <p id="starText" style="color: #ffc107; font-weight: 600; font-size: 14px; height: 20px; margin-top: 5px;"></p>
            </div>
            
            <div style="margin-bottom: 20px;">
                <textarea id="rev_comment" name="comment" rows="4" placeholder="Hãy chia sẻ nhận xét của bạn về sản phẩm này nhé..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; font-size: 14px; box-sizing: border-box; resize: vertical;"></textarea>
            </div>
            
            <button type="button" id="submitReviewBtn" class="btn-submit-modal" style="width: 100%;">GỬI ĐÁNH GIÁ</button>
        </form>
    </div>
</div>

<style>
    /* CSS Chung */
    .order-status-badge { padding: 3px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; display: inline-block; }
    .badge-pending { background: #fff3cd; color: #f08c00; border: 1px solid #ffe066; }
    .badge-confirmed { background: #e7f5ff; color: #1971c2; border: 1px solid #a5d8ff; }
    .badge-shipping { background: #e7f5ff; color: #1c7ed6; border: 1px solid #a5d8ff; }
    .badge-delivered { background: #ebfbee; color: #2f9e44; border: 1px solid #b2f2bb; }
    .badge-cancelled { background: #fff5f5; color: #e03131; border: 1px solid #ffc9c9; }

    .btn-view-order:hover { text-decoration: underline; }
    
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6); display: none; justify-content: center; align-items: center;
        z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    .modal-box { transform: translateY(-20px); transition: transform 0.3s ease; background: #fff; }
    .modal-overlay.show .modal-box { transform: translateY(0); }
    .btn-close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #888; }
    .btn-close-modal:hover { color: #a4161a !important; }

    .btn-cancel-order {
        display: inline-block; padding: 10px 20px; border: 1px solid #e03131;
        color: #e03131; background: #fff; border-radius: 6px;
        font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.2s; cursor: pointer; text-align: center;
    }
    .btn-cancel-order:hover:not(.disabled) { background: #fff5f5; }
    .btn-cancel-order.disabled { border-color: #ddd; color: #aaa; background: #f5f5f5; cursor: not-allowed; }

    .btn-mini-review {
        background: #fff; border: 1px solid #ffc107; color: #d39e00; padding: 4px 10px; border-radius: 4px;
        font-size: 12px; font-weight: 600; cursor: pointer; margin-top: 5px; transition: 0.2s;
    }
    .btn-mini-review:hover { background: #fffde7; }

    .star-rating-box .stars { font-size: 30px; color: #ccc; cursor: pointer; }
    .star-rating-box .stars i { transition: 0.2s; margin: 0 5px; }
    .star-rating-box .stars i.active, .star-rating-box .stars i.hover { color: #ffc107; }
    
    .btn-submit-modal { 
        background: #a4161a; color: white; border: none; padding: 12px 24px; 
        border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; 
    }
    .btn-submit-modal:hover { background: #800f13; }
</style>

<script>
// Toggle hiển thị input hủy đơn
function showCancelInput() {
    document.getElementById('md_cancel_init_box').style.display = 'none';
    document.getElementById('cancel_section').style.display = 'block';
}

function hideCancelInput() {
    document.getElementById('md_cancel_init_box').style.display = 'block';
    document.getElementById('cancel_section').style.display = 'none';
}

document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('orderDetailModal');
    const closeBtn = document.getElementById('closeOrderModalBtn');
    const cancelBtn = document.getElementById('md_cancel_btn');
    
    const closeModal = () => modal.classList.remove('show');
    closeBtn.addEventListener('click', closeModal);
    
    const formatVND = (num) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);

    const statusDict = {
        'pending': 'Chờ xử lý', 'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao hàng', 'delivered': 'Giao thành công', 'cancelled': 'Đã hủy'
    };
    const paymentDict = {
        'cash': 'COD (Nhận hàng thanh toán)', 'transfer': 'Chuyển khoản', 'online': 'Thanh toán trực tuyến'
    };

    document.querySelectorAll('.btn-view-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            const originalText = this.innerText;
            
            this.innerText = '...';
            this.style.pointerEvents = 'none';

            cancelBtn.setAttribute('data-id', orderId);
            hideCancelInput(); 

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

                    document.getElementById('modalOrderTitle').innerText = 'Chi tiết đơn hàng #' + order.id;
                    document.getElementById('md_fullname').innerText = order.shipping_fullname;
                    document.getElementById('md_phone').innerText = order.shipping_phone;
                    document.getElementById('md_address').innerText = `${order.shipping_street}, ${order.shipping_ward}, ${order.shipping_district}, ${order.shipping_city}`;
                    
                    document.getElementById('md_payment').innerText = paymentDict[order.payment_method] || order.payment_method;
                    
                    // ==========================================
                    // 1. LÔ GIC THANH TOÁN (ĐÃ FIX)
                    // ==========================================
                    const payStatusBox = document.getElementById('md_payment_status');
                    let isPaid = (order.payment_status == 'paid'); 
                    
                    if (order.payment_method === 'cash' || order.payment_method === 'online') {
                        let textTT = order.payment_method === 'cash' ? 'Khi nhận hàng (COD)' : 'Đã thanh toán Online';
                        payStatusBox.innerHTML = `<span style="color: #2f9e44; font-weight: bold;"><i class="fa-solid fa-circle-check"></i> Thanh toán: ${textTT}</span>`;
                    } else if (order.payment_method === 'transfer') {
                        if (isPaid) { 
                            payStatusBox.innerHTML = '<span style="color: #2f9e44; font-weight: bold;"><i class="fa-solid fa-circle-check"></i> Xác nhận chuyển khoản thành công</span>';
                        } else {
                            payStatusBox.innerHTML = '<span style="color: #d97706; font-weight: bold;"><i class="fa-solid fa-hourglass-half"></i> Đang xác nhận chuyển khoản</span>';
                        }
                    } else {
                        payStatusBox.innerHTML = '';
                    }

                    // ==========================================
                    // 2. LÔ GIC VẼ TIMELINE BẰNG DỮ LIỆU HISTORY (ĐÃ FIX)
                    // ==========================================
                    const stBox = document.getElementById('md_status');
                    stBox.innerText = statusDict[order.status] || order.status;
                    if(order.status === 'cancelled') { stBox.style.background = '#fff5f5'; stBox.style.color = '#e03131'; stBox.style.border = '1px solid #ffc9c9'; }
                    else if(order.status === 'delivered') { stBox.style.background = '#ebfbee'; stBox.style.color = '#2f9e44'; stBox.style.border = '1px solid #b2f2bb'; }
                    else if(order.status === 'confirmed' || order.status === 'shipping') { stBox.style.background = '#e7f5ff'; stBox.style.color = '#1c7ed6'; stBox.style.border = '1px solid #a5d8ff'; }
                    else { stBox.style.background = '#fff3cd'; stBox.style.color = '#f08c00'; stBox.style.border = '1px solid #ffe066'; }

                    // Bắt đầu vẽ Timeline từ lúc tạo đơn
                    let tlHtml = `<div><strong><i class="fa-regular fa-calendar-plus"></i> Đặt hàng:</strong> ${order.created_at}</div>`;
                    
                    // Nếu Backend có trả về mảng history thì vẽ tiếp các chặng sau
                    if (data.history && data.history.length > 0) {
                        data.history.forEach(hist => {
                            let icon = 'fa-arrow-right';
                            let color = '#4a5568';
                            let stName = statusDict[hist.status] || hist.status;
                            
                            if (hist.status === 'confirmed') { icon = 'fa-box'; color = '#1c7ed6'; }
                            if (hist.status === 'shipping') { icon = 'fa-truck-fast'; color = '#1c7ed6'; }
                            if (hist.status === 'delivered') { icon = 'fa-check-double'; color = '#2f9e44'; }
                            if (hist.status === 'cancelled') { icon = 'fa-ban'; color = '#c53030'; }

                            let noteText = hist.note ? `<br><span style="color:#888; font-size:12px; margin-left:20px;">        - ${hist.note}</span>` : '';
                            
                            tlHtml += `<div style="color: ${color}; margin-top: 8px; border-left: 2px solid #ddd; padding-left: 10px; margin-left: 5px;">
                                          <strong><i class="fa-solid ${icon}"></i> ${stName}:</strong> ${hist.changed_at}
                                          ${noteText}
                                       </div>`;
                        });
                    }
                    
                    document.getElementById('md_timeline_content').innerHTML = tlHtml;
                    document.getElementById('md_total_amount').innerText = formatVND(order.total_amount);

                    // ==========================================
                    // 3. LOGIC ẨN/HIỆN NÚT HỦY (GIỮ NGUYÊN)
                    // ==========================================
                    if (order.status === 'pending' || order.status === 'confirmed') {
                        cancelBtn.style.display = 'inline-block';
                        cancelBtn.className = 'btn-cancel-order';
                        cancelBtn.innerText = 'Hủy đơn hàng này';
                        cancelBtn.onclick = showCancelInput;
                    } else if (order.status === 'shipping' || order.status === 'delivered') {
                        cancelBtn.style.display = 'inline-block';
                        cancelBtn.className = 'btn-cancel-order disabled';
                        cancelBtn.onclick = function() { showToast('Đơn hàng đang giao hoặc đã giao, không thể hủy.', 'warning'); return false; };
                        cancelBtn.innerText = 'Không thể hủy';
                    } else {
                        cancelBtn.style.display = 'none';
                    }

                    // Đổ list sản phẩm
                    const tbody = document.getElementById('md_items_body');
                    tbody.innerHTML = ''; 
                    const isDelivered = (order.status === 'delivered');

                    items.forEach(item => {
                        const imgUrl = item.image_url ? item.image_url : 'default-lego.jpg'; 
                        
                        let reviewBtnHtml = '';
                        if (isDelivered) {
                            reviewBtnHtml = `<br><button class="btn-mini-review" onclick="openReviewModal(${item.product_id}, '${item.name.replace(/'/g, "\\'")}')"><i class="fa-regular fa-star"></i> Đánh giá</button>`;
                        }

                        const tr = `
                            <tr>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; align-items: flex-start; gap: 12px;">
                                    <img src="/lego_shop_php/public/assets/images/${imgUrl}" style="width:45px; height:45px; object-fit:contain; border:1px solid #ddd; border-radius:6px; background:#fff;">
                                    <div>
                                        <span style="font-weight: 500; color: #333;">${item.name}</span>
                                        ${reviewBtnHtml}
                                    </div>
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
                    showToast(data.message || 'Có lỗi xảy ra khi tải đơn hàng!', 'error');
                }
            })
            .catch(err => {
                console.error("Lỗi Fetch: ", err);
                this.innerText = originalText;
                this.style.pointerEvents = 'auto';
                showToast("Lỗi kết nối đến máy chủ!", "error");
            });
        });
    });

    // XỬ LÝ HỦY ĐƠN (AJAX)
    document.getElementById('confirm_cancel_btn').addEventListener('click', function() {
        const orderId = cancelBtn.getAttribute('data-id'); 
        const reason = document.getElementById('cancel_reason_input').value.trim();

        if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này không? Hành động này không thể hoàn tác.')) return;

        this.innerText = 'ĐANG XỬ LÝ...';
        this.disabled = true;

        fetch('/lego_shop_php/checkout/cancelOrderAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ order_id: orderId, reason: reason })
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showToast("Đã hủy đơn hàng thành công!", "success");
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || "Lỗi hệ thống khi hủy đơn!", "error");
                this.innerText = 'XÁC NHẬN HỦY ĐƠN';
                this.disabled = false;
            }
        })
        .catch(err => {
            showToast("Lỗi kết nối máy chủ!", "error");
            this.innerText = 'XÁC NHẬN HỦY ĐƠN';
            this.disabled = false;
        });
    });

    // --- XỬ LÝ MODAL ĐÁNH GIÁ (GIỮ NGUYÊN) ---
    const reviewModal = document.getElementById('reviewModal');
    const closeReviewBtn = document.getElementById('closeReviewModalBtn');
    
    closeReviewBtn.addEventListener('click', () => { reviewModal.classList.remove('show'); });
    
    window.addEventListener('click', (e) => { 
        if (e.target == modal) closeModal(); 
        if (e.target == reviewModal) reviewModal.classList.remove('show');
    });

    const stars = document.querySelectorAll('.star-rating-box .stars i');
    const ratingInput = document.getElementById('rev_rating');
    const starText = document.getElementById('starText');
    const textDesc = ["", "Rất tệ", "Tệ", "Bình thường", "Tốt", "Tuyệt vời"];

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            let val = parseInt(this.getAttribute('data-val'));
            stars.forEach(s => {
                if(parseInt(s.getAttribute('data-val')) <= val) { s.classList.remove('fa-regular'); s.classList.add('fa-solid', 'hover'); }
                else { s.classList.add('fa-regular'); s.classList.remove('fa-solid', 'hover'); }
            });
        });

        star.addEventListener('mouseout', function() {
            let currentVal = parseInt(ratingInput.value);
            stars.forEach(s => {
                s.classList.remove('hover');
                if(parseInt(s.getAttribute('data-val')) <= currentVal) { s.classList.remove('fa-regular'); s.classList.add('fa-solid'); }
                else { s.classList.add('fa-regular'); s.classList.remove('fa-solid'); }
            });
        });

        star.addEventListener('click', function() {
            let val = parseInt(this.getAttribute('data-val'));
            ratingInput.value = val;
            starText.innerText = textDesc[val];
            stars.forEach(s => {
                if(parseInt(s.getAttribute('data-val')) <= val) { s.classList.remove('fa-regular'); s.classList.add('fa-solid', 'active'); }
                else { s.classList.add('fa-regular'); s.classList.remove('fa-solid', 'active'); }
            });
        });
    });

    document.getElementById('submitReviewBtn').addEventListener('click', function() {
        const productId = document.getElementById('rev_product_id').value;
        const rating = document.getElementById('rev_rating').value;
        const comment = document.getElementById('rev_comment').value.trim();

        if (rating == 0) {
            showToast("Vui lòng chọn số sao đánh giá!", "warning");
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.innerHTML = 'ĐANG GỬI...';
        btn.style.pointerEvents = 'none';

        fetch('/lego_shop_php/checkout/submitReviewAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId, rating: rating, comment: comment })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';

            if(data.success) {
                showToast("Đã đánh giá thành công", "success");
                document.getElementById('reviewModal').classList.remove('show');
            } else {
                showToast(data.message || 'Có lỗi xảy ra khi gửi đánh giá!', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            btn.innerHTML = originalText;
            btn.style.pointerEvents = 'auto';
            showToast("Lỗi kết nối máy chủ!", "error");
        });
    });
});

function openReviewModal(productId, productName) {
    document.getElementById('rev_product_id').value = productId;
    document.getElementById('reviewProductName').innerText = productName;
    
    document.getElementById('rev_rating').value = 0;
    document.getElementById('rev_comment').value = '';
    document.getElementById('starText').innerText = '';
    document.querySelectorAll('.star-rating-box .stars i').forEach(s => {
        s.classList.add('fa-regular');
        s.classList.remove('fa-solid', 'active');
    });
    const submitBtn = document.getElementById('submitReviewBtn');
    submitBtn.innerText = 'ĐANG TẢI...';
    submitBtn.style.pointerEvents = 'none';

    document.getElementById('reviewModal').classList.add('show');

    fetch(`/lego_shop_php/checkout/getReviewAjax?product_id=${productId}`)
    .then(res => res.json())
    .then(data => {
        submitBtn.style.pointerEvents = 'auto';
        if (data.success && data.review) {
            const rating = data.review.rating;
            document.getElementById('rev_rating').value = rating;
            document.getElementById('rev_comment').value = data.review.comment;
            
            const textDesc = ["", "Rất tệ", "Tệ", "Bình thường", "Tốt", "Tuyệt vời"];
            document.getElementById('starText').innerText = textDesc[rating];

            document.querySelectorAll('.star-rating-box .stars i').forEach(s => {
                if(parseInt(s.getAttribute('data-val')) <= rating) {
                    s.classList.remove('fa-regular');
                    s.classList.add('fa-solid', 'active');
                }
            });

            submitBtn.innerText = 'CẬP NHẬT ĐÁNH GIÁ';
            if(data.review.status === 'pending') {
                showToast("Đánh giá này đang chờ duyệt. Bạn có thể sửa lại.", "info");
            }
        } else {
            submitBtn.innerText = 'GỬI ĐÁNH GIÁ';
        }
    })
    .catch(err => {
        submitBtn.innerText = 'GỬI ĐÁNH GIÁ';
        submitBtn.style.pointerEvents = 'auto';
    });
}
</script>