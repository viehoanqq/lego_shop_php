<?php $error = $data['error'] ?? null; ?>
<style>
    /* Dùng chung style với trang forgot, chỉ đổi tên class để khóa vùng */
    .reset-wrapper { background: url('/lego_shop_php/public/assets/images/login-bgr.webp') no-repeat center center; background-size: cover; padding: 60px 0; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Inter', sans-serif;}
    .reset-card { width: 100%; max-width: 420px; background: rgba(255, 255, 255, 0.96); border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); overflow: hidden; }
    .reset-header { text-align: center; padding: 30px 20px 10px; }
    .reset-header h2 { margin: 0; font-size: 22px; color: #a4161a; font-weight: 700; text-transform: uppercase; }
    .reset-body { padding: 20px 40px 40px; }
    
    .reset-body label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #444; }
    .reset-body input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: 0.3s; box-sizing: border-box; }
    .reset-body input:focus { border-color: #a4161a; outline: none; box-shadow: 0 0 0 3px rgba(164, 22, 26, 0.1); }
    .reset-body input.invalid { border-color: #e03131; background-color: #fff5f5; }
    
    .error-msg { color: #e03131; font-size: 11px; margin-top: 5px; display: block; height: 12px; }
    .alert-danger { background: #fff5f5; color: #c92a2a; padding: 12px; border-left: 5px solid #ff6b6b; margin-bottom: 20px; border-radius: 4px; font-size: 14px; text-align: center; font-weight: 500;}
    .btn-submit { width: 100%; background: #a4161a; color: white; border: none; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; margin-top: 10px; transition: 0.3s;}
    .btn-submit:hover { background: #801215; }
</style>

<div class="reset-wrapper">
    <div class="reset-card">
        <div class="reset-header">
            <h2>Tạo mật khẩu mới</h2>
        </div>
        <div class="reset-body">
            <?php if ($error): ?> <div class="alert-danger">⚠️ <?= $error ?></div> <?php endif; ?>

            <form id="resetForm" action="/lego_shop_php/account/actionReset" method="POST" novalidate>
                <div style="margin-bottom: 20px;">
                    <label>Mật khẩu mới</label>
                    <input type="password" name="password" id="password" required>
                    <span class="error-msg" id="password-error"></span>
                </div>
                <div style="margin-bottom: 20px;">
                    <label>Xác nhận mật khẩu</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <span class="error-msg" id="confirm-error"></span>
                </div>
                <button type="submit" class="btn-submit">LƯU MẬT KHẨU</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('resetForm').addEventListener('submit', function(e) {
        const pass = document.getElementById('password').value;
        const conf = document.getElementById('confirm_password').value;
        let valid = true;

        if (pass.length < 6) {
            document.getElementById('password-error').innerText = "Tối thiểu 6 ký tự.";
            document.getElementById('password').classList.add('invalid');
            valid = false;
        } else {
            document.getElementById('password-error').innerText = "";
            document.getElementById('password').classList.remove('invalid');
        }

        if (pass !== conf || conf === "") {
            document.getElementById('confirm-error').innerText = "Không khớp.";
            document.getElementById('confirm_password').classList.add('invalid');
            valid = false;
        } else {
            document.getElementById('confirm-error').innerText = "";
            document.getElementById('confirm_password').classList.remove('invalid');
        }

        if (!valid) e.preventDefault();
    });
</script>