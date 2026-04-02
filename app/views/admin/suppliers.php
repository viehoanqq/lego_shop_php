<style>
    * { box-sizing: border-box; }
    .header { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 15px;}
    .header h2 { margin: 0 0 15px 0; color: #1a202c; font-size: 22px; font-weight: 700; }
    .search-form { display: flex; gap: 10px; align-items: center; flex: 1; }
    .form-control { padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; outline: none; width: 100%; transition: 0.2s;}
    .form-control:focus { border-color: #3182ce; box-shadow: 0 0 0 2px rgba(49,130,206,0.1); }
    .btn-add { background: #3182ce; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; height: 42px;}
    
    .table-container { background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; }
    .table th { background: #f8fafc; padding: 15px; text-align: left; font-size: 13px; color: #64748b; border-bottom: 2px solid #e2e8f0; text-transform: uppercase;}
    .table td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155;}
    
    .badge-active { background: #d1fae5; color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    .badge-locked { background: #fee2e2; color: #e53e3e; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 700; }
    
    .form-container { background: #fff; padding: 25px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 25px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: 600; margin-bottom: 5px; color: #475569;}
    .btn-submit { background: #38a169; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s;}
    .btn-submit:hover { background: #2f855a; }
    .error-text { color: #e53e3e; font-size: 12px; display: block; margin-top: 4px; min-height: 18px;}
    .input-error { border-color: #e53e3e !important; background-color: #fff5f5; }
    .input-success { border-color: #38a169 !important; }
    
    #status-alert-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
    .alert-box { padding: 15px; border-radius: 8px; margin-bottom: 10px; color: #fff; font-weight: 600; display: flex; gap: 10px; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.15);}
    .success-js { background: #38a169; }
    .error-js { background: #e53e3e; }
</style>

<?php 
$session_msg = get_flash_message('msg');
$session_error = get_flash_message('error');
if($session_msg || $session_error): ?>
    <div id="status-alert-container">
        <?php if($session_msg): ?>
            <div class="alert-box success-js">
                <i class="fa-solid fa-circle-check"></i> 
                <?php 
                    if ($session_msg == 'deleted') echo "Đã xóa vĩnh viễn Nhà cung cấp.";
                    elseif ($session_msg == 'soft_deleted') echo "Nhà cung cấp đã có giao dịch, đã TẠM ẨN.";
                    else echo "Thao tác thành công!";
                ?>
            </div>
        <?php endif; ?>
        <?php if($session_error): ?>
            <div class="alert-box error-js"><i class="fa-solid fa-triangle-exclamation"></i> Có lỗi xảy ra, vui lòng kiểm tra lại!</div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if(!isset($is_form) || $is_form === false): ?>
    <div class="header">
        <div>
            <h2><i class="fa-solid fa-handshake" style="color: #3182ce;"></i> Quản lý Nhà cung cấp</h2>
            <form action="/lego_shop_php/adminsupplier" method="GET" class="search-form">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm tên, SĐT, Email..." value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>" style="width: 250px;">
                <select name="status" class="form-control" onchange="this.form.submit()" style="width: 150px; cursor: pointer;">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="active" <?= ($filters['status'] == 'active') ? 'selected' : '' ?>>Đang hợp tác</option>
                    <option value="locked" <?= ($filters['status'] == 'locked') ? 'selected' : '' ?>>Tạm ngừng</option>
                </select>
                <button type="submit" style="display:none;"></button>
            </form>
        </div>
        <a href="/lego_shop_php/adminsupplier/add" class="btn-add"><i class="fa-solid fa-plus"></i> Thêm đối tác mới</a>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nhà cung cấp</th>
                    <th>Liên hệ</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th style="text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($suppliers)): ?>
                    <?php foreach($suppliers as $s): ?>
                        <tr style="<?= $s['status'] == 'locked' ? 'opacity: 0.7; background: #f8fafc;' : '' ?>">
                            <td>
                                <strong style="color: #1e293b; font-size: 15px;"><?= htmlspecialchars($s['name']) ?></strong><br>
                                <span style="color: #94a3b8; font-size: 12px;">ID: #SUP-<?= $s['id'] ?></span>
                            </td>
                            <td>
                                <div><i class="fa-solid fa-phone" style="color:#718096; width:16px;"></i> <?= htmlspecialchars($s['phone']) ?></div>
                                <?php if(!empty($s['email'])): ?>
                                    <div><i class="fa-solid fa-envelope" style="color:#718096; width:16px;"></i> <?= htmlspecialchars($s['email']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td style="max-width: 250px; line-height: 1.4;"><?= htmlspecialchars($s['address']) ?></td>
                            <td>
                                <?php if($s['status'] == 'active'): ?>
                                    <span class="badge-active">Đang hợp tác</span>
                                <?php else: ?>
                                    <span class="badge-locked">Tạm ngừng</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; white-space: nowrap;">
                                <a href="/lego_shop_php/adminsupplier/edit/<?= $s['id'] ?>" style="color: #3182ce; margin-right: 12px; text-decoration: none; font-weight: 600;"><i class="fa-solid fa-pen"></i> Sửa</a>
                                
                                <a href="/lego_shop_php/adminsupplier/toggleStatus/<?= $s['id'] ?>" style="color: <?= $s['status'] == 'active' ? '#dd6b20' : '#38a169' ?>; margin-right: 12px; text-decoration: none; font-weight: 600;" onclick="return confirm('Xác nhận thay đổi trạng thái đối tác này?')">
                                    <i class="fa-solid <?= $s['status'] == 'active' ? 'fa-lock' : 'fa-lock-open' ?>"></i> <?= $s['status'] == 'active' ? 'Ngừng' : 'Mở lại' ?>
                                </a>

                                <a href="/lego_shop_php/adminsupplier/delete/<?= $s['id'] ?>" style="color: #e53e3e; text-decoration: none; font-weight: 600;" onclick="return confirm('Bạn có chắc muốn xóa Nhà cung cấp này? Nếu đã có giao dịch, hệ thống sẽ chỉ ẩn đi để bảo lưu dữ liệu.')">
                                    <i class="fa-solid fa-trash"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; color: #94a3b8; padding: 40px;"><i class="fa-solid fa-box-open" style="font-size: 24px; margin-bottom: 10px; display: block;"></i> Chưa có dữ liệu nhà cung cấp.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div class="form-container">
        <h3 style="margin-top:0; color: #1e293b; padding-bottom: 15px; border-bottom: 2px solid #f1f5f9;">
            <i class="fa-solid fa-building-user" style="color: #3182ce;"></i> 
            <?= isset($supplier) ? 'Chỉnh sửa Đối tác: <span style="color: #38a169;">' . htmlspecialchars($supplier['name']) . '</span>' : 'Thêm Nhà cung cấp mới' ?>
        </h3>
        
        <form id="supplierForm" action="<?= isset($supplier) ? '/lego_shop_php/adminsupplier/update/'.$supplier['id'] : '/lego_shop_php/adminsupplier/store' ?>" method="POST" style="margin-top: 20px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Tên nhà cung cấp / Công ty <span style="color:red">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= $supplier['name'] ?? '' ?>" placeholder="Nhập tên đối tác...">
                    <small class="error-text"></small>
                </div>
                <div class="form-group">
                    <label>Số điện thoại liên hệ <span style="color:red">*</span></label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= $supplier['phone'] ?? '' ?>" placeholder="Ví dụ: 0987654321">
                    <small class="error-text"></small>
                </div>
            </div>
            
            <div class="form-group">
                <label>Email liên hệ</label>
                <input type="email" id="email" name="email" class="form-control" value="<?= $supplier['email'] ?? '' ?>" placeholder="Ví dụ: contact@congty.com">
                <small class="error-text"></small>
            </div>

            <div class="form-group">
                <label>Địa chỉ công ty / kho hàng</label>
                <textarea id="address" name="address" class="form-control" rows="3" placeholder="Nhập địa chỉ đầy đủ..."><?= $supplier['address'] ?? '' ?></textarea>
                <small class="error-text"></small>
            </div>

            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #f1f5f9; display: flex; gap: 10px;">
                <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu thông tin</button>
                <a href="/lego_shop_php/adminsupplier" style="padding: 10px 20px; color: #475569; text-decoration: none; font-weight: 600; background: #edf2f7; border-radius: 8px;">Hủy bỏ</a>
            </div>
        </form>
    </div>

    <script>
        const form = document.getElementById("supplierForm");
        const nameInput = document.getElementById("name");
        const phoneInput = document.getElementById("phone");
        const emailInput = document.getElementById("email");
        const addressInput = document.getElementById("address");

        // Hàm hiển thị lỗi
        function showError(input, message) {
            input.classList.add("input-error");
            input.classList.remove("input-success");
            input.nextElementSibling.innerText = message;
        }

        // Hàm xóa lỗi (Thành công)
        function showSuccess(input) {
            input.classList.remove("input-error");
            input.classList.add("input-success");
            input.nextElementSibling.innerText = "";
        }

        // 1. Kiểm tra Tên
        function validateName() {
            const value = nameInput.value.trim();
            if (value === "") { showError(nameInput, "Tên nhà cung cấp không được để trống!"); return false; }
            if (value.length < 3) { showError(nameInput, "Tên nhà cung cấp quá ngắn!"); return false; }
            showSuccess(nameInput); return true;
        }

        // 2. Kiểm tra Số điện thoại
        function validatePhone() {
            const value = phoneInput.value.trim();
            const phoneRegex = /(84|0[3|5|7|8|9])+([0-9]{8})\b/;
            if (value === "") { showError(phoneInput, "Số điện thoại không được để trống!"); return false; }
            if (!phoneRegex.test(value)) { showError(phoneInput, "Định dạng SĐT không hợp lệ (Bắt đầu bằng 0 và gồm 10 số)!"); return false; }
            showSuccess(phoneInput); return true;
        }

        // 3. Kiểm tra Email (Tùy chọn, nhưng nếu nhập thì phải đúng chuẩn)
        function validateEmail() {
            const value = emailInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value !== "" && !emailRegex.test(value)) { 
                showError(emailInput, "Định dạng Email không hợp lệ!"); return false; 
            }
            showSuccess(emailInput); return true;
        }

        // 4. Kiểm tra Địa chỉ (Tùy chọn)
        function validateAddress() {
            const value = addressInput.value.trim();
            if (value !== "" && value.length < 5) {
                showError(addressInput, "Địa chỉ quá ngắn, vui lòng nhập rõ hơn."); return false;
            }
            showSuccess(addressInput); return true;
        }

        // Bắt sự kiện gõ phím để báo lỗi ngay lập tức
        nameInput.addEventListener("input", validateName);
        phoneInput.addEventListener("input", validatePhone);
        emailInput.addEventListener("input", validateEmail);
        addressInput.addEventListener("input", validateAddress);

        // Bắt sự kiện khi bấm nút Submit
        form.addEventListener("submit", function(e) {
            const isNameValid = validateName();
            const isPhoneValid = validatePhone();
            const isEmailValid = validateEmail();
            const isAddressValid = validateAddress();

            // Nếu có bất kỳ lỗi nào -> Chặn form không cho gửi
            if (!(isNameValid && isPhoneValid && isEmailValid && isAddressValid)) {
                e.preventDefault(); 
                alert("⚠️ Vui lòng kiểm tra và sửa lại các trường bị báo đỏ trước khi lưu!");
                
                // Cuộn trang lên chỗ bị lỗi
                const firstError = document.querySelector(".input-error");
                if(firstError) firstError.focus();
            }
        });
    </script>
<?php endif; ?>

<script>
    // Ẩn thông báo góc phải sau 4 giây
    setTimeout(() => {
        let alerts = document.querySelectorAll('.alert-box');
        alerts.forEach(el => { el.style.transition = "opacity 0.5s ease"; el.style.opacity = "0"; setTimeout(() => el.style.display = 'none', 500); });
    }, 4000);
</script>