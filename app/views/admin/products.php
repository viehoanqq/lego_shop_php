<style>
    /* --- CSS HEADER ĐỒNG BỘ --- */
.header { 
    background: #fff; 
    padding: 25px; 
    border-radius: 12px; 
    box-shadow: 0 2px 12px rgba(0,0,0,0.05); 
    margin-bottom: 25px;
    display: flex; 
    justify-content: space-between; 
    align-items: flex-end; 
    gap: 20px;
    flex-wrap: wrap; /* Giúp header tự xuống dòng nếu màn hình nhỏ */
    width: 100%;
    box-sizing: border-box; /* Đảm bảo padding không làm tràn width */
}

.header-left { 
    flex: 1; 
    min-width: 300px; 
}

.header-left h2 { 
    margin: 0 0 15px 0; 
    color: #1a202c; 
    font-size: 24px; 
    font-weight: 700; 
}

.search-form { 
    display: flex; 
    gap: 10px; 
    align-items: center; 
    flex-wrap: wrap; 
}

.btn-add-product {
    background: #3182ce; 
    color: white; 
    text-decoration: none; 
    padding: 12px 25px; 
    border-radius: 8px; 
    font-weight: 600; 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    white-space: nowrap;
    transition: 0.2s;
}

.btn-add-product:hover {
    background: #2b6cb0;
    transform: translateY(-1px);
}
.table-container { 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
        margin-top: 10px;

        max-height: 70vh; /* Chiều cao bằng 70% màn hình */
        overflow-y: auto; 
    }

    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }

    .lego-table th { 
        position: sticky; 
        top: 0; 
        z-index: 10;
        background: #f8fafc; 
        padding: 15px; 
        text-align: left; 
        color: #64748b; 
        font-size: 13px; 
        text-transform: uppercase; 
        border-bottom: 2px solid #e2e8f0; 
    }

    .lego-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    
    .table-container::-webkit-scrollbar { width: 6px; }
    .table-container::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
    .product-cell { display: flex; align-items: center; gap: 15px; }
    .img-product { width: 55px; height: 55px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; background: #f7fafc; }
    .price-tag { color: #2b6cb0; font-weight: 700; font-size: 15px; white-space: nowrap; }
    .stock-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .stock-low { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
    .stock-ok { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }
    .status-pill { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }
    .btn-action { text-decoration: none; padding: 6px; border-radius: 6px; transition: 0.2s; }
    .btn-action:hover { background: #f1f5f9; }

    .form-container { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 30px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #4a5568; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; }
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }
    .btn-submit { background: #3182ce; color: white; padding: 10px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; }

    .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; padding-bottom: 30px; }
    .page-link { 
        padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 6px; 
        text-decoration: none; color: #4a5568; background: #fff; transition: 0.2s;
    }
    .page-link:hover { background: #edf2f7; border-color: #cbd5e0; }
    .page-link.active { background: #3182ce; color: white; border-color: #3182ce; font-weight: bold; }
    .page-link.disabled { color: #cbd5e0; pointer-events: none; background: #f8fafc; }
    .stock-empty { background: #fff5f5; color: #c53030; border: 1px solid #63171b; }
    /* --- CSS THÔNG BÁO GÓC TRÊN BÊN PHẢI --- */
#status-alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999; /* Luôn nằm trên cùng */
    width: 350px;  /* Độ rộng cố định cho thông báo */
    pointer-events: none; /* Tránh cản trở việc click chuột khi đang ẩn */
}

.alert-box {
    pointer-events: auto; /* Cho phép click vào thông báo nếu cần */
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    font-weight: 600;
    color: #fff;
    animation: slideInRight 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border-left: 6px solid rgba(0,0,0,0.2);
}

.success-js {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.error-js {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
}

.alert-box i {
    font-size: 22px;
}

@keyframes slideInRight {
    from { 
        opacity: 0; 
        transform: translateX(100%); 
    }
    to { 
        opacity: 1; 
        transform: translateX(0); 
    }
}

.fade-out {
    opacity: 0;
    transform: translateX(50px);
    transition: all 0.5s ease;
}


</style>

<?php 
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
?>

<?php if($session_msg || $session_error): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        
        <?php if($session_msg): ?>
            <div class="alert-box success-js">
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php
                        switch($session_msg) {
                            case 'success': echo "Thêm sản phẩm thành công!"; break;
                            case 'updated': echo "Đã cập nhật thông tin sản phẩm!"; break;
                            case 'show':    echo "Đã mở khóa sản phẩm!"; break;
                            case 'hidden':  echo "Đã khóa sản phẩm khỏi cửa hàng!"; break;
                            case 'deleted': echo "Đã xóa sản phẩm thành công!"; break;
                            default:        echo "Thao tác thành công!"; break;
                        }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($session_error): ?>
            <div class="alert-box error-js">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        switch($session_error) {
                            case 'empty':          echo "Vui lòng điền đầy đủ các trường bắt buộc!"; break;
                            case 'db':             echo "Lỗi hệ thống: Không thể xử lý dữ liệu."; break;
                            case 'sku_exists':     echo "Lỗi: Mã SKU này đã tồn tại!"; break;
                            case 'already_hidden': echo "Sản phẩm này đã bị khóa!"; break;
                            case 'already_shown':  echo "Sản phẩm này hiện đang được mở bán!"; break;
                            case 'notfound':       echo "Không tìm thấy sản phẩm yêu cầu!"; break;
                            case 'has_history':    echo "Không thể xóa! Sản phẩm này đã có trong phiếu nhập hoặc đơn hàng."; break;
                            case 'cat_is_locked':  echo "Danh mục của sản phẩm này đã bị khóa, không thể mở."; break;
                            case 'db':             echo "Lỗi hệ thống: Không thể xóa dữ liệu vào lúc này."; break;
                            default:               echo "Có lỗi xảy ra, vui lòng thử lại."; break;
                        }
                    ?>
                </span>
            </div>
        <?php endif; ?>
        
    </div>
<?php endif; ?>

<div class="header">
    <div class="header-left">
        <h2>📦 Quản lý Kho LEGO</h2>
        
        <form action="/lego_shop_php/adminproduct" method="GET" class="search-form">
            <div style="position: relative; flex: 2; min-width: 200px;">
                <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: #a0aec0;"></i>
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm tên sản phẩm..." 
                       value="<?= $_GET['keyword'] ?? '' ?>"
                       style="padding-left: 35px; border-radius: 8px;">
            </div>

            <select name="category" class="form-control" onchange="this.form.submit()" style="flex: 1; min-width: 150px; cursor: pointer;">
                <option value="all">-- Tất cả danh mục --</option>
                <?php foreach($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>>
                        <?= $cat['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="status" class="form-control" onchange="this.form.submit()" style="flex: 1; min-width: 150px; cursor: pointer;">
                <option value="1,2" <?= (!isset($_GET['status']) || $_GET['status'] == '1,2') ? 'selected' : '' ?>>Tất cả trạng thái</option>
                <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Đang bán</option>
                <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Tạm khóa</option>
            </select>
        </form>
    </div>

    <?php if(!isset($is_form) || $is_form === false): ?>
        <a href="/lego_shop_php/adminproduct/add" class="btn-add-product">
            <i class="fa-solid fa-plus"></i> Thêm sản phẩm
        </a>
    <?php endif; ?>
</div>

<?php if(isset($is_form) && $is_form === true): ?>
    <div class="form-container">
        <h3 style="margin-top:0;"><?= isset($product) ? '🛠️ Chỉnh sửa: ' . $product['name'] : '🆕 Thêm sản phẩm mới' ?></h3>
        <form action="<?= isset($product) ? '/lego_shop_php/adminproduct/update/'.$product['id'] : '/lego_shop_php/adminproduct/store' ?>" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label>Tên sản phẩm LEGO</label>
                        <input type="text" name="name" class="form-control" value="<?= $product['name'] ?? '' ?>" required>
                    </div>
                    <div style="display: flex; gap: 15px;">
                        <div class="form-group" style="flex: 1;">
                            <label>Mã SKU</label>
                            <input type="text" name="sku" class="form-control" value="<?= $product['sku'] ?? '' ?>" required>
                        </div>
                        <div class="form-group" style="flex: 1;">
                            <label>Dòng LEGO</label>
                            <select name="category_id" class="form-control">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mô tả chi tiết</label>
                        <textarea name="description" class="form-control" rows="5"><?= $product['description'] ?? '' ?></textarea>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label>Giá bán (VNĐ)</label>
                        <input type="number" name="selling_price" class="form-control" value="<?= $product['selling_price'] ?? '' ?>" style="font-weight: bold; color: #2b6cb0;"
                        readonly>
                    </div>
                    <div class="form-group">
                        <label>Số mảnh ghép (Pieces)</label>
                        <input type="number" name="pieces" class="form-control" value="<?= $product['pieces'] ?? '' ?>">
                    </div>
                    <div class="form-group">
                        <label>Hình ảnh</label>
                        <input type="file" name="main_image" class="form-control">
                        <?php if(isset($product['main_image'])): ?>
                            <img src="/lego_shop_php/public/assets/images/<?= $product['main_image'] ?>" style="width: 100px; margin-top: 10px; border-radius: 8px;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" class="btn-submit">Lưu dữ liệu</button>
                <a href="/lego_shop_php/adminproduct" style="padding: 10px 20px; color: #718096; text-decoration: none;">Hủy bỏ</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="table-container">
    <table class="lego-table">
        <thead>
            <tr>
                <th style="width: 30%;">Sản phẩm</th>
                <th>Dòng LEGO</th>
                <th>Giá bán</th>
                <th>Thông số</th>
                <th>Tồn kho</th>
                <th>Trạng thái</th>
                <th style="text-align: center;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($products)): ?>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td>
                        <div class="product-cell">
                            <img src="/lego_shop_php/public/assets/images/<?= !empty($p['main_image']) ? $p['main_image'] : 'default.jpg' ?>" 
                                class="img-product" 
                                onerror="this.src='https://placehold.co/60x60?text=LEGO'">
                            <div>
                                <a href="/lego_shop_php/adminproduct/detail/<?= $p['id'] ?>" 
                                style="text-decoration: none; display: block; group">
                                    <div style="font-weight: 700; color: #3182ce; transition: 0.2s;" onmouseover="this.style.color='#2c5282'" onmouseout="this.style.color='#3182ce'">
                                        <?= htmlspecialchars($p['name']) ?> 
                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 10px; margin-left: 4px; opacity: 0.5;"></i>
                                    </div>
                                </a>
                                <div style="font-size: 11px; color: #a0aec0; letter-spacing: 0.5px;">SKU: <?= strtoupper($p['sku']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size: 13px; font-weight: 500; color: #4a5568;">
                        <span style="background: #edf2f7; padding: 3px 8px; border-radius: 4px;"><?= $p['category_name'] ?></span>
                    </td>
                    <td class="price-tag"><?= number_format($p['selling_price'], 0, ',', '.') ?>đ</td>
                    <td style="font-size: 13px; color: #718096;">
                        <strong><?= number_format($p['pieces']) ?></strong> pcs
                    </td>
                    <td>
                        <?php if($p['stock_quantity'] <= 0): ?>
                            <span class="stock-badge stock-empty">Hết hàng</span>
                        <?php elseif($p['stock_quantity'] <= $p['min_stock_level']): ?>
                            <span class="stock-badge stock-low">Sắp hết: <?= $p['stock_quantity'] ?></span>
                        <?php else: ?>
                            <span class="stock-badge stock-ok">Còn: <?= $p['stock_quantity'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($p['status'] == 1): ?>
                            <span style="color: #38a169; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#48bb78"></span>Đang bán</span>
                        <?php else: ?>
                            <span style="color: #e53e3e; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#f56565"></span>Tạm khóa</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="/lego_shop_php/adminproduct/edit/<?= $p['id'] ?>" class="btn-action" title="Chỉnh sửa" style="color: #3182ce;">
                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                            </a>
                            <?php 
                                // 1. Tính toán trạng thái ngay trong vòng lặp
                                $isActive = ($p['status'] == 1); // 1 là đang bán, 2 là tạm khóa
                                $btnIcon  = $isActive ? 'fa-lock' : 'fa-lock-open';
                                $btnColor = $isActive ? '#e53e3e' : '#38a169'; // Đỏ khi sắp Khóa, Xanh khi sắp Mở
                                $btnText  = $isActive ? 'Khóa' : 'Mở';
                            ?>

                            <a href="/lego_shop_php/adminproduct/toggleStatus/<?= $p['id'] ?>?current=<?= $p['status'] ?>" 
                                class="btn-action btn-toggle-status" 
                                title="<?= $isActive ? 'Tạm dừng bán' : 'Mở bán lại' ?>" 
                                style="color: <?= $btnColor ?>;"
                                onclick="return confirm('Xác nhận <?= mb_strtolower($btnText) ?> sản phẩm này?')">
                                
                                <i class="fa-solid <?= $btnIcon ?>"></i>
                                <span style="margin-left: 4px; font-weight: 600;">
                                    <?= $btnText ?>
                                </span>
                            </a>

                            <?php if($p['status'] != 0): ?>
                                <a href="/lego_shop_php/adminproduct/hide/<?= $p['id'] ?>" 
                                class="btn-action" 
                                style="color: #e53e3e;" 
                                title="Ẩn hoàn toàn (Xóa mềm)"
                                onclick="return confirm('Sản phẩm sẽ bị ẩn hoàn toàn. Xác nhận?')">
                                    <i class="fa-solid fa-eye-slash"></i> Ẩn
                                </a>
                            <?php else: ?>
                                <a href="/lego_shop_php/adminproduct/show/<?= $p['id'] ?>" 
                                class="btn-action" 
                                style="color: #718096;" 
                                title="Hiện lại">
                                    <i class="fa-solid fa-eye"></i> Hiện
                                </a>
                            <?php endif; ?>

                            <a href="/lego_shop_php/adminproduct/delete/<?= $p['id'] ?>" class="btn-action" style="color: #e53e3e;" onclick="return confirm('Xóa vĩnh viễn sản phẩm?')">
                                <i class="fa-solid fa-trash"></i> Xóa
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 40px; color: #a0aec0;">Chưa có sản phẩm nào trong kho hàng.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php if(isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">
        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" 
           class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
               class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" 
           class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
    </div>
    <div style="text-align: center; color: #718096; font-size: 13px; margin-top: -10px;">
        Hiển thị trang <?= $currentPage ?> / <?= $totalPages ?> (Tổng <?= $totalItems ?> sản phẩm)
    </div>
<?php endif; ?>

<script>
// Tự động ẩn thông báo sau 5 giây
setTimeout(function() {
    let alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(el => {
        el.style.transition = "opacity 0.5s ease";
        el.style.opacity = "0";
        setTimeout(() => el.style.display = 'none', 500);
    });
}, 5000);
</script>