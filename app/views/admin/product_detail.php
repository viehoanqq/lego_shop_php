<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .detail-container {
        background: #f8fafc;
        padding: 30px;
        border-radius: 15px;
        min-height: 100vh;
    }

    .back-link {
        text-decoration: none;
        color: #64748b;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        transition: 0.3s;
    }

    .back-link:hover { color: #3182ce; }

    .card-detail {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        display: grid;
        grid-template-columns: 350px 1fr;
    }

    /* Cột bên trái: Ảnh và trạng thái */
    .detail-sidebar {
        background: #f1f5f9;
        padding: 30px;
        border-right: 1px solid #e2e8f0;
        text-align: center;
    }

    .img-preview {
        width: 100%;
        aspect-ratio: 1/1;
        object-fit: cover;
        border-radius: 12px;
        border: 4px solid #fff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    /* Cột bên phải: Form nội dung */
    .detail-content { padding: 40px; }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 25px;
        padding-bottom: 10px;
        border-bottom: 2px solid #3182ce;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .grid-form {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group { margin-bottom: 20px; }

    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.2s;
    }

    .form-control:focus {
        border-color: #3182ce;
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        outline: none;
    }

    .full-width { grid-column: span 2; }

    .btn-save {
        background: #3182ce;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: 0.2s;
    }

    .btn-save:hover { background: #2b6cb0; transform: translateY(-1px); }

    .badge-info {
        background: #ebf8ff;
        color: #2b6cb0;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
    }
</style>
<?php if(isset($_GET['msg']) || isset($_GET['error'])): ?>
    <div id="status-alert-container" style="margin-bottom: 20px;">
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert-box success-js" style="padding: 15px; border-radius: 8px; background: #f0fff4; color: #2f855a; border: 1px solid #c6f6d5; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-circle-check"></i>
                <span>
                    <?php 
                        if($_GET['msg'] == 'updated') echo "Đã cập nhật thông số kỹ thuật thành công!";
                        else echo "Thao tác thành công!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert-box error-js" style="padding: 15px; border-radius: 8px; background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php 
                        if($_GET['error'] == 'db') echo "Lỗi hệ thống: Không thể lưu dữ liệu.";
                        else echo "Có lỗi xảy ra, vui lòng thử lại.";
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<div class="detail-container">
    <a href="/lego_shop_php/adminproduct" class="back-link">
        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
    </a>

    <div class="card-detail">
        <div class="detail-sidebar">
            <img src="/lego_shop_php/public/assets/images/<?= $product['main_image'] ?? 'default.jpg' ?>" 
                 class="img-preview" alt="Lego Image">
            
            <h3 style="margin: 10px 0; color: #1e293b;"><?= htmlspecialchars($product['name']) ?></h3>
            <p style="color: #64748b; font-size: 14px;">Mã SKU: <strong><?= strtoupper($product['sku']) ?></strong></p>
            
            <div style="margin-top: 20px;">
                <span class="badge-info"><?= $product['category_name'] ?></span>
                <span class="badge-info"><?= number_format($product['selling_price'], 0, ',', '.') ?>đ</span>
            </div>
        </div>

        <div class="detail-content">
            <form action="/lego_shop_php/adminproduct/updateDetail/<?= $product['id'] ?>" method="POST">
                
                <div class="section-title">
                    <i class="fa-solid fa-gears"></i> Thông số kỹ thuật chi tiết
                </div>

                <div class="grid-form">
                    <div class="form-group">
                        <label>Nhà sản xuất</label>
                        <input type="text" name="manufacturer" class="form-control" 
                               value="<?= $product['manufacturer'] ?? 'Tập đoàn LEGO' ?>">
                    </div>

                    <div class="form-group">
                        <label>Xuất xứ</label>
                        <input type="text" name="country_of_origin" class="form-control" 
                               value="<?= $product['country_of_origin'] ?? 'Đan Mạch' ?>">
                    </div>

                    <div class="form-group">
                        <label>Chất liệu</label>
                        <input type="text" name="material" class="form-control" 
                               value="<?= $product['material'] ?? 'Nhựa ABS an toàn' ?>">
                    </div>

                    <div class="form-group">
                        <label>Kích thước hộp (cm)</label>
                        <input type="text" name="dimensions" class="form-control" 
                               placeholder="VD: 48 x 37.8 x 9.4 cm"
                               value="<?= $product['dimensions'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Độ tuổi khuyến nghị</label>
                        <input type="text" name="age_range" class="form-control" 
                               placeholder="VD: 18+, 9-12..."
                               value="<?= $product['age_range'] ?? '' ?>">
                    </div>

                    <div class="form-group">
                        <label>Số mảnh ghép (Pieces)</label>
                        <input type="number" name="pieces" class="form-control" 
                               value="<?= $product['pieces'] ?? 0 ?>">
                    </div>

                    <div class="form-group">
                        <label>Năm phát hành</label>
                        <input type="number" name="release_year" class="form-control" 
                               value="<?= $product['release_year'] ?? date('Y') ?>">
                    </div>

                    <div class="form-group full-width">
                        <label>Câu chuyện chủ đề / Mô tả ngắn</label>
                        <textarea name="theme_story" class="form-control" rows="4" 
                                  placeholder="Nhập nội dung giới thiệu về dòng sản phẩm này..."><?= $product['theme_story'] ?? '' ?></textarea>
                    </div>
                </div>

                <div style="margin-top: 30px; text-align: right;">
                    <button type="submit" class="btn-save">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu thông số kỹ thuật
                    </button>
                </div>
            </form>
        </div>
    </div>
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