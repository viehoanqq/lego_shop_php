<header class="admin-header">
    <div class="header-left">
        <div class="page-title">
            <h1><?= $title ?? 'Bảng điều khiển' ?></h1>
            <span class="breadcrumb">Admin / <?= $title ?? 'Tổng quan' ?></span>
        </div>
    </div>

    <div class="header-right">
        <div class="action-item">
            <div class="icon-badge">
                <i class="fa-solid fa-bell"></i>
                <span class="badge">3</span>
            </div>
        </div>

        <div class="action-item">
            <div class="icon-badge">
                <i class="fa-solid fa-message"></i>
                <span class="badge blue">2</span>
            </div>
        </div>

        <div class="divider"></div>

        <div class="admin-profile">
            <div class="profile-info">
                <p class="name"><?= $_SESSION['admin_name'] ?? 'Hoàng Nguyễn' ?></p>
                <p class="role"><?= strtoupper($_SESSION['admin_role'] ?? 'Quản trị viên') ?></p>
            </div>
            <div class="profile-avatar">
                <img src="/lego_shop_php/public/assets/images/avt.png" 
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= $_SESSION['admin_name'] ?? 'H' ?>&background=6366f1&color=fff&bold=true'" 
                     alt="Avatar">
            </div>
        </div>
    </div>
</header>