<?php
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
    // Gọi Model lấy cài đặt hệ thống
    require_once __DIR__ . '/../../models/SettingModel.php'; 
    $settingModel = new SettingModel();
    $settings = $settingModel->getSettings();

    // Gán biến mặc định
    $shop_name = $settings['shop_name'] ?? 'LEGO World Store';
    $logo_url  = $settings['logo_url'] ?? 'logo.png';
    
    $policy_1 = $settings['policy_1'] ?? 'Miễn phí giao hàng đơn từ 500k';
    $policy_2 = $settings['policy_2'] ?? 'Giao hàng hỏa tốc 4 tiếng';
    $policy_4 = $settings['policy_4'] ?? 'Mua hàng trả góp';
    $policy_5 = $settings['policy_5'] ?? 'Hệ thống 200 cửa hàng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ' . htmlspecialchars($shop_name) : htmlspecialchars($shop_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/global.css">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/components/header.css">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/components/footer.css">
    <link rel="stylesheet" href="/lego_shop_php/public/assets/css/components/productCard.css">
    <script src="/lego_shop_php/public/assets/js/main.js" defer></script>
</head>
<body>
  <header>
    <div class="top-bar">
      <?php if(!empty($policy_1)): ?><span><i class="fa-solid fa-truck-fast"></i> <?= htmlspecialchars($policy_1) ?></span><?php endif; ?>
      <?php if(!empty($policy_2)): ?><span><i class="fa-solid fa-bolt"></i> <?= htmlspecialchars($policy_2) ?></span><?php endif; ?>
      <?php if(!empty($policy_3)): ?><span><i class="fa-solid fa-gift"></i> <?= htmlspecialchars($policy_3) ?></span><?php endif; ?>
      <?php if(!empty($policy_4)): ?><span><i class="fa-solid fa-credit-card"></i> <?= htmlspecialchars($policy_4) ?></span><?php endif; ?>
      <?php if(!empty($policy_5)): ?><span><i class="fa-solid fa-store"></i> <?= htmlspecialchars($policy_5) ?></span><?php endif; ?>
    </div>

    <div class="main-header">
      <div class="logo">
        <a href="/lego_shop_php/home">
          <img src="/lego_shop_php/public/assets/images/<?= htmlspecialchars($logo_url) ?>" alt="<?= htmlspecialchars($shop_name) ?>" />
        </a>
      </div>

      <div class="search-bar" style="position: relative; display: flex; align-items: center;">
        <form action="/lego_shop_php/product/search" method="GET" style="display: flex; flex: 1;">
            <input class="search-input" name="keyword" type="text" id="liveSearchInput" autocomplete="off" placeholder="Nhập từ khóa (ví dụ: lắp ráp, mô hình...)">
            <button type="submit" class="normal-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        <button type="button" class="advanced-search-trigger" id="openAdvancedSearch" title="Tìm kiếm nâng cao">
          <i class="fa-solid fa-sliders"></i>
        </button>
        <div id="searchSuggestions" class="search-suggestions" style="display: none;"></div>
      </div>

      <div class="advanced-search-overlay" id="advancedSearchOverlay">
        <div class="advanced-search-modal">
          <div class="advanced-search-header">
            <h2><i class="fa-solid fa-search"></i> Tìm kiếm nâng cao</h2>
            <button type="button" class="close-popup" id="closeAdvancedSearch">&times;</button>
          </div>

          <form id="advancedSearchForm" class="advanced-search-form" action="/lego_shop_php/product/filter" method="GET">
            <div class="form-group">
              <label><i class="fa-solid fa-magnifying-glass"></i> Từ khóa</label>
              <input type="text" name="keyword" id="keyword" placeholder="Tên sản phẩm, mã SKU...">
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-layer-group"></i> Danh mục</label>
              <select name="category" id="category">
                <option value="all">Tất cả danh mục</option>
                <?php if(!empty($header_categories)): ?>
                    <?php foreach($header_categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-puzzle-piece"></i> Số mảnh ghép</label>
              <select name="pieces" id="pieces">
                <option value="">Mọi kích cỡ</option>
                <option value="0-500">Dưới 500 mảnh (Nhỏ)</option>
                <option value="500-1000">Từ 500 - 1.000 mảnh (Vừa)</option>
                <option value="1000-2000">Từ 1.000 - 2.000 mảnh (Lớn)</option>
                <option value="2000-5000">Từ 2.000 - 5.000 mảnh (Rất lớn)</option>
                <option value="5000-99999">Trên 5.000 mảnh (Siêu khổng lồ)</option>
              </select>
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-coins"></i> Khoảng giá (VNĐ)</label>
              <div class="price-slider-container">
                  <div class="price-slider-values">
                      <span id="priceMinValue">0đ</span> - <span id="priceMaxValue">10.000.000đ</span>
                  </div>
                  <div class="slider-track-wrapper">
                      <div class="slider-track"></div>
                      <div class="slider-highlight" id="sliderHighlight"></div>
                      <input type="range" name="min_price" id="rangeMin" min="0" max="10000000" step="100000" value="0">
                      <input type="range" name="max_price" id="rangeMax" min="0" max="10000000" step="100000" value="10000000">
                  </div>
              </div>
            </div>

            <div class="form-actions">
              <button type="reset" class="btn-reset">Xóa tất cả</button>
              <button type="submit" class="btn-submit">Tìm kiếm ngay</button>
            </div>
          </form>
        </div>
      </div>

      <div class="user-options">
        <?php if(isset($_SESSION['user_fullname'])): ?>
            <div class="user-dropdown-wrapper" style="position: relative; display: inline-block;">
                <a href="/lego_shop_php/profile" id="account-link">
                    <i class="fa-solid fa-user"></i> <span id="name"><?= htmlspecialchars($_SESSION['user_fullname']) ?></span>
                </a>
                <ul class="user-dropdown-menu">
                     <li><a href="/lego_shop_php/profile/edit"><i class="fa-solid fa-user-pen"></i> Xem trang cá nhân</a></li>
                    <li><a href="/lego_shop_php/wishlist"><i class="fa-solid fa-heart"></i> Sản phẩm yêu thích</a></li>
                    <li><a href="/lego_shop_php/account/logout" style="color: #dc3545;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                </ul>
            </div>
            
            <a href="/lego_shop_php/cart" id="cart-link">
              <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
            </a>

        <?php else: ?>
            <a href="/lego_shop_php/account/login" id="account-link">
                <i class="fa-solid fa-user"></i> <span id="name">Đăng nhập</span>
            </a>
            <a href="javascript:void(0);" id="cart-link" onclick="showToast('Bạn cần đăng nhập để xem giỏ hàng!', 'error'); setTimeout(() => window.location.href='/lego_shop_php/account/login', 1000);">
              <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng
            </a>
        <?php endif; ?>
      </div>
    </div>

    <nav class="nav-bar">
      <ul class="header-menu-ul" style="display: flex; gap: 30px; justify-content: center; padding: 15px 0; background-color: #a4161a; margin: 0;">
        <li><a href="/lego_shop_php/home" style="color: white; font-weight: 700;">TRANG CHỦ</a></li>
        <li><a href="/lego_shop_php/product" style="color: white; font-weight: 700;">SẢN PHẨM</a></li>
        
        <?php if(!empty($header_categories)): ?>
            <li style="position: relative;" class="dropdown-chu-de">
                <a href="#" style="color: white; font-weight: 700;">CHỦ ĐỀ <i class="fa-solid fa-chevron-down" style="font-size: 12px;"></i></a>
                <ul class="dropdown-menu" style="position: absolute; top: 100%; left: 0; background: white; width: auto; min-width: 200px; display: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 10px 0; z-index: 1000;">
                    <?php foreach($header_categories as $cat): ?>
                        <li>
                            <a href="/lego_shop_php/product/category/<?= $cat['id'] ?>" 
                               style="color: #333; display: block; padding: 10px 15px; text-transform: none; font-weight: 500;">
                                <?= htmlspecialchars($cat['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>
      </ul>
    </nav>
</header>
<style>
    /* Chống rớt dòng cụm Header */
    .main-header { display: flex; flex-wrap: nowrap !important; align-items: center; justify-content: space-between; gap: 15px; }
    .user-options { display: flex; align-items: center; gap: 15px; white-space: nowrap !important; flex-shrink: 0; }
    .search-bar { flex: 1; max-width: 500px; }

    /* --- DROPDOWN CHỦ ĐỀ --- */
    .dropdown-chu-de:hover .dropdown-menu { display: block !important; }
    .dropdown-menu li a { text-align: center !important; white-space: nowrap !important; transition: 0.2s; }
    .dropdown-menu li a:hover { background-color: #f8f9fa !important; color: #a4161a !important; }

    /* --- DROPDOWN USER (ĐĂNG NHẬP) --- */
    .user-dropdown-wrapper { padding: 0px 0; } 
    .user-dropdown-wrapper:hover .user-dropdown-menu { display: block; }
    .user-dropdown-menu { display: none; position: absolute; top: 100%; right: 0; background: white; min-width: 180px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); list-style: none; padding: 5px 0; z-index: 9999; border-radius: 4px; border: 1px solid #eaeaea; }
    .user-dropdown-menu li a { display: block; padding: 10px 15px; color: #333; text-decoration: none; font-size: 14px; font-weight: 500; transition: 0.2s; }
    .user-dropdown-menu li a i { width: 20px; text-align: center; margin-right: 5px; }
    .user-dropdown-menu li a:hover { background-color: #f8f9fa; color: #a4161a; }
    
    /* ==========================================
       CSS POPUP TÌM KIẾM NÂNG CAO (MODAL)
    ========================================== */
    .advanced-search-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0, 0, 0, 0.6); z-index: 10000; display: flex; align-items: center; justify-content: center; opacity: 0; visibility: hidden; transition: 0.3s ease; backdrop-filter: blur(3px); }
    .advanced-search-overlay.active { opacity: 1; visibility: visible; }
    .advanced-search-modal { background: #fff; width: 90%; max-width: 500px; border-radius: 12px; padding: 25px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); transform: translateY(-30px); transition: 0.3s ease; }
    .advanced-search-overlay.active .advanced-search-modal { transform: translateY(0); }
    
    .advanced-search-header { 
        display: flex; justify-content: space-between; align-items: center; 
        background-color: #a4161a; 
        padding: 15px 20px; 
        margin: -25px -25px 20px -25px; 
        border-radius: 12px 12px 0 0;
    }
    .advanced-search-header h2 { margin: 0; color: #ffffff; font-size: 18px; } 
    .close-popup { background: none; border: none; font-size: 24px; color: #ffffff; cursor: pointer; transition: 0.2s; padding: 0 5px;}
    .close-popup:hover { transform: scale(1.1); color: #f0f0f0; }
    
    .advanced-search-form .form-group { margin-bottom: 20px; }
    .advanced-search-form label { display: block; margin-bottom: 8px; font-weight: 600; color: #333; font-size: 14px; }
    .advanced-search-form input[type="text"], .advanced-search-form input[type="number"], .advanced-search-form select { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none; box-sizing: border-box; transition: border 0.3s; }
    .advanced-search-form input:focus, .advanced-search-form select:focus { border-color: #a4161a; }
    
    .form-actions { display: flex; justify-content: flex-end; gap: 15px; margin-top: 30px; }
    .btn-reset { background: #f8f9fa; border: 1px solid #ddd; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; color: #666; transition: 0.2s; }
    .btn-reset:hover { background: #e2e6ea; }
    .btn-submit { background: #a4161a; color: white; border: none; padding: 10px 25px; border-radius: 8px; cursor: pointer; font-weight: 600; transition: 0.2s; }
    .btn-submit:hover { background: #800f13; }

    /* --- CSS THANH TRƯỢT KÉO GIÁ (DUAL RANGE SLIDER) --- */
    .price-slider-container { width: 100%; margin-top: 10px; }
    .price-slider-values { font-weight: 700; color: #a4161a; margin-bottom: 15px; text-align: center; font-size: 15px; }
    
    .slider-track-wrapper { position: relative; width: 100%; height: 6px; background: #e0e0e0; border-radius: 5px; }
    .slider-highlight { position: absolute; height: 100%; background: #a4161a; border-radius: 5px; z-index: 1; left: 0%; width: 100%; }
    
    .slider-track-wrapper input[type="range"] {
        position: absolute; width: 100%; height: 6px; top: -1px;
        background: none; pointer-events: none; -webkit-appearance: none; outline: none; z-index: 2; margin: 0;
    }
    
    .slider-track-wrapper input[type="range"]::-webkit-slider-thumb {
        pointer-events: auto; -webkit-appearance: none; width: 20px; height: 20px;
        background: #fff; border: 3px solid #a4161a; border-radius: 50%; cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2); transition: transform 0.1s;
    }
    .slider-track-wrapper input[type="range"]::-webkit-slider-thumb:hover { transform: scale(1.15); }
    .slider-track-wrapper input[type="range"]::-moz-range-thumb {
        pointer-events: auto; width: 20px; height: 20px; background: #fff; border: 3px solid #a4161a; border-radius: 50%; cursor: pointer;
    }
</style>

<script>
    const IS_LOGGED_IN = <?= isset($_SESSION['user_fullname']) ? 'true' : 'false' ?>;

    document.addEventListener("DOMContentLoaded", function() {
        // --- 1. CODE ĐÓNG/MỞ POPUP TÌM KIẾM NÂNG CAO ---
        const openBtn = document.getElementById("openAdvancedSearch");
        const closeBtn = document.getElementById("closeAdvancedSearch");
        const overlay = document.getElementById("advancedSearchOverlay");

        if (openBtn && overlay) {
            openBtn.addEventListener("click", () => overlay.classList.add("active"));
        }
        if (closeBtn && overlay) {
            closeBtn.addEventListener("click", () => overlay.classList.remove("active"));
        }
        if (overlay) {
            overlay.addEventListener("click", (e) => {
                if (e.target === overlay) overlay.classList.remove("active");
            });
        }

        // --- 2. CODE XỬ LÝ THANH TRƯỢT KÉO GIÁ ---
        const rangeMin = document.getElementById('rangeMin');
        const rangeMax = document.getElementById('rangeMax');
        const valMin = document.getElementById('priceMinValue');
        const valMax = document.getElementById('priceMaxValue');
        const highlight = document.getElementById('sliderHighlight');
        const priceGap = 100000; // Khoảng cách tối thiểu là 100k

        function updateSlider() {
            if (!rangeMin || !rangeMax) return;
            
            let minVal = parseInt(rangeMin.value);
            let maxVal = parseInt(rangeMax.value);

            // Chống 2 cục kéo chạy vượt qua nhau
            if (maxVal - minVal < priceGap) {
                if (document.activeElement === rangeMin) {
                    rangeMin.value = maxVal - priceGap;
                    minVal = parseInt(rangeMin.value);
                } else {
                    rangeMax.value = minVal + priceGap;
                    maxVal = parseInt(rangeMax.value);
                }
            }

            // Định dạng số tiền sang chuẩn VNĐ
            if (valMin) valMin.textContent = new Intl.NumberFormat('vi-VN').format(minVal) + 'đ';
            if (valMax) valMax.textContent = new Intl.NumberFormat('vi-VN').format(maxVal) + 'đ';

            // Tô màu đoạn giữa thanh kéo
            if (highlight) {
                let percentMin = (minVal / rangeMin.max) * 100;
                let percentMax = (maxVal / rangeMax.max) * 100;
                highlight.style.left = percentMin + "%";
                highlight.style.width = (percentMax - percentMin) + "%";
            }
        }

        // Lắng nghe sự kiện kéo (input)
        if (rangeMin && rangeMax) {
            rangeMin.addEventListener('input', updateSlider);
            rangeMax.addEventListener('input', updateSlider);
            updateSlider(); // Gọi hàm 1 lần lúc load trang
        }
    });
</script>