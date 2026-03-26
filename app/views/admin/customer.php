<style>
    :root {
        --primary: #3182ce;
        --success: #38a169;
        --danger: #e53e3e;
        --text-main: #2d3748;
        --text-muted: #718096;
        --bg-body: #f7fafc;
    }

    body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-main); }

    /* --- HEADER ĐỒNG BỘ VỚI CATEGORY --- */
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
    
    .search-wrapper-sync { position: relative; flex: 2; }
    .search-wrapper-sync i { position: absolute; left: 12px; top: 12px; color: #a0aec0; }

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

</style>

<?php if ($msg = get_flash_message('success')): ?>
    <div id="status-alert-container">
        <div class="alert-box alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?= $msg ?></span>
        </div>
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
                </div>

                <select name="status" class="form-control-sync" onchange="this.form.submit()" style="flex: 1; cursor: pointer; padding-left: 12px;">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="locked" <?= ($status ?? '') === 'locked' ? 'selected' : '' ?>>Đã khóa</option>
                </select>
            </form>
        </div>

        <?php if(!isset($is_form) || $is_form === false): ?>
            <a href="/lego_shop_php/" class="btn-add">
                <i class="fa-solid fa-plus"></i> Thêm người dùng
            </a>
        <?php endif; ?>
    </div>
    

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
                            <span class="fw-bold text-muted" style="font-size: 12px;">
                                CUS-<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">

                                <div class="fw-bold text-dark"><?= htmlspecialchars($user['fullname'] ?: 'Người dùng mới') ?></div>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #4a5568; "><i class="fa-regular fa-envelope me-1 opacity-50" style="margin-right: 10px;"></i><?= $user['email'] ?></div>
                            <div style="font-size: 11px; color: #a0aec0; "><i class="fa-solid fa-phone me-1 opacity-50" style="margin-right: 10px;"></i><?= $user['phone'] ?></div>
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
                        <td class="text-muted" style="font-size: 13px;">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="/lego_shop_php/admincustomer/edit/<?= $user['id'] ?>" class="btn-action btn-edit" title="Sửa">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="/lego_shop_php/admincustomer/toggleStatus/<?= $user['id'] ?>" 
                                   class="btn-action btn-lock" 
                                   onclick="return confirm('Thay đổi trạng thái tài khoản này?')">
                                    <i class="fa-solid <?= $user['status'] === 'active' ? 'fa-lock' : 'fa-unlock' ?>"></i>
                                    <?= $user['status'] === 'active' ? 'Khóa' : 'Mở' ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="pagination">
        <a href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $currentPage - 1 ?>" 
           class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </a>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $i ?>" 
               class="page-link <?= ($currentPage == $i) ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <a href="?search=<?= urlencode($search) ?>&status=<?= $status ?>&page=<?= $currentPage + 1 ?>" 
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