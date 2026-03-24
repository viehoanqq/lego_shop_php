<aside class="side-bar">
    <div class="logo">
        <h2><a href="/lego_shop_php/admin/dashboard">LEGO STORE</a></h2>
    </div>
    <nav class="menu">
        <ul class="system">
            <?php 
                $current_page = $_GET['url'] ?? ''; 
            ?>
            <li>
                <a href="/lego_shop_php/admin/dashboard" class="<?= strpos($current_page, 'dashboard') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-house"></i>Tổng quan
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/messages" class="<?= strpos($current_page, 'messages') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-envelope"></i>Tin nhắn <span class="message">2</span>
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/products" class="<?= strpos($current_page, 'products') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-box"></i>Sản phẩm
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/categories" class="<?= strpos($current_page, 'categories') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-tags"></i>Danh mục
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/orders" class="<?= strpos($current_page, 'orders') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-receipt"></i>Đơn hàng
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/customers" class="<?= strpos($current_page, 'customers') !== false ? 'active' : '' ?>">
                    <i class="fa-solid fa-users"></i>Khách hàng
                </a>
            </li>
        </ul>
        <ul class="support">
            <li><a href="/lego_shop_php/admin/logout"><i class="fa-solid fa-right-from-bracket"></i>Đăng xuất</a></li>
        </ul>
    </nav>
</aside>