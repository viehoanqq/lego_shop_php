<style>
    .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
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

</style>


<?php if(isset($_GET['msg']) || isset($_GET['error'])): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert-box success-js" style="padding: 15px; border-radius: 8px; background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php
                        if($_GET['msg'] == 'success') echo "✨ Thêm danh mục mới thành công!";
                        if($_GET['msg'] == 'updated') echo "✅ Đã cập nhật thông tin danh mục!";
                        if($_GET['msg'] == 'hidden') echo "🔒 Đã chuyển danh mục sang trạng thái Khóa!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-box error-js" style="padding: 15px; border-radius: 8px; background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        if($_GET['error'] == 'db') echo "❌ Lỗi hệ thống: Không thể xử lý dữ liệu.";
                        if($_GET['error'] == 'empty') echo "⚠️ Vui lòng không để trống các trường bắt buộc!";
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="admin-header">
    <div>
        <h2 style="margin:0">📂 Quản lý Danh Mục LEGO</h2>
        <small style="color: #a0aec0;">Tổng cộng <?= count($categories) ?> danh mục đang hoạt động</small>
    </div>
    <a href="/lego_shop_php/admincategory/add" style="background: #e3000b; color: #fff; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: 14px;">+ THÊM DANH MỤC MỚI</a>
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
    <?php if(!empty($categories)): ?>
        <?php foreach ($categories as $cat): ?>
        <div class="card-cat">
            <span class="cat-badge"><?= $cat['product_count'] ?> PRODUCTS</span>
            
            <div class="cat-img-wrapper">
                <img src="/lego_shop_php/public/assets/images/<?= !empty($cat['image_url']) ? $cat['image_url'] : 'default.jpg' ?>" 
                     class="cat-img" 
                     onerror="this.src='https://placehold.co/400x200?text=LEGO+<?= urlencode($cat['name']) ?>'">
            </div>

            <div class="cat-info">
                <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
                <div class="cat-desc"><?= htmlspecialchars($cat['description']) ?></div>
                
                <div class="cat-meta">
                    <span style="font-size: 12px; color: #cbd5e0;">ID: #<?= $cat['id'] ?></span>
                    <div style="display: flex; gap: 15px;">
                        <a href="/lego_shop_php/admincategory/edit/<?= $cat['id'] ?>" class="btn-edit">✎ Sửa</a>
                        <a href="/lego_shop_php/admincategory/delete/<?= $cat['id'] ?>" 
                           class="btn-delete" 
                           onclick="return confirm('Bạn có chắc chắn muốn ẩn danh mục <?= htmlspecialchars($cat['name']) ?>?')">🗑 Ẩn</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #fff; border-radius: 10px;">
            <h3>Chưa có dữ liệu danh mục.</h3>
        </div>
    <?php endif; ?>
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