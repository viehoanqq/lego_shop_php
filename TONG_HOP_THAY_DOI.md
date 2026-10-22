# Tổng hợp thay đổi – LEGO World Store

Ngày cập nhật: 22/10/2026

Dự án vẫn giữ kiến trúc **PHP MVC** như cũ. Đợt này:

- Thêm 3 tính năng mới: **mã giảm giá**, **thanh toán online qua VietQR**, **hoàn tiền / trả hàng**, kèm trang quản trị cho cả ba.
- Sửa một số lỗi cũ trong database và code.
- Đổi đường dẫn trang admin sang dạng `/admin/<trang>`.
- Làm gọn giao diện (bớt màu, bỏ chữ giải thích thừa), giữ icon Font Awesome và thêm **responsive cho điện thoại**.
- Viết bộ test cho các chức năng cơ bản.

---

## Mục lục

1. [Database](#1-database)
2. [Sửa lỗi cũ](#2-sửa-lỗi-cũ)
3. [Tính năng mới – Giao diện khách hàng](#3-tính-năng-mới--giao-diện-khách-hàng)
4. [Tính năng mới – Trang quản trị (Admin)](#4-tính-năng-mới--trang-quản-trị-admin)
5. [Đường dẫn trang admin](#5-đường-dẫn-trang-admin)
6. [Giao diện & responsive](#6-giao-diện--responsive)
7. [Quy trình nghiệp vụ](#7-quy-trình-nghiệp-vụ)
8. [Kiểm thử (test)](#8-kiểm-thử-test)
9. [Cài đặt & cấu hình](#9-cài-đặt--cấu-hình)
10. [Danh sách file thay đổi](#10-danh-sách-file-thay-đổi)
11. [Lưu ý & hạn chế](#11-lưu-ý--hạn-chế)
12. [Bảo mật & file .env](#12-bảo-mật--file-env)

---

## 1. Database

Thay đổi nằm trong thư mục `database/migrations/` và đã áp dụng vào DB `lego_shop`. File `lego_shop.sql` đã được xuất lại theo cấu trúc mới, nên cài từ đầu chỉ cần import file này.

### Migration 1 – `2026_10_22_payment_voucher_refund.sql`

**Bảng `orders`, thêm cột:**

| Cột | Ý nghĩa |
|---|---|
| `subtotal_amount` | Tổng tiền hàng trước giảm giá |
| `discount_amount` | Số tiền được giảm |
| `voucher_id`, `voucher_code` | Mã giảm giá đã dùng |
| `payment_code` | Nội dung chuyển khoản, ví dụ `LEGO25` (unique) |
| `paid_at` | Thời điểm nhận được tiền |

- `payment_method` thêm giá trị `vietqr`.
- `payment_status` thêm `refund_pending` (đang hoàn tiền) và `refunded` (đã hoàn tiền).
- Thêm index cho `status` và `created_at`.
- Đơn cũ được gán `subtotal_amount = total_amount`.

**4 bảng mới:**

| Bảng | Dùng để |
|---|---|
| `vouchers` | Mã giảm giá: kiểu giảm (cố định / %), giảm tối đa, đơn tối thiểu, tổng lượt, lượt mỗi khách, thời hạn, trạng thái |
| `voucher_usages` | Ghi lại đơn nào dùng mã nào, giảm bao nhiêu |
| `payment_transactions` | Nhật ký mọi khoản tiền vào (webhook ngân hàng, giả lập, admin xác nhận). Unique theo `(provider, transaction_ref)` để không xử lý trùng |
| `refund_requests` | Yêu cầu hoàn tiền: loại (`cancel` / `return`), số tiền, lý do, ảnh minh chứng, tài khoản nhận tiền, trạng thái, admin xử lý |

**Bảng `shop_settings`, thêm cột:**
- Tài khoản nhận tiền: `bank_code`, `bank_account_no`, `bank_account_name`.
- `payment_webhook_key`: API key cho webhook (dự phòng; key chính nay nằm trong file `.env`, xem mục 12).
- `payment_sandbox`: bật nút giả lập thanh toán khi demo.
- `payment_timeout_minutes`: thời gian chờ thanh toán, mặc định 30 phút.
- `refund_days`: số ngày được trả hàng, mặc định 7 ngày.

**Dữ liệu mẫu:** 3 mã giảm giá `WELCOME50K`, `LEGO10`, `FREESHIP30K`.

### Migration 2 – `2026_10_22_admin_refund_returned.sql`

- `orders.status` thêm giá trị `returned` (đã trả hàng).
- Lý do: báo cáo doanh thu và xuất kho chỉ tính đơn `delivered`. Đơn trả hàng chuyển sang `returned` nên tự động không bị tính vào doanh thu và lượng xuất kho nữa.

### Sửa dữ liệu
- Mật khẩu admin trước đây lưu dạng chữ thường `123456`, nay đã băm bằng bcrypt. Vẫn đăng nhập bằng `admin` / `123456`.

---

## 2. Sửa lỗi cũ

| Lỗi | Hậu quả | Đã sửa |
|---|---|---|
| `Database::getConnection()` mở **kết nối mới mỗi lần gọi** | Transaction trải qua nhiều Model thực chất không có tác dụng | Dùng chung 1 kết nối cho cả request; charset đổi `utf8` → `utf8mb4` |
| Đặt hàng so số lượng với `stock_quantity`, không trừ hàng đang giữ cho đơn khác | **Bán vượt tồn kho** | So với **tồn khả dụng** (tồn kho trừ hàng trong các đơn đang chờ/đang giao) và khoá dòng sản phẩm khi đặt |
| Thêm vào giỏ không kiểm tra kho; tăng số lượng so với `stock_quantity` | Giỏ hàng vượt kho | Kiểm tra theo tồn khả dụng |
| Tạo đơn bằng cách ghép chuỗi SQL, không gói trong transaction | Rủi ro SQL injection, dễ tạo đơn dở dang | Dùng prepared statement và gói toàn bộ trong 1 transaction |
| Checkout: khung thông tin chuyển khoản so với `'banking'` | Khung không bao giờ hiện | Viết lại phần phương thức thanh toán |
| Trang thanh toán dùng ảnh QR tĩnh, là một trang HTML riêng nhét giữa header/footer | Sai số tiền, vỡ bố cục | Viết lại thành trang VietQR động |
| `/admin/dashboard` rơi vào `AdminController::dashboard()` | Dashboard hiển thị không có dữ liệu | Định tuyến mới đưa vào đúng `AdminDashboardController` |
| Sidebar hồ sơ ghi cứng `hihihi@gmail.com` | Hiển thị sai email | Hiển thị email thật |
| Thẻ "Hủy / Trả hàng" trên dashboard chỉ đếm đơn hủy | Số liệu thiếu | Đếm cả đơn `returned` |
| Admin chuyển đơn chuyển khoản sang "Giao thành công" khi chưa nhận tiền: chỉ chặn bằng JavaScript | Có thể vượt qua | Chặn thêm ở server |
| Voucher giảm hết tiền (đơn 0đ) mà chọn VietQR vẫn phải "quét QR 0đ" | Đơn treo chờ thanh toán | Đơn 0đ được ghi nhận đã thanh toán ngay, bỏ qua trang QR |
| Header không cho xuống dòng, bố cục trang sản phẩm viết inline | Cả trang bị tràn ngang trên điện thoại | Thêm responsive (mục 6.5) |

---

## 3. Tính năng mới – Giao diện khách hàng

### 3.1 Mã giảm giá
- **Trang thanh toán:** ô nhập mã (Enter hoặc nút "Áp dụng") và danh sách mã gợi ý, bấm để dùng; mã chưa đủ điều kiện hiện mờ. Tổng tiền cập nhật ngay, không tải lại trang.
- **Kiểm tra lại ở server** khi đặt hàng: thời hạn, đơn tối thiểu, tổng lượt, lượt của từng khách. Dòng voucher được khoá khi kiểm tra để không bị dùng vượt lượt.
- **Hồ sơ → "Mã giảm giá":** danh sách mã đang áp dụng, số lượt còn lại, nút sao chép.
- Hủy đơn (dù do khách, admin hay tự hủy vì quá hạn) đều **trả lại lượt dùng mã**.

### 3.2 Thanh toán online VietQR
- **Phương thức thanh toán:** COD hoặc **Chuyển khoản qua mã VietQR**. Bỏ lựa chọn "Momo, VNPay" giả.
- **Trang thanh toán:**
  - Mã QR chuẩn VietQR (`img.vietqr.io`) với đúng số tiền và nội dung `LEGO<mã đơn>`.
  - Thông tin chuyển khoản, có nút sao chép.
  - Đếm ngược thời gian hết hạn.
- **Tự động xác nhận:** trang QR hỏi trạng thái mỗi 4 giây; khi ngân hàng báo tiền về (webhook) thì tự chuyển sang trang thành công.
- **Chế độ giả lập:** nút "Giả lập thanh toán" để demo trên localhost.
- **Tự hủy đơn quá hạn:** đơn VietQR chưa trả tiền sau 30 phút bị hủy, nhả tồn kho và trả lại lượt voucher.
- Đơn chưa thanh toán có nút **"Thanh toán ngay"** trong danh sách đơn.

### 3.3 Hoàn tiền / Trả hàng
- **Hủy đơn đã thanh toán online:** khách nhập tài khoản ngân hàng; hệ thống tạo yêu cầu hoàn tiền và chuyển thanh toán sang "Đang hoàn tiền".
- **Trả hàng:**
  - Áp dụng cho đơn đã giao, trong vòng 7 ngày kể từ lúc giao.
  - Khách nhập lý do (ít nhất 10 ký tự), có thể đính kèm ảnh minh chứng (JPG/PNG/WEBP, tối đa 5MB; kiểm tra theo nội dung file) và tài khoản nhận tiền.
- **Bổ sung tài khoản:** đơn bị cửa hàng hủy mà chưa có tài khoản nhận tiền thì khách nhập ngay trong chi tiết đơn.
- **Hồ sơ → "Hoàn tiền / Trả hàng":** theo dõi trạng thái từng yêu cầu và phản hồi của cửa hàng.

### 3.4 Các trang khác được cập nhật
- **Chi tiết đơn (popup và trang riêng):** tạm tính, giảm giá, trạng thái thanh toán, thông tin hoàn tiền. Dòng thời gian có thêm các mốc "Đã thanh toán", "Yêu cầu hoàn tiền", "Đã hoàn tiền", "Đã trả hàng".
- **Trang đặt hàng thành công:** số tiền, giảm giá, trạng thái thanh toán.

---

## 4. Tính năng mới – Trang quản trị (Admin)

Menu "Kinh doanh" có thêm 3 mục.

### 4.1 Mã giảm giá (`/admin/voucher`)
- Danh sách có tìm kiếm và lọc theo trạng thái (đang chạy / sắp diễn ra / hết hạn / hết lượt / ngừng áp dụng).
- Form tạo/sửa có kiểm tra dữ liệu. Trang sửa kèm danh sách các đơn đã dùng mã.
- Mã đã có đơn dùng:
  - Bấm "Xóa" chỉ **ngừng áp dụng**, để giữ lịch sử.
  - Không được đổi tên mã hoặc kiểu giảm.
  - Tổng lượt không được đặt nhỏ hơn số lượt đã dùng.

### 4.2 Hoàn tiền / Trả hàng (`/admin/refund`)
- Ô thống kê theo trạng thái (bấm để lọc); menu hiện số yêu cầu đang cần xử lý.
- **Trang chi tiết:**
  - Thông tin khách, lý do, ảnh minh chứng, đơn hàng, lịch sử đơn.
  - **Mã QR chuyển tiền hoàn về đúng tài khoản khách**: admin quét bằng app ngân hàng của cửa hàng.
  - Nhập hoặc sửa tài khoản nhận tiền của khách.
- **Thao tác:**
  - **Duyệt → Đã chuyển tiền**, hoặc **Từ chối** (bắt buộc ghi lý do).
  - Yêu cầu do hủy đơn đã thanh toán thì **không được từ chối**.
  - Hoàn tất yêu cầu trả hàng: đơn chuyển sang **"Đã trả hàng"** và hàng được **cộng lại vào kho**.
  - Chặn xử lý trùng khi hai admin thao tác cùng lúc.

### 4.3 Giao dịch VietQR (`/admin/payment`)
- Tổng tiền đã nhận qua VietQR, số giao dịch khớp đơn, số giao dịch **cần kiểm tra** (chuyển thiếu, chuyển trùng, chuyển sau khi đơn đã hủy, hoặc không khớp đơn nào).
- Bảng giao dịch có tìm kiếm và lọc.

### 4.4 Cập nhật trang có sẵn
- **Chi tiết đơn hàng** (viết lại):
  - Hiển thị hình thức và trạng thái thanh toán, nội dung chuyển khoản, voucher, tạm tính/giảm giá, các giao dịch đã ghi nhận, yêu cầu hoàn tiền.
  - **Xác nhận đã nhận tiền** thủ công cho cả đơn VietQR (có ghi log).
  - **Admin hủy đơn đã thanh toán:** tự tạo yêu cầu hoàn tiền và trả lại lượt voucher. Có thể nhập luôn tài khoản của khách hoặc để khách tự bổ sung.
  - Đơn online chưa nhận tiền thì không chuyển được sang "Giao thành công".
- **Danh sách đơn và dashboard:** nhãn VietQR, trạng thái "Đã trả hàng", trạng thái thanh toán, bộ lọc "Đã trả hàng".
- **Cài đặt hệ thống:**
  - Chọn ngân hàng, số tài khoản, chủ tài khoản, có QR mẫu để quét thử.
  - Chỉnh thời gian hết hạn thanh toán, số ngày được trả hàng, bật/tắt giả lập.
  - Xem webhook URL và API key (key trong `.env` chỉ hiện dạng che bớt).

---

## 5. Đường dẫn trang admin

Trước đây trang admin có dạng dính liền, ví dụ `/lego_shop_php/adminimport`. Nay đổi sang dạng có dấu `/`:

| Trước | Sau |
|---|---|
| `/lego_shop_php/adminimport` | `/lego_shop_php/admin/import` |
| `/lego_shop_php/adminorder/detail/5` | `/lego_shop_php/admin/order/detail/5` |
| `/lego_shop_php/admindashboard` | `/lego_shop_php/admin/dashboard` |

- **Bộ định tuyến (`app/core/App.php`):**
  - `/admin/<tên>/<hàm>/<tham số>` → `Admin<Tên>Controller::<hàm>(<tham số>)`.
  - Nếu không có controller `Admin<Tên>` thì dùng `AdminController`, ví dụ `/admin/login`, `/admin/logout`, `/admin/profile`.
- **URL cũ vẫn dùng được:** mở link dạng `adminxxx` (GET) sẽ tự chuyển hướng 301 sang dạng mới, giữ nguyên query string.
- Toàn bộ link, form, chuyển hướng trong controller/view đã được đổi sang dạng mới.

---

## 6. Giao diện & responsive

### 6.1 Màu sắc
- **Trang khách:** nền trung tính, màu nhấn là đỏ thương hiệu. Nhãn trạng thái đơn để xám; chỉ việc **cần xử lý** (chờ thanh toán, chờ hoàn tiền) mới có màu. Dòng thời gian của đơn dùng một màu duy nhất.
- **Badge trạng thái ở admin:** giữ kiểu badge cũ (viên bo tròn, chữ in hoa) nhưng chỉ còn 3 kiểu:
  - **Viền xanh lá:** bình thường / hoàn tất, ví dụ đang chạy, đã hoàn tiền, khớp đơn, đang giao.
  - **Nền đỏ:** cần xử lý, ví dụ chờ xử lý, chờ chuyển tiền, cần kiểm tra.
  - **Nền xám:** đã kết thúc / ngừng, ví dụ đã hủy, đã trả hàng, hết hạn, từ chối.

### 6.2 Icon
- **Giữ nguyên icon Font Awesome như giao diện gốc** (header, footer, trang sản phẩm, bộ lọc, checkout…). Phần mới cũng dùng icon cùng thư viện, ví dụ mỗi mốc trong tiến trình đơn hàng có icon riêng.
- **Bỏ các ký tự giả icon** như `→`, `∞`, `⚠️`, `❌`, thay bằng chữ hoặc icon Font Awesome.
- **Font Awesome 6.6.0 được tải về dự án** (`public/assets/vendor/fontawesome/`) thay vì dùng CDN, để icon luôn hiển thị (trước đó có máy không tải được font icon solid/brands từ CDN).

### 6.3 Câu chữ
- Bỏ các dòng mô tả/hướng dẫn không cần thiết, ví dụ:
  - "Vui lòng giữ nguyên số tiền và nội dung chuyển khoản…"
  - "Quản lý và theo dõi trạng thái các đơn hàng"
  - "Hãy thêm địa chỉ mới nhé!"
  - "Kiểm tra sản phẩm trước khi thanh toán."
- Các thông báo sau thao tác được rút gọn.

### 6.4 Thống kê & bộ lọc ở admin
- 3 trang mới (mã giảm giá, hoàn tiền, giao dịch) dùng **cùng kiểu với trang Đơn hàng / Dashboard**:
  - **Thẻ thống kê:** tiêu đề, số lớn, ô icon. Bấm để lọc, bấm lần nữa để bỏ lọc.
  - **Thanh lọc:** nhãn có icon, nút "Lọc", nút đặt lại.
- Trang mã giảm giá có thêm 4 thẻ đếm: đang chạy, sắp diễn ra, hết hạn / hết lượt, ngừng áp dụng.
- Đã kiểm tra các bộ lọc trả đúng kết quả.
- Style dùng chung nằm trong `public/assets/css/admin_ui.css`.

### 6.4b Hộp thoại thay cho alert / confirm
- Thư viện dùng chung `public/assets/js/ui-dialog.js`, nạp ở cả trang khách và admin:
  - `uiAlert(message, type)`: hộp thông báo.
  - `uiConfirm(message, { danger, okText })`: hộp xác nhận, trả về `true`/`false`.
  - Thuộc tính `data-confirm="..."` trên link / nút / form: tự hỏi xác nhận trước khi đi tiếp hoặc gửi form. Hành động xóa/hủy/khóa có nút đỏ.
- **Đã thay toàn bộ** `alert()` / `confirm()` của trình duyệt: 29 chỗ `alert` và 24 chỗ `confirm` ở admin (danh mục, sản phẩm, khách hàng, nhà cung cấp, phiếu nhập, tồn kho, giá, đánh giá, đơn hàng, hoàn tiền, voucher) và trang khách (giỏ hàng, đăng ký, hủy đơn).
- Các chỗ PHP in `<script>alert(...)</script>` rồi quay lại trang trước (checkout, xem đơn, đổi trạng thái đơn, in phiếu nhập):
  - Nay chuyển hướng kèm thông báo (`set_flash_message('dialog', ...)`).
  - Trang đích tự hiện hộp thoại.

### 6.4c Một số chỉnh sửa giao diện khác
- **Menu tài khoản trên header:** thêm mục "Đơn hàng của tôi".
- **Tiến trình đơn hàng (popup chi tiết đơn):** không dùng icon từng mốc nữa. Thay bằng đường dọc có **chấm tròn** ở mỗi mốc, mốc mới nhất tô đậm, cuối đường có **mũi tên chỉ xuống**.
- **Trang Hoàn tiền / Trả hàng (khách):** mỗi yêu cầu có nút **"Xem"** mở chi tiết đơn.
- **Chi tiết đơn (admin):** 2 khung **Thanh toán** và **Cập nhật đơn hàng** được làm nổi bật:
  - Viền xanh, tiêu đề có icon.
  - Khối trạng thái thanh toán lớn: xanh lá khi đã thanh toán, đỏ khi chưa.
  - Hiện trạng thái hiện tại trước ô "Chuyển sang".

### 6.5 Responsive (điện thoại / tablet)
File `public/assets/css/responsive.css` (trang khách) và phần cuối `admin_ui.css` (admin). Mốc: ≤ 992px (tablet) và ≤ 576px (điện thoại).

**Trang khách:**
- **Header:** thanh chính sách cuộn ngang; logo thu nhỏ; ô tìm kiếm xuống dòng riêng, rộng hết màn hình. Trước đây header không cho xuống dòng nên cả trang bị tràn ngang.
- **Sản phẩm:**
  - Lưới 2 cột trên điện thoại, thẻ sản phẩm gọn lại.
  - Bộ lọc thu vào nút **"Bộ lọc"** (bấm để mở).
  - Sửa lỗi bố cục 2 cột viết inline nên không xuống 1 cột trên màn hình nhỏ.
- **Giỏ hàng:** mỗi sản phẩm hiển thị thành một thẻ.
- **Checkout / xem đơn:** khung rộng hết màn hình, bảng sản phẩm ẩn cột đơn giá, các nút xếp dọc.
- **Hồ sơ:** khối avatar thu gọn, menu chuyển thành thanh tab cuộn ngang.
- **Popup chi tiết đơn:** các khối thông tin xếp dọc, ô nhập tài khoản ngân hàng 1 cột.

**Admin:**
- **Sidebar:** ẩn đi, mở bằng nút menu ☰ (`fa-bars`); chạm ra ngoài hoặc chọn mục menu thì tự đóng.
- **Header:** thu gọn.
- **Nội dung:** thẻ thống kê 2 cột, bộ lọc xếp dọc, bảng cuộn ngang trong khung.

Đã kiểm tra bằng trình duyệt ở bề rộng 390px: không trang nào còn bị tràn ngang. Trên desktop giao diện giữ nguyên.

---

## 7. Quy trình nghiệp vụ

### Thanh toán VietQR
```
Đặt hàng (VietQR) ──> Trang QR ──┬─ Ngân hàng báo có (webhook) / giả lập ──> Đã thanh toán
                                 ├─ Admin xác nhận thủ công ─────────────────> Đã thanh toán
                                 └─ Quá 30 phút chưa trả ────────────────────> Tự hủy đơn
```

### Hoàn tiền
```
Hủy đơn đã thanh toán ──┐
(khách hoặc admin)      ├─> Yêu cầu: Chờ xử lý ──> Đã duyệt ──> Đã hoàn tiền
Khách trả hàng ─────────┘           │                              (trả hàng: đơn -> Đã trả hàng + cộng lại kho)
(trong 7 ngày)                      └─> Từ chối (chỉ với trả hàng; khách được gửi lại)
```

### Tồn kho
- **Tồn khả dụng** = tồn kho − số lượng trong các đơn *Chờ xử lý / Đã xác nhận / Đang giao*.
- Kho bị trừ thật khi đơn chuyển sang **Giao thành công**, và được cộng lại khi **hoàn tất trả hàng**.

---

## 8. Kiểm thử (test)

File test: `tests/run_tests.php`, viết bằng PHP thuần, không cần cài thêm thư viện.

```bash
php tests/run_tests.php          # chạy toàn bộ, xong tự xóa DB test
php tests/run_tests.php --keep   # giữ lại DB test để xem dữ liệu
```

- Chạy trên **database riêng `lego_shop_test`**, tạo mới từ `lego_shop.sql` mỗi lần, nên **không ảnh hưởng dữ liệu thật**.
- Để làm được việc này, `Database.php` đọc tên DB từ biến môi trường `LEGO_DB_NAME`; không có biến này thì vẫn dùng `lego_shop`.
- Kết quả hiện tại: **38/38 test đạt** (đã chạy lại sau khi đổi URL admin và làm gọn giao diện).

| Nhóm | Số test | Nội dung |
|---|---|---|
| Tài khoản | 5 | Đăng ký, băm mật khẩu, trùng email, đăng nhập email/SĐT, sai mật khẩu, tài khoản khoá, admin |
| Giỏ hàng & tồn kho | 6 | Giới hạn kho, tồn khả dụng, tăng/giảm số lượng, không sửa được giỏ người khác |
| Đơn hàng | 2 | Lưu đúng tạm tính/giảm giá/tổng tiền/giá vốn, nội dung chuyển khoản |
| Mã giảm giá | 6 | Cách tính giảm, các trường hợp bị từ chối, giới hạn lượt, trả lượt khi hủy đơn |
| Thanh toán VietQR | 6 | Đọc nội dung chuyển khoản, link QR, thiếu/đủ tiền, webhook trùng, đơn đã hủy, tự hủy đơn quá hạn |
| Hoàn tiền | 6 | Kiểm tra tài khoản ngân hàng, hạn 7 ngày, trả hàng cộng kho, hoàn tiền đơn hủy, từ chối rồi gửi lại, QR hoàn tiền |
| HTTP smoke | 7 | Các trang chính trả về 200, chặn khi chưa đăng nhập (kể cả `/admin/refund`), webhook sai key trả 401 (cần Apache; Apache tắt thì bỏ qua) |

---

## 9. Cài đặt & cấu hình

1. **DB đang dùng bản cũ:** chạy lần lượt 2 file trong `database/migrations/`. **Cài mới:** import `lego_shop.sql`.
2. Vào **Admin → Cài đặt hệ thống** (`/admin/setting`), nhập tài khoản ngân hàng nhận tiền và quét QR mẫu để kiểm tra.
3. **Tự động xác nhận thanh toán** (khi chạy thật trên server có domain):
   - Đăng ký **SePay** hoặc **Casso**, liên kết với tài khoản ngân hàng.
   - Webhook URL: `https://<domain>/lego_shop_php/payment/webhook`.
   - API key: dòng `PAYMENT_WEBHOOK_KEY` trong file `.env`.
     - SePay gửi header `Authorization: Apikey <key>`.
     - Casso gửi header `Secure-Token: <key>`.
     - Hoặc thêm `?key=<key>` vào URL.
4. **Tắt chế độ giả lập** khi chạy thật.

`.htaccess` đã được bổ sung dòng chuyển tiếp header `Authorization` để webhook SePay hoạt động.

---

## 10. Danh sách file thay đổi

### File mới
| File | Mô tả |
|---|---|
| `database/migrations/2026_10_22_payment_voucher_refund.sql` | Migration 1 |
| `database/migrations/2026_10_22_admin_refund_returned.sql` | Migration 2 |
| `app/models/VoucherModel.php` | Kiểm tra / tính / ghi nhận voucher, quản lý voucher cho admin |
| `app/models/PaymentModel.php` | QR VietQR, xác nhận thanh toán, tự hủy đơn quá hạn, nhật ký giao dịch |
| `app/models/RefundModel.php` | Yêu cầu hoàn tiền, điều kiện trả hàng, duyệt / từ chối / hoàn tất |
| `app/controllers/PaymentController.php` | Webhook ngân hàng, API trạng thái thanh toán, giả lập |
| `app/controllers/AdminVoucherController.php` | Admin quản lý mã giảm giá |
| `app/controllers/AdminRefundController.php` | Admin xử lý hoàn tiền |
| `app/controllers/AdminPaymentController.php` | Admin xem giao dịch |
| `app/views/user/profile/vouchers.php` | Mã giảm giá của khách |
| `app/views/user/profile/refunds.php` | Yêu cầu hoàn tiền của khách |
| `app/views/admin/vouchers.php` | Danh sách + form voucher |
| `app/views/admin/refunds.php` | Danh sách yêu cầu hoàn tiền |
| `app/views/admin/refund_detail.php` | Chi tiết & xử lý hoàn tiền |
| `app/views/admin/payments.php` | Giao dịch VietQR |
| `public/assets/css/admin_ui.css` | Style dùng chung cho các trang admin mới + responsive admin |
| `public/assets/css/responsive.css` | Responsive cho trang khách |
| `public/assets/js/ui-dialog.js` | Hộp thoại dùng chung (uiAlert / uiConfirm / data-confirm) thay cho alert/confirm |
| `public/assets/vendor/fontawesome/` | Font Awesome 6.6.0 tải về dự án (CSS + font), thay cho CDN |
| `tests/run_tests.php` | Bộ test |

### File sửa – lõi & model
| File | Nội dung sửa |
|---|---|
| `app/core/App.php` | Định tuyến `/admin/<trang>`, chuyển hướng URL cũ |
| `app/core/Database.php` | Dùng chung kết nối, `utf8mb4`, đọc tên DB từ biến môi trường |
| `app/models/CartModel.php` | Kiểm tra tồn khả dụng khi thêm / tăng số lượng |
| `app/models/OrderModel.php` | `createOrder` dùng prepared statement, hàm ghi lịch sử, bảng nhãn trạng thái dùng chung |
| `app/models/SettingModel.php` | Lưu cấu hình thanh toán & hoàn tiền |
| `app/models/DashboardModel.php` | Đếm cả đơn trả hàng |

### File sửa – controller
| File | Nội dung sửa |
|---|---|
| `CheckoutController.php` | Voucher, đặt hàng trong transaction, trang VietQR, hủy đơn kèm hoàn tiền |
| `ProfileController.php` | Trang voucher / hoàn tiền, API trả hàng, bổ sung tài khoản ngân hàng |
| `CartController.php` | Báo lỗi khi vượt tồn kho |
| `AccountController.php` | Lưu email vào session |
| `AdminOrderController.php` | Xác nhận thanh toán thủ công, hủy đơn đã thanh toán, chặn giao khi chưa nhận tiền |
| Các controller admin còn lại | Đổi link / chuyển hướng sang URL dạng `/admin/<trang>` |

### File sửa – giao diện
| File | Nội dung sửa |
|---|---|
| `user/cart/checkout.php`, `payment.php`, `success.php`, `view_order.php` | Voucher, VietQR, giảm giá, hoàn tiền; làm gọn |
| `user/profile/orders.php` | Thanh toán ngay, hủy kèm hoàn tiền, trả hàng, bổ sung tài khoản; làm gọn popup và dòng thời gian |
| `components/header.php` | Nạp Font Awesome nội bộ và `responsive.css` |
| `components/footer.php` | Bỏ dòng ghi chú dưới ô đăng ký nhận tin |
| `user/product.php` | Nút "Bộ lọc" thu gọn trên điện thoại |
| `user/cart/index.php` | Bỏ dòng phụ đề |
| `user/product_detail.php`, `wishlist.php`, `forgot.php`, `reset.php`, `profile/info.php`, `profile/addresses.php` | Rút gọn câu chữ; thay emoji ⚠️ bằng icon Font Awesome |
| `components/profile_sidebar.php` | Thêm menu, sửa email |
| `components/admin_sidebar.php` | Thêm 3 menu, số yêu cầu chờ xử lý, URL mới |
| `admin/admin_layout.php` | Nạp `admin_ui.css` và Font Awesome nội bộ; đóng sidebar khi chạm ra ngoài (mobile) |
| `components/admin_header.php` | Nút menu ☰ mở sidebar trên điện thoại |
| `admin/order_detail.php` | Viết lại |
| `admin/orders.php`, `dashboard.php`, `settings.php` | Nhãn mới, làm gọn phần đã thêm |
| Các view admin còn lại | Đổi link sang URL mới; `alert`/`confirm` chuyển sang hộp thoại |

### Khác
| File | Nội dung sửa |
|---|---|
| `.htaccess` | Chuyển tiếp header `Authorization` cho webhook |
| `.gitignore` | Bỏ qua ảnh minh chứng hoàn tiền `public/assets/images/refunds/` |
| `lego_shop.sql` | Xuất lại theo cấu trúc DB mới |
| `README.md` | Hướng dẫn VietQR, đường dẫn admin, chạy test |

---

## 11. Lưu ý & hạn chế

- **Chế độ giả lập thanh toán đang bật** để demo trên localhost, vì ngân hàng không gọi webhook vào localhost được. Phải tắt khi chạy thật.
- **Hoàn tiền là chuyển khoản tay:** hệ thống tạo QR và ghi nhận, admin tự chuyển tiền bằng app ngân hàng rồi bấm "Đã chuyển tiền". Chưa tích hợp API chi hộ.
- **Trả hàng luôn cộng lại toàn bộ đơn vào kho**, để báo cáo tồn kho khớp với kho thực tế. Hàng lỗi không bán lại được thì chỉnh tay ở trang Quản lý tồn kho. Chưa hỗ trợ trả một phần đơn.
- **Đơn VietQR admin đã xác nhận nhưng khách chưa trả tiền** sẽ không bị tự hủy; chỉ đơn còn "Chờ xử lý" mới tự hủy.
- **Khách chuyển tiền sau khi đơn đã bị hủy:** giao dịch được ghi vào mục "Cần kiểm tra", admin liên hệ khách để hoàn tiền thủ công.
- **Các trang admin có sẵn từ trước** (dashboard, sản phẩm, danh mục, phiếu nhập, tồn kho, báo cáo, khách hàng, nhà cung cấp, đánh giá) đã được đổi URL và dùng được trên điện thoại (sidebar, bảng cuộn ngang), nhưng **màu sắc vẫn giữ như cũ**.
- **Đổi cấu trúc DB** thì nhớ xuất lại `lego_shop.sql`, vì bộ test dựng DB test từ file này.

---

## 12. Bảo mật & file `.env`

### 12.1 File `.env`
Mật khẩu và key được chuyển ra file `.env` ở thư mục gốc. File này **không commit lên git** (đã thêm vào `.gitignore`) và **bị chặn truy cập qua web**. Mẫu để chia sẻ: `.env.example`.

| Biến | Ý nghĩa |
|---|---|
| `APP_ENV`, `APP_DEBUG` | `APP_DEBUG=false` thì không hiện lỗi PHP / lỗi kết nối DB ra màn hình |
| `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` | Kết nối MySQL (trước đây ghi cứng trong `Database.php`) |
| `SESSION_IDLE_MINUTES` | Tự đăng xuất sau N phút không thao tác (mặc định 120) |
| `PAYMENT_WEBHOOK_KEY` | API key webhook SePay/Casso. Key cũ trong DB đã bị lộ qua `lego_shop.sql` nên đã **vô hiệu hoá** và thay bằng key mới |

Đọc bằng `app/core/Env.php` (hàm `env('KEY', mặc_định)`), không cần thư viện ngoài.

### 12.2 Session
- **Trước đây:**
  - Dùng hoàn toàn mặc định của XAMPP: `session_start()` ở `index.php`, cấu hình trong `C:\xampp\php\php.ini`.
  - Cookie mất khi đóng trình duyệt (`session.cookie_lifetime=0`).
  - Phía server: session không hoạt động quá 24 phút (`session.gc_maxlifetime=1440`) chỉ *có thể* bị xoá, vì việc dọn chạy ngẫu nhiên khoảng 1/1000 request, nên thực tế có thể sống lâu hơn nhiều.
  - Cookie không có HttpOnly/SameSite.
- **Hiện tại** (`index.php`):
  - Tự đăng xuất sau **`SESSION_IDLE_MINUTES` phút không thao tác** (mặc định 120), kiểm tra mỗi request.
  - Cookie vẫn mất khi đóng trình duyệt.
  - Cookie có **HttpOnly**, **SameSite=Lax**, và **Secure** khi chạy HTTPS; bật `session.use_strict_mode`.
  - **Đổi session ID khi đăng nhập** (khách và admin) để chống chiếm phiên.

### 12.3 Lỗ hổng đã sửa

| Mức | Lỗ hổng | Đã sửa |
|---|---|---|
| Nghiêm trọng | Tải được `lego_shop.sql` (toàn bộ dữ liệu, hash mật khẩu, key webhook), thư mục `.git` (toàn bộ mã nguồn), file migration, tài liệu `.md` qua URL | `.htaccess` chặn `app/`, `database/`, `tests/`, file/thư mục bắt đầu bằng `.`, `*.sql`, `*.md`, `*.env`…; tắt liệt kê thư mục |
| Nghiêm trọng | Chạy được `tests/run_tests.php` và các file view trong `app/` trực tiếp qua URL | Như trên |
| Cao | Upload ảnh danh mục / logo / sản phẩm giữ nguyên tên và đuôi file người dùng gửi lên, nên tải được file `.php` và chạy mã trên server | Hàm chung `saveUploadedImage()`: kiểm tra nội dung là ảnh thật, tự đặt tên, đuôi theo loại ảnh. Thêm `public/.htaccess` cấm chạy PHP trong `public/` |
| Cao | XSS: email/SĐT khách in thô ở trang admin Khách hàng (đăng ký email chứa `<script>` là chạy được trên máy admin); form đăng ký/đăng nhập in lại dữ liệu cũ không escape; tên sản phẩm chèn thẳng vào popup đơn hàng | Escape 29 chỗ in dữ liệu; kiểm tra định dạng email/SĐT/họ tên/mật khẩu phía server khi đăng ký; popup dùng `escapeHtml` |
| Cao | Giỏ hàng nhận **số lượng âm**, có thể làm giảm tổng tiền đơn | Chỉ nhận số lượng 1–99 |
| Trung bình | SQL injection ở bộ lọc trạng thái sản phẩm (admin, tham số `status`) | Chỉ cho phép danh sách số nguyên |
| Trung bình | Đăng nhập admin còn chấp nhận mật khẩu dạng chữ thường | Bỏ nhánh này (mọi mật khẩu đều đã băm) |
| Trung bình | Không đổi session ID khi đăng nhập; cookie không HttpOnly | Xem 12.2 |
| Thấp | Lỗi kết nối DB / lỗi PHP in ra màn hình (lộ đường dẫn, user DB) | Ẩn khi `APP_DEBUG=false` |

### 12.4 Còn tồn tại – cần quyết định thêm

| Mức | Vấn đề | Đề xuất |
|---|---|---|
| **Nghiêm trọng** | **Quên mật khẩu không có bước xác minh:** nhập email hoặc SĐT của bất kỳ ai là được đặt mật khẩu mới cho tài khoản đó | Gửi mã OTP / link có hạn qua email (cần cấu hình SMTP trong `.env`). Trước mắt có thể tạm khoá chức năng này |
| Trung bình | Chưa có token CSRF; nhiều thao tác admin (xoá, khoá, bật/tắt) chạy bằng link GET | SameSite=Lax đã chặn phần lớn trường hợp gửi form từ trang khác. Nên thêm token CSRF cho form và đổi các thao tác xoá/khoá sang POST |
| Thấp | Không giới hạn số lần đăng nhập sai (dò mật khẩu) | Khoá tạm sau N lần sai |
| Thấp | Chạy HTTP | Khi lên server dùng HTTPS (cookie tự bật Secure) |
