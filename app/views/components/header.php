<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'LEGO World Store' ?></title>
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
      <span><i class="fa-solid fa-truck-fast"></i> Miễn phí giao hàng đơn từ 500k</span>
      <span><i class="fa-solid fa-bolt"></i> Giao hàng hỏa tốc 4 tiếng</span>
      <span><i class="fa-solid fa-gift"></i> Chương trình thành viên</span>
      <span><i class="fa-solid fa-credit-card"></i> Mua hàng trả góp</span>
      <span><i class="fa-solid fa-store"></i> Hệ thống 200 cửa hàng</span>
    </div>

    <div class="main-header">
      <div class="logo">
        <a href="/lego_shop_php/home">
          <img src="/lego_shop_php/public/assets/images/logo.png" alt="LEGO World Store" />
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
            <button type="button" class="close-popup" id="closeAdvancedSearch">×</button>
          </div>

          <form id="advancedSearchForm" class="advanced-search-form">
            <div class="form-group">
              <label><i class="fa-solid fa-magnifying-glass"></i> Từ khóa</label>
              <input type="text" id="keyword" placeholder="Tên sản phẩm, mã SKU, nhân vật...">
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-layer-group"></i> Danh mục</label>
              <select id="category">
                <option value="">Tất cả danh mục</option>
                <option value="xe-o-to">Xe ô tô mô hình</option>
                <option value="tau-vu-tru">Tàu vũ trụ</option>
                <option value="lau-dai">Lâu đài cổ tích</option>
                <option value="robot">Robot chiến binh</option>
                <option value="thanh-pho">Thành phố hiện đại</option>
                <option value="dong-vat">Động vật hoang dã</option>
                <option value="sieu-anh-hung">Siêu anh hùng</option>
                <option value="kien-truc">Kiến trúc nổi tiếng</option>
              </select>
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-child"></i> Độ tuổi</label>
              <select id="age">
                <option value="">Mọi lứa tuổi</option>
                <option value="4">4+</option>
                <option value="6">6+</option>
                <option value="8">8+</option>
                <option value="10">10+</option>
                <option value="12">12+</option>
                <option value="16">16+</option>
                <option value="18">18+ (Adult)</option>
              </select>
            </div>

            <div class="form-group">
              <label><i class="fa-solid fa-coins"></i> Khoảng giá (VNĐ)</label>
              <div class="price-range-input">
                <input type="number" id="priceMin" placeholder="Từ (VD: 500000)" min="0">
                <span>→</span>
                <input type="number" id="priceMax" placeholder="Đến (VD: 5000000)" min="0">
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
                    <li><a href="/lego_shop_php/profile/orders"><i class="fa-solid fa-box-open"></i> Đơn hàng</a></li>
                    <li><a href="/lego_shop_php/profile/edit"><i class="fa-solid fa-user-pen"></i> Sửa trang cá nhân</a></li>
                    <li><a href="/lego_shop_php/account/logout" style="color: #dc3545;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a href="/lego_shop_php/account/login" id="account-link">
                <i class="fa-solid fa-user"></i> <span id="name">Đăng nhập</span>
            </a>
        <?php endif; ?>

        <a href="/lego_shop_php/cart" id="cart-link">
          <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng (1)
        </a>
      </div>
    </div>

    <nav class="nav-bar">
      <ul class="header-menu-ul" style="display: flex; gap: 30px; justify-content: center; padding: 15px 0; background-color: #a4161a;">
    <li><a href="/lego_shop_php/home" style="color: white; font-weight: 700;">TRANG CHỦ</a></li>
    <li><a href="/lego_shop_php/product" style="color: white; font-weight: 700;">SẢN PHẨM</a></li>
    
    <?php if(!empty($header_categories)): ?>
        <li style="position: relative;" class="dropdown-chu-de">
            <a href="#" style="color: white; font-weight: 700;">CHỦ ĐỀ <i class="fa-solid fa-chevron-down" style="font-size: 12px;"></i></a>
            
            <ul class="dropdown-menu" style="position: absolute; top: 100%; left: 0; background: white; width: auto; min-width: 200px; display: none; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 10px 0;">
                <?php foreach($header_categories as $cat): ?>
                    <li>
                        <a href="/lego_shop_php/product/category/<?= $cat['id'] ?>" style="color: #333; display: block; padding: 10px 15px; text-transform: none; font-weight: 500;">
                            <?= htmlspecialchars($cat['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endif; ?>

</ul>

<style>
    /* Chống rớt dòng cụm Header */
    .main-header { display: flex; flex-wrap: nowrap !important; align-items: center; justify-content: space-between; gap: 15px; }
    .user-options { display: flex; align-items: center; gap: 15px; white-space: nowrap !important; flex-shrink: 0; }
    .search-bar { flex: 1; max-width: 500px; }

    /* --- DROPDOWN CHỦ ĐỀ --- */
    .dropdown-chu-de:hover .dropdown-menu {
        display: block !important;
    }
    .dropdown-menu li a {
        text-align: center !important; /* CĂN GIỮA CHỮ */
        white-space: nowrap !important; /* CẤM BẺ CHỮ LEGO ARCHITECTURE */
        transition: 0.2s;
    }
    .dropdown-menu li a:hover {
        background-color: #f8f9fa !important;
        color: #a4161a !important;
    }

    /* --- DROPDOWN USER (ĐĂNG NHẬP) --- */
    .user-dropdown-wrapper { padding: 0px 0; } /* Tạo vùng đệm để hover không bị ngắt */
    .user-dropdown-wrapper:hover .user-dropdown-menu {
        display: block;
    }
    .user-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%; /* Đẩy xuống dưới chữ tên */
        right: 0;  /* Canh lề phải */
        background: white;
        min-width: 180px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        list-style: none;
        padding: 5px 0;
        z-index: 9999;
        border-radius: 4px;
        border: 1px solid #eaeaea;
    }
    .user-dropdown-menu li a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: 0.2s;
    }
    .user-dropdown-menu li a i {
        width: px;
        text-align: center;
        margin-right: 5px;
    }
    .user-dropdown-menu li a:hover {
        background-color: #f8f9fa;
        color: #a4161a;
    }
    
</style>
    </nav>
  </header>

  <div class="main-content">