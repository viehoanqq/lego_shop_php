# LEGO World Store

Website bán đồ chơi LEGO viết bằng **PHP thuần theo mô hình MVC**, chạy trên XAMPP (Apache + MySQL/MariaDB).

**Khách hàng:**
- Xem sản phẩm, tìm kiếm / lọc, yêu thích, giỏ hàng.
- Đặt hàng, dùng **mã giảm giá**, thanh toán **COD** hoặc **chuyển khoản VietQR** (tự xác nhận qua webhook ngân hàng).
- Theo dõi đơn, hủy đơn, **trả hàng / hoàn tiền**, đánh giá sản phẩm.

**Quản trị:**
- Sản phẩm, danh mục, giá bán.
- Nhà cung cấp, phiếu nhập, tồn kho.
- Đơn hàng, khách hàng, đánh giá.
- Mã giảm giá, hoàn tiền, giao dịch VietQR.
- Báo cáo, cài đặt cửa hàng.

Giao diện dùng được trên cả máy tính và điện thoại.

---

## Mục lục

1. [Yêu cầu](#1-yêu-cầu)
2. [Cài đặt và chạy](#2-cài-đặt-và-chạy)
3. [Tài khoản và đường dẫn](#3-tài-khoản-và-đường-dẫn)
4. [Cấu hình `.env`](#4-cấu-hình-env)
5. [Thanh toán VietQR](#5-thanh-toán-vietqr)
6. [Chạy test](#6-chạy-test)
7. [Cấu trúc thư mục](#7-cấu-trúc-thư-mục)
8. [Xử lý sự cố](#8-xử-lý-sự-cố)
9. [Đưa lên server thật](#9-đưa-lên-server-thật)
10. [Tài liệu khác](#10-tài-liệu-khác)

---

## 1. Yêu cầu

| Thành phần | Phiên bản |
|---|---|
| [XAMPP](https://www.apachefriends.org/) | Bản có **PHP 8.1 trở lên** (đã chạy trên XAMPP 8.2.12: PHP 8.2, MariaDB 10.4) |
| Apache module | `mod_rewrite` (XAMPP bật sẵn) |
| PHP extension | `mysqli`, `fileinfo`, `mbstring` (XAMPP bật sẵn) |
| Trình duyệt | Chrome, Edge hoặc Firefox bản mới |

Không cần Composer hay Node.js.

---

## 2. Cài đặt và chạy

> **Quan trọng:** đường dẫn `/lego_shop_php/` được dùng cố định trong code (`.htaccess`, link, form). Thư mục dự án **phải tên là `lego_shop_php`** và mở được tại `http://localhost/lego_shop_php/`.

### Bước 1 – Lấy mã nguồn

Có 2 cách, chọn một:

**Cách A – đặt thẳng vào htdocs (đơn giản nhất):**
```bash
cd C:\xampp\htdocs
git clone <url-repo> lego_shop_php
```

**Cách B – để code ở ổ khác, tạo liên kết vào htdocs.** Mở **Command Prompt bằng quyền Administrator**:
```bat
mklink /D "C:\xampp\htdocs\lego_shop_php" "E:\duong-dan\toi\lego_shop_php"
```

### Bước 2 – Bật XAMPP

Mở **XAMPP Control Panel** → bấm **Start** cho **Apache** và **MySQL**.

### Bước 3 – Tạo database và import dữ liệu

File `lego_shop.sql` **không tự tạo database**, nên phải tạo trước.

**Bằng phpMyAdmin:**
1. Vào `http://localhost/phpmyadmin`.
2. **New** → tên database: `lego_shop`, collation: `utf8mb4_general_ci` → **Create**.
3. Chọn database `lego_shop` → tab **Import** → chọn file `lego_shop.sql` trong thư mục dự án → **Import**.

**Hoặc bằng dòng lệnh** (Command Prompt, đứng ở thư mục dự án):
```bat
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE lego_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci"
C:\xampp\mysql\bin\mysql.exe -u root lego_shop < lego_shop.sql
```

> **Đã có database `lego_shop` từ phiên bản cũ** thì không import lại. Chạy lần lượt các file trong `database/migrations/` (theo thứ tự tên file) để cập nhật cấu trúc.

### Bước 4 – Tạo file `.env`

Sao chép file mẫu:
```bat
copy .env.example .env
```
Mở `.env`, điền:
- **`DB_USER` / `DB_PASS`:** XAMPP mặc định là `root` và mật khẩu rỗng, giữ nguyên nếu chưa đổi.
- **`PAYMENT_WEBHOOK_KEY`:** tạo một chuỗi ngẫu nhiên bằng lệnh:
  ```bat
  C:\xampp\php\php.exe -r "echo bin2hex(random_bytes(24));"
  ```

Ý nghĩa từng biến xem [mục 4](#4-cấu-hình-env).

### Bước 5 – Mở website

- **Trang khách:** http://localhost/lego_shop_php/
- **Trang quản trị:** http://localhost/lego_shop_php/admin/login

Mở được trang chủ và thấy danh sách sản phẩm là đã cài xong.

---

## 3. Tài khoản và đường dẫn

**Tài khoản quản trị mặc định:** `admin` / `123456`. Hãy đổi mật khẩu sau khi đăng nhập (Hồ sơ của tôi → đổi mật khẩu).

**Tài khoản khách:** tự đăng ký tại `/account/register`. Các tài khoản khách có sẵn trong dữ liệu mẫu không công bố mật khẩu.

| Trang | Đường dẫn (sau `http://localhost/lego_shop_php`) |
|---|---|
| Trang chủ / Sản phẩm | `/home`, `/product`, `/product/detail/<id>` |
| Giỏ hàng / Thanh toán | `/cart`, `/checkout` |
| Hồ sơ khách | `/profile/index`, `/profile/orders`, `/profile/vouchers`, `/profile/refunds` |
| Đăng nhập admin | `/admin/login` |
| Trang admin | `/admin/<trang>`, ví dụ `/admin/dashboard`, `/admin/order`, `/admin/product`, `/admin/voucher`, `/admin/refund`, `/admin/payment`, `/admin/setting` |
| Webhook thanh toán | `/payment/webhook` (POST, dành cho SePay/Casso) |

Link admin kiểu cũ (`/adminorder`, `/adminproduct`…) vẫn dùng được, sẽ tự chuyển sang dạng `/admin/order`.

---

## 4. Cấu hình `.env`

File `.env` nằm ở thư mục gốc dự án:
- Chứa mật khẩu và key, **không commit lên git** (đã có trong `.gitignore`).
- Bị chặn truy cập qua web.
- Nội dung mẫu ở `.env.example`.

| Biến | Mặc định | Ý nghĩa |
|---|---|---|
| `APP_ENV` | `local` | `local` hoặc `production` |
| `APP_DEBUG` | `true` | `true`: hiện lỗi PHP ra màn hình (chỉ dùng khi phát triển) |
| `DB_HOST` | `localhost` | Máy chủ MySQL |
| `DB_PORT` | `3306` | Cổng MySQL |
| `DB_NAME` | `lego_shop` | Tên database |
| `DB_USER` | `root` | Tài khoản MySQL |
| `DB_PASS` | *(rỗng)* | Mật khẩu MySQL |
| `SESSION_IDLE_MINUTES` | `120` | Tự đăng xuất sau bao nhiêu phút không thao tác |
| `PAYMENT_WEBHOOK_KEY` | *(phải tự tạo)* | API key để SePay/Casso gọi webhook thanh toán |

Các cài đặt cửa hàng khác (tên shop, logo, tài khoản ngân hàng nhận tiền, thời gian hết hạn thanh toán, số ngày được trả hàng, chế độ giả lập thanh toán) chỉnh trong **Admin → Cài đặt hệ thống**.

---

## 5. Thanh toán VietQR

- Khách chọn **"Chuyển khoản qua mã VietQR"** khi đặt hàng. Hệ thống tạo mã QR đúng số tiền, nội dung `LEGO<mã đơn>`.
- Khi ngân hàng báo có tiền (qua **SePay** hoặc **Casso** gọi `/payment/webhook`), đơn tự chuyển sang **Đã thanh toán**.
- Đơn chưa thanh toán sau 30 phút (chỉnh được) sẽ tự hủy.
- **Trên máy local** ngân hàng không gọi webhook vào được, nên dự án có **chế độ giả lập (sandbox)**: trang QR có nút "Giả lập thanh toán". Bật/tắt ở **Admin → Cài đặt hệ thống**.

Hướng dẫn chi tiết cách test (nút giả lập, gửi webhook giả bằng curl, webhook thật qua ngrok): **[HUONG_DAN_SANDBOX.md](HUONG_DAN_SANDBOX.md)**.

---

## 6. Chạy test

Bộ test nằm ở `tests/run_tests.php`, viết bằng PHP thuần (38 test: tài khoản, giỏ hàng, đơn hàng, mã giảm giá, thanh toán, hoàn tiền, kiểm tra trang).

```bat
C:\xampp\php\php.exe tests\run_tests.php          :: chạy toàn bộ, xong tự xoá DB test
C:\xampp\php\php.exe tests\run_tests.php --keep   :: giữ lại DB test để xem dữ liệu
```

- **Không ảnh hưởng dữ liệu thật:** test chạy trên database riêng `lego_shop_test`, tạo mới từ `lego_shop.sql` mỗi lần.
- **Cần bật MySQL.** Nhóm "HTTP smoke test" cần thêm Apache; Apache tắt thì nhóm này được bỏ qua.
- **Khi đổi cấu trúc database**, nhớ xuất lại `lego_shop.sql` để test dùng đúng cấu trúc mới.

---

## 7. Cấu trúc thư mục

```plaintext
lego_shop_php/
├── app/
│   ├── core/                 # App.php (định tuyến URL), Controller.php, Database.php, Env.php (đọc .env)
│   ├── controllers/          # Xử lý request. Trang admin: Admin<Tên>Controller -> URL /admin/<tên>
│   ├── models/               # Truy vấn database
│   └── views/
│       ├── user/             # Giao diện khách hàng
│       ├── admin/            # Giao diện quản trị
│       └── components/       # Header, footer, sidebar dùng chung
├── public/
│   └── assets/
│       ├── css/              # admin_ui.css (style admin dùng chung), responsive.css (điện thoại)...
│       ├── js/               # main.js, ui-dialog.js (hộp thoại thay alert/confirm)...
│       ├── images/           # Ảnh sản phẩm, banner, ảnh tải lên
│       └── vendor/fontawesome/  # Font Awesome 6.6.0 (icon, chạy offline)
├── database/migrations/      # Script cập nhật database cho bản cài cũ
├── tests/run_tests.php       # Bộ test
├── index.php                 # Điểm vào: đọc .env, cấu hình session, khởi chạy App
├── .htaccess                 # Rewrite URL về index.php + chặn truy cập file nhạy cảm
├── .env.example              # Mẫu cấu hình (sao chép thành .env)
└── lego_shop.sql             # Cấu trúc + dữ liệu mẫu
```

**Luồng xử lý một request:**
- `/admin/order/detail/5` → `.htaccess` chuyển về `index.php?url=admin/order/detail/5` → `App.php` gọi `AdminOrderController::detail(5)`.
- `/checkout/payment` → `CheckoutController::payment()`.

---

## 8. Xử lý sự cố

| Hiện tượng | Cách xử lý |
|---|---|
| Mọi trang trừ trang chủ đều **404 Not Found** | Kiểm tra thư mục tên đúng `lego_shop_php`. Trong `C:\xampp\apache\conf\httpd.conf`, dòng `LoadModule rewrite_module` không bị comment, và thư mục `htdocs` có `AllowOverride All`. Restart Apache |
| **"Hệ thống đang bảo trì"** hoặc **"Lỗi kết nối CSDL"** | MySQL chưa Start, hoặc thông tin `DB_*` trong `.env` sai, hoặc chưa tạo/import database `lego_shop` |
| Chữ tiếng Việt bị lỗi font trong dữ liệu | Database phải dùng `utf8mb4_general_ci`. Xoá database, tạo lại đúng collation rồi import lại |
| Không hiện icon | Kiểm tra mở được `http://localhost/lego_shop_php/public/assets/vendor/fontawesome/css/all.min.css`. Tải lại trang bằng Ctrl+F5 |
| Bị đăng xuất liên tục | Tăng `SESSION_IDLE_MINUTES` trong `.env` |
| Upload ảnh báo lỗi | Chỉ nhận JPG/PNG/WEBP/GIF, tối đa 2MB (ảnh minh chứng hoàn tiền: 5MB) |
| Webhook trả **401** | API key gửi lên khác `PAYMENT_WEBHOOK_KEY` trong `.env` |
| Mở `.env`, `.sql`, `.md`, thư mục `app/`… qua trình duyệt bị **403** | Bình thường, đã chặn có chủ đích để bảo mật |

---

## 9. Đưa lên server thật

- [ ] Trong `.env`: `APP_ENV=production`, `APP_DEBUG=false`.
- [ ] Tạo tài khoản MySQL riêng (không dùng `root`), điền vào `DB_USER` / `DB_PASS`.
- [ ] Tạo `PAYMENT_WEBHOOK_KEY` mới và cấu hình cùng key đó bên SePay/Casso.
- [ ] Dùng **HTTPS** (cookie đăng nhập tự bật cờ Secure khi chạy HTTPS).
- [ ] Đổi mật khẩu tài khoản `admin`.
- [ ] **Tắt** "Chế độ giả lập thanh toán" trong Admin → Cài đặt hệ thống.
- [ ] Nhập đúng tài khoản ngân hàng nhận tiền và quét thử QR mẫu trong trang Cài đặt.
- [ ] Dự án phải chạy tại đường dẫn `/lego_shop_php/` trên domain (xem lưu ý ở Bước 1).

---

## 10. Tài liệu khác

- **[TONG_HOP_THAY_DOI.md](TONG_HOP_THAY_DOI.md):** tổng hợp toàn bộ thay đổi:
  - database, tính năng mới (mã giảm giá, VietQR, hoàn tiền), trang admin;
  - giao diện & responsive;
  - bảo mật (mục 12, gồm các vấn đề còn tồn tại).
- **[HUONG_DAN_SANDBOX.md](HUONG_DAN_SANDBOX.md):** hướng dẫn test thanh toán VietQR trên máy local.
