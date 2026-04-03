<style>
    :root {
        --primary: #3182ce;
        --success: #38a169;
        --danger: #e53e3e;
        --text-main: #2d3748;
        --text-muted: #718096;
        --bg-body: #f7fafc;
    }

    * { box-sizing: border-box; }
    body { background-color: var(--bg-body); color: var(--text-main);}

    .header-sync { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; gap: 20px; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); flex-wrap: wrap;}
    .header-left-group { flex-grow: 1; }
    .header-left-group h2 { margin: 0 0 15px 0; color: #1a202c; font-size: 22px; font-weight: 700; }

    .filter-form-sync { display: flex; gap: 10px; align-items: center; flex-wrap: wrap;}
    .search-wrapper-sync { position: relative; flex: 2; min-width: 250px; }
    .search-wrapper-sync i.fa-magnifying-glass { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted); z-index: 1; }
    
    /* ĐÃ SỬA LỖI LỌC TRẠNG THÁI HIỂN THỊ CÓ 1 CHÚT */
    .form-control-sync { width: 100%; padding: 10px 15px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; font-size: 14px; height: 40px; transition: 0.2s;}
    .search-input-sync { padding-left: 35px; padding-right: 90px; } /* Lớp này chỉ dành cho input search */
    .form-control-sync:focus { border-color: #3182ce; box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1); }
    
    .btn-search-inside { position: absolute; right: 5px; top: 50%; transform: translateY(-50%); background: var(--primary); color: white; border: none; padding: 6px 15px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s; height: 30px; display: flex; align-items: center; }
    .btn-search-inside:hover { background: #2b6cb0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }

    /* CHUẨN HÓA FONT CHỮ NÚT THÊM */
    .btn-add { background: #3182ce; color: white; text-decoration: none; padding: 0 20px; border-radius: 8px; font-weight: 600; font-family: inherit; display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; transition: 0.2s; height: 40px;}
    .btn-add:hover { background: #2b6cb0; transform: translateY(-1px); color: white;}

    /* --- TABLE & BADGES --- */
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow-x: auto; margin-bottom: 25px;}
    .custom-table { width: 100%; border-collapse: collapse; min-width: 800px;}
    .custom-table th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; white-space: nowrap;}
    .custom-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; vertical-align: middle;}

    /* ĐÃ LÀM NỔI BẬT QUẢN TRỊ VIÊN VÀ ĐÃ KHÓA */
    .badge-custom { 
        padding: 6px 12px; 
        border-radius: 20px; 
        font-size: 11px; 
        font-weight: 700; 
        text-transform: uppercase; 
        display: inline-flex;       /* Chuyển sang flex để căn giữa */
        justify-content: center;    /* Căn giữa chữ bên trong nút */
        align-items: center;
        min-width: 120px;           /* Ép kích thước tất cả các nút đều bằng 120px */
        letter-spacing: 0.5px;
    }
    .status-active { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }
    
    .status-locked { background: #e53e3e; color: #fff; border: none; box-shadow: 0 2px 5px rgba(229, 62, 62, 0.3); } /* Đỏ đậm */
    .role-admin { background: #2b6cb0; color: #fff; border: none; box-shadow: 0 2px 5px rgba(43, 108, 176, 0.3); } /* Xanh biển đậm */
    
    .status-deleted { background: #e2e8f0; color: #4a5568; border: 1px solid #cbd5e0; }
    .role-user { background: #f7fafc; color: #4a5568; border: 1px solid #e2e8f0; }

    .btn-action { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 6px; transition: all 0.2s ease; border: 1px solid transparent; text-decoration: none; font-weight: 600; font-size: 13px; margin: 0 2px;}
    .btn-edit { background: #ebf8ff; color: #3182ce; }
    .btn-edit:hover { background: #bee3f8; transform: translateY(-1px); color: #2b6cb0;}
    
    .btn-status-toggle.is-locking { background: #fff5f5; color: #e53e3e; }
    .btn-status-toggle.is-locking:hover { background: #e53e3e; color: #fff; }
    .btn-status-toggle.is-unlocking { background: #f0fff4; color: #38a169; border: 1px solid #9ae6b4; }
    .btn-status-toggle.is-unlocking:hover { background: #38a169; color: #fff; }
    
    .btn-delete { background: #fff5f5; color: #c53030; }
    .btn-delete:hover { background: #e53e3e; color: #fff; }

    /* ===== ALERT ===== */
    #status-alert-container { position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; }
    .alert-box { display: flex; align-items: center; gap: 15px; padding: 15px 20px; border-radius: 12px; margin-bottom: 10px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); font-weight: 600; color: #fff; animation: slideInRight 0.5s ease; }
    .success-js { background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); border-left: 5px solid #276749; }
    .error-js { background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%); border-left: 5px solid #9b2c2c; }
    @keyframes slideInRight { from { opacity: 0; transform: translateX(100px); } to { opacity: 1; transform: translateX(0); } }

    /* ===== PAGINATION ===== */
    .pagination { display: flex; justify-content: center; gap: 8px; margin: 30px 0; }
    .page-link { padding: 8px 16px; border: 1px solid #e2e8f0; border-radius: 8px; text-decoration: none; color: #4a5568; background: #fff; font-weight: 600; transition: 0.2s; }
    .page-link:hover { background: #edf2f7; border-color: #cbd5e0; color: #1a202c;}
    .page-link.active { background: #3182ce; color: #fff; border-color: #3182ce; }
    .page-link.disabled { opacity: 0.5; pointer-events: none; background: #f7fafc; }

    /* ===== FORM NÂNG CẤP (ĐÃ XÓA VIỀN ĐỎ BÊN TRÁI) ===== */
    .form-container.user-form { background: #fff; padding: 35px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); margin-bottom: 30px; border-left: none; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 8px; font-weight: 700; color: #4a5568; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }
    .form-input { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 8px; outline: none; background-color: #f8fafc; font-size: 15px; color: #1a202c; transition: all 0.2s ease; box-sizing: border-box; font-family: inherit;}
    .form-input:focus { background-color: #fff; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(49, 130, 206, 0.1); }
    
    .role-badge-select { display: flex; gap: 15px; margin-top: 10px; background: #f1f5f9; padding: 12px; border-radius: 10px; }
    .role-option { display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 5px 10px; border-radius: 6px; transition: 0.2s; }
    .role-option:hover { background: rgba(255, 255, 255, 0.5); }
    .role-option input[type="radio"] { accent-color: var(--primary); width: 18px; height: 18px; cursor: pointer;}
    .password-note { font-size: 12px; color: #718096; margin-top: 8px; background: #fffaf0; padding: 8px 12px; border-radius: 6px; border-left: 3px solid #ed8936;}

    .btn-submit { background: var(--primary); color: white; padding: 14px 30px; border: none; border-radius: 10px; cursor: pointer; font-weight: 800; font-size: 14px; display: inline-flex; align-items: center; gap: 10px; box-shadow: 0 4px 12px rgba(49, 130, 206, 0.3); transition: all 0.3s ease; font-family: inherit;}
    .btn-submit:hover { background: #2b6cb0; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(49, 130, 206, 0.4); }
    .btn-cancel-link { margin-left: 20px; text-decoration: none; color: #718096; font-weight: 600; font-size: 14px; transition: 0.2s; }
    .btn-cancel-link:hover { color: #1a202c; text-decoration: underline; }
    
    .error-text { color: #e53e3e; font-size: 12px; margin-top: 6px; display: block; min-height: 18px;}
    .input-error { border-color: #e53e3e !important; background: #fff5f5 !important;}
    .input-success { border-color: #38a169 !important; }
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
                        if($session_msg == 'success') echo "Thêm người dùng mới thành công!";
                        if($session_msg == 'updated') echo "Đã cập nhật thông tin người dùng!";
                        if($session_msg == 'deleted') echo "Đã xóa vĩnh viễn tài khoản người dùng!";
                        if($session_msg == 'soft_deleted') echo "Tài khoản đã có giao dịch, đã được Tạm Ẩn!";
                        if($session_msg == 'hidden') echo "Đã KHÓA tài khoản!";
                        if($session_msg == 'unlocked') echo "Đã MỞ KHÓA tài khoản!";
                        if($session_msg == 'restored') echo "Khôi phục tài khoản thành công!";
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if($session_error): ?>
            <div class="alert-box error-js">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>
                    <?php
                        if($session_error == 'exists_email') echo "Email này đã được đăng ký!";
                        if($session_error == 'exists_phone') echo "Số điện thoại này đã được sử dụng!";
                        if($session_error == 'cannot_delete_self') echo "Lỗi: Bạn không thể tự xóa tài khoản của chính mình!";
                        if($session_error == 'empty') echo "Vui lòng điền đầy đủ các trường bắt buộc!";
                        if($session_error == 'db') echo "Lỗi hệ thống: Không thể xử lý dữ liệu.";
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if(!isset($is_form) || $is_form === false): ?>

    <div class="header-sync">
        <div class="header-left-group">
            
            <form action="/lego_shop_php/admincustomer/index" method="GET" class="filter-form-sync">
                <div class="search-wrapper-sync">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" class="form-control-sync search-input-sync" 
                           placeholder="Tìm tên, email hoặc số điện thoại..." 
                           value="<?= htmlspecialchars($search ?? '') ?>">
                    <button type="submit" class="btn-search-inside">Tìm kiếm</button>
                </div>

                <select name="status" class="form-control-sync" onchange="this.form.submit()" style="flex: unset; width: 170px; cursor: pointer;">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="locked" <?= ($status ?? '') === 'locked' ? 'selected' : '' ?>>Đã khóa</option>
                    <option value="deleted" <?= ($status ?? '') === 'deleted' ? 'selected' : '' ?>>Đã bị ẩn (Xóa mềm)</option>
                </select>
            </form>
        </div>
        <a href="/lego_shop_php/admincustomer/add" class="btn-add">
            <i class="fa-solid fa-plus"></i> Thêm người dùng
        </a>
    </div>

    <div class="table-container">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="padding-left: 20px;">Mã số</th>
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
                    <tr><td colspan="7" style="text-align: center; padding: 50px; color: #a0aec0;"><i class="fa-regular fa-folder-open" style="font-size: 30px; margin-bottom: 10px; display: block;"></i> Chưa có dữ liệu người dùng.</td></tr>
                <?php else: ?>
                    <?php foreach ($customers as $user): ?>
                    <?php $is_deleted = ($user['status'] === 'deleted'); ?>
                    <tr style="<?= $is_deleted ? 'opacity: 0.5; background: #f8fafc;' : '' ?>">
                        <td style="padding-left: 20px;">
                            <span style="font-weight: 700; color: #a0aec0;">
                                CUS-<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #1a202c;"><?= htmlspecialchars($user['fullname'] ?: 'Chưa cập nhật') ?></div>
                        </td>
                        <td>
                            <div style="color: #4a5568; margin-bottom: 4px;"><i class="fa-solid fa-envelope" style="color: #cbd5e1; width: 18px;"></i> <?= $user['email'] ?></div>
                            <div style="color: #718096; font-size: 13px;"><i class="fa-solid fa-phone" style="color: #cbd5e1; width: 18px;"></i> <?= $user['phone'] ?></div>
                        </td>
                        <td>
                            <?php if (($user['role'] ?? '') === 'admin'): ?>
                                <span class="badge-custom role-admin">Quản trị viên</span>
                            <?php else: ?> 
                                <span class="badge-custom role-user">Khách hàng</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['status'] === 'active'): ?>
                                <span class="badge-custom status-active">Hoạt động</span>
                            <?php elseif ($user['status'] === 'locked'): ?>
                                <span class="badge-custom status-locked">Đã khóa</span>
                            <?php else: ?>
                                <span class="badge-custom status-deleted"><i class="fa-solid fa-eye-slash"></i> Đã bị ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td style="color: #718096; font-size: 13px;">
                            <?= date('d/m/Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td style="text-align: center; white-space: nowrap;">
                            <div style="display: flex; justify-content: center; gap: 5px;">
                                <?php if($is_deleted): ?>
                                    <a href="/lego_shop_php/admincustomer/restore/<?= $user['id'] ?>" class="btn-action btn-edit" style="color: #38a169; background: #f0fff4;" title="Khôi phục" onclick="return confirm('Bạn muốn khôi phục tài khoản này?')">
                                        <i class="fa-solid fa-rotate-left"></i> Khôi phục
                                    </a>
                                <?php else: ?>
                                    <a href="/lego_shop_php/admincustomer/edit/<?= $user['id'] ?>?search=<?= urlencode($search ?? '') ?>&status=<?= $status ?? '' ?>&page=<?= $currentPage ?? 1 ?>" class="btn-action btn-edit" title="Chỉnh sửa">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <?php 
                                        $isLocked = ($user['status'] !== 'active');
                                        $statusClass = $isLocked ? 'is-unlocking' : 'is-locking';
                                    ?>
                                    <a href="/lego_shop_php/admincustomer/toggleStatus/<?= $user['id'] ?>" class="btn-action btn-status-toggle <?= $statusClass ?>" onclick="return confirm('Xác nhận <?= $isLocked ? 'MỞ KHÓA' : 'KHÓA' ?> tài khoản này?')">
                                        <i class="fa-solid <?= $isLocked ? 'fa-unlock' : 'fa-lock' ?>"></i>
                                    </a>

                                    <a href="/lego_shop_php/admincustomer/delete/<?= $user['id'] ?>" class="btn-action btn-delete" title="Xóa" onclick="return confirm('CẢNH BÁO: Xóa tài khoản này?\n\nNếu người dùng đã có đơn hàng, hệ thống sẽ chỉ TẠM ẨN để bảo lưu dữ liệu kế toán.')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php 
        $baseUrl = "/lego_shop_php/admincustomer/index";
        $queryString = "?search=" . urlencode($search ?? '') . "&status=" . ($status ?? '');
    ?>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <div class="pagination">
            <a href="<?= $baseUrl . $queryString ?>&page=<?= $currentPage - 1 ?>" class="page-link <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                <i class="fa-solid fa-chevron-left"></i>
            </a>

            <?php 
            $start = max(1, $currentPage - 2);
            $end = min($totalPages, $currentPage + 2);
            for ($i = $start; $i <= $end; $i++): ?>
                <a href="<?= $baseUrl . $queryString ?>&page=<?= $i ?>" class="page-link <?= ($currentPage == $i) ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <a href="<?= $baseUrl . $queryString ?>&page=<?= $currentPage + 1 ?>" class="page-link <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    <?php endif; ?>

<?php else: ?>

    <div class="form-container user-form">
        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
            <i class="fa-solid <?= (isset($customer) && $customer) ? 'fa-user-pen' : 'fa-user-plus' ?>" style="color: #e3000b; font-size: 28px;"></i>
            <h3 style="margin:0; color: #1a202c; font-weight: 800; font-size: 22px;">
                <?= (isset($customer) && $customer) ? 'CHỈNH SỬA THÔNG TIN: <span style="color: #3182ce;">' . htmlspecialchars($customer['fullname']) . '</span>' : 'THÊM NGƯỜI DÙNG MỚI' ?>
            </h3>
        </div>
        
        <form action="/lego_shop_php/admincustomer/<?= (!empty($customer) && isset($customer['id'])) ? 'update/'.$customer['id'] : 'store' ?>" method="POST">    
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
                <div>
                    <div class="form-group">
                        <label>Họ và tên <span style="color:red">*</span></label>
                        <input type="text" id="fullname" name="fullname" class="form-input" placeholder="VD: Nguyễn Văn A..." value="<?= $customer['fullname'] ?? '' ?>">
                        <small class="error-text"></small>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại <span style="color:red">*</span></label>
                        <input type="text" id="phone" name="phone" class="form-input" placeholder="VD: 0987654321" value="<?= $customer['phone'] ?? '' ?>">
                        <small class="error-text"></small>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ Email <span style="color:red">*</span></label>
                        <input type="text" id="email" name="email" class="form-input" placeholder="name@example.com" value="<?= $customer['email'] ?? '' ?>">
                        <small class="error-text"></small>
                    </div>
                </div>

                <div>
                    <div class="form-group">
                        <label>Mật khẩu <?= (!empty($customer)) ? '(Để trống nếu không đổi)' : '<span style="color:red">*</span>' ?></label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="********">
                        <?php if(!empty($customer)): ?>
                            <div class="password-note"><i class="fa-solid fa-circle-info" style="margin-right: 5px;"></i> Lưu ý: Chỉ nhập khi muốn thay đổi mật khẩu mới cho người dùng.</div>
                        <?php endif; ?>
                        <small class="error-text"></small>
                    </div>

                    <div class="form-group">
                        <label>Nhập lại mật khẩu <?= (!empty($customer)) ? '' : '<span style="color:red">*</span>' ?></label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-input" placeholder="Nhập lại mật khẩu">
                        <small class="error-text"></small>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Phân quyền hệ thống</label>
                            <div class="role-badge-select">
                                <label class="role-option">
                                    <input type="radio" name="role" value="customer" <?= (empty($customer) || (isset($customer['role']) && $customer['role'] == 'customer')) ? 'checked' : '' ?>>
                                    <span class="badge-custom role-user">Khách hàng</span>
                                </label>
                                <label class="role-option">
                                    <input type="radio" name="role" value="admin" <?= (isset($customer['role']) && $customer['role'] == 'admin') ? 'checked' : '' ?>>
                                    <span class="badge-custom role-admin">Admin</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Trạng thái tài khoản</label>
                            <select name="status" class="form-input" style="height: 48px; background: #f1f5f9; border-color: transparent;">
                                <option value="active" <?= (isset($customer['status']) && $customer['status'] == 'active') ? 'selected' : '' ?>>Đang hoạt động</option>
                                <option value="locked" <?= (isset($customer['status']) && $customer['status'] == 'locked') ? 'selected' : '' ?>>Khóa (Tạm ngừng)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div style="margin-top: 20px; padding-top: 25px; border-top: 1px solid #edf2f7; display: flex; align-items: center;">
                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> 
                    <?= (!empty($customer)) ? 'CẬP NHẬT TÀI KHOẢN' : 'TẠO MỚI NGƯỜI DÙNG' ?>
                </button>
                <a href="/lego_shop_php/admincustomer" class="btn-cancel-link">Hủy bỏ / Quay lại</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.querySelector(".form-container form");
        const fullname = document.getElementById("fullname");
        const phone = document.getElementById("phone");
        const email = document.getElementById("email");
        const password = document.getElementById("password");
        const confirmPassword = document.getElementById("confirm_password");
        const isEditMode = <?= (!empty($customer)) ? 'true' : 'false' ?>;

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

        function validateFullname() {
            const value = fullname.value.trim();
            if (value === "") { showError(fullname, "Không được để trống"); return false; }
            if (value.length < 3) { showError(fullname, "Tối thiểu 3 ký tự"); return false; }
            showSuccess(fullname); return true;
        }

        function validatePhone() {
            const value = phone.value.trim();
            const regex = /^0[0-9]{9}$/;
            if (value === "") { showError(phone, "Không được để trống"); return false; }
            if (!regex.test(value)) { showError(phone, "SĐT phải 10 số và bắt đầu bằng 0"); return false; }
            showSuccess(phone); return true;
        }

        function validateEmail() {
            const value = email.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value === "") { showError(email, "Không được để trống"); return false; }
            if (!regex.test(value)) { showError(email, "Email không hợp lệ"); return false; }
            showSuccess(email); return true;
        }

        function validatePassword() {
            const value = password.value;
            if (isEditMode && value === "") { showSuccess(password); return true; }
            if (value === "") { showError(password, "Vui lòng nhập mật khẩu"); return false; }
            if (value.length < 6) { showError(password, "Tối thiểu 6 ký tự"); return false; }
            showSuccess(password); return true;
        }

        function validateConfirmPassword() {
            const pass = password.value;
            const confirm = confirmPassword.value;
            if (isEditMode && pass === "" && confirm === "") { showSuccess(confirmPassword); return true; }
            if (confirm === "") { showError(confirmPassword, "Vui lòng xác nhận mật khẩu"); return false; }
            if (pass !== confirm) { showError(confirmPassword, "Mật khẩu không khớp"); return false; }
            showSuccess(confirmPassword); return true;
        }

        fullname.addEventListener("input", validateFullname);
        phone.addEventListener("input", validatePhone);
        email.addEventListener("input", validateEmail);
        password.addEventListener("input", () => { validatePassword(); validateConfirmPassword(); });
        confirmPassword.addEventListener("input", validateConfirmPassword);

        form.addEventListener("submit", function(e) {
            const vName = validateFullname();
            const vPhone = validatePhone();
            const vEmail = validateEmail();
            const vPass = validatePassword();
            const vConf = validateConfirmPassword();

            if (!(vName && vPhone && vEmail && vPass && vConf)) {
                e.preventDefault();
                alert("⚠️ Vui lòng kiểm tra lại các trường bị báo đỏ trước khi lưu!");
                const firstError = document.querySelector(".input-error");
                if(firstError) firstError.focus();
            }
        });
    </script>
<?php endif; ?>

<script>
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