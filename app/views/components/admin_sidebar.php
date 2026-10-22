<aside class="side-bar">
    <div class="logo">
        <h2><a href="/lego_shop_php/admin/dashboard">LEGO ADMIN</a></h2>
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
                <a href="/lego_shop_php/admin/dashboard" class="<?= isActive('admin/dashboard', $current_page) ?>">
                    <i class="fa-solid fa-gauge-high"></i>Tổng quan
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Kinh doanh</li>
            <li>
                <a href="/lego_shop_php/admin/order" class="<?= isActive('admin/order', $current_page) ?>">
                    <i class="fa-solid fa-cart-shopping"></i>Quản lý đơn hàng
                </a>
            </li>
            <?php
                // Số yêu cầu hoàn tiền cần xử lý (chờ duyệt + chờ chuyển tiền)
                require_once __DIR__ . '/../../models/RefundModel.php';
                $refund_counts = (new RefundModel())->countByStatus();
                $refund_open = $refund_counts['pending'] + $refund_counts['approved'];
            ?>
            <li>
                <a href="/lego_shop_php/admin/refund" class="<?= isActive('admin/refund', $current_page) ?>">
                    <i class="fa-solid fa-rotate-left"></i>Hoàn tiền / Trả hàng
                    <?php if ($refund_open > 0): ?><span class="menu-count"><?= $refund_open ?></span><?php endif; ?>
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/payment" class="<?= isActive('admin/payment', $current_page) ?>">
                    <i class="fa-solid fa-money-bill-transfer"></i>Giao dịch VietQR
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/voucher" class="<?= isActive('admin/voucher', $current_page) ?>">
                    <i class="fa-solid fa-ticket"></i>Mã giảm giá
                </a>
            </li>
             <li>
                <a href="/lego_shop_php/admin/review" class="<?= isActive('admin/review', $current_page) ?>">
                    <i class="fa-solid fa-star"></i>Đánh giá
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/customer" class="<?= isActive('admin/customer', $current_page) ?>">
                    <i class="fa-solid fa-user-group"></i>Khách hàng
                </a>
            </li>
        </ul>

        <ul class="system">
            <li class="menu-label">Sản phẩm</li>
            <li>
                <a href="/lego_shop_php/admin/category" class="<?= isActive('admin/category', $current_page) ?>">
                    <i class="fa-solid fa-layer-group"></i>Danh mục
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/product" class="<?= isActive('admin/product', $current_page) ?>">
                    <i class="fa-solid fa-cubes"></i>Sản phẩm
                </a>
            </li>
            
            <li>
    <a href="/lego_shop_php/admin/price" class="<?= isActive('admin/price', $current_page) ?>">
        <i class="fa-solid fa-tags"></i>Quản lý giá bán
    </a>
</li>
        </ul>

        <ul class="system">
            <li class="menu-label">Kho hàng</li>
            <li>
                <a href="/lego_shop_php/admin/supplier" class="<?= isActive('admin/supplier', $current_page) ?>">
                    <i class="fa-solid fa-handshake"></i>Nhà cung cấp
                </a>
            </li>
            <li>
    <a href="/lego_shop_php/admin/import" class="<?= isActive('admin/import', $current_page) ?>">
        <i class="fa-solid fa-file-invoice-dollar"></i>Quản lý phiếu nhập
    </a>
</li>
<li>
                <a href="/lego_shop_php/admin/inventory" class="<?= isActive('admin/inventory', $current_page) ?>">
                    <i class="fa-solid fa-warehouse"></i>Quản lý tồn kho
                </a>
            </li>
            <li>
                <a href="/lego_shop_php/admin/report" class="<?= isActive('admin/report', $current_page) ?>">
                    <i class="fa-solid fa-chart-line"></i>Thống kê báo cáo
                </a>
            </li>
        </ul>

        <ul class="support">
            <li><a href="/lego_shop_php/admin/setting" class="<?= isActive('admin/setting', $current_page) ?>">
                <i class="fa-solid fa-gear"></i>Cài đặt hệ thống
            </a></li>
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
    .menu-count {
        margin-left: auto;
        background: #2563eb;
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
</style>