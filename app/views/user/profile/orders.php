<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box" style="border: 2px solid #a4161a; border-radius: 12px; padding: 30px; background: #fff;">
                <div class="address-header" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 25px;">
                    <h2 class="section-title" style="margin: 0; color: #a4161a; font-size: 22px;">Đơn hàng của tôi</h2>
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
                                'shipping' => ['label' => 'Đang giao hàng', 'class' => 'badge-shipping'],
                                'returned' => ['label' => 'Đã trả hàng', 'class' => 'badge-refunded']
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
                                    <p style="margin: 0;"><strong>Tổng tiền:</strong> <span style="color: #a4161a; font-weight: 700;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</span>
                                        <?php if ($order['discount_amount'] > 0): ?>
                                            <span style="color: #666; font-size: 13px; margin-left: 6px;">(giảm <?= number_format($order['discount_amount'], 0, ',', '.') ?>đ (<?= htmlspecialchars($order['voucher_code']) ?>)</span>
                                        <?php endif; ?>
                                    </p>
                                    <p style="margin: 0;"><strong>Thanh toán:</strong>
                                        <?= $order['payment_method'] === 'vietqr' ? 'VietQR' : ($order['payment_method'] === 'cash' ? 'COD' : 'Chuyển khoản') ?>
                                        <?php if ($order['payment_status'] === 'paid'): ?>
                                            - Đã thanh toán
                                        <?php elseif ($order['payment_status'] === 'refund_pending'): ?>
                                            - Đang hoàn tiền
                                        <?php elseif ($order['payment_status'] === 'refunded'): ?>
                                            - Đã hoàn tiền
                                        <?php elseif ($order['payment_method'] === 'vietqr' && $st !== 'cancelled'): ?>
                                            - Chưa thanh toán
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <?php if ($order['payment_method'] === 'vietqr' && $order['payment_status'] === 'unpaid' && $st === 'pending'): ?>
                                    <a href="/lego_shop_php/checkout/payment?order_id=<?= (int)$order['id'] ?>" class="btn-pay-now">
                                        Thanh toán ngay
                                    </a>
                                <?php endif; ?>
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
            
            <div id="md_timeline" style="margin-bottom: 20px; padding: 15px; border-radius: 8px; background: #fafafa; border: 1px solid #eee;">
                <h4 style="margin: 0 0 10px 0; color: #333; font-size: 15px;"><i class="fa-solid fa-clock-rotate-left"></i> Tiến trình đơn hàng</h4>
                <div id="md_timeline_content" style="font-size: 13px; color: #4a5568; line-height: 1.6;">
                    </div>
            </div>

            <div class="md-info-row" style="display: flex; gap: 20px; margin-bottom: 20px; font-size: 14px;">
                <div style="flex: 1; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; background: #fafafa;">
                    <strong style="color: #333;"><i class="fa-solid fa-location-dot" style="color: #a4161a;"></i> Giao hàng đến</strong>
                    <p id="md_fullname" style="margin: 5px 0 2px 0; font-weight: 600;"></p>
                    <p id="md_phone" style="margin: 0 0 5px 0; color: #666;"></p>
                    <p id="md_address" style="margin: 0; color: #666;"></p>
                </div>
                <div style="flex: 1; border: 1px dashed #ccc; padding: 15px; border-radius: 8px; background: #fafafa;">
                    <strong style="color: #333;"><i class="fa-solid fa-credit-card" style="color: #a4161a;"></i> Thanh toán & Trạng thái</strong>
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

            <div id="md_price_breakdown" class="md-breakdown"></div>

            <div id="md_refund_box" class="md-refund-box" style="display: none;"></div>

            <?php
                // Khối nhập tài khoản nhận tiền hoàn (dùng chung cho hủy đơn đã thanh toán và trả hàng)
                $bankFields = function ($prefix) use ($banks) { ?>
                <div class="bank-fields">
                    <select id="<?= $prefix ?>_bank_name">
                        <option value="">-- Chọn ngân hàng --</option>
                        <?php foreach ($banks as $b): ?>
                            <option value="<?= htmlspecialchars($b) ?>"><?= htmlspecialchars($b) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="<?= $prefix ?>_bank_account_number" placeholder="Số tài khoản" inputmode="numeric">
                    <input type="text" id="<?= $prefix ?>_bank_account_name" placeholder="Tên chủ tài khoản (không dấu)" style="text-transform: uppercase;">
                </div>
            <?php }; ?>

            <div id="refund_bank_section" style="display: none; margin-top: 12px; padding: 12px; border: 1px solid #eee; border-radius: 8px; background: #fafafa;">
                <p class="bank-hint">Tài khoản nhận tiền hoàn</p>
                <?php $bankFields('refund'); ?>
                <button type="button" id="save_refund_bank_btn" class="btn-cancel-order" style="width: 100%; margin-top: 10px; background: #a4161a; border-color: #a4161a; color: #fff;">LƯU</button>
            </div>

            <div id="cancel_section" style="display: none; border-top: 1px dashed #ccc; padding-top: 15px; margin-top: 15px;">
                <p style="margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #333;">Lý do hủy đơn hàng:</p>
                <textarea id="cancel_reason_input" placeholder="Vui lòng nhập lý do hủy đơn (không bắt buộc)..."
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; resize: none; box-sizing: border-box;"></textarea>
                <div id="cancel_bank_box" style="display: none; margin-top: 12px;">
                    <p class="bank-hint">Tài khoản nhận tiền hoàn</p>
                    <?php $bankFields('cancel'); ?>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="button" id="confirm_cancel_btn" class="btn-cancel-order" style="flex: 1; background: #e03131; color: #fff;">XÁC NHẬN HỦY ĐƠN</button>
                    <button type="button" onclick="hideCancelInput()" style="flex: 1; border: 1px solid #ccc; background: #fff; border-radius: 6px; cursor: pointer; font-weight: bold;">QUAY LẠI</button>
                </div>
            </div>

            <div id="return_section" style="display: none; border-top: 1px dashed #ccc; padding-top: 15px; margin-top: 15px;">
                <p style="margin-bottom: 4px; font-size: 14px; font-weight: 700; color: #333;">Yêu cầu trả hàng / hoàn tiền</p>
                <p class="bank-hint" id="return_deadline_text"></p>
                <textarea id="return_reason_input" rows="3" placeholder="Mô tả lý do trả hàng (sản phẩm lỗi, thiếu mảnh, giao sai...) - tối thiểu 10 ký tự"
                          style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; resize: vertical; box-sizing: border-box;"></textarea>
                <label style="display:block; font-size: 13px; color:#555; margin: 10px 0 5px;">Ảnh minh chứng (không bắt buộc)</label>
                <input type="file" id="return_evidence" accept="image/jpeg,image/png,image/webp" style="font-size: 13px;">
                <p class="bank-hint" style="margin-top: 12px;">Tài khoản nhận tiền hoàn</p>
                <?php $bankFields('return'); ?>
                <div style="display: flex; gap: 10px; margin-top: 12px;">
                    <button type="button" id="confirm_return_btn" class="btn-cancel-order" style="flex: 1; background: #a4161a; border-color: #a4161a; color: #fff;">GỬI YÊU CẦU</button>
                    <button type="button" onclick="hideReturnInput()" style="flex: 1; border: 1px solid #ccc; background: #fff; border-radius: 6px; cursor: pointer; font-weight: bold;">QUAY LẠI</button>
                </div>
            </div>

            <div class="md-footer-row" style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                <div id="md_cancel_init_box" style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="javascript:void(0)" id="md_cancel_btn" class="btn-cancel-order" onclick="showCancelInput()" style="display: none;">Hủy đơn hàng này</a>
                    <a href="javascript:void(0)" id="md_return_btn" class="btn-return-order" onclick="showReturnInput()" style="display: none;"><i class="fa-solid fa-rotate-left"></i> Trả hàng / Hoàn tiền</a>
                    <a href="#" id="md_pay_btn" class="btn-pay-now" style="display: none; margin: 0;"><i class="fa-solid fa-qrcode"></i> Thanh toán ngay</a>
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
    .order-status-badge { background: #f1f3f5; color: #333; border: 1px solid #e5e5e5; }
    .badge-pending { color: #a4161a; border-color: #f0c4c6; background: #fff; }
    .badge-cancelled, .badge-refunded { color: #888; background: #fff; }

    .btn-pay-now { display: inline-block; margin-top: 12px; background: #c92a2a; color: #fff !important; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 700; text-decoration: none; transition: .2s; }
    .btn-pay-now:hover { background: #a4161a; }
    .btn-return-order { display: inline-block; padding: 10px 20px; border: 1px solid #ccc; color: #333; background: #fff; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; }
    .btn-return-order:hover { background: #f8f9fa; }

    .md-breakdown { margin-top: 15px; font-size: 14px; color: #555; }
    .md-breakdown .row { display: flex; justify-content: space-between; padding: 4px 0; }
    .md-refund-box { margin-top: 15px; padding: 15px; border-radius: 8px; background: #fafafa; border: 1px solid #eee; font-size: 13px; color: #334; line-height: 1.7; }
    .md-refund-box h4 { margin: 0 0 6px; color: #333; font-size: 15px; }
    .bank-hint { font-size: 13px; color: #666; margin: 0 0 8px; }
    .bank-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .bank-fields select, .bank-fields input { padding: 9px 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 13px; width: 100%; box-sizing: border-box; }
    .bank-fields select { grid-column: span 2; }
    .bank-fields input:focus, .bank-fields select:focus { outline: none; border-color: #a4161a; }

    .btn-view-order:hover { text-decoration: underline; }
    #md_status { background: #f1f3f5; color: #333; border: 1px solid #e5e5e5; }
    #md_timeline_content { position: relative; padding: 2px 0 18px 26px; }
    #md_timeline_content::before { content: ''; position: absolute; left: 7px; top: 10px; bottom: 10px; width: 2px; background: #d6d6d6; }
    #md_timeline_content::after { content: ''; position: absolute; left: 2px; bottom: 2px; border-left: 6px solid transparent; border-right: 6px solid transparent; border-top: 10px solid #d6d6d6; }
    .tl-item { position: relative; padding: 5px 0 9px; }
    .tl-item::before { content: ''; position: absolute; left: -24px; top: 10px; width: 10px; height: 10px; border-radius: 50%; background: #fff; border: 2px solid #a4161a; box-sizing: border-box; }
    .tl-item:last-child::before { background: #a4161a; }
    .tl-name { font-weight: 600; color: #333; margin-right: 8px; }
    .tl-time { color: #888; font-size: 12px; }
    .tl-note { color: #777; font-size: 12px; margin-top: 2px; }
    
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
    document.getElementById('md_cancel_init_box').style.display = 'flex';
    document.getElementById('cancel_section').style.display = 'none';
}

function showReturnInput() {
    document.getElementById('md_cancel_init_box').style.display = 'none';
    document.getElementById('return_section').style.display = 'block';
}

function hideReturnInput() {
    document.getElementById('md_cancel_init_box').style.display = 'flex';
    document.getElementById('return_section').style.display = 'none';
}

// Đơn đang mở trong modal (để biết đơn đã thanh toán hay chưa khi hủy)
let currentOrder = null;

document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('orderDetailModal');
    const closeBtn = document.getElementById('closeOrderModalBtn');
    const cancelBtn = document.getElementById('md_cancel_btn');
    
    const closeModal = () => modal.classList.remove('show');
    closeBtn.addEventListener('click', closeModal);
    
    const formatVND = (num) => new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(num);

    const statusDict = {
        'pending': 'Chờ xử lý', 'confirmed': 'Đã xác nhận',
        'shipping': 'Đang giao hàng', 'delivered': 'Giao thành công', 'cancelled': 'Đã hủy', 'returned': 'Đã trả hàng',
        'paid': 'Đã thanh toán', 'refund_requested': 'Yêu cầu hoàn tiền',
        'refund_approved': 'Duyệt hoàn tiền', 'refund_rejected': 'Từ chối hoàn tiền', 'refunded': 'Đã hoàn tiền'
    };
    const paymentDict = {
        'cash': 'COD (Nhận hàng thanh toán)', 'transfer': 'Chuyển khoản', 'online': 'Thanh toán trực tuyến',
        'vietqr': 'Thanh toán online VietQR'
    };
    const escapeHtml = (s) => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    document.querySelectorAll('.btn-view-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const orderId = this.getAttribute('data-id');
            const originalText = this.innerText;
            
            this.innerText = '...';
            this.style.pointerEvents = 'none';

            cancelBtn.setAttribute('data-id', orderId);
            hideCancelInput();
            hideReturnInput();

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
                    currentOrder = order;

                    const payLabels = { refund_pending: 'Đang hoàn tiền', refunded: 'Đã hoàn tiền', paid: 'Đã thanh toán' };
                    if (payLabels[order.payment_status]) {
                        payStatusBox.innerText = payLabels[order.payment_status] + (order.paid_at && isPaid ? ' lúc ' + order.paid_at : '');
                    } else if (order.payment_method === 'cash') {
                        payStatusBox.innerText = 'Thanh toán khi nhận hàng';
                    } else {
                        payStatusBox.innerText = 'Chưa thanh toán';
                    }

                    // ==========================================
                    // 2. NHÃN TRẠNG THÁI + TIMELINE TỪ order_history
                    // ==========================================
                    const stBox = document.getElementById('md_status');
                    stBox.innerText = statusDict[order.status] || order.status;

                    const tlRow = (name, time, note) => `<div class="tl-item"><span class="tl-name">${name}</span><span class="tl-time">${time}</span>${note}</div>`;
                    let tlHtml = tlRow('Đặt hàng', order.created_at, '');
                    (data.history || []).forEach(hist => {
                        const note = hist.note ? `<div class="tl-note">${escapeHtml(hist.note)}</div>` : '';
                        tlHtml += tlRow(statusDict[hist.status] || escapeHtml(hist.status), hist.changed_at, note);
                    });
                    document.getElementById('md_timeline_content').innerHTML = tlHtml;
                    document.getElementById('md_total_amount').innerText = formatVND(order.total_amount);

                    // Tạm tính / giảm giá
                    const breakdown = document.getElementById('md_price_breakdown');
                    if (parseInt(order.discount_amount) > 0) {
                        breakdown.innerHTML = `
                            <div class="row"><span>Tạm tính</span><span>${formatVND(order.subtotal_amount)}</span></div>
                            <div class="row"><span>Mã giảm giá <strong>${escapeHtml(order.voucher_code)}</strong></span><span>-${formatVND(order.discount_amount)}</span></div>`;
                    } else {
                        breakdown.innerHTML = '';
                    }

                    // Thông tin yêu cầu hoàn tiền (nếu có)
                    const refundBox = document.getElementById('md_refund_box');
                    const refundBankSection = document.getElementById('refund_bank_section');
                    const r0 = data.refund;
                    refundBankSection.style.display = (r0 && ['pending', 'approved'].includes(r0.status) && !r0.bank_account_number) ? 'block' : 'none';
                    if (data.refund) {
                        const r = data.refund;
                        refundBox.innerHTML = `
                            <h4><i class="fa-solid fa-rotate-left"></i> ${escapeHtml(r.type_label)}</h4>
                            <div>Trạng thái: <strong>${escapeHtml(r.status_label)}</strong></div>
                            <div>Số tiền hoàn: <strong>${formatVND(r.amount)}</strong>${r.bank_account_number ? ` về ${escapeHtml(r.bank_name)} - ${escapeHtml(r.bank_account_number)} (${escapeHtml(r.bank_account_name)})` : ' (chưa có tài khoản nhận)'}</div>
                            <div>Gửi lúc: ${escapeHtml(r.created_at)}</div>
                            ${r.admin_note ? `<div>Phản hồi cửa hàng: <em>${escapeHtml(r.admin_note)}</em></div>` : ''}`;
                        refundBox.style.display = 'block';
                    } else {
                        refundBox.style.display = 'none';
                    }

                    // Nút thanh toán lại / trả hàng
                    const payBtn = document.getElementById('md_pay_btn');
                    if (order.payment_method === 'vietqr' && order.payment_status === 'unpaid' && order.status === 'pending') {
                        payBtn.href = '/lego_shop_php/checkout/payment?order_id=' + order.id;
                        payBtn.style.display = 'inline-block';
                    } else {
                        payBtn.style.display = 'none';
                    }

                    const returnBtn = document.getElementById('md_return_btn');
                    returnBtn.style.display = data.can_return ? 'inline-block' : 'none';
                    document.getElementById('return_deadline_text').innerText = data.return_deadline ? `Hạn gửi yêu cầu: ${data.return_deadline}` : '';

                    // Hủy đơn đã thanh toán -> cần tài khoản nhận tiền hoàn
                    document.getElementById('cancel_bank_box').style.display = isPaid ? 'block' : 'none';

                    // ==========================================
                    // 3. LOGIC ẨN/HIỆN NÚT HỦY (GIỮ NGUYÊN)
                    // ==========================================
                    if (order.status === 'pending' || order.status === 'confirmed') {
                        cancelBtn.style.display = 'inline-block';
                        cancelBtn.className = 'btn-cancel-order';
                        cancelBtn.innerText = 'Hủy đơn hàng này';
                        cancelBtn.onclick = showCancelInput;
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
                            reviewBtnHtml = `<br><button class="btn-mini-review" data-name="${escapeHtml(item.name)}" onclick="openReviewModal(${parseInt(item.product_id)}, this.dataset.name)"><i class="fa-regular fa-star"></i> Đánh giá</button>`;
                        }

                        const tr = `
                            <tr>
                                <td style="padding: 12px 15px; border-bottom: 1px solid #eee; display: flex; align-items: flex-start; gap: 12px;">
                                    <img src="/lego_shop_php/public/assets/images/${escapeHtml(imgUrl)}" style="width:45px; height:45px; object-fit:contain; border:1px solid #ddd; border-radius:6px; background:#fff;">
                                    <div>
                                        <span style="font-weight: 500; color: #333;">${escapeHtml(item.name)}</span>
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
    document.getElementById('confirm_cancel_btn').addEventListener('click', async function() {
        const orderId = cancelBtn.getAttribute('data-id');
        const reason = document.getElementById('cancel_reason_input').value.trim();
        const payload = { order_id: orderId, reason: reason };

        if (currentOrder && currentOrder.payment_status === 'paid') {
            payload.bank_name = document.getElementById('cancel_bank_name').value;
            payload.bank_account_number = document.getElementById('cancel_bank_account_number').value.trim();
            payload.bank_account_name = document.getElementById('cancel_bank_account_name').value.trim();
            if (!payload.bank_name || !payload.bank_account_number || !payload.bank_account_name) {
                showToast('Vui lòng nhập đầy đủ tài khoản nhận tiền hoàn!', 'error');
                return;
            }
        }

        if (!(await uiConfirm('Bạn có chắc chắn muốn hủy đơn hàng này không?', { danger: true, okText: 'Hủy đơn' }))) return;

        this.innerText = 'ĐANG XỬ LÝ...';
        this.disabled = true;

        fetch('/lego_shop_php/checkout/cancelOrderAjax', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showToast(data.message || "Đã hủy đơn hàng thành công!", "success");
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

    // BỔ SUNG TÀI KHOẢN NHẬN TIỀN HOÀN
    document.getElementById('save_refund_bank_btn').addEventListener('click', function() {
        const payload = {
            order_id: cancelBtn.getAttribute('data-id'),
            bank_name: document.getElementById('refund_bank_name').value,
            bank_account_number: document.getElementById('refund_bank_account_number').value.trim(),
            bank_account_name: document.getElementById('refund_bank_account_name').value.trim()
        };
        if (!payload.bank_name || !payload.bank_account_number || !payload.bank_account_name) {
            showToast('Vui lòng nhập đầy đủ tài khoản nhận tiền hoàn!', 'error'); return;
        }
        this.disabled = true;
        fetch('/lego_shop_php/profile/updateRefundBankAjax', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            this.disabled = false;
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1200);
        })
        .catch(() => { this.disabled = false; showToast('Lỗi kết nối máy chủ!', 'error'); });
    });

    // XỬ LÝ GỬI YÊU CẦU TRẢ HÀNG / HOÀN TIỀN (multipart vì có ảnh minh chứng)
    document.getElementById('confirm_return_btn').addEventListener('click', function() {
        const fd = new FormData();
        fd.append('order_id', cancelBtn.getAttribute('data-id'));
        fd.append('reason', document.getElementById('return_reason_input').value.trim());
        fd.append('bank_name', document.getElementById('return_bank_name').value);
        fd.append('bank_account_number', document.getElementById('return_bank_account_number').value.trim());
        fd.append('bank_account_name', document.getElementById('return_bank_account_name').value.trim());
        const file = document.getElementById('return_evidence').files[0];
        if (file) fd.append('evidence', file);

        if (fd.get('reason').length < 10) { showToast('Vui lòng mô tả lý do (tối thiểu 10 ký tự)!', 'error'); return; }
        if (!fd.get('bank_name') || !fd.get('bank_account_number') || !fd.get('bank_account_name')) {
            showToast('Vui lòng nhập đầy đủ tài khoản nhận tiền hoàn!', 'error'); return;
        }

        const btn = this;
        btn.innerText = 'ĐANG GỬI...';
        btn.disabled = true;

        fetch('/lego_shop_php/profile/requestRefundAjax', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Không thể gửi yêu cầu!', 'error');
                btn.innerText = 'GỬI YÊU CẦU';
                btn.disabled = false;
            }
        })
        .catch(() => {
            showToast('Lỗi kết nối máy chủ!', 'error');
            btn.innerText = 'GỬI YÊU CẦU';
            btn.disabled = false;
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