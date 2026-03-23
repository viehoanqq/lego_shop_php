
### LEGO WORLD STORE - PHP MVC PROJECT

Chào mừng bạn đến với dự án Website bán đồ chơi Lego được xây dựng bằng PHP theo mô hình MVC (Model-View-Controller). Dự án tập trung vào trải nghiệm người dùng và quy trình quản lý kho hàng.

## Cấu trúc thư mục (Project Structure)
```plaintext
lego_shop_php/
├── app/                    # Thư mục chính chứa logic ứng dụng
│   ├── core/               # Các file hệ thống cốt lõi (`App.php`, `Controller.php`, `Database.php`)
│   ├── controllers/        # Xử lý logic và điều hướng (ví dụ: `HomeController`, `AccountController`)
│   ├── models/             # Tương tác với Database (ví dụ: `ProductModel`, `AccountModel`)
│   └── views/              # Giao diện hiển thị (HTML/PHP)
│       ├── user/           # Giao diện khách hàng (home, login, register,...)
│       ├── admin/          # Giao diện quản trị (dashboard, products,...)
│       └── components/     # Thành phần dùng chung (header, footer, sidebar)
├── public/                 # Thư mục công khai (truy cập từ trình duyệt)
│   ├── assets/             # Tài nguyên tĩnh
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── fonts/
│   └── .htaccess           # Cấu hình URL thân thiện
├── .htaccess               # Rewrite tất cả yêu cầu về `index.php`
├── index.php               # Entry point (gọi `session_start()` và khởi `App`)
└── lego_shop.sql           # SQL schema để import vào MySQL
```
## Yêu cầu hệ thống (Prerequisites)

- XAMPP (PHP 7.4 hoặc 8.x)
- Trình duyệt: Chrome, Edge hoặc Firefox
- Công cụ quản lý DB: phpMyAdmin (hoặc MySQL client)

## Hướng dẫn cài đặt và chạy (Setup Instructions)

### Bước 1: Sao chép dự án
- Tải mã nguồn và giải nén vào thư mục `htdocs` của XAMPP (ví dụ `C:\xampp\htdocs`).
- Đặt tên thư mục là `lego_shop_php`.

### Bước 2: Import database
- Khởi động Apache và MySQL trên XAMPP.
- Mở `http://localhost/phpmyadmin`.
- Tạo database mới tên `lego_shop`.
- Vào tab Import và chọn file `database.sql` để nạp dữ liệu.

### Bước 3: Cấu hình kết nối database
- Mở file `app/core/Database.php` và kiểm tra các biến kết nối:

```php
private $db_name = "lego_shop"; // Tên database
private $username = "root";     // Tên user DB
private $password = "";         // Mật khẩu DB (nếu có)
```

Chỉnh sửa nếu bạn dùng tên database hoặc thông tin kết nối khác.

### Bước 4: Chạy ứng dụng
- Mở trình duyệt và truy cập `http://localhost/lego_shop_php/`.

## Các tính năng nổi bật

- Kiến trúc MVC giúp tách rõ View, Controller và Model.

## Lưu ý quan trọng

- Nếu gặp lỗi 404 khi chuyển trang, kiểm tra `mod_rewrite` đã bật và `.htaccess` hoạt động.
- Đảm bảo `session_start();` được gọi ở đầu `index.php`.

