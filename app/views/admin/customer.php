<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    :root {
        --primary: #3182ce;
        --success: #38a169;
        --danger: #e53e3e;
        --text-main: #2d3748;
        --text-muted: #718096;
        --bg-body: #f7fafc;
    }

    body { background-color: var(--bg-body); font-family: 'Inter', sans-serif; color: var(--text-main); }

    /* Header đồng bộ */
    .header-custom { 
        background: #fff; padding: 25px; border-radius: 12px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 25px;
        display: flex; justify-content: space-between; align-items: center;
    }
    .header-left h2 { margin: 0; font-size: 24px; font-weight: 700; color: #1a202c; }

    /* Card & Table */
    .table-container { 
        background: #fff; border-radius: 12px; 
        box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: hidden;
    }
    .custom-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .custom-table th { 
        background: #f8fafc; padding: 15px; text-align: left; 
        color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 0.05em;
        border-bottom: 2px solid #e2e8f0; 
    }
    .custom-table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 14px; }

    /* Badge Style */
    .badge-custom { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; display: inline-block; }
    .role-admin { background: #ebf8ff; color: #2b6cb0; border: 1px solid #bee3f8; }
    .role-user { background: #f7fafc; color: #4a5568; border: 1px solid #e2e8f0; }
    
    .status-active { background: #f0fff4; color: #2f855a; border: 1px solid #9ae6b4; }
    .status-locked { background: #fff5f5; color: #c53030; border: 1px solid #feb2b2; }

    /* Action Buttons */
    .btn-action { 
        text-decoration: none; padding: 6px 12px; border-radius: 6px; 
        font-size: 13px; font-weight: 600; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;
    }
    .btn-edit { color: var(--primary); }
    .btn-edit:hover { background: #ebf8ff; }
    .btn-lock { color: var(--danger); }
    .btn-lock:hover { background: #fff5f5; }

    /* Alert Toast tương tự Product */
    #status-alert-container { position: fixed; top: 20px; right: 20px; z-index: 9999; width: 320px; }
    .alert-box { 
        padding: 15px 20px; border-radius: 10px; color: #fff; margin-bottom: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; align-items: center; gap: 10px;
        animation: slideIn 0.4s ease-out;
    }
    .alert-success { background: linear-gradient(135deg, #48bb78, #38a169); }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>

<?php if ($msg = get_flash_message('success')): ?>
    <div id="status-alert-container">
        <div class="alert-box alert-success">
            <i class="fa-solid fa-circle-check"></i>
            <span><?= $msg ?></span>
        </div>
    </div>
<?php endif; ?>

<div class="container-fluid px-4 mt-4">
    <div class="header-custom">
        <div class="header-left">
            <h2>Quản lý Người dùng</h2>
        </div>
        <div class="text-end">
            <div class="text-muted small fw-bold">TỔNG CỘNG</div>
            <div class="h4 mb-0 fw-bold text-primary"><?= count($customers) ?></div>
        </div>
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
                    <th class="text-center">Thao tác</th>
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