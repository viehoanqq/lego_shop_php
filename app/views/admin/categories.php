<style>
/* --- HEADER ĐỒNG BỘ VỚI PRODUCT VIEW --- */
    .header { 
        display: flex; 
        justify-content: space-between; 
        align-items: flex-end; 
        margin-bottom: 25px; 
        gap: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }

    .header-left-group { flex-grow: 1; }
    .header-left-group h2 { margin: 0; color: #1a202c; font-size: 24px; font-weight: 700; }

    .filter-form { display: flex; gap: 10px; margin-top: 15px; align-items: center; }
    
    .search-wrapper { position: relative; flex: 2; }
    .search-wrapper i { position: absolute; left: 12px; top: 12px; color: #a0aec0; }

    .form-control { 
        width: 100%; padding: 10px; border: 1px solid #e2e8f0; 
        border-radius: 8px; outline: none; font-size: 14px;
    }
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }

    .btn-add-sync {
        background: #3182ce; color: white; text-decoration: none; padding: 12px 25px; 
        border-radius: 8px; font-weight: 600; display: flex; align-items: center; 
        gap: 8px; white-space: nowrap; transition: 0.2s;
    }
    .btn-add-sync:hover { background: #2b6cb0; }
    .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; padding: 10px; }
    .card-cat { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #edf2f7; transition: 0.3s; position: relative; }
    .card-cat:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    .cat-badge { position: absolute; top: 15px; right: 15px; background: #ffcf00; color: #000; padding: 4px 12px; border-radius: 20px; font-weight: 800; font-size: 11px; z-index: 5; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .cat-img-wrapper { height: 180px; background: #f0f0f0; }
    .cat-img { width: 100%; height: 100%; object-fit: cover; }
    .cat-info { padding: 20px; }
    .cat-name { font-size: 18px; font-weight: 800; color: #1a202c; margin-bottom: 8px; text-transform: uppercase; }
    .cat-desc { color: #718096; font-size: 14px; line-height: 1.4; height: 40px; overflow: hidden; }
    .cat-meta { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee; }
    .btn-edit { color: #3182ce; text-decoration: none; font-weight: 600; }
    .btn-delete { color: #e53e3e; text-decoration: none; font-weight: 600; }

.form-container {
        background: #fff;
        padding: 30px;
        border-radius: 15px;
        margin-bottom: 35px;
        border-left: 5px solid #ffcf00; /* Nhấn nhá màu vàng LEGO */
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        animation: fadeInDown 0.5s ease;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .form-group { margin-bottom: 20px; }
    
    .form-group label {
        display: block;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
        font-size: 14px;
        text-transform: uppercase;
    }

    .form-input {
        width: 100%;
        padding: 12px 15px;
        border: 2px solid #edf2f7;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s;
    }

    .form-input:focus {
        border-color: #ffcf00;
        outline: none;
        background: #fffdf5;
    }

    .btn-submit {
        background: #e3000b;
        color: #fff;
        border: none;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
    }

    .btn-submit:hover {
        background: #c2000a;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(227, 0, 11, 0.3);
    }

    .btn-cancel-link {
        color: #718096;
        text-decoration: none;
        font-weight: 600;
        margin-left: 15px;
        font-size: 14px;
    }

    .btn-cancel-link:hover { color: #2d3748; }

    .preview-img-container {
        margin-top: 10px;
        padding: 10px;
        background: #f7fafc;
        border-radius: 8px;
        display: inline-block;
    }

    /* --- CSS CHO THÔNG BÁO FLASH MESSAGE --- */
#status-alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
}

.alert-box {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 20px;
    border-radius: 12px;
    margin-bottom: 10px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    font-weight: 600;
    animation: slideInRight 0.5s ease;
    color: #fff;
}

.success-js {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border-left: 5px solid #276749;
}

.error-js {
    background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
    border-left: 5px solid #9b2c2c;
}

.alert-box i {
    font-size: 20px;
}

@keyframes slideInRight {
    from { opacity: 0; transform: translateX(100px); }
    to { opacity: 1; transform: translateX(0); }
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-top: 30px;
    margin-bottom: 20px;
}

.page-link {
    padding: 8px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    text-decoration: none;
    color: #4a5568;
    background: #fff;
    font-weight: 600;
    transition: 0.2s;
}

.page-link:hover {
    background: #edf2f7;
    border-color: #cbd5e0;
}

.page-link.active {
    background: #3182ce;
    color: #fff;
    border-color: #3182ce;
}

.page-link.disabled {
    opacity: 0.5;
    pointer-events: none;
    background: #f7fafc;
}

</style>


<?php 
// Lấy thông báo từ session
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
?>

<?php if($session_msg || $session_error): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        <?php if($session_msg): ?>
            <div class="alert-box success-js" ...>
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php
                        if($session_msg == 'success') echo "Thêm danh mục mới thành công!";
                        if($session_msg == 'updated') echo "Đã cập nhật thông tin danh mục!";
                        if($session_msg == 'hidden') echo "Đã chuyển danh mục sang trạng thái Khóa!";
                        if($session_msg == 'unlocked') echo "Đã mở khóa danh mục thành công!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($session_error): ?>
            <div class="alert-box error-js" ...>
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        if($session_error == 'cat_is_locked') echo "Không được mở sản phẩm khi danh mục đang khóa.";
                        if($session_error == 'db') echo "Lỗi hệ thống: Không thể xử lý dữ liệu.";
                        if($session_error == 'empty') echo "Vui lòng không để trống các trường bắt buộc!";
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="header">
    <div class="header-left-group">
        <h2>📁 Quản lý Danh mục</h2>
        
        <form action="/lego_shop_php/admincategory" method="GET" class="filter-form">
            <div class="search-wrapper">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="keyword" class="form-control" 
                       placeholder="Tìm tên danh mục..." 
                       value="<?= $filters['keyword'] ?? '' ?>"
                       style="padding-left: 35px;">
            </div>

            <select name="status" class="form-control" onchange="this.form.submit()" style="flex: 1; cursor: pointer;">
                <option value="all" <?= ($filters['status'] == 'all') ? 'selected' : '' ?>>-- Tất cả trạng thái --</option>
                <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Đang hoạt động</option>
                <option value="locked" <?= ($filters['status'] == 'locked') ? 'selected' : '' ?>>Đã khóa</option>
            </select>
        </form>
    </div>

    <?php if(!isset($is_form) || $is_form === false): ?>
        <a href="/lego_shop_php/admincategory/add" class="btn-add-sync">
            <i class="fa-solid fa-plus"></i> Thêm danh mục
        </a>
    <?php endif; ?>
</div>

<?php if (isset($is_form) && $is_form == true): ?>
    <div class="form-container">
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
            <i class="fa-solid fa-folder-plus" style="color: #ffcf00; font-size: 24px;"></i>
            <h3 style="margin:0; color: #1a202c; font-weight: 800;">
                <?= (isset($category) && $category) ? 'CHỈNH SỬA DANH MỤC' : 'THÊM DANH MỤC MỚI' ?>
            </h3>
        </div>
        
        <form action="/lego_shop_php/admincategory/<?= (isset($category) && $category) ? 'update/'.$category['id'] : 'store' ?>" 
              method="POST" 
              enctype="multipart/form-data">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div class="form-group">
                        <label>Tên danh mục <span style="color:red">*</span></label>
                        <input type="text" name="name" class="form-input" 
                               placeholder="Ví dụ: LEGO Technic..."
                               value="<?= $category['name'] ?? '' ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Hình ảnh đại diện</label>
                        <input type="file" name="image_url" class="form-input" style="padding: 8px;">
                        
                        <?php if(isset($category['image_url']) && !empty($category['image_url'])): ?>
                            <div class="preview-img-container">
                                <small style="display:block; color:#718096; margin-bottom:5px;">Ảnh cũ:</small>
                                <img src="/lego_shop_php/public/assets/images/<?= $category['image_url'] ?>" 
                                     style="height: 50px; border-radius: 4px; border: 1px solid #ddd;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mô tả danh mục</label>
                    <textarea name="description" class="form-input" rows="5" 
                              placeholder="Mô tả ngắn gọn về dòng sản phẩm này..."
                              style="resize: none;"><?= $category['description'] ?? '' ?></textarea>
                </div>
            </div>

            <div style="margin-top: 10px; padding-top: 20px; border-top: 1px solid #edf2f7;">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-check"></i> XÁC NHẬN LƯU
                </button>
                <a href="/lego_shop_php/admincategory" class="btn-cancel-link">Hủy thao tác</a>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="category-grid">
    <?php foreach ($categories as $cat): ?>
        <div class="card-cat" style="<?= $cat['status'] == 'locked' ? 'opacity: 0.7; background: #f8f9fa;' : '' ?>">
            
            <?php if($cat['status'] == 'locked'): ?>
                <span class="cat-badge" style="background: #718096; color: #fff;">LOCKED</span>
            <?php else: ?>
                <span class="cat-badge"><?= $cat['product_count'] ?> PRODUCTS</span>
            <?php endif; ?>

            <div class="cat-img-wrapper">
                <img src="/lego_shop_php/public/assets/images/<?= !empty($cat['image_url']) ? $cat['image_url'] : 'default.jpg' ?>" 
                     class="cat-img" style="<?= $cat['status'] == 'locked' ? 'filter: grayscale(1);' : '' ?>">
            </div>

            <div class="cat-info">
                <div class="cat-name">
                    <?= htmlspecialchars($cat['name']) ?> 
                    <?= $cat['status'] == 'locked' ? '<small>(Bị khóa)</small>' : '' ?>
                </div>
                <div class="cat-desc"><?= htmlspecialchars($cat['description']) ?></div>
                
                <div class="cat-meta">
                    <span style="font-size: 12px; color: #cbd5e0;">ID: #<?= $cat['id'] ?></span>
                    <div style="display: flex; gap: 10px;">
                        <a href="/lego_shop_php/admincategory/edit/<?= $cat['id'] ?>" class="btn-edit">Sửa</a>
                        
                        <?php if($cat['status'] == 'locked'): ?>
                            <a href="/lego_shop_php/admincategory/unlock/<?= $cat['id'] ?>" 
                               class="btn-edit" style="color: #38a169;">Mở khóa</a>
                        <?php else: ?>
                            <a href="/lego_shop_php/admincategory/delete/<?= $cat['id'] ?>" 
                               class="btn-delete" 
                               onclick="return confirm('Ẩn danh mục này?')">Khóa</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">
        <a href="?keyword=<?= urlencode($filters['keyword']) ?>&status=<?= $filters['status'] ?>&page=<?= $currentPage - 1 ?>" 
           class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?keyword=<?= urlencode($filters['keyword']) ?>&status=<?= $filters['status'] ?>&page=<?= $i ?>" 
               class="page-link <?= ($currentPage == $i) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="?keyword=<?= urlencode($filters['keyword']) ?>&status=<?= $filters['status'] ?>&page=<?= $currentPage + 1 ?>" 
           class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-right"></i>
        </a>
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