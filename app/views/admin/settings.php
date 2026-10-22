<style>
    .settings-wrapper { background: #f8fafc; padding: 25px; border-radius: 12px; min-height: calc(100vh - 100px); }
    .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #e2e8f0; padding-bottom: 15px; }
    .page-header h2 { margin: 0; color: #0f172a; font-size: 22px; font-weight: 800; display: flex; align-items: center; gap: 10px; }
    
    .settings-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .settings-grid { grid-template-columns: 1fr; } }

    .admin-card { background: #fff; border-radius: 8px; padding: 20px; border: 1px solid #e2e8f0; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);}
    .card-title { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 20px 0; padding-bottom: 10px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 8px; text-transform: uppercase; }
    
    .form-group { margin-bottom: 15px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .form-control { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; outline: none; transition: 0.2s; box-sizing: border-box; color: #1e293b; }
    .form-control:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
    
    .input-group { position: relative; display: flex; width: 100%; }
    .input-group-text { padding: 10px 15px; background: #f1f5f9; border: 1px solid #cbd5e1; border-right: none; border-radius: 6px 0 0 6px; color: #64748b; font-weight: 600; display: flex; align-items: center; justify-content: center; min-width: 40px;}
    .input-group .form-control { border-radius: 0 6px 6px 0; }

    .btn-save { background: #3b82f6; color: #fff; border: none; padding: 12px 25px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; font-size: 15px; width: 100%;}
    .btn-save:hover { background: #2563eb; }

    .logo-preview { max-width: 200px; max-height: 80px; object-fit: contain; border-radius: 8px; border: 1px dashed #cbd5e1; padding: 10px; background: #c53030; margin-bottom: 10px; display: block; }
</style>

<div class="settings-wrapper">
    <div class="page-header">
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: 600; border: 1px solid #bbf7d0;">
            <i class="fa-solid fa-circle-check"></i> Cập nhật thông tin hệ thống thành công!
        </div>
    <?php endif; ?>

    <form action="/lego_shop_php/admin/setting/update" method="POST" enctype="multipart/form-data">
        <div class="settings-grid">
            
            <div class="dashboard-left">
                <div class="admin-card">
                    <h3 class="card-title">Thông tin Doanh Nghiệp (Footer)</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Tên Cửa Hàng (Hiển thị chung)</label>
                        <input type="text" name="shop_name" class="form-control" value="<?= htmlspecialchars($settings['shop_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên Công Ty (Hiển thị dưới cùng trang)</label>
                        <input type="text" name="company_name" class="form-control" value="<?= htmlspecialchars($settings['company_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số ĐKKD</label>
                        <input type="text" name="business_license" class="form-control" value="<?= htmlspecialchars($settings['business_license']) ?>">
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="card-title">Liên hệ & Giờ mở cửa</h3>
                    <div class="form-group">
                        <label class="form-label">Số Điện Thoại Hotline</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($settings['phone']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email Hỗ Trợ</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($settings['email']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Địa Chỉ Cửa Hàng</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($settings['address']) ?>" required>
                        </div>
                    </div>
                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label class="form-label">Giờ làm việc (Dòng 1)</label>
                            <input type="text" name="working_hours_1" class="form-control" value="<?= htmlspecialchars($settings['working_hours_1']) ?>" placeholder="VD: Thứ 2 - Thứ 7: 8:00 - 17:00">
                        </div>
                        <div>
                            <label class="form-label">Giờ làm việc (Dòng 2)</label>
                            <input type="text" name="working_hours_2" class="form-control" value="<?= htmlspecialchars($settings['working_hours_2']) ?>" placeholder="VD: Chủ nhật: 8:00 - 12:00">
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="card-title">Các chính sách & Cam kết (Banner trên cùng)</h3>
                    <div class="form-group">
                        <label class="form-label">Chính sách 1 (VD: Miễn phí giao hàng đơn từ 500k)</label>
                        <input type="text" name="policy_1" class="form-control" value="<?= htmlspecialchars($settings['policy_1']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Chính sách 2 (VD: Giao hàng hỏa tốc 4 tiếng)</label>
                        <input type="text" name="policy_2" class="form-control" value="<?= htmlspecialchars($settings['policy_2']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Chính sách 3 (VD: Chương trình thành viên)</label>
                        <input type="text" name="policy_3" class="form-control" value="<?= htmlspecialchars($settings['policy_3']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Chính sách 4 (VD: Mua hàng trả góp)</label>
                        <input type="text" name="policy_4" class="form-control" value="<?= htmlspecialchars($settings['policy_4']) ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Chính sách 5 (VD: Hệ thống 200 cửa hàng)</label>
                        <input type="text" name="policy_5" class="form-control" value="<?= htmlspecialchars($settings['policy_5']) ?>">
                    </div>
                </div>
            </div>

            <div class="dashboard-right">
                
                <div class="admin-card">
                    <h3 class="card-title">Logo Cửa Hàng</h3>
                    <div class="form-group">
                        <img src="/lego_shop_php/public/assets/images/<?= $settings['logo_url'] ?>" alt="Logo" class="logo-preview">
                        <input type="file" name="logo_upload" class="form-control" accept="image/*">
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="card-title">Liên Kết Mạng Xã Hội</h3>
                    
                    <div class="form-group">
                        <label class="form-label">Facebook</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-facebook" style="color: #1877F2;"></i></span>
                            <input type="text" name="facebook_url" class="form-control" value="<?= htmlspecialchars($settings['facebook_url']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instagram</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-instagram" style="color: #E4405F;"></i></span>
                            <input type="text" name="instagram_url" class="form-control" value="<?= htmlspecialchars($settings['instagram_url']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">YouTube</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-youtube" style="color: #FF0000;"></i></span>
                            <input type="text" name="youtube_url" class="form-control" value="<?= htmlspecialchars($settings['youtube_url']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">TikTok</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-tiktok" style="color: #000000;"></i></span>
                            <input type="text" name="tiktok_url" class="form-control" value="<?= htmlspecialchars($settings['tiktok_url']) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Zalo</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-comment-dots" style="color: #0068FF;"></i></span>
                            <input type="text" name="zalo_url" class="form-control" value="<?= htmlspecialchars($settings['zalo_url']) ?>">
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <h3 class="card-title">Thanh toán VietQR & Hoàn tiền</h3>
                    <?php
                        $qr_banks = ['MB' => 'MB Bank', 'VCB' => 'Vietcombank', 'ICB' => 'VietinBank', 'BIDV' => 'BIDV', 'VBA' => 'Agribank',
                                     'TCB' => 'Techcombank', 'ACB' => 'ACB', 'VPB' => 'VPBank', 'TPB' => 'TPBank', 'STB' => 'Sacombank',
                                     'VIB' => 'VIB', 'SHB' => 'SHB', 'HDB' => 'HDBank', 'OCB' => 'OCB', 'MSB' => 'MSB', 'EIB' => 'Eximbank',
                                     'LPB' => 'LPBank', 'SEAB' => 'SeABank', 'CAKE' => 'Cake', 'TIMO' => 'Timo'];
                        $webhook_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/lego_shop_php/payment/webhook';
                    ?>
                    <div class="form-group">
                        <label class="form-label">Ngân hàng nhận tiền</label>
                        <select name="bank_code" id="set_bank_code" class="form-control">
                            <?php foreach ($qr_banks as $code => $name): ?>
                                <option value="<?= $code ?>" <?= ($settings['bank_code'] ?? '') === $code ? 'selected' : '' ?>><?= $name ?> (<?= $code ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số tài khoản</label>
                        <input type="text" name="bank_account_no" id="set_bank_no" class="form-control" value="<?= htmlspecialchars($settings['bank_account_no'] ?? '') ?>" pattern="[0-9 ]{6,25}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên chủ tài khoản (không dấu)</label>
                        <input type="text" name="bank_account_name" id="set_bank_name" class="form-control" value="<?= htmlspecialchars($settings['bank_account_name'] ?? '') ?>" style="text-transform: uppercase;" required>
                    </div>
                    <div style="text-align: center; margin-bottom: 15px;">
                        <img id="set_qr_preview" alt="QR mẫu" style="width: 160px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px;">
                    </div>
                    <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        <div>
                            <label class="form-label">Hết hạn thanh toán (phút)</label>
                            <input type="number" name="payment_timeout_minutes" class="form-control" min="5" max="1440" value="<?= (int)($settings['payment_timeout_minutes'] ?? 30) ?>">
                        </div>
                        <div>
                            <label class="form-label">Hạn trả hàng (ngày)</label>
                            <input type="number" name="refund_days" class="form-control" min="0" max="60" value="<?= (int)($settings['refund_days'] ?? 7) ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label style="display: flex; gap: 8px; align-items: center; font-size: 13px; font-weight: 600; color: #475569; cursor: pointer;">
                            <input type="checkbox" name="payment_sandbox" value="1" <?= (int)($settings['payment_sandbox'] ?? 0) === 1 ? 'checked' : '' ?>>
                            Chế độ giả lập thanh toán
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Webhook URL (SePay / Casso)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($webhook_url) ?>" readonly onclick="this.select()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">API Key webhook</label>
                        <?php $env_key = (string)env('PAYMENT_WEBHOOK_KEY', ''); if ($env_key !== ''): ?>
                            <input type="text" class="form-control" value="<?= htmlspecialchars(substr($env_key, 0, 6) . str_repeat('•', 12) . substr($env_key, -4)) ?>" readonly style="font-family: monospace;">
                            <small style="display: block; margin-top: 6px; font-size: 12px; color: #64748b;">Lưu trong file <code>.env</code> (PAYMENT_WEBHOOK_KEY). Đổi key: sửa file .env.</small>
                        <?php else: ?>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($settings['payment_webhook_key'] ?? '') ?>" readonly onclick="this.select()" style="font-family: monospace;">
                            <label style="display: flex; gap: 8px; align-items: center; font-size: 12px; color: #475569; margin-top: 6px; cursor: pointer;">
                                <input type="checkbox" name="regenerate_webhook_key" value="1"> Tạo key mới
                            </label>
                        <?php endif; ?>
                    </div>
                </div>

                <button type="submit" class="btn-save" style="position: sticky; top: 20px; box-shadow: 0 4px 12px rgba(59,130,246,0.3);"><i class="fa-solid fa-floppy-disk"></i> LƯU TOÀN BỘ CÀI ĐẶT</button>
            </div>
        </div>
    </form>
</div>

<script>
(function () {
    const code = document.getElementById('set_bank_code');
    const no = document.getElementById('set_bank_no');
    const name = document.getElementById('set_bank_name');
    const img = document.getElementById('set_qr_preview');
    function refresh() {
        const acc = no.value.replace(/\s+/g, '');
        if (!acc) { img.removeAttribute('src'); return; }
        img.src = `https://img.vietqr.io/image/${code.value}-${encodeURIComponent(acc)}-compact.png?amount=10000&addInfo=TEST&accountName=${encodeURIComponent(name.value.toUpperCase())}`;
    }
    code.addEventListener('change', refresh);
    no.addEventListener('change', refresh);
    name.addEventListener('change', refresh);
    refresh();
})();
</script>