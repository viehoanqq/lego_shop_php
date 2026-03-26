<aside class="side-bar">
    <div class="logo">
        <h2><a href="/lego_shop_php/admin/dashboard">LEGO ADMIN</a></h2>
    </div>
    <nav class="menu">
        <?php 
            $current_page = $_GET['url'] ?? ''; 
            // Hàm hỗ trợ kiểm tra active menu
            function isActive($path, $current) {
                return strpos($current, $path) !== false ? 'active' : '';
            }
        ?>

        <ul class="system">
            <li class="menu-label">Hệ thống</li>
            <li>
                <a href="/lego_shop_php/admin/dashboard" class="<?= isActive('dashboard', $current_page) ?>">
                    <i class="fa-solid fa-gauge-high"></i>Tổng quan
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/alerts" class="<?= isActive('alerts', $current_page) ?>">
                    <i class="fa-solid fa-bell-slash"></i>Cảnh báo hết hàng
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/messages" class="<?= isActive('messages', $current_page) ?>">
                    <i class="fa-solid fa-star"></i>Đánh giá
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Kinh doanh</li>
            <li>
                <a href="/lego_shop_php/admin/orders" class="<?= isActive('orders', $current_page) ?>">
                    <i class="fa-solid fa-cart-shopping"></i>Quản lý đơn hàng
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admincustomer" class="<?= isActive('customer', $current_page) ?>">
                    <i class="fa-solid fa-user-group"></i>Khách hàng
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Sản phẩm</li>
            <li>
                <a href="/lego_shop_php/adminproduct" class="<?= isActive('adminproduct', $current_page) ?>">
                    <i class="fa-solid fa-cubes"></i>Danh sách LEGO
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admincategory" class="<?= isActive('admincategory', $current_page) ?>">
                    <i class="fa-solid fa-layer-group"></i>Danh mục
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/pricing" class="<?= isActive('pricing', $current_page) ?>">
                    <i class="fa-solid fa-tags"></i>Quản lý giá bán
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Kho hàng</li>
            <li>
                <a href="/lego_shop_php/admin/suppliers" class="<?= isActive('suppliers', $current_page) ?>">
                    <i class="fa-solid fa-handshake"></i>Nhà cung cấp
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/imports" class="<?= isActive('imports', $current_page) ?>">
                    <i class="fa-solid fa-file-invoice-dollar"></i>Phiếu nhập hàng
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/reports" class="<?= isActive('reports', $current_page) ?>">
                    <i class="fa-solid fa-chart-line"></i>Thống kê báo cáo
                </a>
            </li>
        </ul>

        <ul class="support">
            <li><a href="/lego_shop_php/admin/settings"><i class="fa-solid fa-gear"></i>Cài đặt hệt thống</a></li>
            <li><a href="/lego_shop_php/admin/logout"><i class="fa-solid fa-power-off"></i>Đăng xuất</a></li>
        </ul>
    </nav>
</aside>

<style>
    /* Thêm một chút CSS cho các nhãn phân nhóm (menu-label) cho đẹp */
    .menu-label {
        padding: 15px 25px 5px 25px;
        font-size: 11px;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .side-bar .menu ul {
        margin-bottom: 15px;
    }
</style>