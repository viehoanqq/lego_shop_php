<div class="profile-container">
    <div class="profile-sidebar">
        <div class="profile-card text-center">
            <div class="avatar-wrapper">
                <img src="/lego_shop_php/public/assets/images/avt.png" 
                     class="profile-avatar-large" 
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= $_SESSION['admin_name'] ?? 'A' ?>&background=6366f1&color=fff&size=150&bold=true'" 
                     alt="Avatar">
               
            </div>
            
            <h2 class="profile-name"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></h2>
            <p class="profile-role-tag"><?= strtoupper($_SESSION['admin_role'] ?? 'QUẢN TRỊ VIÊN') ?></p>
            
            <div class="profile-stats">
                <!-- <div class="stat-item">
                    <h4>124</h4>
                    <p>Đơn đã duyệt</p>
                </div>
                <div class="stat-item">
                    <h4>45</h4>
                    <p>Báo cáo</p>
                </div> -->
            </div>
        </div>
    </div>

    <div class="profile-content">
        
        <?php if(!empty($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= $success_msg ?>
            </div>
        <?php endif; ?>

        <div class="profile-card mb-30">
            <div class="card-header">
                <h3><i class="fa-solid fa-address-card"></i> Thông tin cá nhân</h3>
                <p class="text-muted">Cập nhật thông tin liên hệ và định danh của bạn.</p>
            </div>
            
            <?php if (isset($_SESSION['toast_msg'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Sử dụng hàm showToast bạn đã viết trong main.js
            showToast("<?= $_SESSION['toast_msg'] ?>", "<?= $_SESSION['toast_type'] ?? 'error' ?>");
        });
    </script>
    <?php 
        // Xóa session đi để f5 không bị hiện lại
        unset($_SESSION['toast_msg']); 
        unset($_SESSION['toast_type']); 
    ?>
<?php endif; ?>

            <form action="/lego_shop_php/admin/actionUpdateProfile" method="POST" class="profile-form">
                <div class="form-group full-width">
                    <label>Họ và tên <span style="color:red">*</span></label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="fullname" 
                               value="<?= htmlspecialchars($old['fullname'] ?? $admin_info['fullname'] ?? $_SESSION['admin_name'] ?? '') ?>" 
                               placeholder="Nhập họ và tên...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Số điện thoại <span style="color:red">*</span></label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="phone" 
                                   value="<?= htmlspecialchars($old['phone'] ?? $admin_info['phone'] ?? '') ?>" 
                                   placeholder="Nhập số điện thoại...">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Địa chỉ Email <span style="color:red">*</span></label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" 
                                   value="<?= htmlspecialchars($old['email'] ?? $admin_info['email'] ?? '') ?>" 
                                   placeholder="Nhập địa chỉ email...">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> Lưu thông tin</button>
                </div>
            </form>
        </div>

        <div class="profile-card">
            <div class="card-header">
                <h3><i class="fa-solid fa-shield-halved"></i> Đổi mật khẩu</h3>
                <p class="text-muted">Đảm bảo tài khoản của bạn đang sử dụng mật khẩu dài, ngẫu nhiên để an toàn.</p>
            </div>
            
            <?php if(!empty($password_error)): ?>
                <div class="alert alert-error" style="margin-bottom: 15px;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?= $password_error ?>
                </div>
            <?php endif; ?>

            <form action="/lego_shop_php/admin/actionUpdatePassword" method="POST" class="profile-form">
                <div class="form-group full-width">
                    <label>Mật khẩu hiện tại <span style="color:red">*</span></label>
                    <div class="input-with-icon">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="old_password" placeholder="Nhập mật khẩu hiện tại...">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Mật khẩu mới <span style="color:red">*</span></label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-key"></i>
                            <input type="password" name="new_password" placeholder="Ít nhất 6 ký tự...">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới <span style="color:red">*</span></label>
                        <div class="input-with-icon">
                            <i class="fa-solid fa-check-double"></i>
                            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới...">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save btn-warning"><i class="fa-solid fa-key"></i> Cập nhật mật khẩu</button>
                </div>
            </form>
        </div>

    </div>
</div>