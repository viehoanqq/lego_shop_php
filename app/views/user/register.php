<?php
$old = $data['old'] ?? [];
$error = $data['error'] ?? null;
?>

<style>
    /* Đồng bộ Background với trang Login */
    .register-wrapper { 
        background: url('/lego_shop_php/public/assets/images/login-bgr.webp') no-repeat center center; 
        background-size: cover; 
        padding: 40px 0; 
        min-height: 100vh; 
        font-family: 'Inter', sans-serif; 
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Box trong suốt nhẹ giống Login */
    .register-card { 
        width: 100%;
        max-width: 650px; 
        background: rgba(255, 255, 255, 0.96); 
        border-radius: 12px; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.2); 
        overflow: hidden; 
    }
    
    .register-header { text-align: center; padding: 25px 20px 10px; }
    .register-header img { width: 120px; margin-bottom: 10px; }
    .register-header h2 { margin: 0; font-size: 24px; color: #a4161a; font-weight: 700; }
    .register-body { padding: 25px 35px 35px; }
    
    .form-section { border-bottom: 1px solid #ddd; margin-bottom: 20px; padding-bottom: 5px; color: #a4161a; font-weight: bold; font-size: 13px; }
    .form-row { display: flex; gap: 15px; }
    .form-group { flex: 1; margin-bottom: 15px; }
    
    /* KHÓA VÙNG CSS: Thêm .register-body phía trước để không ảnh hưởng Header */
    .register-body label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #444; }
    
    .register-body input[type="text"], 
    .register-body input[type="email"], 
    .register-body input[type="password"], 
    .register-body select { 
        width: 100%; padding: 11px 15px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: 0.3s;
    }
    
    .register-body input:focus, 
    .register-body select:focus { 
        border-color: #a4161a; outline: none; box-shadow: 0 0 0 3px rgba(164, 22, 26, 0.1); 
    }
    
    .register-body input.invalid, 
    .register-body select.invalid { 
        border-color: #e03131; background-color: #fff5f5; 
    }
    
    .error-msg { color: #e03131; font-size: 11px; margin-top: 5px; display: block; height: 12px; }
    .alert-danger { background: #fff5f5; color: #c92a2a; padding: 12px; border-left: 5px solid #ff6b6b; margin-bottom: 20px; border-radius: 4px; font-size: 14px; text-align: center; font-weight: 500;}
    
    .terms-group { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #444; margin-top: 10px; margin-bottom: 20px; }
    .terms-group input { width: auto; cursor: pointer; }
    .terms-group a { color: #a4161a; font-weight: bold; text-decoration: none; }
    .terms-group .terms {position: relative ; bottom: 3px;}
    .btn-submit { width: 100%; background: #a4161a; color: white; border: none; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; transition: 0.3s;}
    .btn-submit:hover { background: #801215; }
    
    .auth-links { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    .auth-links a { color: #a4161a; text-decoration: none; font-weight: bold; transition: 0.2s; }
    .auth-links a:hover { text-decoration: underline; }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <div class="register-header">
            <img src="/lego_shop_php/public/assets/images/logo.png" alt="LEGO World">
            <h2>ĐĂNG KÝ THÀNH VIÊN</h2>
        </div>
        
        <div class="register-body">
            <?php if ($error): ?>
                <div class="alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form id="registerForm" action="/lego_shop_php/account/actionRegister" method="POST" novalidate>
                <div class="form-section">TÀI KHOẢN</div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại *</label>
                        <input type="text" name="phone" id="phone" value="<?= $old['phone'] ?? '' ?>" placeholder="Nhập số điện thoại">
                        <span class="error-msg" id="phone-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" id="email" value="<?= $old['email'] ?? '' ?>" placeholder="example@gmail.com">
                        <span class="error-msg" id="email-error"></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Mật khẩu *</label>
                        <input type="password" name="password" id="password" placeholder="Mật khẩu">
                        <span class="error-msg" id="password-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Xác nhận *</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Nhập lại mật khẩu">
                        <span class="error-msg" id="confirm-error"></span>
                    </div>
                </div>

                <div class="form-section">THÔNG TIN & ĐỊA CHỈ</div>
                <div class="form-group">
                    <label>Họ và tên *</label>
                    <input type="text" name="fullname" id="fullname" value="<?= $old['fullname'] ?? '' ?>" placeholder="Nhập họ & tên">
                    <span class="error-msg" id="fullname-error"></span>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Thành phố *</label>
                        <select name="city" id="city" onchange="updateDistrict()">
                            <option value="">Chọn Thành phố</option>
                            <option value="Hồ Chí Minh" <?= (isset($old['city']) && $old['city'] == 'Hồ Chí Minh') ? 'selected' : '' ?>>Hồ Chí Minh</option>
                            <option value="Hà Nội" <?= (isset($old['city']) && $old['city'] == 'Hà Nội') ? 'selected' : '' ?>>Hà Nội</option>
                        </select>
                        <span class="error-msg" id="city-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Quận/Huyện *</label>
                        <select name="district" id="district" onchange="updateWard()">
                            <option value="">Chọn Quận/Huyện</option>
                        </select>
                        <span class="error-msg" id="district-error"></span>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Phường/Xã *</label>
                        <select name="ward" id="ward">
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                        <span class="error-msg" id="ward-error"></span>
                    </div>
                    <div class="form-group">
                        <label>Số nhà, tên đường *</label>
                        <input type="text" name="street" id="street" value="<?= $old['street'] ?? '' ?>" placeholder="Nhập địa chỉ cụ thể">
                        <span class="error-msg" id="street-error"></span>
                    </div>
                </div>

                <div class="terms-group">
                    <input type="checkbox" class= "terms" id="terms" name="terms" required <?= isset($old['terms']) ? 'checked' : '' ?>>
                    <label for="terms">Tôi đã đọc và đồng ý với <a href="#">Điều khoản dịch vụ</a> *</label>
                </div>

                <button type="submit" class="btn-submit">ĐĂNG KÝ NGAY</button>
            </form>

            <div class="auth-links">
                Đã có tài khoản? <a href="/lego_shop_php/account/login">Quay lại trang Đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<script>
    const addressData = {
        "Hồ Chí Minh": { "Quận 1": ["Phường Bến Nghé", "Phường Bến Thành"], "Quận 10": ["Phường 1", "Phường 12"] },
        "Hà Nội": { "Quận Hoàn Kiếm": ["Phường Cửa Đông", "Phường Hàng Bạc"], "Quận Ba Đình": ["Phường Đội Cấn"] }
    };

    const rules = {
        fullname: { reg: /^[\p{L} ]+$/u, msg: "Họ tên chỉ chứa chữ cái." },
        phone: { reg: /^(0[35789])[0-9]{8}$/, msg: "SĐT 10 số, đầu 03,05,07,08,09." },
        email: { reg: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, msg: "Email sai định dạng." },
        password: { reg: /^.{6,}$/, msg: "Mật khẩu tối thiểu 6 ký tự." },
        street: { reg: /^.{5,}$/, msg: "Địa chỉ quá ngắn." }
    };

    function validate(id) {
        const el = document.getElementById(id);
        const err = document.getElementById(id + '-error');
        const val = el.value.trim();
        let ok = true;

        if (val === "") {
            err.innerText = "Không được để trống!";
            el.classList.add('invalid');
            return false; 
        }

        if (rules[id]) {
            if (!rules[id].reg.test(val)) {
                err.innerText = rules[id].msg; el.classList.add('invalid'); ok = false;
            } else { err.innerText = ""; el.classList.remove('invalid'); }
        } else if (el.tagName === "SELECT" && val === "") {
            err.innerText = "Vui lòng chọn."; el.classList.add('invalid'); ok = false;
        } else { err.innerText = ""; el.classList.remove('invalid'); }

        if (id === 'confirm_password' || id === 'password') {
            const p = document.getElementById('password').value;
            const c = document.getElementById('confirm_password').value;
            if (c !== "" && c !== p) { 
                document.getElementById('confirm-error').innerText = "Không khớp."; 
                document.getElementById('confirm_password').classList.add('invalid');
                ok = false; 
            } else if (c !== "") { 
                document.getElementById('confirm-error').innerText = ""; 
                document.getElementById('confirm_password').classList.remove('invalid');
            }
        }
        return ok;
    }

    ['fullname', 'phone', 'email', 'password', 'confirm_password', 'street'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => validate(id));
    });

    function updateDistrict() {
        const c = document.getElementById("city").value;
        const d = document.getElementById("district");
        d.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        if (c && addressData[c]) Object.keys(addressData[c]).forEach(k => d.options.add(new Option(k, k)));
        updateWard(); validate('city');
    }

    function updateWard() {
        const c = document.getElementById("city").value;
        const d = document.getElementById("district").value;
        const w = document.getElementById("ward");
        w.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        if (c && d && addressData[c][d]) addressData[c][d].forEach(v => w.options.add(new Option(v, v)));
        validate('district');
    }

    // Chặn gửi form nếu còn ô trống hoặc lỗi
    document.getElementById('registerForm').addEventListener('submit', function(e) {
        let valid = true;
        ['fullname', 'phone', 'email', 'password', 'confirm_password', 'city', 'district', 'ward', 'street'].forEach(id => {
            if (!validate(id)) valid = false;
        });
        
        const terms = document.getElementById('terms');
        if (!terms.checked) {
            alert("Bạn phải đồng ý với Điều khoản dịch vụ để tiếp tục!");
            valid = false;
        }

        if (!valid) { e.preventDefault(); }
    });

    // Tự động khôi phục Dropdown khi load lại do lỗi
    window.onload = function() {
        if ("<?= $old['city'] ?? '' ?>") {
            updateDistrict(); document.getElementById("district").value = "<?= $old['district'] ?? '' ?>";
            updateWard(); document.getElementById("ward").value = "<?= $old['ward'] ?? '' ?>";
        }
    };
</script>