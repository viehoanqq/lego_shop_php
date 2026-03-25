<style>
.table-container { 
        background: #fff; 
        border-radius: 12px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.08); 
        margin-top: 10px;
        /* THÊM 2 DÒNG DƯỚI ĐÂY */
        max-height: 70vh; /* Chiều cao bằng 70% màn hình */
        overflow-y: auto; 
    }

    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }

    /* GIỮ TIÊU ĐỀ ĐỨNG YÊN KHI CUỘN */
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
    
    /* Tùy chỉnh thanh cuộn cho đẹp */
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

</style>

<?php if(isset($_GET['msg']) || isset($_GET['error'])): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert-box success-js" style="padding: 15px; border-radius: 8px; background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php
                        if($_GET['msg'] == 'success') echo "✨ Thêm sản phẩm thành công!";
                        elseif($_GET['msg'] == 'updated') echo "✅ Đã cập nhật thông tin sản phẩm!";
                        elseif($_GET['msg'] == 'hidden') echo "🔒 Đã ẩn sản phẩm khỏi cửa hàng!";
                        elseif($_GET['msg'] == 'deleted') echo "🗑️ Đã xóa sản phẩm thành công!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-box error-js" style="padding: 15px; border-radius: 8px; background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        if($_GET['error'] == 'empty') echo "⚠️ Vui lòng điền đầy đủ các trường bắt buộc!";
                        elseif($_GET['error'] == 'db') echo "❌ Lỗi hệ thống: Không thể xử lý dữ liệu.";
                        elseif($_GET['error'] == 'sku_exists') echo "🆔 Lỗi: Mã SKU này đã tồn tại!";
                        else echo "❌ Có lỗi xảy ra, vui lòng thử lại.";
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="admin-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 10px;">
    <div>
        <h2 style="margin:0; color: #1a202c;">📦 Danh Sách LEGO</h2>
        <small style="color: #718096;">Quản lý chi tiết</small>
    </div>
    <?php if(!isset($is_form) || $is_form === false): ?>
        <a href="/lego_shop_php/adminproduct/add" style="background: #3182ce; color: white; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">+ Thêm sản phẩm</a>
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
                        <input type="number" name="selling_price" class="form-control" value="<?= $product['selling_price'] ?? '' ?>" style="font-weight: bold; color: #2b6cb0;">
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
                                <div style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($p['name']) ?></div>
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
                        <?php if($p['stock_quantity'] <= $p['min_stock_level']): ?>
                            <span class="stock-badge stock-low">Sắp hết: <?= $p['stock_quantity'] ?></span>
                        <?php else: ?>
                            <span class="stock-badge stock-ok">Còn: <?= $p['stock_quantity'] ?></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($p['status'] == 1): ?>
                            <span style="color: #38a169; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#48bb78"></span>Đang bán</span>
                        <?php else: ?>
                            <span style="color: #e53e3e; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#f56565"></span>Tạm ẩn</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="/lego_shop_php/adminproduct/edit/<?= $p['id'] ?>" class="btn-action" title="Chỉnh sửa" style="color: #3182ce;">
                                <i class="fa-solid fa-pen-to-square"></i> Sửa
                            </a>
                            <a href="/lego_shop_php/adminproduct/hide/<?= $p['id'] ?>" class="btn-action" title="Xóa" style="color: #e53e3e;" onclick="return confirm('Bạn có chắc chắn muốn ẩn sản phẩm <?= $p['name'] ?>?')">
                                <i class="fa-solid fa-eye-slash"></i> Ẩn
                            </a>
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