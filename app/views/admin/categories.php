<style>
    /* ===== CÁC CSS CŨ VÀ MỚI ===== */
    * { box-sizing: border-box; margin: 0; padding: 0 }

    /* ===== HEADER & TÌM KIẾM ===== */
    .header { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 15px;}
    .header h2 { margin: 0 0 15px 0; color: #1e293b; font-size: 22px; font-weight: 700; }
    .filter-form { display: flex; gap: 10px; align-items: center; flex: 1; }
    .search-wrapper { position: relative; flex: 2; min-width: 250px; }
    .search-wrapper i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
    .form-control { padding: 10px 10px 10px 35px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; width: 100%; transition: 0.2s; font-size: 14px;}
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49,130,206,0.1); }
    .btn-search-inside { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: #3182ce; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px; transition: 0.2s;}
    .btn-search-inside:hover { background: #2b6cb0; }
    .btn-add-sync { background: #3182ce; color: #fff; text-decoration: none; padding: 0 20px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; height: 42px; transition: 0.2s;}
    .btn-add-sync:hover { background: #2b6cb0; transform: translateY(-1px); }

    /* ===== BẢNG DANH MỤC (DẠNG GRID CARD) ===== */
    .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .card-cat { background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; transition: 0.3s; position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.04);}
    .card-cat:hover { transform: translateY(-4px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); border-color: #cbd5e1;}
    
    .cat-img-wrapper { height: 180px; background: #f8fafc; position: relative; overflow: hidden; border-bottom: 1px solid #e2e8f0;}
    .cat-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
    .card-cat:hover .cat-img { transform: scale(1.05); }
    
    .cat-badge { position: absolute; bottom: 10px; right: 10px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; z-index: 2; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.15);}
    
    .cat-info { padding: 20px; }
    .cat-name { font-weight: 700; font-size: 16px; color: #1e293b; margin-bottom: 6px; }
    .cat-desc { color: #64748b; font-size: 13px; height: 38px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.5;}
    
    .cat-meta { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; border-top: 1px dashed #e2e8f0; padding-top: 15px; }
    .btn-edit, .btn-action-icon { text-decoration: none; font-weight: 600; font-size: 13px; display: flex; align-items: center; gap: 5px; padding: 6px 10px; border-radius: 6px; transition: 0.2s;}
    .btn-edit:hover, .btn-action-icon:hover { background: #f1f5f9; }

    /* ===== FORM THÊM/SỬA ===== */
    .form-container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 25px; border: 1px solid #e2e8f0;}
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 8px; color: #475569; font-size: 14px;}
    .form-input { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; outline: none; transition: 0.2s; font-size: 14px;}
    .form-input:focus { border-color: #3182ce; background: #f8fafc;}
    .btn-submit { background: #38a169; color: #fff; padding: 12px 25px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; transition: 0.2s; font-size: 14px;}
    .btn-submit:hover { background: #2f855a; transform: translateY(-1px);}
    .btn-cancel-link { margin-left: 15px; color: #64748b; text-decoration: none; font-weight: 600; padding: 12px 20px; border-radius: 8px; background: #f1f5f9; transition: 0.2s;}
    .btn-cancel-link:hover { background: #e2e8f0; color: #1e293b;}
    
    .error-text { color: #e53e3e; font-size: 12px; display: block; margin-top: 6px; min-height: 18px; font-weight: 500;}
    .input-error { border-color: #e53e3e !important; background: #fff5f5 !important;}
    .input-success { border-color: #38a169 !important; }

    /* ===== ALERT & PHÂN TRANG ===== */
    #status-alert-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
    .alert-box { display: flex; gap: 10px; padding: 15px 20px; border-radius: 10px; margin-bottom: 10px; color: #fff; font-weight: 600; box-shadow: 0 4px 15px rgba(0,0,0,0.15); align-items: center;}
    .success-js { background: #38a169; }
    .error-js { background: #e53e3e; }
    
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 30px; margin-bottom: 30px;}
    .page-link { padding: 8px 16px; border-radius: 8px; border: 1px solid #cbd5e1; text-decoration: none; color: #475569; font-weight: 600; transition: 0.2s; background: #fff;}
    .page-link:hover { background: #f1f5f9; }
    .page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; }
    .page-link.disabled { opacity: 0.5; pointer-events: none; background: #f8fafc;}
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
                        if($session_msg == 'success') echo "Thêm danh mục mới thành công!";
                        if($session_msg == 'updated') echo "Đã cập nhật thông tin danh mục!";
                        if($session_msg == 'hidden') echo "Đã chuyển danh mục sang trạng thái Khóa!";
                        if($session_msg == 'unlocked') echo "Đã mở khóa danh mục thành công!";
                        if($session_msg == 'deleted') echo "Đã xóa vĩnh viễn danh mục trống!";
                        if($session_msg == 'soft_deleted') echo "Danh mục đang có SP nên đã TẠM ẨN!";
                        if($session_msg == 'restored') echo "Đã khôi phục danh mục thành công!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($session_error): ?>
            <div class="alert-box error-js">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        if($session_error == 'cat_is_locked') echo "Không được mở sản phẩm khi danh mục đang khóa.";
                        if($session_error == 'db') echo "Lỗi hệ thống: Không thể xử lý dữ liệu.";
                        if($session_error == 'empty') echo "Vui lòng không để trống các trường bắt buộc!";
                        if($session_error == 'name_exists') echo "Lỗi: Tên danh mục này đã tồn tại trong hệ thống!"; // Thông báo lỗi mới
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if(!isset($is_form) || $is_form === false): ?>
    
    <div class="header">
        <div class="header-left-group" style="flex: 1; display: flex; flex-direction: column;">
            <form action="/lego_shop_php/admincategory" method="GET" class="filter-form">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" class="form-control" placeholder="Nhập tên danh mục cần tìm..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
                    <button type="submit" class="btn-search-inside">Tìm kiếm</button>
                </div>
                <select name="status" class="form-control" onchange="this.form.submit()" style="flex: unset; width: 180px; cursor: pointer; padding-left: 15px;">
                    <option value="all" <?= ($filters['status'] == 'all') ? 'selected' : '' ?>>Tất cả trạng thái</option>
                    <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="locked" <?= ($filters['status'] == 'locked') ? 'selected' : '' ?>>Đã khóa</option>
                    <option value="hidden" <?= ($filters['status'] == 'hidden') ? 'selected' : '' ?>>Đã bị ẩn (Xóa mềm)</option>
                </select>
            </form>
        </div>
        <a href="/lego_shop_php/admincategory/add" class="btn-add-sync"><i class="fa-solid fa-plus"></i> Thêm danh mục</a>
    </div>

    <div class="category-grid">
        <?php if(!empty($categories)): ?>
            <?php foreach ($categories as $cat): ?>
                <?php 
                    $is_hidden = ($cat['status'] == 'hidden');
                    $is_locked = ($cat['status'] == 'locked');
                    $opacity = $is_hidden ? '0.6' : ($is_locked ? '0.8' : '1');
                    $bg = ($is_hidden || $is_locked) ? '#f8fafc' : '#fff';
                    $grayscale = $is_hidden ? 'grayscale(1)' : ($is_locked ? 'grayscale(0.5)' : 'none');
                ?>
                <div class="card-cat" style="cursor: pointer; opacity: <?= $opacity ?>; background: <?= $bg ?>;" 
                     onclick="window.location.href='/lego_shop_php/admincategory/edit/<?= $cat['id'] ?>'">
                    
                    <div class="cat-img-wrapper">
                        <?php if($is_hidden): ?>
                            <span class="cat-badge" style="background: #718096; color: #fff;"><i class="fa-solid fa-eye-slash"></i> ĐÃ ẨN</span>
                        <?php elseif($is_locked): ?>
                            <span class="cat-badge" style="background: #e53e3e; color: #fff;"><i class="fa-solid fa-lock"></i> ĐÃ KHÓA</span>
                        <?php else: ?>
                            <span class="cat-badge" style="background: rgba(15, 23, 42, 0.7); color: #fff; backdrop-filter: blur(4px);">
                                <i class="fa-solid fa-cube"></i> <?= $cat['product_count'] ?? 0 ?> SP
                            </span>
                        <?php endif; ?>

                        <img src="/lego_shop_php/public/assets/images/<?= !empty($cat['image_url']) ? $cat['image_url'] : 'default.jpg' ?>" 
                             class="cat-img" style="filter: <?= $grayscale ?>;"
                             onerror="this.src='https://placehold.co/300x180?text=LEGO'">
                    </div>

                    <div class="cat-info">
                        <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
                        <div class="cat-desc"><?= htmlspecialchars($cat['description']) ?></div>
                        
                        <div class="cat-meta" onclick="event.stopPropagation();">
                            <span style="font-size: 12px; color: #94a3b8; font-weight: 600;">ID: #CAT-<?= $cat['id'] ?></span>
                            
                            <div style="display: flex; gap: 5px;">
                                <a href="/lego_shop_php/admincategory/edit/<?= $cat['id'] ?>" class="btn-edit" style="color: #3182ce;" title="Chỉnh sửa"><i class="fa-solid fa-pen-to-square"></i></a>

                                <?php if($is_hidden): ?>
                                    <a href="/lego_shop_php/admincategory/restore/<?= $cat['id'] ?>" class="btn-action-icon" style="color: #38a169;" title="Khôi phục danh mục" onclick="return confirm('Bạn muốn khôi phục danh mục này?')"><i class="fa-solid fa-rotate-left"></i></a>
                                <?php else: ?>
                                    <?php if($is_locked): ?>
                                        <a href="/lego_shop_php/admincategory/unlock/<?= $cat['id'] ?>" class="btn-action-icon" style="color: #38a169;" title="Mở khóa"><i class="fa-solid fa-lock-open"></i></a>
                                    <?php else: ?>
                                        <a href="/lego_shop_php/admincategory/lock/<?= $cat['id'] ?>" class="btn-action-icon" style="color: #dd6b20;" title="Khóa danh mục" onclick="return confirm('Khóa danh mục này sẽ TẠM ẨN tất cả sản phẩm bên trong?')"><i class="fa-solid fa-lock"></i></a>
                                    <?php endif; ?>
                                    
                                    <a href="/lego_shop_php/admincategory/delete/<?= $cat['id'] ?>" class="btn-action-icon" style="color: #e53e3e;" title="Xóa" onclick="return confirm('Bạn có chắc muốn xóa/ẩn danh mục này khỏi hệ thống?')"><i class="fa-solid fa-trash"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #64748b; background: #fff; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <i class="fa-solid fa-folder-open" style="font-size: 40px; margin-bottom: 15px; color: #cbd5e1;"></i><br>
                Chưa có danh mục nào.
            </div>
        <?php endif; ?>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <a href="?keyword=<?= urlencode($filters['keyword']) ?>&status=<?= $filters['status'] ?>&page=<?= $currentPage - 1 ?>" 
               class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-left"></i></a>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?keyword=<?= urlencode($filters['keyword']) ?>&status=<?= $filters['status'] ?>&page=<?= $i ?>" 
                   class="page-link <?= ($currentPage == $i) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <a href="?keyword=<?= urlencode($filters['keyword']) ?>&status=<?= $filters['status'] ?>&page=<?= $currentPage + 1 ?>" 
               class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>"><i class="fa-solid fa-chevron-right"></i></a>
        </div>
    <?php endif; ?>

<?php else: ?>
    
    <div class="form-container">
        <h3 style="margin-top:0; color: #1e293b; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9;">
            <i class="fa-solid <?= isset($category) ? 'fa-pen-to-square' : 'fa-folder-plus' ?>" style="color: #3182ce; margin-right: 10px;"></i>
            <?= isset($category) ? 'Chỉnh sửa Danh mục: <span style="color:#dd6b20;">'.htmlspecialchars($category['name']).'</span>' : 'Thêm Danh mục mới' ?>
        </h3>
        
        <form id="categoryForm" action="<?= isset($category) ? '/lego_shop_php/admincategory/update/'.$category['id'] : '/lego_shop_php/admincategory/store' ?>" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label>Tên danh mục <span style="color:red">*</span></label>
                        <input type="text" id="name" name="name" class="form-input" placeholder="Ví dụ: LEGO Technic..." value="<?= htmlspecialchars($category['name'] ?? '') ?>">
                        <small class="error-text"></small>
                    </div>

                    <div class="form-group">
                        <label>Mô tả chi tiết</label>
                        <textarea id="description" name="description" class="form-input" rows="6" placeholder="Mô tả ngắn gọn về dòng sản phẩm này..." style="resize: vertical;"><?= htmlspecialchars($category['description'] ?? '') ?></textarea>
                        <small class="error-text"></small>
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label>Hình ảnh đại diện</label>
                        <div style="border: 2px dashed #cbd5e1; padding: 20px; border-radius: 12px; text-align: center; background: #f8fafc;">
                            <input type="file" name="image_url" id="image_url" style="width: 100%; margin-bottom: 15px;">
                            
                            <?php if(isset($category['image_url']) && !empty($category['image_url'])): ?>
                                <div style="margin-top: 10px;">
                                    <p style="font-size: 13px; color: #64748b; margin-bottom: 5px;">Ảnh hiện tại:</p>
                                    <img src="/lego_shop_php/public/assets/images/<?= $category['image_url'] ?>" style="max-width: 100%; height: 120px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; padding: 5px;">
                                </div>
                            <?php else: ?>
                                <i class="fa-solid fa-image" style="font-size: 40px; color: #cbd5e1; margin-top: 10px;"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 10px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk" style="margin-right: 8px;"></i> XÁC NHẬN LƯU</button>
                <a href="/lego_shop_php/admincategory" class="btn-cancel-link">Hủy bỏ</a>
            </div>
        </form>
    </div>

    <script>
        var form = document.getElementById("categoryForm");
        if (form) {
            const nameInput = document.getElementById("name");
            const descInput = document.getElementById("description");

            function showError(input, message) {
                input.classList.add("input-error");
                input.classList.remove("input-success");
                input.nextElementSibling.innerText = message;
            }

            function showSuccess(input) {
                input.classList.remove("input-error");
                input.classList.add("input-success");
                input.nextElementSibling.innerText = "";
            }

            function validateName() {
                const value = nameInput.value.trim();
                if (value === "") { showError(nameInput, "Tên danh mục không được để trống!"); return false; }
                if (value.length < 3) { showError(nameInput, "Tên tối thiểu phải có 3 ký tự."); return false; }
                if (value.length > 100) { showError(nameInput, "Tên không được vượt quá 100 ký tự."); return false; }
                showSuccess(nameInput); return true;
            }

            function validateDescription() {
                const value = descInput.value.trim();
                if (value.length > 255) { showError(descInput, "Mô tả không được vượt quá 255 ký tự."); return false; }
                showSuccess(descInput); return true;
            }

            nameInput.addEventListener("input", validateName);
            descInput.addEventListener("input", validateDescription);

            form.addEventListener("submit", function(e) {
                const isNameValid = validateName();
                const isDescValid = validateDescription();

                if (!(isNameValid && isDescValid)) {
                    e.preventDefault();
                    alert("⚠️ Vui lòng kiểm tra lại các trường bị báo đỏ trước khi lưu!");
                    nameInput.focus();
                }
            });
        }
    </script>
<?php endif; ?>

<script>
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert-box');
        alerts.forEach(el => {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = "0";
            setTimeout(() => el.style.display = 'none', 500);
        });
    }, 4000);
</script>