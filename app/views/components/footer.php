<?php
    // 1. Gọi Model trực tiếp để lấy dữ liệu cài đặt
    require_once __DIR__ . '/../../models/SettingModel.php'; 
    $settingModel = new SettingModel();
    $settings = $settingModel->getSettings();

    // 2. Đặt giá trị mặc định phòng trường hợp Database bị trống
    $shop_name = $settings['shop_name'] ?? 'LEGO World Store';
    $logo_url  = $settings['logo_url'] ?? 'logo.png';
    $phone     = $settings['phone'] ?? '1900 1208';
    $email     = $settings['email'] ?? 'hotro@legoworldstore.com.vn';
    $address   = $settings['address'] ?? '273 An Dương Vương, Phường 1, Quận 5, TP. Hồ Chí Minh';
    
    // Giờ làm việc
    $working_hours_1 = $settings['working_hours_1'] ?? 'Thứ 2 - Thứ 7: 8:00 - 17:00';
    $working_hours_2 = $settings['working_hours_2'] ?? 'Chủ nhật: 8:00 - 12:00';

    // Chính sách / Cam kết (Top bar)
    $policy_1 = $settings['policy_1'] ?? 'Miễn phí giao hàng đơn từ 500k';
    $policy_2 = $settings['policy_2'] ?? 'Giao hàng hỏa tốc 4 tiếng';
    $policy_4 = $settings['policy_4'] ?? 'Mua hàng trả góp';
    $policy_5 = $settings['policy_5'] ?? 'Hệ thống 200 cửa hàng';

    // Công ty & ĐKKD
    $company_name = $settings['company_name'] ?? 'Công ty cổ phần LEGO';
    $business_license = $settings['business_license'] ?? '0309132354';

    // Link Mạng xã hội
    $fb = $settings['facebook_url'] ?? '#';
    $ig = $settings['instagram_url'] ?? '#';
    $yt = $settings['youtube_url'] ?? '#';
    $tk = $settings['tiktok_url'] ?? '#';
    $zl = $settings['zalo_url'] ?? '#';
?>
</div> <footer class="main-footer">
    <div class="footer-top">
        <div class="container" style="display: flex; justify-content: space-between; flex-wrap: wrap;">
            <?php if(!empty($policy_1)): ?><span><i class="fa-solid fa-truck-fast"></i> <?= htmlspecialchars($policy_1) ?></span><?php endif; ?>
            <?php if(!empty($policy_2)): ?><span><i class="fa-solid fa-bolt"></i> <?= htmlspecialchars($policy_2) ?></span><?php endif; ?>
            <?php if(!empty($policy_3)): ?><span><i class="fa-solid fa-gift"></i> <?= htmlspecialchars($policy_3) ?></span><?php endif; ?>
            <?php if(!empty($policy_4)): ?><span><i class="fa-solid fa-credit-card"></i> <?= htmlspecialchars($policy_4) ?></span><?php endif; ?>        </div>
    </div>

    <div class="footer-body">
        <div class="footer-subscribe">
            <div class="logo">
                <a href="/lego_shop_php/home">
                    <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($shop_name) ?>" />
                </a>
            </div>
            <div class="subscribe-box">
                <h4>
                    <p class="subscribe-title"><strong>Tham gia ngay</strong> để nhận thông tin ưu đãi từ <?= htmlspecialchars(strtoupper($shop_name)) ?></p>
                </h4>
                <form>
                    <input type="email" placeholder="Nhập email của bạn" required />
                    <button type="submit">Đăng ký</button>
                </form>
                <small class="subscribe-note">*Bạn có thể hủy đăng ký bất kỳ lúc nào!</small>
            </div>
        </div>

        <div class="footer-social">
            <h4>Kết nối với chúng tôi</h4>
            <div class="social-links">
                <a href="<?= htmlspecialchars($fb) ?>" target="_blank"><i class="fa-brands fa-facebook-f"></i> Facebook</a>
                <a href="<?= htmlspecialchars($ig) ?>" target="_blank"><i class="fa-brands fa-instagram"></i> Instagram</a>
                <a href="<?= htmlspecialchars($yt) ?>" target="_blank"><i class="fa-brands fa-youtube"></i> YouTube</a>
                <a href="<?= htmlspecialchars($tk) ?>" target="_blank"><i class="fa-brands fa-tiktok"></i> TikTok</a>
                <a href="<?= htmlspecialchars($zl) ?>" target="_blank"><i class="fa-solid fa-comment-dots"></i> Zalo</a>
            </div>
        </div>

        <div class="footer-contact">
            <h4>Thông tin liên hệ</h4>
            <p><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($address) ?></p>
            <p><i class="fa-solid fa-phone"></i> <?= htmlspecialchars($phone) ?></p>
            <p><i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($email) ?></p>
            
            <p><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($working_hours_1) ?></p>
            <?php if(!empty($working_hours_2)): ?>
                <p><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($working_hours_2) ?></p>
            <?php endif; ?>
        </div>

        <div class="footer-grid">
            <div class="footer-links">
                <h4>Điều khoản & Chính sách</h4>
                <ul>
                    <li><a href="#">Chính sách giao hàng</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Chính sách bảo hành & đổi trả</a></li>
                    <li><a href="#">Chính sách thanh toán</a></li>
                    <li><a href="#">Điều kiện & Điều khoản thành viên</a></li>
                    <li><a href="#">Chính sách trả góp</a></li>
                    <li><a href="#">Hệ thống cửa hàng</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($shop_name) ?>. All rights reserved.</p>
            <p><?= htmlspecialchars($company_name) ?> - Số ĐKKD: <?= htmlspecialchars($business_license) ?></p>
        </div>
    </div>
</footer>
</body>
</html>