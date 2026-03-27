<style>
    :root {
        --primary: #3182ce;
        --success: #38a169;
        --danger: #e53e3e;
        --text-main: #2d3748;
        --text-muted: #718096;
        --bg-body: #f7fafc;
    }

    body { background-color: var(--bg-body); color: var(--text-main); }

    .header-sync { 
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

    .filter-form-sync { display: flex; gap: 10px; margin-top: 15px; align-items: center; }
    
/* Container giữ vị trí tương đối */
.search-wrapper-sync {
    position: relative;
    flex: 2; /* Để thanh search dài hơn */
}

.search-wrapper-sync i.fa-magnifying-glass {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    z-index: 1;
}

.form-control-sync { 
    width: 100%; 
    padding: 10px 100px 10px 35px ; /* Trừa chỗ cho nút tìm kiếm bên phải */
    box-sizing: border-box;
}

/* Style cho nút bấm bên trong */
.btn-search-inside {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--primary);
    color: white;
    border: none;
    padding: 6px 15px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    height: 30px;
    display: flex;
    align-items: center;
}

.btn-search-inside:hover {
    background: #2b6cb0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Nếu bạn muốn dùng icon thay vì chữ "Tìm kiếm" */
.btn-search-inside i {
    font-size: 14px;
}

    .form-control-sync { 
        width: 100%; 
        padding: 10px 10px 10px 35px; /* Padding left lớn để trừ hao cho icon */
        border: 1px solid #e2e8f0; 
        border-radius: 8px; 
        outline: none; 
        font-size: 14px;
        height: 40px;
    }
    .form-control-sync:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }

    .btn-refresh-sync {
        background: #edf2f7; color: #4a5568; text-decoration: none; padding: 0 20px; 
        border-radius: 8px; font-weight: 600; display: flex; align-items: center; 
        gap: 8px; white-space: nowrap; transition: 0.2s; height: 40px;
        border: 1px solid #e2e8f0;
    }
    .btn-refresh-sync:hover { background: #e2e8f0; color: #1a202c; }

    /* --- TABLE & BADGES --- */
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden; }
    .custom-table { width: 100%; border-collapse: collapse; }
    .custom-table th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
    .custom-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }

    .badge-custom { padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
    .status-active { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }
    .status-locked { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }
    
    .role-admin { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }
    .role-user { background: #f7fafc; color: #4a5568; border: 1px solid #e2e8f0; }

    .btn-action { text-decoration: none; font-weight: 600; font-size: 13px; margin: 0 5px; }
    .btn-edit { color: var(--primary); }
    .btn-lock { color: var(--danger); }

    /* Stats right */
    .stats-right { text-align: right; min-width: 120px; }
    .stats-label { color: #718096; font-size: 11px; font-weight: 800; text-transform: uppercase; }
    .stats-value { color: var(--primary); font-size: 24px; font-weight: 800; line-height: 1; }



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
    border-radius: 8px;
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

.btn-add {
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

.btn-add:hover {
    background: #2b6cb0;
    transform: translateY(-1px);
}


/* Màu sắc đặc trưng cho User Form */
.form-container.user-form {
    border-left: 5px solid #e3000b; /* Màu đỏ LEGO */
}

.password-note {
    font-size: 12px;
    color: #a0aec0;
    margin-top: 5px;
}

.role-badge-select {
    display: flex;
    gap: 20px;
    margin-top: 10px;
}

.role-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}


.form-container.user-form { 
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        border-left: 6px solid var(--danger-lego);
        transition: all 0.3s ease;
    }

    /* 2. PHÂN CHIA VÙNG NHẬP LIỆU (FORM GROUP) */
    .form-group { 
        margin-bottom: 20px; 
    }
    
    .form-group label { 
        display: block; 
        margin-bottom: 8px; 
        font-weight: 700; 
        color: var(--text-label);
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input { 
        width: 100%; 
        padding: 12px 15px; 
        border: 1px solid var(--border-color); 
        border-radius: 8px; 
        outline: none;
        background-color: var(--bg-field);
        font-size: 15px;
        color: var(--text-dark);
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .form-input:focus { 
        background-color: #fff;
        border-color: var(--primary); 
        box-shadow: 0 0 0 4px rgba(49, 130, 206, 0.1); 
    }

    /* 3. VÙNG PHÂN QUYỀN & LỰA CHỌN (ROLE & STATUS) */
    .role-badge-select {
        display: flex;
        gap: 15px;
        margin-top: 10px;
        background: #f1f5f9;
        padding: 10px;
        border-radius: 10px;
    }

    .role-option {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 6px;
        transition: 0.2s;
    }

    .role-option:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .role-option input[type="radio"] {
        accent-color: var(--primary);
        width: 18px;
        height: 18px;
    }

    .password-note {
        font-size: 12px;
        color: #718096;
        margin-top: 8px;
        background: #fffaf0;
        padding: 5px 10px;
        border-left: 3px solid #f6ad55;

    }

    /* 4. VÙNG NÚT BẤM (ACTION AREA) */
    .btn-submit { 
        background: var(--primary); 
        color: white !important; 
        padding: 14px 30px; 
        border: none; 
        border-radius: 10px; 
        cursor: pointer; 
        font-weight: 800; 
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3);
        transition: all 0.3s ease;
    }

    .btn-submit:hover { 
        background: var(--primary-dark); 
        transform: translateY(-2px); 
        box-shadow: 0 6px 15px rgba(49, 130, 206, 0.4);
    }

    .btn-cancel-link {
        margin-left: 20px;
        text-decoration: none;
        color: #718096;
        font-weight: 600;
        font-size: 14px;
        transition: 0.2s;
    }

    .btn-cancel-link:hover {
        color: var(--text-dark);
        text-decoration: underline;
    }

    /* Hiệu ứng phân biệt 2 cột */
    @media (min-width: 768px) {
        .form-container form > div:first-child {
            border-bottom: none;
        }
    }

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

/* Style chung cho nút thao tác để cân bằng kích thước */
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 6px;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

/* Nút Sửa (giữ phong cách cũ của bạn nhưng làm gọn hơn) */
.btn-edit {
    background: #ebf8ff;
    color: #3182ce ;
}
.btn-edit:hover {
    background: #bee3f8;
    transform: translateY(-1px);
}

/* NÚT KHÓA/MỞ KHÓA RIÊNG BIỆT */
.btn-status-toggle {
    min-width: 75px; /* Cố định độ rộng để không nhảy chữ */
    justify-content: center;
    font-weight: 700;
}

/* Trạng thái khi chuẩn bị KHÓA (Nút hiện màu đỏ nhạt) */
.btn-status-toggle.is-locking {
    background: #fff5f5;
    color: #e53e3e ;
}
.btn-status-toggle.is-locking:hover {
    background: #e53e3e;
    color: #fff;
}

/* Trạng thái khi chuẩn bị MỞ (Nút hiện màu xanh nhạt) */
.btn-status-toggle.is-unlocking {
    background: #f0fff4;
    color: #38a169 ;
    border: 1px solid #9ae6b4;
}
.btn-status-toggle.is-unlocking:hover {
    background: #38a169;
    color: #fff ;
}

/* Hiệu ứng icon */
.btn-status-toggle i {
    font-size: 12px;
}

.btn-search-inside {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--primary);
    color: white;
    border: none;
    padding: 6px 15px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s;
    height: 30px;
    display: flex;
    align-items: center;
}

.btn-search-inside:hover {
    background: #2b6cb0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* Nếu bạn muốn dùng icon thay vì chữ "Tìm kiếm" */
.btn-search-inside i {
    font-size: 14px;
}

</style>

<?php 
// Lấy thông báo từ session
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
                        if($session_msg == 'success') echo "Thêm thành viên mới thành công!";
                        if($session_msg == 'updated') echo "Đã cập nhật thông tin thành viên!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($session_error): ?>
            <div class="alert-box error-js">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        if($session_error == 'exists_email') echo "Email này đã được sử dụng!";
                        if($session_error == 'exists_phone') echo "Số điện thoại này đã được sử dụng!";
                        if($session_error == 'empty') echo "Vui lòng điền đầy đủ các trường bắt buộc!";
                        if($session_error == 'db') echo "Lỗi hệ thống: Không thể xử lý dữ liệu.";
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<div class="header-sync">
        <div class="header-left-group">
            <h2>Quản lý Người dùng</h2>
            
            <form action="/lego_shop_php/admincustomer/index" method="GET" class="filter-form-sync">
                <div class="search-wrapper-sync">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control-sync" 
                           placeholder="Tìm tên, email hoặc số điện thoại..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                    <button type="submit" class="btn-search-inside"> Tìm kiếm </button>
                </div>

                <select name="status" class="form-control-sync" onchange="this.form.submit()" style="flex: 1; cursor: pointer; padding-left: 12px;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="locked" <?= ($status ?? '') === 'locked' ? 'selected' : '' ?>>Đã khóa</option>
                </select>
            </form>
        </div>

        <?php if(!isset($is_form) || $is_form === false): ?>
            <a href="/lego_shop_php/admincustomer/add" class="btn-add">
                <i class="fa-solid fa-plus"></i> Thêm người dùng
            </a>
        <?php endif; ?>
</div>

<?php if (isset($is_form) && $is_form == true): ?>
        <div class="form-container user-form">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 25px;">
                <i class="fa-solid fa-user-gear" style="color: #e3000b; font-size: 24px;"></i>
                <h3 style="margin:0; color: #1a202c; font-weight: 800;">
                    <?= (isset($customer) && $customer) ? 'CHỈNH SỬA THÀNH VIÊN' : 'THÊM NGƯỜI DÙNG MỚI' ?>
                </h3>
            </div>
            
                <form action="/lego_shop_php/admincustomer/<?= (!empty($customer) && isset($customer['id'])) ? 'update/'.$customer['id'] : 'store' ?>" method="POST">    
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <div>
            <div class="form-group">
                <label>Họ và tên <span style="color:red">*</span></label>
                <input type="text" name="fullname" class="form-input" 
                    placeholder="Nhập tên khách hàng..."
                    value="<?= $customer['fullname'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại <span style="color:red">*</span></label>
                <input type="text" name="phone" class="form-input" 
                    placeholder="Ví dụ: 0961xxxxxx"
                    value="<?= $customer['phone'] ?? '' ?>" required>
            </div>

            <div class="form-group">
                <label>Địa chỉ Email <span style="color:red">*</span></label>
                <input type="email" name="email" class="form-input" 
                    placeholder="name@example.com"
                    value="<?= $customer['email'] ?? '' ?>" required>
            </div>
        </div>

        <div>
            <div class="form-group">
                <label>Mật khẩu <?= (!empty($customer)) ? '(Để trống nếu không đổi)' : '<span style="color:red">*</span>' ?></label>
                <input type="password" name="password" class="form-input" 
                    placeholder="********" 
                    <?= (!empty($customer)) ? '' : 'required' ?>>
                <?php if(!empty($customer)): ?>
                    <p class="password-note">Lưu ý: Chỉ nhập khi muốn thay đổi mật khẩu mới.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Phân quyền hệ thống</label>
                <div class="role-badge-select">
                    <label class="role-option">
                        <input type="radio" name="role" value="customer" <?= (empty($customer) || (isset($customer['role']) && $customer['role'] == 'customer')) ? 'checked' : '' ?>>
                        <span class="badge-custom role-user">Khách hàng</span>
                    </label>
                    <label class="role-option">
                        <input type="radio" name="role" value="admin" <?= (isset($customer['role']) && $customer['role'] == 'admin') ? 'checked' : '' ?>>
                        <span class="badge-custom role-admin">Quản trị viên</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label>Trạng thái tài khoản</label>
                <select name="status" class="form-input">
                    <option value="active" <?= (isset($customer['status']) && $customer['status'] == 'active') ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="locked" <?= (isset($customer['status']) && $customer['status'] == 'locked') ? 'selected' : '' ?>>Khóa tài khoản</option>
                </select>
            </div>
        </div>
    </div>

    <div style="margin-top: 15px; padding-top: 20px; border-top: 1px solid #edf2f7; display: flex; align-items: center;">
        <button type="submit" class="btn-submit">
            <i class="fa-solid fa-floppy-disk"></i> 
            <?= (!empty($customer)) ? 'CẬP NHẬT THÔNG TIN' : 'LƯU NGƯỜI DÙNG' ?>
        </button>
        <a href="/lego_shop_php/admincustomer" class="btn-cancel-link">Quay lại danh sách</a>
    </div>
</form>
        </div>
<?php endif; ?>
    

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th class="ps-4">Mã số</th>
                    <th>Thành viên</th>
                    <th>Liên hệ</th>
                    <th>Quyền hạn</th>
                    <th>Trạng thái</th>
                    <th>Ngày tham gia</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($customers)): ?>
                    <tr><td colspan="7" class="text-center py-5 text-muted">Chưa có dữ liệu người dùng.</td></tr>
                <?php else: ?>
                    <?php foreach ($customers as $user): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold text-muted" style="">
                                CUS-<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">

                                <div class="fw-bold text-dark"><?= htmlspecialchars($user['fullname'] ?: 'Người dùng mới') ?></div>
                            </div>
                        </td>
                        <td>
                            <div style=" color: #4a5568; "><i class="fa-regular fa-envelope me-1 opacity-50" style="margin-right: 10px;"></i><?= $user['email'] ?></div>
                            <div style=" color: #a0aec0; "><i class="fa-solid fa-phone me-1 opacity-50" style="margin-right: 10px;"></i><?= $user['phone'] ?></div>
                        </td>
                        <td>
                            <?php 
                            $role = $user['role'] ?? 'customer'; 
                            if ($role === 'admin'): 
                            ?>
                                <span class="badge-custom role-admin">Quản trị viên</span>
                            <?php else: ?> 
                                <span class="badge-custom role-user">Khách hàng</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge-custom status-active">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge-custom status-locked">Đã khóa</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted" style="">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/lego_shop_php/admincustomer/edit/<?= $user['id'] ?>?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $currentPage ?>" 
                                    class="btn-action btn-edit" title="Sửa">
                                        <i class="fa-solid fa-pen-to-square"></i> Sửa
                                    </a>
                                <?php 
                                    $isLocked = ($user['status'] !== 'active');
                                    $toggleLabel = $isLocked ? 'Mở' : 'Khóa';
                                    $toggleIcon = $isLocked ? 'fa-unlock' : 'fa-lock';
                                    $statusClass = $isLocked ? 'is-unlocking' : 'is-locking';
                                ?>
                                <a href="/lego_shop_php/admincustomer/toggleStatus/<?= $user['id'] ?>" 
                                class="btn-action btn-status-toggle <?= $statusClass ?>" 
                                onclick="return confirm('Xác nhận <?= mb_strtolower($toggleLabel) ?> tài khoản này?')">
                                    <i class="fa-solid <?= $toggleIcon ?>"></i>
                                    <span><?= $toggleLabel ?></span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php 
        // --- LOGIC XỬ LÝ URL PHÂN TRANG GIỐNG CATEGORY ---
        $baseUrl = "/lego_shop_php/admincustomer/";
        
        if (isset($currentAction) && $currentAction === 'add') {
            $baseUrl .= "add";
        } elseif (isset($currentAction) && $currentAction === 'edit') {
            $baseUrl .= "edit/" . ($editId ?? '');
        } else {
            $baseUrl .= "index";
        }

        // Giữ lại bộ lọc tìm kiếm và trạng thái
        $queryString = "?search=" . urlencode($search ?? '') . "&status=" . ($status ?? '');
    ?>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <a href="<?= $baseUrl . $queryString ?>&page=<?= $currentPage - 1 ?>" 
            class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <i class="fa-solid fa-chevron-left"></i>
            </a>

            <?php 
            // Hiển thị tối đa 5 trang để tránh bị tràn giao diện nếu có quá nhiều user
            $start = max(1, $currentPage - 2);
            $end = min($totalPages, $currentPage + 2);
            
            for ($i = $start; $i <= $end; $i++): ?>
                <a href="<?= $baseUrl . $queryString ?>&page=<?= $i ?>" 
                class="page-link <?= ($currentPage == $i) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <a href="<?= $baseUrl . $queryString ?>&page=<?= $currentPage + 1 ?>" 
            class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
    // Tự động ẩn thông báo sau 4 giây
    setTimeout(() => {
        const alert = document.querySelector('.alert-box');
        if (alert) {
            alert.style.transition = "0.5s";
            alert.style.opacity = "0";
            alert.style.transform = "translateX(100px)";
            setTimeout(() => alert.remove(), 500);
        }
    }, 4000);
</script>