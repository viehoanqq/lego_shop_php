<div class="main-content" style="width: 100%; max-width: 1200px; margin: 30px auto; background-color: #f9f9f9;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box">
                <h2 class="section-title">Thông tin cá nhân</h2>
                <p class="section-desc">Cập nhật thông tin để nhận ưu đãi và giao hàng nhanh hơn.</p>

                <form id="profileForm" class="profile-form" action="/lego_shop_php/profile/updateInfo" method="POST">
                    
                    <div class="input-group">
                        <label>Họ và tên <span style="color: red;">*</span></label>
                        <input type="text" id="fullname" name="fullname" value="<?= htmlspecialchars($user_info['fullname'] ?? '') ?>" required />
                        <small id="nameError" style="color: red; display: none; margin-top: 5px;"></small>
                    </div>

                    <div class="input-group">
                        <label>Email (Tài khoản đăng nhập)</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user_info['email'] ?? '') ?>" readonly style="background-color: #f5f5f5; cursor: not-allowed; color: #888;" title="Email đăng nhập không thể thay đổi" />
                    </div>

                    <div class="input-group">
                        <label>Số điện thoại <span style="color: red;">*</span></label>
                        <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user_info['phone'] ?? '') ?>" required />
                        <small id="phoneError" style="color: red; display: none; margin-top: 5px;"></small>
                        <small style="display: block; margin-top: 6px; color: #888; font-size: 12px;">Nhập 10 số, bắt đầu bằng 03, 05, 07, 08, 09</small>
                    </div>

                    <button type="submit" class="update-btn"><i class="fa-solid fa-floppy-disk"></i> Cập nhật thông tin</button>
                </form>
            </div>
        </section>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. Nếu có thông báo từ Server trả về -> Hiện Toast
    <?php if (isset($msg) && !empty($msg)): ?>
        showToast("<?= htmlspecialchars($msg) ?>", "<?= htmlspecialchars($msg_type ?? 'success') ?>");
    <?php endif; ?>

    // 2. Bắt lỗi người dùng nhập form ngay tại trình duyệt
    const form = document.getElementById('profileForm');
    const inputName = document.getElementById('fullname');
    const inputPhone = document.getElementById('phone');
    const nameError = document.getElementById('nameError');
    const phoneError = document.getElementById('phoneError');

    // Regex check sđt VN chuẩn
    const phoneRegex = /^(0[3|5|7|8|9])+([0-9]{8})$/;

    form.addEventListener('submit', function(event) {
        let isValid = true;

        // Reset thông báo lỗi
        nameError.style.display = 'none';
        phoneError.style.display = 'none';
        inputName.style.borderColor = '#e0e0e0';
        inputPhone.style.borderColor = '#e0e0e0';

        // Check Tên (Không được để toàn dấu cách hoặc quá ngắn)
        if (inputName.value.trim().length < 2) {
            nameError.textContent = "Họ tên quá ngắn!";
            nameError.style.display = 'block';
            inputName.style.borderColor = 'red';
            isValid = false;
        }

        // Check Số điện thoại
        if (!phoneRegex.test(inputPhone.value.trim())) {
            phoneError.textContent = "Số điện thoại không hợp lệ!";
            phoneError.style.display = 'block';
            inputPhone.style.borderColor = 'red';
            isValid = false;
        }

        // Nếu có lỗi -> Chặn không cho gửi lên Server, báo Toast
        if (!isValid) {
            event.preventDefault(); 
            showToast("Vui lòng kiểm tra lại thông tin nhập!", "error");
        }
    });
});
</script>