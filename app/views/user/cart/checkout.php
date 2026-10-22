<link rel="stylesheet" href="/lego_shop_php/public/assets/css/cart.css?v=<?= time() ?>">
<style>

    .voucher-box { border-top: 2px solid #f1f1f1; padding-top: 18px; margin-top: 10px; margin-bottom: 15px; }
    .voucher-label { display: block; font-weight: 700; font-size: 14px; color: #333; margin-bottom: 10px; }
    .voucher-input-row { display: flex; gap: 8px; }
    .voucher-input-row input { flex: 1; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; text-transform: uppercase; min-width: 0; }
    .voucher-input-row input:focus { outline: none; border-color: #a4161a; }
    .voucher-input-row button { background: #a4161a; color: #fff; border: none; border-radius: 6px; padding: 0 16px; font-weight: 700; cursor: pointer; }
    .voucher-input-row button:disabled { opacity: .6; cursor: wait; }
    .voucher-applied { display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; border: 1px solid #ddd; border-radius: 6px; padding: 10px 12px; font-size: 13px; color: #333; }
    .voucher-applied strong { margin-right: 6px; }
    .voucher-applied button { background: none; border: none; color: #888; cursor: pointer; font-size: 16px; }
    .voucher-applied button:hover { color: #e03131; }

    .voucher-suggest { display: flex; flex-direction: column; gap: 8px; margin-top: 12px; max-height: 210px; overflow-y: auto; }
    .voucher-chip { display: grid; grid-template-columns: auto 1fr; grid-template-rows: auto auto; column-gap: 10px; text-align: left; background: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 8px 10px; cursor: pointer; transition: .2s; }
    .voucher-chip:hover { border-color: #a4161a; }
    .voucher-chip .chip-code { grid-row: span 2; align-self: center; font-weight: 700; color: #333; font-size: 13px; }
    .voucher-chip .chip-desc { font-size: 13px; color: #333; font-weight: 600; }
    .voucher-chip .chip-cond { font-size: 11px; color: #888; }
    .voucher-chip.disabled { cursor: not-allowed; opacity: .5; }
</style>
<div class="checkout-page-wrapper">
    <div class="checkout-container">
        <?php $current_step = 2; require __DIR__ . '/../../components/checkout_progress.php'; ?>

        <div class="checkout-header-title">
            <h2>Thanh toán đơn hàng</h2>
        </div>

        <form action="/lego_shop_php/checkout/process" method="POST" id="checkoutForm">
            <div class="checkout-layout">
                
                <div class="checkout-left">
                    <div class="checkout-box" id="addressSection">
                        <h3 class="box-title"><i class="fa-solid fa-location-dot"></i> Địa chỉ nhận hàng</h3>
                        <div class="address-selection">
                            <?php if(!empty($addresses)): ?>
                                <?php foreach($addresses as $addr): ?>
                                    <label class="address-option <?= $addr['is_default'] ? 'active' : '' ?>">
                                        <input type="radio" name="address_id" value="<?= $addr['id'] ?>" <?= $addr['is_default'] ? 'checked' : '' ?>>
                                        <div class="addr-content">
                                           <span class="addr-name">
    <?= htmlspecialchars($addr['receiver_name']) ?> — <?= htmlspecialchars($addr['receiver_phone']) ?>
    <?php if($addr['is_default']): ?>
        <span class="addr-badge-default">Mặc định</span>
    <?php endif; ?>
</span>
                                            <p class="addr-text"><?= htmlspecialchars($addr['street']) ?>, <?= htmlspecialchars($addr['ward']) ?>, <?= htmlspecialchars($addr['district']) ?>, <?= htmlspecialchars($addr['city']) ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <label class="address-option">
                                <input type="radio" name="address_id" value="new" id="radioNewAddr">
                                <div class="addr-content"><span class="addr-name">+ Nhập địa chỉ mới</span></div>
                            </label>
                        </div>

                        <div id="newAddressForm" class="new-address-fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Tên người nhận <span class="text-red">*</span></label>
                                    <input type="text" id="new_name" name="new_receiver_name" placeholder="Nhập tên">
                                </div>
                                <div class="form-group">
                                    <label>Số điện thoại <span class="text-red">*</span></label>
                                    <input type="tel" id="new_phone" name="new_receiver_phone" placeholder="Nhập SĐT">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Tỉnh/Thành phố <span class="text-red">*</span></label>
                                    <select id="city" name="new_city" onchange="updateDistrict()">
                                        <option value="">Chọn Thành phố</option>
                                        <option value="Hồ Chí Minh">TP. Hồ Chí Minh</option>
                                        <option value="Hà Nội">Hà Nội</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Quận/Huyện <span class="text-red">*</span></label>
                                    <select id="district" name="new_district" onchange="updateWard()">
                                        <option value="">Chọn Quận/Huyện</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Phường/Xã <span class="text-red">*</span></label>
                                    <select id="ward" name="new_ward"><option value="">Chọn Phường/Xã</option></select>
                                </div>
                                <div class="form-group">
                                    <label>Địa chỉ cụ thể <span class="text-red">*</span></label>
                                    <input type="text" id="new_street" name="new_street" placeholder="Số nhà, tên đường...">
                                </div>
                            </div>
                            <div style="text-align: right; margin-top: 10px;">
                                <button type="button" class="btn-save-address" onclick="saveNewAddress()">Xác nhận & Lưu địa chỉ</button>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-box">
                        <h3 class="box-title"><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</h3>
                        <div class="payment-methods">
                            <label class="payment-item">
                                <input type="radio" name="payment_method" value="cash" checked>
                                <span>Thanh toán khi nhận hàng (COD)</span>
                            </label>
                            <label class="payment-item">
                                <input type="radio" name="payment_method" value="vietqr">
                                <span>Chuyển khoản qua mã VietQR</span>
                                <i class="fa-solid fa-qrcode" style="margin-left: auto; color: #555;"></i>
                            </label>
                        </div>
                        <div class="back-to-cart-wrapper">
                            <a href="/lego_shop_php/cart" class="btn-back-red">
                                <i class="fa-solid fa-arrow-left"></i> Quay lại giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>

                <div class="checkout-right">
                    <div class="checkout-box">
                        <h3 class="box-title">Tóm tắt đơn hàng</h3>
                        <div class="order-summary-list">
                            <?php foreach($cart_items as $item): 
                                $img = !empty($item['main_image']) ? $item['main_image'] : 'default-lego.jpg';
                            ?>
                                <div class="order-summary-item">
                                    <img src="/lego_shop_php/public/assets/images/<?= $img ?>" alt="">
                                    <div class="order-item-info">
                                        <div class="order-item-name"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="order-item-qty">x<?= $item['quantity'] ?></div> </div>
                                    <div class="order-item-price"><?= number_format($item['selling_price'], 0, ',', '.') ?>đ</div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- MÃ GIẢM GIÁ -->
                        <div class="voucher-box">
                            <label class="voucher-label"><i class="fa-solid fa-ticket"></i> Mã giảm giá</label>
                            <div class="voucher-input-row" id="voucherInputRow" style="<?= $applied_voucher ? 'display:none;' : '' ?>">
                                <input type="text" id="voucherCode" placeholder="Nhập mã giảm giá" autocomplete="off">
                                <button type="button" id="btnApplyVoucher" onclick="applyVoucher()">Áp dụng</button>
                            </div>
                            <div class="voucher-applied" id="voucherApplied" style="<?= $applied_voucher ? '' : 'display:none;' ?>">
                                <div>
                                    <strong id="voucherAppliedCode"><?= htmlspecialchars($applied_voucher['code'] ?? '') ?></strong>
                                    <span id="voucherAppliedDesc"><?= $applied_voucher ? htmlspecialchars(VoucherModel::describe($applied_voucher)) : '' ?></span>
                                </div>
                                <button type="button" onclick="removeVoucher()" title="Bỏ mã"><i class="fa-solid fa-xmark"></i></button>
                            </div>

                            <?php if (!empty($vouchers)): ?>
                                <div class="voucher-suggest">
                                    <?php foreach ($vouchers as $v):
                                        $usable = $v['remaining_for_user'] > 0 && $total_price >= $v['min_order_value'];
                                    ?>
                                        <button type="button" class="voucher-chip <?= $usable ? '' : 'disabled' ?>"
                                                <?= $usable ? 'onclick="pickVoucher(\'' . htmlspecialchars($v['code']) . '\')"' : '' ?>
                                                title="<?= htmlspecialchars($v['description'] ?? '') ?>">
                                            <span class="chip-code"><?= htmlspecialchars($v['code']) ?></span>
                                            <span class="chip-desc"><?= htmlspecialchars(VoucherModel::describe($v)) ?></span>
                                            <span class="chip-cond">
                                                <?php if ($v['remaining_for_user'] <= 0): ?>Bạn đã dùng hết lượt
                                                <?php elseif ($total_price < $v['min_order_value']): ?>Đơn tối thiểu <?= number_format($v['min_order_value'], 0, ',', '.') ?>đ
                                                <?php else: ?>Dùng ngay<?php endif; ?>
                                            </span>
                                        </button>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="price-breakdown">
                            <div class="price-row">
                                <span>Tạm tính</span>
                                <span id="sumSubtotal"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="price-row" id="rowDiscount" style="<?= $discount > 0 ? '' : 'display:none;' ?>">
                                <span>Giảm giá</span>
                                <span id="sumDiscount">-<?= number_format($discount, 0, ',', '.') ?>đ</span>
                            </div>
                            <div class="price-row">
                                <span>Phí giao hàng</span>
                                <span class="text-success" style="font-weight: 700;">Miễn phí</span>
                            </div>
                            <div class="price-row total-final">
                                <span class="text-red">Tổng cộng:</span>
                                <span class="text-red font-weight-bold" id="sumTotal"><?= number_format($total_price - $discount, 0, ',', '.') ?>đ</span>
                            </div>
                        </div>
                        <button type="submit" class="btn-confirm-order">XÁC NHẬN ĐƠN HÀNG</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const addressData = {
        "Hồ Chí Minh": { "Quận 1": ["Phường Bến Nghé", "Phường Bến Thành"], "Quận 10": ["Phường 14", "Phường 15"] },
        "Hà Nội": { "Quận Ba Đình": ["Phường Đội Cấn", "Phường Ngọc Hà"] }
    };

    function updateDistrict() {
        const c = document.getElementById("city").value;
        const d = document.getElementById("district");
        d.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        if (c && addressData[c]) Object.keys(addressData[c]).forEach(k => d.options.add(new Option(k, k)));
        updateWard();
    }

    function updateWard() {
        const c = document.getElementById("city").value;
        const d = document.getElementById("district").value;
        const w = document.getElementById("ward");
        w.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (c && d && addressData[c][d]) addressData[c][d].forEach(v => w.options.add(new Option(v, v)));
    }

    document.addEventListener("DOMContentLoaded", function() {
        // Hiện form địa chỉ mới
        document.querySelectorAll('input[name="address_id"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('newAddressForm').style.display = (this.value === 'new') ? 'block' : 'none';
                document.querySelectorAll('.address-option').forEach(opt => opt.classList.remove('active'));
                this.closest('.address-option').classList.add('active');
            });
        });

        // Nhấn Enter trong ô mã giảm giá -> áp dụng mã thay vì submit form đặt hàng
        document.getElementById('voucherCode').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); applyVoucher(); }
        });
    });

    // ================= MÃ GIẢM GIÁ =================
    const fmtVND = (n) => Number(n).toLocaleString('vi-VN') + 'đ';

    function renderTotals(data) {
        document.getElementById('sumSubtotal').innerText = fmtVND(data.subtotal);
        document.getElementById('sumDiscount').innerText = '-' + fmtVND(data.discount);
        document.getElementById('rowDiscount').style.display = data.discount > 0 ? '' : 'none';
        document.getElementById('sumTotal').innerText = fmtVND(data.total);
    }

    function pickVoucher(code) {
        document.getElementById('voucherCode').value = code;
        applyVoucher();
    }

    function applyVoucher() {
        const input = document.getElementById('voucherCode');
        const code = input.value.trim();
        if (!code) { showToast('Vui lòng nhập mã giảm giá!', 'error'); return; }

        const btn = document.getElementById('btnApplyVoucher');
        btn.disabled = true;
        fetch('/lego_shop_php/checkout/applyVoucher', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ code })
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            if (!data.success) { showToast(data.message, 'error'); return; }
            showToast(data.message, 'success');
            document.getElementById('voucherAppliedCode').innerText = data.code;
            document.getElementById('voucherAppliedDesc').innerText = data.description;
            document.getElementById('voucherInputRow').style.display = 'none';
            document.getElementById('voucherApplied').style.display = 'flex';
            renderTotals(data);
        })
        .catch(() => { btn.disabled = false; showToast('Lỗi kết nối máy chủ!', 'error'); });
    }

    function removeVoucher() {
        fetch('/lego_shop_php/checkout/removeVoucher', { method: 'POST' })
        .then(res => res.json())
        .then(data => {
            document.getElementById('voucherCode').value = '';
            document.getElementById('voucherInputRow').style.display = 'flex';
            document.getElementById('voucherApplied').style.display = 'none';
            renderTotals(data);
        });
    }

 function saveNewAddress() {
    // 1. Lấy dữ liệu
    const name = document.getElementById('new_name').value.trim();
    const phone = document.getElementById('new_phone').value.trim();
    const city = document.getElementById('city').value;
    const district = document.getElementById('district').value;
    const ward = document.getElementById('ward').value;
    const street = document.getElementById('new_street').value.trim();

    if(!name || !phone || !city || !district || !ward || !street) {
        showToast("Vui lòng nhập đầy đủ thông tin địa chỉ!", "error");
        return;
    }

    const data = {
        receiver_name: name,
        receiver_phone: phone,
        city: city,
        district: district,
        ward: ward,
        street: street
    };

    fetch('/lego_shop_php/profile/addAddressAjax', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if(result.success) {
            showToast("Đã thêm địa chỉ mới!", "success");

            // 2. TẠO HTML CHO ĐỊA CHỈ MỚI
            const addressList = document.querySelector('.address-selection');
            const newAddressHTML = `
                <label class="address-option active">
                    <input type="radio" name="address_id" value="${result.new_id}" checked>
                    <div class="addr-content">
                        <span class="addr-name">${name} — ${phone}</span>
                        <p class="addr-text">${street}, ${ward}, ${district}, ${city}</p>
                    </div>
                </label>
            `;

            // 3. CHÈN VÀO ĐẦU DANH SÁCH (trên các địa chỉ cũ)
            addressList.insertAdjacentHTML('afterbegin', newAddressHTML);

            // 4. XỬ LÝ GIAO DIỆN
            // - Bỏ class active của tất cả các ô khác
            document.querySelectorAll('.address-option').forEach(opt => opt.classList.remove('active'));
            // - Ô mới thêm đã có class active (ở bước 2) và được checked

            // - Ẩn form nhập và reset
            document.getElementById('newAddressForm').style.display = 'none';
            document.getElementById('radioNewAddr').checked = false; 
            
            // Reset các ô input để lần sau nhập cái khác
            document.getElementById('new_name').value = '';
            document.getElementById('new_phone').value = '';
            document.getElementById('new_street').value = '';
            // Reset select (tùy Hoàng có muốn reset tỉnh thành ko)
            document.getElementById('city').value = '';
            document.getElementById('district').innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            document.getElementById('ward').innerHTML = '<option value="">Chọn Phường/Xã</option>';

        } else {
            showToast(result.message || "Lỗi lưu địa chỉ!", "error");
        }
    })
    .catch(err => {
        console.error("Lỗi:", err);
        showToast("Lỗi hệ thống!", "error");
    });
}
</script>