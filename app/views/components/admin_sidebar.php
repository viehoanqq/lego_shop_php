<aside class="side-bar">
    <div class="logo">
        <h2><a href="/admindashboard">LEGO ADMIN</a></h2>
    </div>
    <nav class="menu">
    <?php 
        // 1. Dùng REQUEST_URI để lấy chính xác 100% đường dẫn thực tế trên thanh địa chỉ
        $current_page = $_SERVER['REQUEST_URI'] ?? ''; 
        
        // 2. Hàm hỗ trợ kiểm tra active menu
        function isActive($path, $current) {
            // Dùng stripos để kiểm tra xem chữ 'adminprice' có nằm trong cái URL dài loằng ngoằng kia không
            // !== false nghĩa là "Có tìm thấy"
            return stripos($current, $path) !== false ? 'active' : '';
        }
    ?>

        <ul class="system">
            <li class="menu-label">Hệ thống</li>
            <li>
                <a href="/admindashboard" class="<?= isActive('admindashboard', $current_page) ?>">
                    <i class="fa-solid fa-gauge-high"></i>Tổng quan
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Kinh doanh</li>
            <li>
                <a href="/adminorder" class="<?= isActive('adminorder', $current_page) ?>">
                    <i class="fa-solid fa-cart-shopping"></i>Quản lý đơn hàng
                </a>
            </li>
             <li>
                <a href="/adminreview" class="<?= isActive('adminreview', $current_page) ?>">
                    <i class="fa-solid fa-star"></i>Đánh giá
                </a>
            </li>
            <li>
                <a href="/admincustomer" class="<?= isActive('admincustomer', $current_page) ?>">
                    <i class="fa-solid fa-user-group"></i>Khách hàng
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Sản phẩm</li>
            <li>
                <a href="/admincategory" class="<?= isActive('admincategory', $current_page) ?>">
                    <i class="fa-solid fa-layer-group"></i>Danh mục
                </a>
            </li>
            <li>
                <a href="/adminproduct" class="<?= isActive('adminproduct', $current_page) ?>">
                    <i class="fa-solid fa-cubes"></i>Sản phẩm
                </a>
            </li>
            
            <li>
    <a href="/adminprice" class="<?= isActive('adminprice', $current_page) ?>">
        <i class="fa-solid fa-tags"></i>Quản lý giá bán
    </a>
</li>
        </ul>

        <ul class="system">
            <li class="menu-label">Kho hàng</li>
            <li>
                <a href="/adminsupplier" class="<?= isActive('adminsupplier', $current_page) ?>">
                    <i class="fa-solid fa-handshake"></i>Nhà cung cấp
                </a>
            </li>
            <li>
    <a href="/adminimport" class="<?= isActive('adminimport', $current_page) ?>">
        <i class="fa-solid fa-file-invoice-dollar"></i>Quản lý phiếu nhập
    </a>
</li>
<li>
                <a href="/admininventory" class="<?= isActive('admininventory', $current_page) ?>">
                    <i class="fa-solid fa-warehouse"></i>Quản lý tồn kho
                </a>
            </li>
            <li>
                <a href="/adminreport" class="<?= isActive('adminreport', $current_page) ?>">
                    <i class="fa-solid fa-chart-line"></i>Thống kê báo cáo
                </a>
            </li>
        </ul>

        <ul class="support">
            <li><a href="/adminsetting" class="<?= isActive('adminsetting', $current_page) ?>">
                <i class="fa-solid fa-gear"></i>Cài đặt hệ thống
            </a></li>
            <li><a href="/admin/logout"><i class="fa-solid fa-power-off"></i>Đăng xuất</a></li>
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