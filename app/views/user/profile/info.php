<div class="main-content" style="width: 80%; max-width: 1200px; margin: 30px auto;">
    <div class="profile-container">
        
        <?php require __DIR__ . '/../../components/profile_sidebar.php'; ?>

        <section class="profile-main">
            <div class="profile-form-box">
                <h2 class="section-title">Thông tin cá nhân</h2>
                <p class="section-desc">Cập nhật thông tin để nhận ưu đãi và giao hàng nhanh hơn.</p>

                <form class="profile-form" action="/lego_shop_php/profile/updateInfo" method="POST">
                    
                    <div class="input-group">
                        <label>Họ và tên</label>
                        <input type="text" name="fullname" value="Nguyễn Văn A" required />
                    </div>

                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" value="nguyenvana@gmail.com" readonly style="background-color: #f5f5f5; cursor: not-allowed;" title="Email đăng nhập không thể thay đổi" />
                    </div>

                    <div class="input-group">
                        <label>Số điện thoại</label>
                        <input type="tel" name="phone" value="0901234567" pattern="[0-9]{10,11}" required />
                        <small style="display: block; margin-top: 6px; color: #888; font-size: 12px;">Nhập 10 số, bắt đầu bằng 0</small>
                    </div>

                    <div class="input-group">
                        <label>Ngày sinh</label>
                        <input type="date" name="dob" value="1995-06-15" />
                    </div>

                    <div class="input-group">
                        <label>Giới tính</label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="gender" value="male" checked />
                                <span class="radio-check"></span> Nam
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="gender" value="female" />
                                <span class="radio-check"></span> Nữ
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="gender" value="other" />
                                <span class="radio-check"></span> Khác
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="update-btn">Cập nhật thông tin</button>
                </form>
            </div>
        </section>

    </div>
</div>