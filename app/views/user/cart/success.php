<link rel="stylesheet" href="/lego_shop_php/public/assets/css/checkout.css?v=<?= time() ?>">

<style>
    /* CSS dành riêng cho trang thông báo thành công */
    .success-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 15px;
    }
    
    .success-box {
        background: #fff;
        width: 100%;
        max-width: 650px;
        text-align: center;
        padding: 50px 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
    }

    .success-icon {
        font-size: 80px;
        color: #2f9e44; /* Màu xanh lá báo thành công */
        margin-bottom: 25px;
        animation: scaleUp 0.5s ease-out;
    }

    .success-title {
        font-size: 28px;
        font-weight: 800;
        color: #333;
        margin-bottom: 15px;
    }

    .order-number-box {
        background: #fff5f5;
        border: 1px dashed #ffc9c9;
        padding: 15px;
        border-radius: 8px;
        display: inline-block;
        margin-bottom: 20px;
    }

    .order-number-box span {
        font-size: 15px;
        color: #666;
    }

    .order-number-box strong {
        font-size: 22px;
        color: #e03131;
        display: block;
        margin-top: 5px;
    }

    .success-desc {
        font-size: 15px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 35px;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
    }

    .btn-outline-red {
        padding: 14px 28px;
        border: 2px solid #e03131;
        color: #e03131;
        background: transparent;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-outline-red:hover {
        background: #fff5f5;
    }

    .btn-solid-red {
        padding: 14px 28px;
        background: #e03131;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-solid-red:hover {
        background: #c92a2a;
        transform: translateY(-2px);
    }

    @keyframes scaleUp {
        0% { transform: scale(0.5); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 576px) {
        .action-buttons { flex-direction: column; }
        .btn-outline-red, .btn-solid-red { width: 100%; }
    }
</style>

<div class="checkout-page-wrapper">
    <div class="checkout-container">
        <?php $current_step = 4; require __DIR__ . '/../../components/checkout_progress.php'; ?>

        <div class="success-wrapper">
            <div class="success-box">
                <div class="success-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                
                <h2 class="success-title">Đặt hàng thành công!</h2>
                
                <div class="order-number-box">
                    <span>Mã đơn hàng của bạn</span>
                    <strong>#<?= htmlspecialchars($order_id ?? 'N/A') ?></strong>
                </div>
                
                <p class="success-desc">
                    Cảm ơn bạn đã mua sắm tại <strong>LEGO WORLD STORE</strong>.<br>
                    Hệ thống đã ghi nhận đơn hàng. Chúng tôi sẽ sớm liên hệ với bạn để xác nhận và giao hàng trong thời gian sớm nhất.
                </p>

                <div class="action-buttons">
                    <a href="/lego_shop_php/home" class="btn-outline-red">
                        <i class="fa-solid fa-house"></i> Về trang chủ
                    </a>
                    <a href="/lego_shop_php/checkout/view_order?order_id=<?= $order_id ?>" class="btn-solid-red">Xem lại đơn hàng </a>
                </div>
            </div>
        </div>
    </div>
</div>