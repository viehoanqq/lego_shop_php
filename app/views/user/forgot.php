<?php $error = $data['error'] ?? null; ?>
<style>
    .forgot-wrapper { background: url('/lego_shop_php/public/assets/images/login-bgr.webp') no-repeat center center; background-size: cover; padding: 60px 0; min-height: 100vh; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; }
    .forgot-card { width: 100%; max-width: 420px; background: rgba(255, 255, 255, 0.96); border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); overflow: hidden; }
    .forgot-header { text-align: center; padding: 30px 20px 10px; }
    .forgot-header img { width: 120px; margin-bottom: 10px; }
    .forgot-header h2 { margin: 0; font-size: 22px; color: #a4161a; font-weight: 700; text-transform: uppercase; }
    .forgot-header p { font-size: 13px; color: #666; margin-top: 10px; }
    .forgot-body { padding: 20px 40px 40px; }
    
    .forgot-body label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #444; }
    .forgot-body input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: 0.3s; box-sizing: border-box; }
    .forgot-body input:focus { border-color: #a4161a; outline: none; box-shadow: 0 0 0 3px rgba(164, 22, 26, 0.1); }
    .forgot-body input.invalid { border-color: #e03131; background-color: #fff5f5; }
    
    .error-msg { color: #e03131; font-size: 11px; margin-top: 5px; display: block; height: 12px; }
    .alert-danger { background: #fff5f5; color: #c92a2a; padding: 12px; border-left: 5px solid #ff6b6b; margin-bottom: 20px; border-radius: 4px; font-size: 14px; text-align: center; font-weight: 500;}
    
    .btn-submit { width: 100%; background: #a4161a; color: white; border: none; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 15px; margin-top: 10px; transition: 0.3s;}
    .btn-submit:hover { background: #801215; }
    .auth-links { text-align: center; margin-top: 20px; font-size: 14px; }
    .auth-links a { color: #a4161a; text-decoration: none; font-weight: bold; }
</style>

<div class="forgot-wrapper">
    <div class="forgot-card">
        <div class="forgot-header">
            <img src="/lego_shop_php/public/assets/images/logo.png" alt="LEGO">
            <h2>Quên mật khẩu</h2>
            <p>Nhập Email hoặc SĐT để tìm lại tài khoản</p>
        </div>
        <div class="forgot-body">
            <?php if ($error): ?> <div class="alert-danger">⚠️ <?= $error ?></div> <?php endif; ?>

            <form id="forgotForm" action="/lego_shop_php/account/actionForgot" method="POST" novalidate>
                <div style="margin-bottom: 20px;">
                    <label>Email hoặc Số điện thoại</label>
                    <input type="text" name="username" id="username" required>
                    <span class="error-msg" id="username-error"></span>
                </div>
                <button type="submit" class="btn-submit">TIẾP TỤC</button>
            </form>
            <div class="auth-links">
                <a href="/lego_shop_php/account/login"><i class="fa-solid fa-arrow-left"></i> Quay lại Đăng nhập</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('forgotForm').addEventListener('submit', function(e) {
        const input = document.getElementById('username');
        if (input.value.trim() === "") {
            e.preventDefault();
            input.classList.add('invalid');
            document.getElementById('username-error').innerText = "Vui lòng nhập trường này.";
        }
    });
</script>