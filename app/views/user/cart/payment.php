<?php
// Hàm định dạng tiền tệ VND
if (!function_exists('format_vnd')) {
    function format_vnd($amount) {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

// Đảm bảo có dữ liệu từ Controller truyền sang
$current_order_id = $order_id ?? 0;
$current_total_price = $total_price ?? 0;

// Link tạo mã VietQR tự động (Thay MB bằng ngân hàng của bạn, 0961589023 là STK)
$qr_code_url = "/lego_shop_php/public/assets/images/qrcode.png"; // Thay bằng link tạo mã QR động nếu có
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán chuyển khoản - LEGO World Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* --- CSS STYLES --- */
        :root {
            --primary-red: #c92a2a;
            --primary-red-hover: #a4161a;
            --gray-bg: #f9f9f9;
            --white-bg: #ffffff;
            --text-dark: #333333;
            --text-gray: #666666;
            --border-color: #eeeeee;
        }

        body {
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--gray-bg);
            color: var(--text-dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* --- Thanh tiến trình --- */
        .progress-bar-container { display: flex; justify-content: center; margin-bottom: 50px; }
        .progress-bar { display: flex; align-items: center; }
        .progress-step { display: flex; flex-direction: column; align-items: center; position: relative; text-align: center; width: 160px; }
        .step-number { width: 32px; height: 32px; border-radius: 50%; background-color: #e6e6e6; color: #999; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 15px; margin-bottom: 10px; z-index: 1; }
        .step-label { font-size: 14px; color: #999; font-weight: 500; }
        
        .progress-step.completed .step-number, .progress-step.active .step-number { background-color: var(--primary-red); color: white; }
        .progress-step.completed .step-label, .progress-step.active .step-label { color: var(--text-dark); font-weight: 600; }
        
        .progress-line { height: 2px; background-color: #e6e6e6; width: 128px; margin: 0 -16px; position: absolute; top: 16px; left: 50%; }
        .progress-step.completed .progress-line { background-color: var(--primary-red); }
        .progress-step:last-child .progress-line { display: none; }

        /* --- Header --- */
        .page-header { text-align: center; margin-bottom: 50px; }
        .page-title { font-size: 36px; color: var(--primary-red); font-weight: 800; margin-bottom: 10px; margin-top: 0; }
        .page-description { color: var(--text-gray); font-size: 16px; margin: 0; }

        /* --- Bố cục 2 cột --- */
        .checkout-layout { display: flex; gap: 40px; justify-content: center; }
        .column { flex: 1; background-color: var(--white-bg); padding: 40px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0,0,0,0.03); display: flex; flex-direction: column; }
        .section-title { font-size: 18px; color: var(--primary-red); font-weight: 700; margin-top: 0; margin-bottom: 25px; }

        /* --- Chọn ngân hàng --- */
        .bank-selection { display: flex; gap: 15px; margin-bottom: 35px; }
        .bank-option { border: 1px solid var(--border-color); border-radius: 8px; padding: 10px; cursor: pointer; display: flex; align-items: center; justify-content: center; min-width: 100px; min-height: 45px; transition: all 0.2s ease; }
        .bank-option:hover { border-color: #ccc; }
        .bank-option.active { border-color: var(--primary-red); box-shadow: 0 0 0 1px var(--primary-red); }
        .bank-option span { font-weight: 700; font-size: 16px; }

        /* --- Tóm tắt chuyển khoản --- */
        .summary-info { font-size: 15px; line-height: 1.8; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; border-bottom: 1px solid #f1f1f1; padding: 15px 0; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { color: var(--text-gray); font-weight: 500; }
        .summary-value { color: var(--text-dark); font-weight: 600; text-align: right; }

        /* --- QR Code Section --- */
        .qr-code-section { text-align: center; margin-top: 20px; margin-bottom: 30px; }
        .qr-code-img { max-width: 280px; height: auto; border-radius: 12px; border: 1px solid #eee; padding: 10px; }

        /* --- Nút Đã thanh toán --- */
        .paid-button-container { margin-top: auto; text-align: center; }
        .paid-button { background-color: var(--primary-red); color: white; border: none; border-radius: 8px; padding: 18px 0; font-size: 17px; font-weight: 700; cursor: pointer; width: 100%; transition: background-color 0.2s; }
        .paid-button:hover { background-color: var(--primary-red-hover); }

        /* --- Responsive --- */
        @media (max-width: 1024px) { .container { max-width: 95%; } }
        @media (max-width: 992px) {
            .checkout-layout { flex-direction: column; align-items: center; gap: 20px; }
            .column { width: 100%; max-width: 600px; box-sizing: border-box; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="progress-bar-container">
            <div class="progress-bar">
                <div class="progress-step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Giỏ hàng</div>
                    <div class="progress-line"></div>
                </div>
                <div class="progress-step completed">
                    <div class="step-number">2</div>
                    <div class="step-label">Thanh toán</div>
                    <div class="progress-line"></div>
                </div>
                <div class="progress-step active">
                    <div class="step-number">3</div>
                    <div class="step-label">Chuyển khoản</div>
                    <div class="progress-line"></div>
                </div>
                <div class="progress-step">
                    <div class="step-number">4</div>
                    <div class="step-label">Thành công</div>
                    <div class="progress-line"></div>
                </div>
                <div class="progress-step">
                    <div class="step-number">5</div>
                    <div class="step-label">Xem lại</div>
                </div>
            </div>
        </div>

        <div class="page-header">
            <h1 class="page-title">Thanh toán chuyển khoản</h1>
            <p class="page-description">Vui lòng dùng App Ngân hàng quét mã QR để thanh toán đơn hàng.</p>
        </div>

        <div class="checkout-layout">
            <div class="column">
                <h2 class="section-title">1. Chọn ngân hàng</h2>
                <div class="bank-selection">
                    <div class="bank-option active" onclick="selectBank(this)">
                        <span style="color: #0056a0">MB Bank</span>
                    </div>
                </div>

                <h2 class="section-title" style="margin-top: 10px;">2. Tóm tắt chuyển khoản</h2>
                <div class="summary-info">
                    <div class="summary-row">
                        <span class="summary-label">Người nhận</span>
                        <span class="summary-value">NGUYEN HOANG</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Số tài khoản</span>
                        <span class="summary-value">0961589023 (MB Bank)</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Nội dung chuyển khoản</span>
                        <span class="summary-value" style="color: var(--primary-red);">LEGO<?= htmlspecialchars($current_order_id) ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Số tiền cần chuyển</span>
                        <span class="summary-value" style="font-size: 18px; color: var(--primary-red);"><?= format_vnd($current_total_price) ?></span>
                    </div>
                </div>
                
                <p style="font-size: 14px; color: #888; text-align: center; margin-top: auto;">
                    <i>Lưu ý: Vui lòng nhập đúng số tiền và nội dung chuyển khoản để hệ thống tự động xác nhận.</i>
                </p>
            </div>

            <div class="column" style="justify-content: center;">
                <h2 class="section-title" style="text-align: center; margin-bottom: 10px;">3. Quét mã QR để thanh toán</h2>
                <div class="qr-code-section">
                    <img src="<?= $qr_code_url ?>" alt="Mã QR Chuyển Khoản" class="qr-code-img">
                </div>

                <div class="paid-button-container">
                    <button type="button" class="paid-button" onclick="confirmPayment(<?= htmlspecialchars($current_order_id) ?>)">TÔI ĐÃ CHUYỂN KHOẢN XONG</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectBank(element) {
            const options = document.querySelectorAll('.bank-option');
            options.forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');
        }

        // Fix lỗi truyền thiếu order_id
        function confirmPayment(orderId) {
            if (!orderId || orderId === 0) {
                alert("Lỗi: Không tìm thấy mã đơn hàng!");
                return;
            }
            // Chuyển hướng chuẩn xác mang theo mã đơn hàng
            window.location.href = '/lego_shop_php/checkout/success?order_id=' + orderId;
        }
    </script>
</body>
</html>