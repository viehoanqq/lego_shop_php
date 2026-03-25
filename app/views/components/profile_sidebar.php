
<link rel="stylesheet" href="/lego_shop_php/public/assets/css/profile.css?v=<?= time() ?>" />
<aside class="profile-sidebar">
    <div class="profile-avatar">
        <img src="/lego_shop_php/public/assets/images/avt.png" alt="Avatar" class="avatar-img" />
        <h3 class="user-name"><?= htmlspecialchars($_SESSION['user_fullname'] ?? 'Khách hàng') ?></h3>
        <p class="user-email">hihihi@gmail.com</p>
    </div>

    <nav class="profile-menu">
        <a href="/lego_shop_php/profile/index" class="menu-item <?= (isset($active_tab) && $active_tab == 'info') ? 'active' : '' ?>">
            <span>Thông tin cá nhân</span>
        </a>
        <a href="/lego_shop_php/profile/orders" class="menu-item <?= (isset($active_tab) && $active_tab == 'orders') ? 'active' : '' ?>">
            <span>Đơn hàng của tôi</span>
        </a>
        
        <a href="/lego_shop_php/profile/addresses" class="menu-item <?= (isset($active_tab) && $active_tab == 'addresses') ? 'active' : '' ?>">
            <span>Địa chỉ giao hàng</span>
        </a>
        
        <a href="/lego_shop_php/profile/password" class="menu-item <?= (isset($active_tab) && $active_tab == 'password') ? 'active' : '' ?>">
            <span>Đổi mật khẩu</span>
        </a>
        <a href="/lego_shop_php/account/logout" id="logout-btn" class="menu-item text-danger">
            <span>Đăng xuất</span>
        </a>
    </nav>
</aside>