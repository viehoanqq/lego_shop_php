<?php
// Bắt biến $current_step từ trang gọi đến. Nếu không có thì mặc định là bước 1.
$step = $current_step ?? 1;
?>

<div class="checkout-progress">
    <div class="step <?= $step >= 1 ? 'active' : '' ?>">
        <div class="step-circle">1</div>
        <div class="step-text">Giỏ hàng</div>
    </div>
    <div class="step-line <?= $step >= 2 ? 'active-line' : '' ?>"></div>
    
    <div class="step <?= $step >= 2 ? 'active' : '' ?>">
        <div class="step-circle">2</div>
        <div class="step-text">Thanh toán</div>
    </div>
    <div class="step-line <?= $step >= 3 ? 'active-line' : '' ?>"></div>
    
    <div class="step <?= $step >= 3 ? 'active' : '' ?>">
        <div class="step-circle">3</div>
        <div class="step-text">Chuyển khoản</div>
    </div>
    <div class="step-line <?= $step >= 4 ? 'active-line' : '' ?>"></div>
    
    <div class="step <?= $step >= 4 ? 'active' : '' ?>">
        <div class="step-circle">4</div>
        <div class="step-text">Thành công</div>
    </div>
    <div class="step-line <?= $step >= 5 ? 'active-line' : '' ?>"></div>
    
    <div class="step <?= $step >= 5 ? 'active' : '' ?>">
        <div class="step-circle">5</div>
        <div class="step-text">Xem lại đơn hàng</div>
    </div>
</div>

<style>
    /* CSS gom riêng cho thanh tiến trình để dùng ở mọi trang */
    .checkout-progress { display: flex; justify-content: center; align-items: center; margin-bottom: 40px; margin-top: 20px; }
    .step { display: flex; flex-direction: column; align-items: center; position: relative; width: 120px; }
    
    .step-circle { width: 36px; height: 36px; border-radius: 50%; background-color: #fff; border: 2px solid #ddd; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 16px; margin-bottom: 8px; z-index: 2; transition: 0.3s; }
    .step-text { font-size: 13px; color: #555; font-weight: 500; text-align: center; transition: 0.3s; }
    
    /* Trạng thái Active (Hoàn thành hoặc Đang ở bước này) */
    .step.active .step-circle { background-color: #c92a2a; border-color: #c92a2a; color: #fff; }
    .step.active .step-text { color: #333; font-weight: 700; }
    
    /* Đường nối giữa các bước */
    .step-line { flex: 1; height: 3px; background-color: #ddd; margin-top: -24px; max-width: 80px; z-index: 1; transition: 0.3s; }
    .step-line.active-line { background-color: #c92a2a; } /* Đổi màu đỏ nếu đã qua bước này */
</style>