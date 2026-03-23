<div class="login-container" style="background: url('/lego_shop_php/public/assets/images/login-bgr.webp') no-repeat center center; background-size: cover; min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div style="background: rgba(255, 255, 255, 0.95); padding: 40px; border-radius: 15px; width: 100%; max-width: 400px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="/lego_shop_php/public/assets/images/logo.png" width="120">
            <h2 style="color: #a4161a; margin-top: 15px;">Đăng Nhập</h2>
        </div>

        <form action="/lego_shop_php/account/actionLogin" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Mật khẩu</label>
                <input type="password" name="password" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
            </div>

            <button type="submit" style="width: 100%; background: #a4161a; color: white; border: none; padding: 12px; border-radius: 8px; font-weight: bold; cursor: pointer;">ĐĂNG NHẬP</button>
        </form>

        <p style="text-align: center; margin-top: 20px;">
            Chưa có tài khoản? <a href="/lego_shop_php/account/register" style="color: #a4161a; text-decoration: none; font-weight: bold;">Đăng ký ngay</a>
        </p>
    </div>
</div>