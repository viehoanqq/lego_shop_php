<style>
.review-management-container {
    padding: 24px;
    background-color: #f7fafc;
    min-height: 100vh;
}

.header {
    background: #ffffff;
    padding: 24px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 25px;
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
    padding: 12px 110px 12px 45px; /* chừa chỗ cho button */
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

.search-group .btn-search-inside:hover {
    background: #2b6cb0;
}

/* icon */
.search-group i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

/* select */
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
}

.lego-table {
    width: 100%;
    border-collapse: collapse;
}

.lego-table th {
    background-color: #f8fafc;
    padding: 16px;
    text-align: left;
}

.lego-table td {
    padding: 20px 8px;
    padding-left: 4px;
    border-bottom: 1px solid #edf2f7;
}

.lego-table td div {
    margin-left: 0;
}

.star-rating {
    color: #ecc94b;
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

.status-badge {
    padding: 4px 12px;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
}

.status-approved {
    background-color: #f0fff4;
    color: #2f855a;
}

.status-hidden {
    background-color: #fff5f5;
    color: #c53030;
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
}

.btn-toggle {
    background-color: #ebf8ff;
    color: #3182ce;
}

.btn-toggle:hover {
    background-color: #3182ce;
    color: white;
}

/* ===== DELETE BUTTON ===== */
.btn-delete {
    background-color: #fff5f5;
    color: #e53e3e;
    transition: all 0.2s ease;
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
                        case 'deleted':  echo "Đã xóa thành công!"; break;
                        case 'updated':  echo "Cập nhật thành công!"; break;
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
                        case 'db':       echo "Lỗi hệ thống!"; break;
                        default:         echo "Có lỗi xảy ra!";
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
            <h2><i class="fa-solid fa-comments" style="color: #3182ce;"></i> Quản lý Đánh giá</h2>
            <p>Hiện có <?= count($reviews) ?> phản hồi từ khách hàng trong hệ thống.</p>

            <form action="/lego_shop_php/adminreview" method="GET" 
                  style="display: flex; gap: 12px; margin-top: 20px; width: 100%; align-items: center;">

                <!-- SEARCH -->
                <div class="search-group">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="keyword" 
                           value="<?= htmlspecialchars($keyword ?? '') ?>" 
                           placeholder="Tìm theo tên khách hàng hoặc sản phẩm...">
                    
                    <button type="submit" class="btn-search-inside">
                        Tìm kiếm
                    </button>
                </div>

                <!-- FILTER -->
                <select name="rating" class="filter-input" onchange="this.form.submit()">
                    <option value="">Tất cả sao</option>
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
                            <div style="display: flex; gap: 12px;">
                                <div class="user-avatar">
                                    <?= mb_substr($r['fullname'], 0, 1) ?: 'U' ?>
                                </div>
                                <div>
                                    <div><?= htmlspecialchars($r['fullname']) ?></div>
                                    <small><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <b><?= htmlspecialchars($r['product_name']) ?></b><br>
                            <small>ID: #<?= $r['product_id'] ?></small>
                        </td>

                        <td>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= $r['rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <?= nl2br(htmlspecialchars($r['comment'])) ?>
                        </td>

                        <td style="text-align:center;">
                            <span class="status-badge <?= $r['status']=='approved'?'status-approved':'status-hidden' ?>">
                                <?= $r['status']=='approved'?'Hiển thị':'Ẩn' ?>
                            </span>
                        </td>

                        <td style="text-align:center;">
                            <a href="/lego_shop_php/adminreview/toggleStatus?id=<?= $r['id'] ?>&status=<?= $r['status'] ?>" 
                               class="action-btn btn-toggle">
                                <i class="fa-solid <?= $r['status']=='approved'?'fa-eye-slash':'fa-eye' ?>"></i>
                            </a>
                            <a href="/lego_shop_php/adminreview/delete/<?= $r['id'] ?>" 
                                class="action-btn btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?')"
                                title="Xóa">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Không có dữ liệu</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

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