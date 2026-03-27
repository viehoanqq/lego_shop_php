
<style>
    /* Tổng quan container */
    .review-management-container {
        padding: 24px;
        background-color: #f7fafc;
        min-height: 100vh;
    }

 /* Container chính của Header */
.header {
    background: #ffffff;
    padding: 24px;
    border-radius: 16px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 25px;
}

/* Dòng trên cùng: Tiêu đề và Nút Quay lại */
.header-top-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
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

/* Thanh công cụ Tìm kiếm & Lọc */
.filter-form {
    display: flex;
    gap: 15px;
    width: 100%;
    align-items: center;
}

/* Ô tìm kiếm giàn đều ở giữa */
.search-group {
    flex: 1;
    position: relative;
}

.search-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
}

.search-input {
    width: 100%;
    padding: 12px 15px 12px 45px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
    transition: all 0.2s;
}

.search-input:focus {
    border-color: #3182ce;
    outline: none;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

/* Nhóm bộ lọc bên phải */
.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-label {
    font-size: 14px;
    color: #4a5568;
    font-weight: 600;
    white-space: nowrap;
}

.rating-select {
    width: 250px;
    cursor: pointer;
    padding: 12px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background-color: #f8fafc;
    font-size: 14px;
}

/* Nút xóa lọc nhanh */
.btn-reset-filter {
    padding: 12px 15px;
    background: #edf2f7;
    border-radius: 10px;
    color: #4a5568;
    text-decoration: none;
    font-size: 14px;
    transition: background 0.2s;
}

.btn-reset-filter:hover {
    background: #e2e8f0;
}

    /* Nút quay lại kiểu LEGO */
    .btn-back {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background-color: white;
        color: #4a5568;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-back:hover {
        background-color: #f7fafc;
        border-color: #cbd5e0;
        transform: translateX(-3px);
    }

    /* Custom lại bảng lego-table cho cột review */
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
        color: #4a5568;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.05em;
        padding: 16px;
        text-align: left;
        border-bottom: 2px solid #edf2f7;
    }

    .lego-table td {
        padding: 20px 16px;
        border-bottom: 1px solid #edf2f7;
        vertical-align: top;
    }

    /* Rating stars */
    .star-rating {
        color: #ecc94b; /* Màu vàng LEGO */
        font-size: 14px;
        margin-bottom: 4px;
    }

    /* Avatar giả lập */
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
        font-size: 18px;
    }

    /* Badge trạng thái mượn từ stock-badge của bạn */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-approved {
        background-color: #f0fff4;
        color: #2f855a;
        border: 1px solid #c6f6d5;
    }

    .status-hidden {
        background-color: #fff5f5;
        color: #c53030;
        border: 1px solid #fed7d7;
    }

    /* Actions buttons */
    .action-btn {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: none;
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

    .btn-delete {
        background-color: #fff5f5;
        color: #e53e3e;
    }

    .btn-delete:hover {
        background-color: #e53e3e;
        color: white;
    }

    .product-info-name {
        font-weight: 600;
        color: #2d3748;
        display: block;
        margin-bottom: 4px;
    }

    .filter-bar {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .filter-input {
        padding: 10px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        outline: none;
        font-size: 14px;
        width: 250px;
    }

    .filter-input:focus {
        border-color: #3182ce;
        box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.1);
    }

    .btn-filter {
        background: #3182ce;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-reset {
        background: #edf2f7;
        color: #4a5568;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 14px;
    }
</style>

<div class="review-management-container">
    <div class="header">
        <div class="header-title">
            <h2><i class="fa-solid fa-comments" style="color: #3182ce;"></i> Quản lý Đánh giá</h2>
            <p>Hiện có <?= count($reviews) ?> phản hồi từ khách hàng trong hệ thống.</p>

            <form action="/lego_shop_php/adminreview" method="GET" style="display: flex; gap: 12px; margin-top: 20px; width: 100%; align-items: center;">
                <div style="flex: 1; position: relative;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: #a0aec0;"></i>
                    <input type="text" name="keyword" value="<?= htmlspecialchars($keyword ?? '') ?>" 
                        placeholder="Tìm theo tên khách hàng hoặc sản phẩm..." 
                        class="filter-input" style="width: 100%; padding-left: 40px;">
                </div>

                <select name="rating" class="filter-input" style=" cursor: pointer;" onchange="this.form.submit()">
                    <option value="">Tất cả sao</option>
                    <?php for($i=5; $i>=1; $i--): ?>
                        <option value="<?= $i ?>" <?= (isset($rating) && $rating == $i) ? 'selected' : '' ?>>
                            <?= $i ?> Sao
                        </option>
                    <?php endfor; ?>
                </select>
            </form>
        </div>
        <!-- <a href="/lego_shop_php/adminproduct" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Quay lại Kho sản phẩm
        </a> -->
    </div>

    <div class="table-container">
        <table class="lego-table" >
            <thead >
                <tr >
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
                        <td style="width: 20%;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div class="user-avatar">
                                    <?= mb_substr($r['fullname'], 0, 1) ?: 'U' ?>
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #2d3748;"><?= htmlspecialchars($r['fullname'] ?: 'Ẩn danh') ?></div>
                                    <div style="font-size: 12px; color: #a0aec0;"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></div>
                                </div>
                            </div>
                        </td>

                        <td style="width: 20%;">
                            <span class="product-info-name"><?= htmlspecialchars($r['product_name']) ?></span>
                            <small style="color: #718096;">ID: #<?= $r['product_id'] ?></small>
                        </td>

                        <td style="width: 35%;">
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?= $i <= $r['rating'] ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <div style="font-size: 14px; color: #4a5568; line-height: 1.5; margin-top: 8px;">
                                <?= nl2br(htmlspecialchars($r['comment'])) ?>
                            </div>
                        </td>

                        <td style="text-align: center;" style="width: 15%;">
                            <span class="status-badge <?= $r['status'] == 'approved' ? 'status-approved' : 'status-hidden' ?>">
                                <i class="fa-solid <?= $r['status'] == 'approved' ? 'fa-check-circle' : 'fa-eye-slash' ?>" style="margin-right: 6px;"></i>
                                <?= $r['status'] == 'approved' ? 'Đang hiển thị' : 'Đang ẩn' ?>
                            </span>
                        </td>

                        <td style="width: 10%; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="/lego_shop_php/adminreview/toggleStatus?id=<?= $r['id'] ?>&status=<?= $r['status'] ?>" 
                                   class="action-btn btn-toggle" 
                                   title="<?= $r['status'] == 'approved' ? 'Bấm để ẩn đánh giá' : 'Bấm để duyệt hiện' ?>">
                                    <i class="fa-solid <?= $r['status'] == 'approved' ? 'fa-eye-slash' : 'fa-eye' ?>"></i>
                                </a>
                                
                                <!-- <a href="/lego_shop_php/adminreview/delete/<?= $r['id'] ?>" 
                                   class="action-btn btn-delete" 
                                   onclick="return confirm('Bạn có chắc chắn muốn xóa vĩnh viễn đánh giá này?')"
                                   title="Xóa đánh giá">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a> -->
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 60px; color: #a0aec0;">
                            <i class="fa-solid fa-comment-slash" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                            Chưa có dữ liệu đánh giá nào để hiển thị.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>