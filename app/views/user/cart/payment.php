<?php
// --- GIẢ ĐỊNH DỮ LIỆU ĐƠN HÀNG ---
// Trong thực tế, dữ liệu này sẽ được lấy từ Session hoặc Database sau khi người dùng tạo đơn hàng
$order_data = [
    'order_id' => 1025,
    'total_amount' => 1200000,
    'receiver_name' => 'LEGO WORLD STORE',
    'receiver_account' => '1234 5678 9999',
    'receiver_bank' => 'Ngân hàng ACB',
    'transfer_content' => 'THANH TOAN DON #1025',
    'qr_code_image' => 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=vietqr%3A%2F%2F%2F%3Fservice%3Dvietqr%26version%3D1.1%26payType%3D2%26merchantName%3DLEGO%2BWORLD%2BSTORE%26merchantId%3DACB%26merchantAccount%3D123456789999%26description%3DTHANHTOANDON%25201025%26amount%3D1200000%26ccy%3DVND' 
    // Dùng QRServer tạo QR Code giả định cho ví dụ, bạn có thể thay bằng QR VietQR thật
];

// Hàm định dạng tiền tệ VND
function format_vnd($amount) {
    return number_format($amount, 0, ',', '.') . ' đ';
}
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

        /* Container căn giữa và giới hạn chiều rộng */
        .container {
            max-width: 1200px; /* Giới hạn chiều rộng trang */
            margin: 0 auto;     /* Căn giữa */
            padding: 20px;
        }

        /* --- Thanh tiến trình (Progress Bar) --- */
        .progress-bar-container {
            display: flex;
            justify-content: center;
            margin-bottom: 50px;
        }

        .progress-bar {
            display: flex;
            align-items: center;
        }

        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            text-align: center;
            width: 160px; /* Độ rộng mỗi bước */
        }

        .step-number {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #e6e6e6;
            color: #999;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 15px;
            margin-bottom: 10px;
            z-index: 1;
        }

        .step-label {
            font-size: 14px;
            color: #999;
            font-weight: 500;
        }

        /* Bước hiện tại (Active) */
        .progress-step.active .step-number {
            background-color: var(--primary-red);
            color: white;
        }

        .progress-step.active .step-label {
            color: var(--text-dark);
            font-weight: 600;
        }

        /* Đường line giữa các bước */
        .progress-line {
            height: 2px;
            background-color: #e6e6e6;
            width: 128px; /* Độ rộng đường line */
            margin: 0 -16px; /* Căn lề âm để nối liền */
            position: absolute;
            top: 16px; /* Căn giữa theo chiều dọc với số */
            left: 50%;
        }

        .progress-step:last-child .progress-line {
            display: none; /* Bước cuối không có line */
        }

        /* --- Page Header --- */
        .page-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .page-title {
            font-size: 36px;
            color: var(--primary-red);
            font-weight: 800;
            margin-bottom: 10px;
            margin-top: 0;
        }

        .page-description {
            color: var(--text-gray);
            font-size: 16px;
            margin: 0;
        }

        /* --- Bố cục 2 cột --- */
        .checkout-layout {
            display: flex;
            gap: 40px; /* Khoảng cách giữa 2 cột */
            justify-content: center;
        }

        .column {
            flex: 1;
            background-color: var(--white-bg);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.03); /* Đổ bóng nhẹ */
            display: flex;
            flex-direction: column; /* Để nút "Đã thanh toán" nằm dưới cùng */
        }

        .section-title {
            font-size: 18px;
            color: var(--primary-red);
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 25px;
        }

        /* --- Chọn ngân hàng --- */
        .bank-selection {
            display: flex;
            gap: 15px;
            margin-bottom: 35px;
        }

        .bank-option {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 100px;
            min-height: 45px;
            transition: all 0.2s ease;
        }

        .bank-option:hover {
            border-color: #ccc;
        }

        .bank-option.active {
            border-color: var(--primary-red);
            box-shadow: 0 0 0 1px var(--primary-red); /* Viền đỏ khi được chọn */
        }

        .bank-option span {
            font-weight: 700;
            font-size: 16px;
        }

        /* --- Form Inputs --- */
        .input-group {
            margin-bottom: 20px;
        }

        .input-field {
            width: 100%;
            padding: 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            color: var(--text-dark);
            box-sizing: border-box; /* Width 100% bao gồm cả padding */
            transition: border-color 0.2s;
        }

        .input-field:focus {
            border-color: #ccc;
            outline: none;
        }

        .input-field::placeholder {
            color: #b1b1b1;
        }

        /* --- Tóm tắt chuyển khoản (Cột 2) --- */
        .summary-info {
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 35px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #f1f1f1;
            padding: 12px 0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-label {
            color: var(--text-gray);
            font-weight: 500;
        }

        .summary-value {
            color: var(--text-dark);
            font-weight: 600;
            text-align: right;
        }

        /* --- QR Code Section --- */
        .qr-code-section {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .qr-code-img {
            max-width: 280px;
            height: auto;
            border-radius: 12px;
        }

        /* --- Nút Đã thanh toán --- */
        .paid-button-container {
            margin-top: auto; /* Đẩy xuống dưới cùng của cột */
            text-align: center;
        }

        .paid-button {
            background-color: var(--primary-red);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 18px 0;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s;
        }

        .paid-button:hover {
            background-color: var(--primary-red-hover);
        }

        /* --- Responsive --- */
        @media (max-width: 1024px) {
            .container {
                max-width: 95%; /* Cho màn hình nhỏ hơn */
            }
        }

        @media (max-width: 992px) {
            .checkout-layout {
                flex-direction: column; /* Chuyển thành 1 cột */
                align-items: center;
                gap: 20px;
            }
            .column {
                width: 100%;
                max-width: 600px; /* Giới hạn chiều rộng khi là 1 cột */
                box-sizing: border-box;
            }
            .paid-button-container {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="progress-bar-container">
            <div class="progress-bar">
                <div class="progress-step">
                    <div class="step-number">1</div>
                    <div class="step-label">Giỏ hàng</div>
                    <div class="progress-line"></div>
                </div>
                <div class="progress-step">
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
            <p class="page-description">Vui lòng chọn ngân hàng và thực hiện thanh toán theo hướng dẫn bên dưới.</p>
        </div>

        <div class="checkout-layout">
            <div class="column">
                <h2 class="section-title">1. Chọn ngân hàng</h2>
                <div class="bank-selection">
                    <div class="bank-option active" onclick="selectBank(this)">
                        <span style="color: #ae2070">momo</span>
                    </div>
                    <div class="bank-option" onclick="selectBank(this)">
                        <span style="color: #0056a0">VNPAY</span>
                    </div>
                    <div class="bank-option" onclick="selectBank(this)">
                        <span style="color: #0076a9">ZaloPay</span>
                    </div>
                </div>

                <h2 class="section-title">2. Thông tin người chuyển</h2>
                <form id="transfer-form">
                    <div class="input-group">
                        <input type="text" class="input-field" name="fullname" placeholder="Họ và tên người chuyển">
                    </div>
                    <div class="input-group">
                        <input type="text" class="input-field" name="account_number" placeholder="Số tài khoản / Số thẻ">
                    </div>
                    <div class="input-group">
                        <input type="text" class="input-field" name="bank_name" placeholder="Ngân hàng của bạn">
                    </div>
                    <div class="input-group">
                        <input type="text" class="input-field" id="amount" name="amount" placeholder="Số tiền chuyển (VD: 1.200.000 đ)" value="<?= format_vnd($order_data['total_amount']) ?>">
                    </div>
                </form>
            </div>

            <div class="column summary-column">
                <h2 class="section-title">Tóm tắt chuyển khoản</h2>
                <div class="summary-info">
                    <div class="summary-row">
                        <span class="summary-label">Người nhận</span>
                        <span class="summary-value"><?= $order_data['receiver_name'] ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Số tài khoản</span>
                        <span class="summary-value"><?= $order_data['receiver_account'] ?> (<?= $order_data['receiver_bank'] ?>)</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Nội dung chuyển khoản</span>
                        <span class="summary-value"><?= $order_data['transfer_content'] ?></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">Số tiền</span>
                        <span class="summary-value"><?= format_vnd($order_data['total_amount']) ?></span>
                    </div>
                </div>

                <h2 class="section-title">3. Quét mã QR để thanh toán</h2>
                <div class="qr-code-section">
                    <img src="<?= $order_data['qr_code_image'] ?>" alt="Mã QR Chuyển Khoản" class="qr-code-img">
                </div>

                <div class="paid-button-container">
                    <button type="button" class="paid-button" onclick="confirmPayment()">Đã thanh toán?</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectBank(element) {
            // Xóa class active khỏi các ngân hàng khác
            const options = document.querySelectorAll('.bank-option');
            options.forEach(opt => opt.classList.remove('active'));
            // Thêm class active cho ngân hàng được chọn
            element.classList.add('active');
        }

        function confirmPayment() {
            // Xử lý khi người dùng nhấn nút "Đã thanh toán?"
            // Thu thập dữ liệu từ form
            const form = document.getElementById('transfer-form');
            const formData = new FormData(form);
            const transfer_amount = formData.get('amount');

            // Hiển thị dữ liệu người dùng nhập
            alert("Đã nhận xác nhận từ người dùng.\nHọ tên: " + formData.get('fullname') + "\nSố tài khoản: " + formData.get('account_number') + "\nNgân hàng: " + formData.get('bank_name') + "\nSố tiền: " + transfer_amount);

            // Bước tiếp theo: Gửi dữ liệu này đến Backend qua AJAX để tạo yêu cầu xác nhận đơn hàng
            // fetch('xu_ly_thanh_toan.php', { method: 'POST', body: formData })
        }
    </script>
</body>
</html>