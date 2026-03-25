<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box">
                <div class="address-header">
                    <h2 class="section-title" style="margin: 0;">Địa chỉ của tôi</h2>
                    <button class="btn-add-address"><i class="fa-solid fa-plus"></i> Thêm địa chỉ mới</button>
                </div>
                <p class="section-desc" style="border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">Quản lý địa chỉ nhận hàng</p>

                <div class="address-list">
                    <?php if (empty($addresses)): ?>
                        <div style="text-align: center; padding: 40px; color: #888;">
                            <i class="fa-regular fa-map" style="font-size: 40px; margin-bottom: 10px;"></i>
                            <p>Bạn chưa có địa chỉ nào. Hãy thêm địa chỉ mới nhé!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($addresses as $addr): ?>
                            <div class="address-card">
                                <div class="address-top">
                                    <div class="address-info">
                                        <span class="addr-name"><?= htmlspecialchars($addr['receiver_name']) ?></span>
                                        <span class="addr-divider">|</span>
                                        <span class="addr-phone"><?= htmlspecialchars($addr['receiver_phone']) ?></span>
                                        
                                        <?php if($addr['is_default'] == 1): ?>
                                            <span class="addr-badge"><i class="fa-solid fa-check"></i> Mặc định</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="address-actions">
                                        <a href="#" class="text-blue btn-edit-address" 
                                           data-id="<?= $addr['id'] ?>"
                                           data-name="<?= htmlspecialchars($addr['receiver_name']) ?>"
                                           data-phone="<?= htmlspecialchars($addr['receiver_phone']) ?>"
                                           data-city="<?= htmlspecialchars($addr['city']) ?>"
                                           data-district="<?= htmlspecialchars($addr['district']) ?>"
                                           data-ward="<?= htmlspecialchars($addr['ward']) ?>"
                                           data-street="<?= htmlspecialchars($addr['street']) ?>"
                                           data-default="<?= $addr['is_default'] ?>">Cập nhật</a>
                                        
                                        <?php if($addr['is_default'] == 0): ?>
                                            <a href="/lego_shop_php/profile/deleteAddress?id=<?= $addr['id'] ?>" class="text-red btn-delete-address">Xóa</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="address-bottom">
                                    <div class="address-detail">
                                        <p><?= htmlspecialchars($addr['street']) ?></p>
                                        <p><?= htmlspecialchars($addr['ward']) ?>, <?= htmlspecialchars($addr['district']) ?>, <?= htmlspecialchars($addr['city']) ?></p>
                                    </div>
                                    <div class="address-set-default">
                                        <form action="/lego_shop_php/profile/setDefaultAddress" method="POST" style="margin: 0;">
                                            <input type="hidden" name="address_id" value="<?= $addr['id'] ?>">
                                            <button type="submit" class="btn-set-default <?= $addr['is_default'] == 1 ? 'disabled' : '' ?>" <?= $addr['is_default'] == 1 ? 'disabled' : '' ?>>
                                                Thiết lập mặc định
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

            </div>
        </section>

    </div>
</div>

<div class="modal-overlay" id="addressModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3 id="modalTitle">Thêm địa chỉ mới</h3>
            <button type="button" class="btn-close-modal" id="closeModalBtn">&times;</button>
        </div>
        
        <form id="addressForm" action="/lego_shop_php/profile/addAddress" method="POST" novalidate class="modal-form">
            <div class="form-row">
                <div class="form-group">
                    <label>Tên người nhận <span class="text-red">*</span></label>
                    <input type="text" id="receiver_name" name="receiver_name" placeholder="Nhập tên người nhận">
                    <span class="error-msg" id="receiver_name-error"></span>
                </div>
                <div class="form-group">
                    <label>Số điện thoại <span class="text-red">*</span></label>
                    <input type="tel" id="receiver_phone" name="receiver_phone" placeholder="Nhập số điện thoại">
                    <span class="error-msg" id="receiver_phone-error"></span>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Tỉnh/Thành phố <span class="text-red">*</span></label>
                    <select id="city" name="city" onchange="updateDistrict()">
                        <option value="">Chọn Thành phố</option>
                        <option value="Hồ Chí Minh">TP. Hồ Chí Minh</option>
                        <option value="Hà Nội">Hà Nội</option>
                    </select>
                    <span class="error-msg" id="city-error"></span>
                </div>
                <div class="form-group">
                    <label>Quận/Huyện <span class="text-red">*</span></label>
                    <select id="district" name="district" onchange="updateWard()">
                        <option value="">Chọn Quận/Huyện</option>
                    </select>
                    <span class="error-msg" id="district-error"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Phường/Xã <span class="text-red">*</span></label>
                <select id="ward" name="ward">
                    <option value="">Chọn Phường/Xã</option>
                </select>
                <span class="error-msg" id="ward-error"></span>
            </div>

            <div class="form-group">
                <label>Địa chỉ cụ thể (Số nhà, Tên đường) <span class="text-red">*</span></label>
                <input type="text" id="street" name="street" placeholder="VD: 451 Phạm Thế Hiển">
                <span class="error-msg" id="street-error"></span>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="is_default" name="is_default" value="1">
                <label for="is_default" style="font-weight: normal; margin-bottom: 0;">Đặt làm địa chỉ mặc định</label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" id="cancelModalBtn">Trở lại</button>
                <button type="submit" class="btn-submit-modal">Hoàn thành</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="deleteModal">
    <div class="modal-box" style="max-width: 400px; text-align: center; padding: 30px 20px;">
        <div style="font-size: 55px; color: #e03131; margin-bottom: 15px;">
            <i class="fa-solid fa-circle-exclamation"></i>
        </div>
        <h3 style="margin-bottom: 10px; font-size: 22px; color: #333;">Xóa địa chỉ?</h3>
        <p style="color: #666; margin-bottom: 25px; font-size: 15px; line-height: 1.5;">
            Bạn có chắc chắn muốn xóa địa chỉ này không?<br>Hành động này không thể hoàn tác.
        </p>
        <div style="display: flex; justify-content: center; gap: 15px;">
            <button class="btn-cancel" id="cancelDeleteBtn">Hủy bỏ</button>
            <a href="#" id="confirmDeleteBtn" class="btn-submit-modal" style="text-decoration: none; padding-top: 11px;">Xóa ngay</a>
        </div>
    </div>
</div>

<style>
    /* ÉP CSS KHÔNG CHO NÚT CHẠY LUNG TUNG */
    .address-top {
        display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;
    }
    .address-info { flex: 1; padding-right: 15px; }
    .address-actions {
        display: flex; 
        justify-content: flex-end; 
        gap: 15px; 
        min-width: 100px; /* Cố định chiều rộng tối thiểu để không bị thụt ra vào */
    }

    /* Overlay mờ ảo */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: none; justify-content: center; align-items: center;
        z-index: 9999; opacity: 0; transition: opacity 0.3s ease;
    }
    .modal-overlay.show { display: flex; opacity: 1; }
    
    /* Box Popup */
    .modal-box {
        background: #fff; width: 100%; max-width: 600px;
        border-radius: 12px; padding: 25px 35px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        transform: translateY(-20px); transition: transform 0.3s ease;
    }
    .modal-overlay.show .modal-box { transform: translateY(0); }
    
    /* Header Modal */
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;
    }
    .modal-header h3 { margin: 0; font-size: 20px; color: #333; font-weight: 700; }
    .btn-close-modal { background: none; border: none; font-size: 28px; color: #888; cursor: pointer; line-height: 1; }
    .btn-close-modal:hover { color: #a4161a; }

    /* Layout Form */
    .form-row { display: flex; gap: 15px; }
    .form-group { flex: 1; margin-bottom: 15px; position: relative; }
    
    /* Style Label */
    .modal-form label { display: block; font-weight: 600; font-size: 14px; margin-bottom: 8px; color: #333; text-align: left; }
    .text-red { color: #e03131; font-weight: bold; }
    
    /* Input & Select */
    .modal-form input[type="text"], .modal-form input[type="tel"], .modal-form select {
        width: 100%; padding: 11px 15px; border: 1px solid #ddd;
        border-radius: 8px; font-size: 14px; color: #333; box-sizing: border-box; transition: 0.3s;
    }
    .modal-form input:focus, .modal-form select:focus {
        border-color: #a4161a; outline: none; box-shadow: 0 0 0 3px rgba(164, 22, 26, 0.1);
    }
    .modal-form input.invalid, .modal-form select.invalid { border-color: #e03131; background-color: #fff5f5; }
    .error-msg { color: #e03131; font-size: 12px; margin-top: 4px; display: block; height: 14px; text-align: left; }

    /* Checkbox & Buttons */
    .checkbox-group { display: flex; align-items: center; gap: 10px; margin-top: 5px; margin-bottom: 25px; }
    .checkbox-group input { width: 18px; height: 18px; cursor: pointer; accent-color: #a4161a; }
    .modal-footer { display: flex; justify-content: flex-end; gap: 15px; }
    .btn-cancel { background: white; border: 1px solid #ccc; color: #333; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-cancel:hover { background: #f5f5f5; border-color: #999; }
    .btn-submit-modal { background: #a4161a; color: white; border: none; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; }
    .btn-submit-modal:hover { background: #800f13; }
</style>

<script>
    const addressData = {
        "Hồ Chí Minh": { 
            "Quận 1": ["Phường Bến Nghé", "Phường Bến Thành", "Phường Cô Giang"], 
            "Quận 10": ["Phường 1", "Phường 12", "Phường 14"],
            "Quận 7": ["Phường Tân Hưng", "Phường Tân Quy"]
        },
        "Hà Nội": { 
            "Quận Hoàn Kiếm": ["Phường Cửa Đông", "Phường Hàng Bạc"], 
            "Quận Ba Đình": ["Phường Đội Cấn", "Phường Ngọc Hà"] 
        }
    };

    function updateDistrict() {
        const c = document.getElementById("city").value;
        const d = document.getElementById("district");
        d.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        if (c && addressData[c]) {
            Object.keys(addressData[c]).forEach(k => d.options.add(new Option(k, k)));
        }
        updateWard(); validateField('city');
    }

    function updateWard() {
        const c = document.getElementById("city").value;
        const d = document.getElementById("district").value;
        const w = document.getElementById("ward");
        w.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (c && d && addressData[c][d]) {
            addressData[c][d].forEach(v => w.options.add(new Option(v, v)));
        }
        validateField('district');
    }

    document.getElementById("ward").addEventListener('change', () => validateField('ward'));

    const rules = {
        receiver_name: { reg: /^[\p{L} ]{2,}$/u, msg: "Họ tên quá ngắn hoặc sai." },
        receiver_phone: { reg: /^(0[35789])[0-9]{8}$/, msg: "SĐT không hợp lệ." },
        street: { reg: /^.{5,}$/, msg: "Địa chỉ cụ thể quá ngắn." }
    };

    function validateField(id) {
        const el = document.getElementById(id);
        const err = document.getElementById(id + '-error');
        const val = el.value.trim();
        let ok = true;

        if (val === "") { err.innerText = "Không được trống!"; el.classList.add('invalid'); return false; }
        if (rules[id]) {
            if (!rules[id].reg.test(val)) { err.innerText = rules[id].msg; el.classList.add('invalid'); ok = false; }
            else { err.innerText = ""; el.classList.remove('invalid'); }
        } else if (el.tagName === "SELECT" && val === "") { err.innerText = "Vui lòng chọn."; el.classList.add('invalid'); ok = false; }
        else { err.innerText = ""; el.classList.remove('invalid'); }
        return ok;
    }

    ['receiver_name', 'receiver_phone', 'street'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => validateField(id));
    });

    document.addEventListener("DOMContentLoaded", function() {
        // Biến Modal Thêm/Sửa
        const modal = document.getElementById('addressModal');
        const form = document.getElementById('addressForm');
        const title = document.getElementById('modalTitle');
        const openBtn = document.querySelector('.btn-add-address'); 
        const closeBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelModalBtn');
        const editBtns = document.querySelectorAll('.btn-edit-address');

        // Tạo input ẩn cho tính năng Sửa
        const hiddenIdInput = document.createElement('input');
        hiddenIdInput.type = 'hidden';
        hiddenIdInput.name = 'address_id';
        form.appendChild(hiddenIdInput);

        const closeModal = () => modal.classList.remove('show');
        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        // Mở Modal THÊM MỚI
        openBtn.addEventListener('click', (e) => { 
            e.preventDefault(); 
            title.innerText = "Thêm địa chỉ mới";
            form.action = "/lego_shop_php/profile/addAddress";
            form.reset(); hiddenIdInput.value = '';
            document.getElementById("district").innerHTML = '<option value="">Chọn Quận/Huyện</option>';
            document.getElementById("ward").innerHTML = '<option value="">Chọn Phường/Xã</option>';
            // Reset lỗi
            document.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
            document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
            modal.classList.add('show'); 
        });

        // Mở Modal SỬA (Đổ dữ liệu)
        editBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                title.innerText = "Cập nhật địa chỉ";
                form.action = "/lego_shop_php/profile/editAddress";
                
                hiddenIdInput.value = btn.getAttribute('data-id');
                document.getElementById('receiver_name').value = btn.getAttribute('data-name');
                document.getElementById('receiver_phone').value = btn.getAttribute('data-phone');
                document.getElementById('street').value = btn.getAttribute('data-street');
                document.getElementById('is_default').checked = btn.getAttribute('data-default') == '1';

                document.getElementById('city').value = btn.getAttribute('data-city');
                updateDistrict();
                document.getElementById('district').value = btn.getAttribute('data-district');
                updateWard();
                document.getElementById('ward').value = btn.getAttribute('data-ward');

                // Reset lỗi
                document.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
                document.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));
                modal.classList.add('show');
            });
        });

        form.addEventListener('submit', function(e) {
            let valid = true;
            ['receiver_name', 'receiver_phone', 'city', 'district', 'ward', 'street'].forEach(id => {
                if (!validateField(id)) valid = false;
            });
            if (!valid) {
                e.preventDefault(); showToast("Vui lòng kiểm tra lại thông tin nhập!", "error");
            }
        });

        // --- XỬ LÝ POPUP XÓA ---
        const deleteModal = document.getElementById('deleteModal');
        const deleteBtns = document.querySelectorAll('.btn-delete-address');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const url = btn.getAttribute('href');
                confirmDeleteBtn.setAttribute('href', url);
                deleteModal.classList.add('show');
            });
        });

        cancelDeleteBtn.addEventListener('click', (e) => {
            e.preventDefault(); deleteModal.classList.remove('show');
        });

        // Bấm ra ngoài vùng tối đóng tất cả Modal
        window.addEventListener('click', (e) => { 
            if (e.target == modal) closeModal(); 
            if (e.target == deleteModal) deleteModal.classList.remove('show');
        });
    });
</script>

<?php if (isset($msg) && !empty($msg)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        showToast("<?= htmlspecialchars($msg) ?>", "<?= htmlspecialchars($msg_type ?? 'success') ?>");
    });
</script>
<?php endif; ?>