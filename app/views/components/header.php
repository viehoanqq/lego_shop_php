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

      <div class="search-bar">
        <input class="search-input" type="text" placeholder="Nhập từ khóa để tìm kiếm (ví dụ: lắp ráp, mô hình, ba lô,...)">
        <a href="/lego_shop_php/product/search"><button><i class="fa-solid fa-magnifying-glass"></i></button></a>
        <button type="button" class="advanced-search-trigger" id="openAdvancedSearch" title="Tìm kiếm nâng cao">
          <i class="fa-solid fa-sliders"></i>
        </button>
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
        <a href="/lego_shop_php/account/login" id="account-link">
          <i class="fa-solid fa-user"></i> <span id="name">Đăng nhập</span>
        </a>
        <a href="/lego_shop_php/cart" id="cart-link">
          <i class="fa-solid fa-cart-shopping"></i> Giỏ hàng (1)
        </a>
      </div>
    </div>

    <nav class="nav-bar">
      <ul>
        <li><a href="/lego_shop_php/home">TRANG CHỦ</a></li>
        <li><a href="/lego_shop_php/product">SẢN PHẨM</a></li>
        <li><a href="/lego_shop_php/new">HÀNG MỚI</a></li>
        <li><a href="/lego_shop_php/sale">KHUYẾN MÃI</a></li>
        <li><a href="/lego_shop_php/theme">CHỦ ĐỀ</a></li>
        <li><a href="/lego_shop_php/news">TIN TỨC</a></li>
      </ul>
    </nav>
  </header>

  <div class="main-content">