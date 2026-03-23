<?php
$old = $data['old'] ?? [];
$error = $data['error'] ?? null;
$success = $data['success'] ?? null;
?>

<style>
    .login-wrapper { background: url('/lego_shop_php/public/assets/images/login-bgr.webp') no-repeat center center; background-size: cover; padding: 60px 0; min-height: 100vh; font-family: 'Inter', sans-serif; display: flex; align-items: center; justify-content: center; }
    .login-card { width: 100%; max-width: 420px; background: rgba(255, 255, 255, 0.96); border-radius: 12px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); overflow: hidden; }
    .login-header { text-align: center; padding: 30px 20px 10px; }
    .login-header img { width: 140px; margin-bottom: 15px; }
    .login-header h2 { margin: 0; font-size: 24px; color: #a4161a; font-weight: 700; text-transform: uppercase; }
    .login-body { padding: 30px 40px 40px; }
    
    .form-group { margin-bottom: 20px; position: relative; }
    .login-body label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 600; color: #444; }
    .login-body input { width: 100%; padding: 12px 15px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; transition: 0.3s; box-sizing: border-box; }
    .login-body input:focus { border-color: #a4161a; outline: none; box-shadow: 0 0 0 3px rgba(164, 22, 26, 0.1); }
    .login-body input.invalid { border-color: #e03131; background-color: #fff5f5; }
    
    .error-msg { color: #e03131; font-size: 11px; margin-top: 5px; display: block; height: 12px; }
    .alert-danger { background: #fff5f5; color: #c92a2a; padding: 12px; border-left: 5px solid #ff6b6b; margin-bottom: 20px; border-radius: 4px; font-size: 14px; text-align: center; font-weight: 500;}
    .alert-success { background: #ebfbee; color: #2b8a3e; padding: 12px; border-left: 5px solid #40c057; margin-bottom: 20px; border-radius: 4px; font-size: 14px; text-align: center; font-weight: 500;}
    
    .login-body .btn-submit { width: 100%; background: #a4161a; color: white; border: none; padding: 14px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 16px; margin-top: 10px; transition: 0.3s;}
    .login-body .btn-submit:hover { background: #801215; }
    .login-body .auth-links { text-align: center; margin-top: 20px; font-size: 14px; color: #666; }
    .login-body .auth-links a { color: #a4161a; text-decoration: none; font-weight: bold; transition: 0.2s; }
    .login-body .auth-links a:hover { text-decoration: underline; }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <img src="/lego_shop_php/public/assets/images/logo.png" alt="LEGO World">
            <h2>ĐĂNG NHẬP</h2>
        </div>

        <div class="login-body">
            <?php if ($success): ?>
                <div class="alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form id="loginForm" action="/lego_shop_php/account/actionLogin" method="POST" novalidate>
                <div class="form-group">
                    <label>Email hoặc Số điện thoại</label>
                    <input type="text" name="username" id="username" value="<?= $old['username'] ?? '' ?>" placeholder="Nhập Email hoặc SĐT của bạn" required>
                    <span class="error-msg" id="username-error"></span>
                </div>

                <div class="form-group">
                    <label>Mật khẩu</label>
                    <input type="password" name="password" id="password" placeholder="Nhập mật khẩu" required>
                    <span class="error-msg" id="password-error"></span>
                </div>

                <div style="text-align: right; margin-bottom: 20px; font-size: 13px;">
                    <a href="/lego_shop_php/account/forgot" style="color: #666; text-decoration: none;">Quên mật khẩu?</a>    </div>

                <button type="submit" class="btn-submit">ĐĂNG NHẬP</button>
            </form>

            <div class="auth-links">
                Chưa có tài khoản? <a href="/lego_shop_php/account/register">Đăng ký ngay</a>
            </div>
        </div>
    </div>
</div>

<script>
    function validateInput(id) {
        const input = document.getElementById(id);
        const error = document.getElementById(id + '-error');
        if (input.value.trim() === "") {
            input.classList.add('invalid');
            error.innerText = "Vui lòng nhập trường này.";
            return false;
        } else {
            input.classList.remove('invalid');
            error.innerText = "";
            return true;
        }
    }

    ['username', 'password'].forEach(id => {
        document.getElementById(id).addEventListener('input', () => validateInput(id));
    });

    document.getElementById('loginForm').addEventListener('submit', function(e) {
        let isUserValid = validateInput('username');
        let isPassValid = validateInput('password');
        
        if (!isUserValid || !isPassValid) {
            e.preventDefault();
        }
    });
</script>