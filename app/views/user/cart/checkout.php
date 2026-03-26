<link rel="stylesheet" href="/lego_shop_php/public/assets/css/cart.css?v=<?= time() ?>">
<div class="checkout-page-wrapper">
    <div class="checkout-container">
        <?php $current_step = 2; require __DIR__ . '/../../components/checkout_progress.php'; ?>

        <div class="checkout-header-title">
            <h2>Thanh toán đơn hàng</h2>
            <p style="color: #666;">Vui lòng kiểm tra lại thông tin trước khi xác nhận.</p>
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
                                <input type="radio" name="payment_method" value="transfer">
                                <span>Chuyển khoản ngân hàng</span>
                            </label>
                            <div id="bankingInfo" class="banking-details" style="display: none;">
                                <p><strong>Ngân hàng:</strong> MB Bank (Quân Đội)</p>
                                <p><strong>Số tài khoản:</strong> 0961589023</p>
                                <p><strong>Chủ tài khoản:</strong> HOANG NGUYEN</p>
                            </div>
                            <label class="payment-item">
                                <input type="radio" name="payment_method" value="online">
                                <span>Thanh toán trực tuyến (Momo, VNPay...)</span>
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

                        <div class="price-breakdown">
                            <div class="price-row">
                                <span>Phí giao hàng</span>
                                <span class="text-success" style="font-weight: 700;">Miễn phí</span>
                            </div>
                            <div class="price-row total-final">
                                <span class="text-red">Tổng cộng:</span>
                                <span class="text-red font-weight-bold"><?= number_format($total_price, 0, ',', '.') ?>đ</span>
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

        // Hiện info banking
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('bankingInfo').style.display = (this.value === 'banking') ? 'block' : 'none';
            });
        });
    });

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