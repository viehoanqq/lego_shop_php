<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box border-red-box">
                <h2 class="section-title text-red" style="margin-bottom: 8px;">Đổi mật khẩu</h2>
                <p class="section-desc" style="margin-bottom: 25px;">Để bảo mật tài khoản, vui lòng không chia sẻ mật khẩu cho người khác</p>

                <form id="changePasswordForm" action="/lego_shop_php/profile/actionUpdatePassword" method="POST" novalidate class="modal-form">
                    
                    <div class="form-group">
                        <label>Mật khẩu hiện tại <span class="text-red">*</span></label>
                        <div class="pwd-wrapper">
                            <input type="password" id="old_password" name="old_password" placeholder="Nhập mật khẩu hiện tại">
                            <i class="fa-solid fa-eye-slash toggle-pwd"></i>
                        </div>
                        <span class="error-msg" id="old_password-error"></span>
                        <a href="/lego_shop_php/account/forgot" class="forgot-link">Quên mật khẩu?</a>
                    </div>

                    <div class="form-group" style="margin-top: 15px;">
                        <label>Mật khẩu mới <span class="text-red">*</span></label>
                        <div class="pwd-wrapper">
                            <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới (Tối thiểu 6 ký tự)">
                            <i class="fa-solid fa-eye-slash toggle-pwd"></i>
                        </div>
                        <span class="error-msg" id="new_password-error"></span>
                    </div>

                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới <span class="text-red">*</span></label>
                        <div class="pwd-wrapper">
                            <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới">
                            <i class="fa-solid fa-eye-slash toggle-pwd"></i>
                        </div>
                        <span class="error-msg" id="confirm_password-error"></span>
                    </div>

                    <div class="form-group" style="margin-top: 30px;">
                        <button type="submit" class="btn-submit-full">Xác nhận thay đổi</button>
                    </div>
                </form>
            </div>
        </section>

    </div>
</div>

<style>
    /* Khung viền đỏ */
    .border-red-box {
        border: 1px solid #a4161a;
        border-radius: 8px;
        padding: 30px 40px !important;
        background-color: #fff;
    }
    .text-red { color: #a4161a !important; }
    
    /* Layout Form */
    .form-group { margin-bottom: 20px; position: relative; }
    .modal-form label {
        display: block; font-weight: 700; font-size: 14px;
        margin-bottom: 8px; color: #333; text-align: left;
    }
    
    /* Box chứa input và icon mắt */
    .pwd-wrapper { position: relative; display: flex; align-items: center; }
    .pwd-wrapper input {
        width: 100%; padding: 12px 45px 12px 15px; border: 1px solid #ddd;
        border-radius: 8px; font-size: 14px; color: #333; box-sizing: border-box; transition: 0.3s;
    }
    .pwd-wrapper input:focus { border-color: #a4161a; outline: none; box-shadow: 0 0 0 3px rgba(164, 22, 26, 0.1); }
    .pwd-wrapper input.invalid { border-color: #e03131; background-color: #fff5f5; }
    
    /* Icon mắt */
    .toggle-pwd { position: absolute; right: 15px; color: #888; cursor: pointer; font-size: 16px; padding: 5px; }
    .toggle-pwd:hover { color: #333; }

    /* Thông báo lỗi & Link Quên pass */
    .error-msg { color: #e03131; font-size: 12px; margin-top: 4px; display: block; height: 14px; text-align: left; }
    .forgot-link {
        display: block; text-align: right; color: #a4161a; font-size: 13px; font-weight: 600;
        margin-top: 6px; text-decoration: none;
    }
    .forgot-link:hover { text-decoration: underline; }

    /* Nút xác nhận đỏ */
    .btn-submit-full {
        width: 100%; background: #a4161a; color: white; border: none;
        padding: 14px 20px; border-radius: 6px; font-weight: 600; font-size: 16px;
        cursor: pointer; transition: 0.2s;
    }
    .btn-submit-full:hover { background: #800f13; }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. TÍNH NĂNG ẨN/HIỆN MẬT KHẨU
    const toggleIcons = document.querySelectorAll('.toggle-pwd');
    toggleIcons.forEach(icon => {
        icon.addEventListener('click', function() {
            const input = this.previousElementSibling;
            if (input.type === 'password') {
                input.type = 'text';
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                input.type = 'password';
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    });

    // 2. CHECK LỖI TRÌNH DUYỆT
    const form = document.getElementById('changePasswordForm');
    const oldPwd = document.getElementById('old_password');
    const newPwd = document.getElementById('new_password');
    const confirmPwd = document.getElementById('confirm_password');

    function validateField(input, errorId, minLength = 1) {
        const val = input.value.trim();
        const err = document.getElementById(errorId);
        let ok = true;

        if (val === "") {
            err.innerText = "Không được để trống!"; input.classList.add('invalid'); ok = false;
        } else if (val.length < minLength) {
            err.innerText = `Tối thiểu ${minLength} ký tự!`; input.classList.add('invalid'); ok = false;
        } else { err.innerText = ""; input.classList.remove('invalid'); }
        return ok;
    }

    form.addEventListener('submit', function(e) {
        let valid = true;
        if (!validateField(oldPwd, 'old_password-error')) valid = false;
        if (!validateField(newPwd, 'new_password-error', 6)) valid = false;
        if (!validateField(confirmPwd, 'confirm_password-error')) valid = false;

        if (newPwd.value !== "" && confirmPwd.value !== "" && newPwd.value !== confirmPwd.value) {
            document.getElementById('confirm_password-error').innerText = "Mật khẩu xác nhận không khớp!";
            confirmPwd.classList.add('invalid'); valid = false;
        }
        if (!valid) { e.preventDefault(); showToast("Vui lòng kiểm tra lại!", "error"); }
    });

    oldPwd.addEventListener('input', () => validateField(oldPwd, 'old_password-error'));
    newPwd.addEventListener('input', () => {
        validateField(newPwd, 'new_password-error', 6);
        if (confirmPwd.value !== "") {
            if (newPwd.value !== confirmPwd.value) {
                document.getElementById('confirm_password-error').innerText = "Mật khẩu xác nhận không khớp!";
                confirmPwd.classList.add('invalid');
            } else {
                document.getElementById('confirm_password-error').innerText = "";
                confirmPwd.classList.remove('invalid');
            }
        }
    });
    confirmPwd.addEventListener('input', () => {
        if (confirmPwd.value !== newPwd.value && confirmPwd.value !== "") {
            document.getElementById('confirm_password-error').innerText = "Mật khẩu xác nhận không khớp!";
            confirmPwd.classList.add('invalid');
        } else { validateField(confirmPwd, 'confirm_password-error'); }
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