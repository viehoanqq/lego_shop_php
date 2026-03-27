<header class="admin-header">
    <div class="header-left">
        <div class="page-title">
            <h1><?= $title ?? 'Bảng điều khiển' ?></h1>
            <span class="breadcrumb">
                <a href="/lego_shop_php/admin/dashboard" style="color: inherit; text-decoration: none;">Admin</a> / <?= $title ?? 'Tổng quan' ?>
            </span>
        </div>
    </div>

    <div class="header-right">
        

        <div class="divider"></div>

        <div class="admin-profile-wrapper">
            <div class="admin-profile">
                <div class="profile-info">
                    <p class="name"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></p>
                    <p class="role"><?= strtoupper($_SESSION['admin_role'] ?? 'Quản trị viên') ?></p>
                </div>
                <div class="profile-avatar">
                    <img src="/lego_shop_php/public/assets/images/avt.png" 
                         onerror="this.src='https://ui-avatars.com/api/?name=<?= $_SESSION['admin_name'] ?? 'A' ?>&background=6366f1&color=fff&bold=true'" 
                         alt="Avatar">
                </div>
                <i class="fa-solid fa-chevron-down dropdown-icon"></i>
            </div>

            <ul class="profile-dropdown-menu">
                <li>
                    <a href="/lego_shop_php/admin/profile"><i class="fa-solid fa-user-gear"></i> Hồ sơ của tôi</a>
                </li>
                <li class="logout-link">
                    <a href="/lego_shop_php/admin/logout"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
                </li>
            </ul>
        </div>
    </div>
</header>