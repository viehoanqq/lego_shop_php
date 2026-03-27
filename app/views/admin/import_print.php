<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>In Phiếu Nhập #<?= $receipt['id'] ?></title>
    <style>
        /* CSS Cơ bản cho tờ giấy A4 */
        body { font-family: 'Times New Roman', serif; color: #000; background: #fff; line-height: 1.5; font-size: 15px; margin: 0; padding: 20px; }
        .invoice-box { max-width: 800px; margin: auto; }
        
        /* Header hóa đơn */
        .invoice-header { text-align: center; margin-bottom: 30px; border-bottom: 2px dashed #000; padding-bottom: 20px; }
        .invoice-header h2 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .invoice-header h1 { margin: 15px 0 5px 0; font-size: 26px; }
        
        /* Bảng dữ liệu */
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th, td { border: 1px solid #000; padding: 10px; text-align: center; }
        th { font-weight: bold; background-color: #f9f9f9 !important; -webkit-print-color-adjust: exact; }
        td.text-left { text-align: left; }
        td.text-right { text-align: right; }
        
        /* Chữ ký */
        .signatures { display: flex; justify-content: space-between; margin-top: 40px; text-align: center; }
        .sig-box { flex: 1; }
        
        /* Ẩn mọi thứ đi nếu xem trên màn hình nhỏ, chỉ tối ưu cho việc in */
        @media print {
            @page { margin: 1.5cm; }
            body { padding: 0; }
        }
    </style>
</head>
<body onload="window.print();">

<div class="invoice-box">
    <div class="invoice-header">
        <h2>CỬA HÀNG ĐỒ CHƠI LEGO CHÍNH HÃNG</h2>
        <p style="margin: 5px 0;">Địa chỉ: 123 Đường ABC, Quận XYZ, TP.HCM | MST: 0123456789</p>
        <h1>PHIẾU NHẬP KHO</h1>
        <p style="margin: 0;">Mã phiếu: <strong>#PN-<?= $receipt['id'] ?></strong> | Ngày nhập: <?= date('d/m/Y', strtotime($receipt['created_at'])) ?></p>
        <p style="margin: 5px 0 0 0;">Nhà cung cấp: <strong><?= htmlspecialchars($receipt['supplier_name']) ?></strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">STT</th>
                <th>Sản phẩm</th>
                <th style="width: 100px;">Số lượng</th>
                <th style="width: 150px;">Đơn giá</th>
                <th style="width: 150px;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php $stt = 1; foreach($receipt_details as $item): ?>
            <tr>
                <td><?= $stt++ ?></td>
                <td class="text-left">
                    <strong><?= htmlspecialchars($item['product_name']) ?></strong><br>
                    <span style="font-size: 13px;">SKU: <?= htmlspecialchars($item['sku']) ?></span>
                </td>
                <td><?= number_format($item['quantity']) ?></td>
                <td class="text-right"><?= number_format($item['price'], 0, ',', '.') ?>đ</td>
                <td class="text-right"><strong><?= number_format($item['quantity'] * $item['price'], 0, ',', '.') ?>đ</strong></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" class="text-right" style="font-size: 18px;"><strong>TỔNG TIỀN THANH TOÁN:</strong></td>
                <td class="text-right" style="font-size: 18px;"><strong><?= number_format($receipt['total_amount'], 0, ',', '.') ?>đ</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-box">
            <p style="margin: 0; font-weight: bold;">Người giao hàng</p>
            <p style="margin: 5px 0 0 0; font-style: italic;">(Ký, ghi rõ họ tên)</p>
        </div>
        <div class="sig-box">
            <p style="margin: 0; font-weight: bold;">Thủ kho</p>
            <p style="margin: 5px 0 0 0; font-style: italic;">(Ký, ghi rõ họ tên)</p>
        </div>
        <div class="sig-box">
            <p style="margin: 0 0 10px 0; font-style: italic;">Ngày <?= date('d') ?> tháng <?= date('m') ?> năm <?= date('Y') ?></p>
            <p style="margin: 0; font-weight: bold;">Người lập phiếu</p>
            <p style="margin: 5px 0 0 0; font-style: italic;">(Ký, ghi rõ họ tên)</p>
            <div style="height: 80px;"></div>
            <p style="font-weight: bold; text-transform: uppercase;"><?= htmlspecialchars($receipt['admin_name']) ?></p>
        </div>
    </div>
</div>

</body>
</html>