<style>
.review-management-container {
    padding: 24px;
    background-color: #f7fafc;
    min-height: 100vh;
}
/* CSS Phân trang */
.pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; padding-bottom: 20px;}
.page-link { padding: 8px 14px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #475569; background: #fff; font-weight: 600; transition: 0.2s; }
.page-link:hover { background: #f8fafc; color: #3182ce; border-color: #3182ce; }
.page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; box-shadow: 0 4px 10px rgba(49, 130, 206, 0.2);}
.header {
    background: #ffffff;
    padding: 24px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.02);
}

.header-title h2 {
    margin: 0;
    font-size: 24px;
    color: #1a202c;
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-title p {
    margin: 5px 0 0 0;
    color: #718096;
    font-size: 14px;
}

/* ===== SEARCH FIX CHUẨN ===== */
.search-group {
    flex: 1;
    position: relative;
}

.search-group input {
    width: 100%;
    padding: 12px 110px 12px 45px; 
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
    position: relative;
    z-index: 1;
}

.search-group input:focus {
    border-color: #3182ce;
    outline: none;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.search-group .btn-search-inside {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 2;
    background: #3182ce;
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    height: 32px;
    display: flex;
    align-items: center;
}

.search-group i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

.filter-input {
    padding: 10px 15px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    width: 200px;
}

/* table */
.table-container {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
}

.lego-table {
    width: 100%;
    border-collapse: collapse;
}

.lego-table th {
    background-color: #f8fafc;
    padding: 16px;
    text-align: left;
    color: #64748b;
    font-size: 13px;
    text-transform: uppercase;
}

.lego-table td {
    padding: 20px 16px;
    border-bottom: 1px solid #edf2f7;
    vertical-align: middle;
}

.star-rating {
    color: #ecc94b;
    margin-bottom: 5px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    background: #ebf8ff;
    color: #3182ce;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* ====================================================
   STYLE BADGE TRẠNG THÁI THEO CHUẨN MỚI
   ==================================================== */
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    min-width: 100px; /* Ép kích thước bằng nhau */
    letter-spacing: 0.5px;
}

/* Hiển thị (Outline Green) */
.status-approved {
    background-color: #ffffff;
    color: #2f855a;
    border: 1px solid #9ae6b4;
}

/* Đang Ẩn (Solid Red + Shadow) */
.status-hidden {
    background-color: #e53e3e;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(229, 62, 62, 0.3);
    border: 1px solid transparent;
}

.action-btn {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    margin: 0 2px;
    transition: 0.2s;
}

.btn-toggle {
    background-color: #ebf8ff;
    color: #3182ce;
}

.btn-toggle:hover {
    background-color: #3182ce;
    color: white;
}

.btn-delete {
    background-color: #fff5f5;
    color: #e53e3e;
}

.btn-delete:hover {
    background-color: #e53e3e;
    color: #fff;
    transform: translateY(-1px);
}

#status-alert-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    width: 320px;
}

.alert-box {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    border-radius: 10px;
    margin-bottom: 10px;
    color: #fff;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    animation: slideIn 0.5s ease;
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

.success-js { background: #38a169; }
.error-js { background: #e53e3e; }

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
                        case 'deleted':  echo "Đã xóa đánh giá thành công!"; break;
                        case 'updated':  echo "Đã cập nhật trạng thái hiển thị!"; break;
                        default:         echo "Thao tác thành công!";
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
                        case 'notfound': echo "Không tìm thấy dữ liệu!"; break;
                        case 'db':       echo "Lỗi kết nối cơ sở dữ liệu!"; break;
                        default:         echo "Có lỗi xảy ra, vui lòng thử lại!";
                    }
                ?>
            </span>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="review-management-container">
    <div class="header">
        <div class="header-title">
            <form action="/lego_shop_php/adminreview" method="GET" 
                  style="display: flex; gap: 12px; margin-top: 20px; width: 100%; align-items: center;">

                <div class="search-group">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" 
                           value="<?= htmlspecialchars($keyword ?? '') ?>" 
                           placeholder="Tìm theo tên khách hàng hoặc sản phẩm...">
                    
                    <button type="submit" class="btn-search-inside">Tìm kiếm</button>
                </div>

                <select name="rating" class="filter-input" onchange="this.form.submit()">
                    <option value="">Tất cả mức sao</option>
                    <?php for($i=5; $i>=1; $i--): ?>
                        <option value="<?= $i ?>" <?= (isset($rating) && $rating == $i) ? 'selected' : '' ?>>
                            <?= $i ?> Sao
                        </option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
    </div>

    <div class="table-container">
        <table class="lego-table">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Sản phẩm</th>
                    <th>Nội dung đánh giá</th>
                    <th style="text-align: center;">Trạng thái</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $r): ?>
                    <tr>
                        <td>
                            <div style="display: flex; gap: 12px; align-items: center;">
                                <div class="user-avatar">
                                    <?= mb_substr($r['fullname'], 0, 1) ?: 'U' ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($r['fullname']) ?></div>
                                    <small style="color: #a0aec0;"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div style="font-weight: 600; color: #3182ce;"><?= htmlspecialchars($r['product_name']) ?></div>
                            <small style="color: #a0aec0;">ID: #<?= $r['product_id'] ?></small>
                        </td>

                        <td>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= $r['rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <div style="color: #4a5568; line-height: 1.5; font-size: 14px;">
                                <?= nl2br(htmlspecialchars($r['comment'])) ?>
                            </div>
                        </td>

                        <td style="text-align:center;">
                            <span class="status-badge <?= $r['status']=='approved'?'status-approved':'status-hidden' ?>">
                                <?= $r['status']=='approved'?'Hiển thị':'Đang Ẩn' ?>
                            </span>
                        </td>

                        <td style="text-align:center;">
                            <div style="display: flex; justify-content: center; gap: 4px;">
                                <a href="/lego_shop_php/adminreview/toggleStatus?id=<?= $r['id'] ?>&status=<?= $r['status'] ?>" 
                                   class="action-btn btn-toggle" title="<?= $r['status']=='approved'?'Ẩn đánh giá':'Hiện đánh giá' ?>">
                                    <i class="fa-solid <?= $r['status']=='approved'?'fa-eye-slash':'fa-eye' ?>"></i>
                                </a>
                                <a href="/lego_shop_php/adminreview/delete/<?= $r['id'] ?>" 
                                    class="action-btn btn-delete"
                                    onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không? Thao tác này không thể hoàn tác!')"
                                    title="Xóa vĩnh viễn">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 50px; color: #a0aec0;">
                            <i class="fa-regular fa-face-frown" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                            Không tìm thấy đánh giá nào.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div> <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <?php 
                // Tạo URL cơ sở để giữ nguyên các tham số lọc khi bấm chuyển trang
                $base_url = "?keyword=" . urlencode($keyword) . "&rating=" . urlencode($rating);
                if (!empty($product_id)) {
                    $base_url .= "&product_id=" . urlencode($product_id);
                }
            ?>
            
            <?php if ($currentPage > 1): ?>
                <a href="<?= $base_url ?>&page=<?= $currentPage - 1 ?>" class="page-link"><i class="fa-solid fa-chevron-left"></i></a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= $base_url ?>&page=<?= $i ?>" class="page-link <?= ($i == $currentPage) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= $base_url ?>&page=<?= $currentPage + 1 ?>" class="page-link"><i class="fa-solid fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<script>
setTimeout(function() {
    let alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(el => {
        el.style.transition = "0.5s";
        el.style.opacity = "0";
        el.style.transform = "translateX(100px)";
        setTimeout(() => el.remove(), 500);
    });
}, 4000);
</script>