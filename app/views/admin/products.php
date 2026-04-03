<style>
    /* ===== CÁC CSS CŨ VÀ MỚI ===== */
    * { box-sizing: border-box; }

    .header { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; }
    .header-left { flex: 1; min-width: 280px; }
    .header-left h2 { margin-bottom: 15px; color: #1a202c; font-size: 22px; font-weight: 700; }

    .search-form { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
    .search-box { position: relative; flex: 2; min-width: 220px; }
    .search-box input { width: 100%; padding: 10px 100px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px; transition: 0.2s; }
    .search-box input:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.1); outline: none; }
    .search-box i { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: #a0aec0; }
    .btn-search-inside { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: #3182ce; color: #fff; border: none; padding: 5px 14px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; height: 30px; display: flex; align-items: center; transition: 0.2s; }
    .btn-search-inside:hover { background: #2b6cb0; }
    .form-control { padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; transition: 0.2s; width: 100%;}
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.1); outline: none; }

    .btn-add-product { background: #3182ce; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; transition: 0.2s; height: 42px;}
    .btn-add-product:hover { background: #2b6cb0; transform: translateY(-1px); }

    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); max-height: 70vh; overflow-y: auto; }
    .lego-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .lego-table th { position: sticky; top: 0; z-index: 10; background: #f8fafc; padding: 14px; text-align: left; font-size: 12px; text-transform: uppercase; color: #64748b; border-bottom: 2px solid #e2e8f0; }
    .lego-table td { padding: 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }

    .product-cell { display: flex; align-items: center; gap: 12px; }
    .img-product { width: 55px; height: 55px; border-radius: 8px; object-fit: cover; border: 1px solid #e2e8f0; }
    .price-tag { color: #2b6cb0; font-weight: 700; }
    .status-pill { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 6px; }

    .stock-badge { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .stock-low { background: #fff5f5; color: #c53030; }
    .stock-ok { background: #f0fff4; color: #2f855a; }
    .stock-empty { background: #fff5f5; color: #c53030; }
    .btn-action { text-decoration: none; padding: 6px; border-radius: 6px; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center;}
    .btn-action:hover { background: #f1f5f9; }

    /* ===== ALERT KHÔI PHỤC ĐẦY ĐỦ ===== */
    #status-alert-container { position: fixed; top: 20px; right: 20px; z-index: 9999; width: 320px; }
    .alert-box { display: flex; align-items: center; gap: 12px; padding: 14px; border-radius: 10px; margin-bottom: 10px; color: #fff; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .success-js { background: #38a169; }
    .error-js { background: #e53e3e; }

    .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
    .page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 6px; text-decoration: none; color: #4a5568; background: #fff; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #edf2f7; }
    .page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; }
    .page-link.disabled { color: #a0aec0; pointer-events: none; background: #f8fafc; }

    .form-container { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 25px; }
    .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
    .form-group label { font-size: 13px; font-weight: 600; color: #4a5568; margin-bottom: 6px; }
    textarea.form-control { resize: vertical; }
    .btn-submit { background: #38a169; color: #fff; padding: 10px 18px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px;}
    .btn-submit:hover { background: #2f855a; transform: translateY(-1px); }
    .error-text { color: #e53e3e; font-size: 12px; margin-top: 4px; min-height: 18px; }
    .input-error { border-color: #e53e3e !important; }
    
    .image-upload-box { width: 100%; height: 200px; border: 2px dashed #cbd5e1; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-direction: column; cursor: pointer; overflow: hidden; position: relative; background: #f8fafc; transition: 0.2s; }
    .image-upload-box:hover { border-color: #3182ce; background: #ebf8ff; }
    .image-upload-box img { width: 100%; height: 100%; object-fit: contain; }
    .image-upload-placeholder { text-align: center; color: #94a3b8; font-size: 13px; font-weight: 600;}
    .image-upload-placeholder i { font-size: 28px; margin-bottom: 8px; color: #cbd5e1; transition: 0.2s;}
    
    .section-block { background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-bottom: 20px; }
    .section-title-form { margin: 0 0 15px 0; color: #1e293b; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; font-size: 16px;}
    .input-group-text { display: flex; align-items: center; background: #edf2f7; border: 1px solid #e2e8f0; border-left: none; padding: 0 15px; border-radius: 0 6px 6px 0; font-weight: 600; color: #4a5568;}
</style>

<?php 
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
?>

<?php if($session_msg || $session_error): ?>
    <div id="status-alert-container">
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
                            case 'deleted': echo "Đã xóa sản phẩm vĩnh viễn thành công!"; break;
                            case 'hidden_due_to_history': echo "Sản phẩm có lịch sử nên đã được tự động Ẩn thay vì Xóa!"; break;
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
                            case 'notfound':       echo "Không tìm thấy sản phẩm yêu cầu!"; break;
                            case 'cat_is_locked':  echo "Danh mục của sản phẩm đã bị khóa!"; break;
                            default:               echo "Có lỗi xảy ra, vui lòng thử lại."; break;
                        }
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if(!isset($is_form) || $is_form === false): ?>
    <div class="header">
        <div class="header-left">
            <h2><i class="fa-solid fa-boxes-stacked" style="color: #3182ce;"></i> Quản lý Sản Phẩm</h2>
            <form action="/lego_shop_php/adminproduct" method="GET" class="search-form">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" placeholder="Tìm tên sản phẩm, mã SKU..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                    <button type="submit" class="btn-search-inside">Tìm kiếm</button>
                </div>
                <select name="category" class="form-control" onchange="this.form.submit()" style="flex: 1; min-width: 150px;">
                    <option value="all">-- Tất cả danh mục --</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-control" onchange="this.form.submit()" style="flex: 1; min-width: 150px;">
                    <option value="1,2" <?= (!isset($_GET['status']) || $_GET['status'] == '1,2') ? 'selected' : '' ?>>Tất cả trạng thái</option>
                    <option value="1" <?= (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : '' ?>>Đang bán</option>
                    <option value="2" <?= (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : '' ?>>Tạm khóa</option>
                    <option value="3" <?= (isset($_GET['status']) && $_GET['status'] == '3') ? 'selected' : '' ?>>Đã bị ẩn (Xóa mềm)</option>
                </select>
            </form>
        </div>
        <a href="/lego_shop_php/adminproduct/add" class="btn-add-product">
            <i class="fa-solid fa-plus"></i> Thêm sản phẩm
        </a>
    </div>

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
                    <tr style="<?= $p['status'] == 3 ? 'opacity: 0.6; background: #f8fafc;' : '' ?>">
                        <td>
                            <div class="product-cell">
                                <img src="/lego_shop_php/public/assets/images/<?= !empty($p['main_image']) ? $p['main_image'] : 'default.jpg' ?>" class="img-product" onerror="this.src='https://placehold.co/60x60?text=LEGO'">
                                <div>
                                    <a href="/lego_shop_php/adminproduct/detail/<?= $p['id'] ?>" style="text-decoration: none; display: block;">
                                        <div style="font-weight: 700; color: #3182ce;"><?= htmlspecialchars($p['name']) ?></div>
                                    </a>
                                    <div style="font-size: 11px; color: #a0aec0;">SKU: <?= strtoupper($p['sku']) ?></div>
                                </div>
                            </div>
                        </td>
                        <td><span style="background: #edf2f7; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; color: #4a5568;"><?= htmlspecialchars($p['category_name']) ?></span></td>
                        <td style="color: #2b6cb0; font-weight: 700;"><?= number_format($p['selling_price'] ?? 0, 0, ',', '.') ?>đ</td>
                        <td style="font-size: 13px; color: #718096;"><strong><?= number_format($p['pieces'] ?? 0) ?></strong> pcs</td>
                        <td>
                            <?php if($p['stock_quantity'] <= 0): ?>
                                <span class="stock-badge stock-empty">Hết hàng</span>
                            <?php elseif($p['stock_quantity'] <= ($p['min_stock_level'] ?? 5)): ?>
                                <span class="stock-badge stock-low">Sắp hết: <?= $p['stock_quantity'] ?></span>
                            <?php else: ?>
                                <span class="stock-badge stock-ok">Còn: <?= $p['stock_quantity'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($p['status'] == 1): ?>
                                <span style="color: #38a169; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#48bb78"></span>Đang bán</span>
                            <?php elseif($p['status'] == 2): ?>
                                <span style="color: #dd6b20; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#ed8936"></span>Tạm khóa</span>
                            <?php elseif($p['status'] == 3): ?>
                                <span style="color: #718096; font-size: 13px; font-weight: 600;"><span class="status-pill" style="background:#a0aec0"></span>Đã bị ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="/lego_shop_php/adminproduct/edit/<?= $p['id'] ?>" class="btn-action" title="Chỉnh sửa" style="color: #3182ce;">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <?php if($p['status'] == 3): ?>
                                    <a href="/lego_shop_php/adminproduct/restore/<?= $p['id'] ?>" class="btn-action" style="color: #38a169;" title="Khôi phục bán lại" onclick="return confirm('Bạn muốn khôi phục sản phẩm này?')">
                                        <i class="fa-solid fa-rotate-left"></i>
                                    </a>
                                <?php else: ?>
                                    <?php 
                                        $isActive = ($p['status'] == 1); 
                                        $btnIcon  = $isActive ? 'fa-eye-slash' : 'fa-eye';
                                        $btnColor = $isActive ? '#dd6b20' : '#38a169'; 
                                    ?>
                                    <a href="/lego_shop_php/adminproduct/toggleStatus/<?= $p['id'] ?>?current=<?= $p['status'] ?>" class="btn-action" style="color: <?= $btnColor ?>;" title="<?= $isActive ? 'Ẩn / Khóa' : 'Hiện / Mở bán' ?>">
                                        <i class="fa-solid <?= $btnIcon ?>"></i>
                                    </a>
                                    <a href="/lego_shop_php/adminproduct/delete/<?= $p['id'] ?>" class="btn-action" style="color: #e53e3e;" onclick="return confirm('Bạn có chắc muốn xóa/ẩn sản phẩm này?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align: center; padding: 40px; color: #a0aec0;">Chưa có sản phẩm nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>" class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-left"></i></a>
            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>" class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-right"></i></a>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="form-container">
        <h3 style="margin-top:0; color: #1e293b; margin-bottom: 20px;">
            <?= isset($product) ? '<i class="fa-solid fa-pen-to-square" style="color: #3182ce;"></i> Chỉnh sửa: ' . htmlspecialchars($product['name']) : '<i class="fa-solid fa-folder-plus" style="color: #38a169;"></i> Thêm sản phẩm mới' ?>
        </h3>
        
        <form id="productForm" action="<?= isset($product) ? '/lego_shop_php/adminproduct/update/'.$product['id'] : '/lego_shop_php/adminproduct/store' ?>" method="POST" enctype="multipart/form-data">
            
            <div style="display: flex; gap: 25px; flex-wrap: wrap;">
                
                <div style="flex: 2; min-width: 400px;">
                    <div class="section-block">
                        <h4 class="section-title-form">1. Thông tin chung</h4>
                        
                        <div class="form-group">
                            <label>Tên sản phẩm LEGO <span style="color:red">*</span></label>
                            <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" placeholder="VD: Millennium Falcon">
                            <small class="error-text"></small>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Mã SKU <span style="color:red">*</span></label>
                                <input type="text" id="sku" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku'] ?? '') ?>" placeholder="VD: SW-75192">
                                <small class="error-text"></small>
                            </div>
                            <div class="form-group">
                                <label>Dòng LEGO (Danh mục)</label>
                                <select name="category_id" class="form-control">
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Đơn vị tính</label>
                                <input type="text" name="unit" class="form-control" value="<?= htmlspecialchars($product['unit'] ?? 'Hộp') ?>" placeholder="Hộp, Bộ, Cái...">
                            </div>
                            
                            <?php if(!isset($product)): ?>
                            <div class="form-group">
                                <label>Hiện trạng lúc tạo <span style="color:red">*</span></label>
                                <select name="status" class="form-control">
                                    <option value="1">Hiển thị (Đang bán)</option>
                                    <option value="2">Ẩn (Tạm khóa không bán)</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Mô tả tổng quan</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả sản phẩm..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; margin-top: 15px;">
                            <label>Câu chuyện chủ đề (Theme Story)</label>
                            <textarea name="theme_story" class="form-control" rows="2" placeholder="Nhập câu chuyện đằng sau bộ LEGO này..."><?= htmlspecialchars($product['theme_story'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="section-block" style="background: #fffbeb; border-color: #fde68a;">
                        <h4 class="section-title-form" style="color: #92400e; border-color: #fde68a;">2. Chi tiết kỹ thuật</h4>
                        
                        <?php 
                            $dim = $product['dimensions'] ?? '';
                            $l = ''; $w = ''; $h = '';
                            if ($dim) {
                                $parts = explode('x', str_replace('cm', '', $dim));
                                if (count($parts) >= 3) {
                                    $l = trim($parts[0]); $w = trim($parts[1]); $h = trim($parts[2]);
                                }
                            }
                            $age = isset($product['age_range']) ? intval($product['age_range']) : 18;
                        ?>

                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                            <div class="form-group">
                                <label>Số mảnh ghép <span style="color:red">*</span></label>
                                <input type="number" id="pieces" name="pieces" class="form-control" value="<?= $product['pieces'] ?? '' ?>" placeholder="VD: 1000">
                                <small class="error-text"></small>
                            </div>
                            <div class="form-group">
                                <label>Độ tuổi (+)</label>
                                <input type="number" id="age_range" name="age_range" class="form-control" value="<?= $age ?>" min="1" max="99">
                            </div>
                            <div class="form-group">
                                <label>Năm phát hành</label>
                                <input type="number" id="release_year" name="release_year" class="form-control" value="<?= $product['release_year'] ?? date('Y') ?>">
                            </div>
                            
                            <div class="form-group">
                                <label>Hãng sản xuất</label>
                                <input type="text" name="manufacturer" class="form-control" value="<?= htmlspecialchars($product['manufacturer'] ?? 'The LEGO Group') ?>">
                            </div>
                            <div class="form-group">
                                <label>Chất liệu</label>
                                <input type="text" name="material" class="form-control" value="<?= htmlspecialchars($product['material'] ?? 'Nhựa ABS an toàn') ?>">
                            </div>
                        </div>
                        
                        <div class="form-group" style="margin-top: 15px; margin-bottom: 0;">
                            <label>Kích thước (Dài x Rộng x Cao) - cm</label>
                            <div style="display: flex; gap: 5px;">
                                <input type="number" name="length" class="form-control" placeholder="Dài" value="<?= $l ?>" style="text-align: center;">
                                <input type="number" name="width" class="form-control" placeholder="Rộng" value="<?= $w ?>" style="text-align: center;">
                                <input type="number" name="height" class="form-control" placeholder="Cao" value="<?= $h ?>" style="text-align: center;">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="flex: 1; min-width: 300px;">
                    
                    <div class="section-block" style="background: #f0fff4; border-color: #c6f6d5;">
                        <h4 class="section-title-form" style="color: #276749; border-color: #c6f6d5;">3. Lợi nhuận & Cấu hình</h4>

                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Tỉ lệ lợi nhuận mong muốn (%)</label>
                            <div style="display: flex;">
                                <input type="number" name="profit_margin" class="form-control" 
                                       style="border-radius: 6px 0 0 6px; <?= isset($product) ? 'background:#e2e8f0; cursor:not-allowed;' : '' ?>" 
                                       value="<?= isset($product['profit_margin']) ? ($product['profit_margin'] * 100) : '30' ?>" 
                                       placeholder="VD: 30" 
                                       <?= isset($product) ? 'readonly title="Vui lòng sang trang Quản lý Giá bán để thay đổi"' : '' ?>>
                                <span class="input-group-text">%</span>
                            </div>
                            
                            <?php if(isset($product)): ?>
                                <small style="color:#e53e3e; margin-top:6px; font-weight: 600;">
                                    <i class="fa-solid fa-lock" style="margin-right: 4px;"></i> Đã khóa. Vui lòng cập nhật qua <a href="/lego_shop_php/adminprice" style="color: #3182ce; text-decoration: underline;">Quản lý Giá bán</a>.
                                </small>
                            <?php else: ?>
                                <small style="color:#718096; margin-top:4px;">Hệ thống sẽ gợi ý giá bán dựa trên tỉ lệ này khi nhập hàng.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="section-block">
                        <h4 class="section-title-form">4. Hình ảnh đại diện</h4>
                        <div class="form-group" style="margin-bottom: 0;">
                            <input type="file" id="main_image" name="main_image" accept="image/*" style="display: none;" onchange="previewImage(this)">
                            <div class="image-upload-box" onclick="document.getElementById('main_image').click()">
                                <?php if(!empty($product['main_image'])): ?>
                                    <img id="image_preview" src="/lego_shop_php/public/assets/images/<?= $product['main_image'] ?>">
                                    <div class="image-upload-placeholder" id="image_placeholder" style="display:none;">
                                        <i class="fa-solid fa-cloud-arrow-up"></i><br>Đổi ảnh khác
                                    </div>
                                <?php else: ?>
                                    <img id="image_preview" src="" style="display:none;">
                                    <div class="image-upload-placeholder" id="image_placeholder">
                                        <i class="fa-solid fa-cloud-arrow-up"></i><br>Nhấn để chọn ảnh
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 10px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu toàn bộ thông tin</button>
                <a href="/lego_shop_php/adminproduct" style="padding: 10px 20px; color: #475569; text-decoration: none; font-weight: 600; background: #edf2f7; border-radius: 8px;">Quay lại</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<script>
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-box');
        alerts.forEach(el => {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = "0";
            setTimeout(() => el.style.display = 'none', 500);
        });
    }, 5000);

    function previewImage(input) {
        const preview = document.getElementById('image_preview');
        const placeholder = document.getElementById('image_placeholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    const form = document.getElementById("productForm");
    if(form) {
        const nameInput = document.getElementById("name");
        const skuInput = document.getElementById("sku");
        const piecesInput = document.getElementById("pieces");

        function showError(input, message) {
            input.classList.add("input-error");
            input.nextElementSibling.innerText = message;
        }
        function showSuccess(input) {
            input.classList.remove("input-error");
            input.nextElementSibling.innerText = "";
        }

        form.addEventListener("submit", function(e) {
            let valid = true;
            
            if (nameInput.value.trim() === "") { showError(nameInput, "Tên không được trống!"); valid = false; } else { showSuccess(nameInput); }
            
            const skuVal = skuInput.value.trim();
            if (skuVal === "") { showError(skuInput, "SKU không được trống!"); valid = false; } 
            else if (!/^[A-Z0-9\-]+$/.test(skuVal)) { showError(skuInput, "SKU chỉ chứa chữ IN HOA, số, dấu -"); valid = false; }
            else { showSuccess(skuInput); }
            
            if (piecesInput.value === "" || parseInt(piecesInput.value) < 0) { showError(piecesInput, "Số mảnh ghép không hợp lệ!"); valid = false; } else { showSuccess(piecesInput); }
            
            if (!valid) {
                e.preventDefault(); 
                alert("Vui lòng kiểm tra lại các trường bị báo đỏ!");
            }
        });
    }
</script>   