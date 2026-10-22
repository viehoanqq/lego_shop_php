# Hướng dẫn dùng chế độ Sandbox – Thanh toán VietQR

Tài liệu này hướng dẫn cách **test toàn bộ luồng thanh toán VietQR trên máy local** mà không cần chuyển tiền thật. Đọc kèm: [TONG_HOP_THAY_DOI.md](TONG_HOP_THAY_DOI.md) (mục 3.2, 4.3, 7).

---

## Mục lục

1. [Sandbox là gì](#1-sandbox-là-gì)
2. [Bật / tắt sandbox](#2-bật--tắt-sandbox)
3. [Cách 1 – Nút "Giả lập thanh toán"](#3-cách-1--nút-giả-lập-thanh-toán)
4. [Cách 2 – Gửi webhook giả bằng curl](#4-cách-2--gửi-webhook-giả-bằng-curl)
5. [Cách 3 – Webhook thật từ SePay / Casso về máy local](#5-cách-3--webhook-thật-từ-sepay--casso-về-máy-local)
6. [Kịch bản test gợi ý](#6-kịch-bản-test-gợi-ý)
7. [Xem kết quả ở đâu](#7-xem-kết-quả-ở-đâu)
8. [Dọn dữ liệu test](#8-dọn-dữ-liệu-test)
9. [Trước khi chạy thật](#9-trước-khi-chạy-thật)

---

## 1. Sandbox là gì

Khi chạy thật, đơn VietQR được xác nhận tự động nhờ **webhook**: ngân hàng có tiền vào → SePay/Casso gọi tới `/payment/webhook` → hệ thống chuyển đơn sang "Đã thanh toán". Trên `localhost`, ngân hàng không gọi tới máy bạn được, nên dự án có 3 cách để test:

| Cách | Cần gì | Dùng khi |
|---|---|---|
| **1. Nút "Giả lập thanh toán"** | Bật sandbox | Demo nhanh, test giao diện |
| **2. Gửi webhook giả bằng curl** | Webhook key | Test đúng đường đi của webhook (thiếu tiền, gửi trùng, đơn đã hủy…) |
| **3. Webhook thật qua tunnel** | Tài khoản SePay/Casso + ngrok/cloudflared + chuyển khoản thật số tiền nhỏ | Kiểm tra lần cuối trước khi đưa lên server |

Cách 2 và 3 **không phụ thuộc** vào việc bật sandbox. Sandbox chỉ điều khiển nút giả lập ở cách 1.

---

## 2. Bật / tắt sandbox

**Qua trang admin (khuyên dùng):**
1. Đăng nhập admin: `http://localhost/lego_shop_php/admin/login` (`admin` / `123456`).
2. Vào **Cài đặt hệ thống** (`/admin/setting`) → khung **Thanh toán VietQR & Hoàn tiền**.
3. Tick / bỏ tick **"Chế độ giả lập thanh toán"** → **Lưu toàn bộ cài đặt**.

**Qua SQL (phpMyAdmin):**
```sql
UPDATE shop_settings SET payment_sandbox = 1 WHERE id = 1;  -- bật
UPDATE shop_settings SET payment_sandbox = 0 WHERE id = 1;  -- tắt
```

Khi bật, trang mã QR của khách có thêm nút **"Giả lập thanh toán"**. Khi tắt, nút này biến mất và đường dẫn `/payment/simulate` trả lỗi 403.

Các cài đặt liên quan trong cùng khung:

| Cài đặt | Mặc định | Ý nghĩa |
|---|---|---|
| Ngân hàng / Số TK / Chủ TK | MB / 0961589023 / NGUYEN HOANG | Tài khoản in lên mã QR |
| Hết hạn thanh toán (phút) | 30 (tối thiểu 5) | Quá thời gian chưa trả tiền thì đơn tự hủy |
| Hạn trả hàng (ngày) | 7 | Dùng cho luồng hoàn tiền |
| API Key webhook | trong file `.env` (`PAYMENT_WEBHOOK_KEY`) | Dùng cho cách 2 và 3. Trang Cài đặt chỉ hiện dạng che bớt |

---

## 3. Cách 1 – Nút "Giả lập thanh toán"

1. Bật sandbox (mục 2).
2. Đăng nhập tài khoản **khách**, thêm sản phẩm vào giỏ → **Thanh toán**.
3. Chọn **"Chuyển khoản qua mã VietQR"** → **Xác nhận đơn hàng**.
4. Ở trang mã QR, bấm **"Giả lập thanh toán"**.
5. Kết quả mong đợi:
   - Mã QR hiện "Thanh toán thành công" rồi tự chuyển sang trang đặt hàng thành công.
   - Đơn có trạng thái thanh toán **Đã thanh toán**.
   - Lịch sử đơn có mốc "Đã thanh toán" với ghi chú *(giả lập)*.
   - `/admin/payment` có một giao dịch nguồn **"Giả lập"**.

Nút giả lập chỉ hoạt động khi:
- sandbox đang bật,
- khách đang đăng nhập và là **chủ của đơn**,
- đơn dùng phương thức VietQR, chưa thanh toán và chưa bị hủy.

> Nếu bấm "Thanh toán sau", vào **Hồ sơ → Đơn hàng của tôi** → **Thanh toán ngay** để mở lại trang QR.

---

## 4. Cách 2 – Gửi webhook giả bằng curl

Cách này gửi đúng định dạng dữ liệu mà SePay/Casso gửi khi có tiền vào, nên test được mọi nhánh xử lý của webhook.

### 4.1 Lấy thông tin cần thiết

- **Webhook URL:** `http://localhost/lego_shop_php/payment/webhook`
- **API key:** mở file `.env` ở thư mục gốc dự án, lấy giá trị dòng `PAYMENT_WEBHOOK_KEY=...`.
- **Mã đơn và số tiền:** đặt một đơn VietQR (mục 3, bước 2–3), xem số đơn (ví dụ `#35`) và tổng tiền trên trang QR. Nội dung chuyển khoản là `LEGO35`.

### 4.2 Gửi theo định dạng SePay

**Git Bash / Linux / macOS:**
```bash
KEY="<api-key>"
curl -X POST "http://localhost/lego_shop_php/payment/webhook" \
  -H "Authorization: Apikey $KEY" \
  -H "Content-Type: application/json" \
  -d '{
        "id": "TEST-0001",
        "gateway": "MBBank",
        "transferType": "in",
        "transferAmount": 228000,
        "content": "NGUYEN VAN A chuyen tien LEGO35",
        "referenceCode": "FT0001"
      }'
```

**PowerShell (Windows):**
```powershell
$key = "<api-key>"
$body = @{
    id             = "TEST-0001"
    gateway        = "MBBank"
    transferType   = "in"
    transferAmount = 228000
    content        = "NGUYEN VAN A chuyen tien LEGO35"
    referenceCode  = "FT0001"
} | ConvertTo-Json
Invoke-RestMethod -Method Post -Uri "http://localhost/lego_shop_php/payment/webhook" `
    -Headers @{ Authorization = "Apikey $key" } -ContentType "application/json; charset=utf-8" -Body $body
```

### 4.3 Gửi theo định dạng Casso

```bash
curl -X POST "http://localhost/lego_shop_php/payment/webhook" \
  -H "Secure-Token: $KEY" \
  -H "Content-Type: application/json" \
  -d '{ "error": 0, "data": [ { "tid": "TEST-0002", "amount": 228000, "description": "LEGO35 thanh toan" } ] }'
```

Nếu không gửi được header, có thể truyền key trên URL: `.../payment/webhook?key=<api-key>`.

### 4.4 Đọc kết quả trả về

Khi key đúng, webhook luôn trả HTTP **200**, kèm kết quả cho từng giao dịch:

```json
{ "success": true, "results": ["paid"] }
```

| `results` | Ý nghĩa | Đơn hàng |
|---|---|---|
| `paid` | Khớp đơn, đủ tiền | Chuyển **Đã thanh toán** |
| `amount_mismatch` | Chuyển **thiếu** tiền | Giữ "Chưa thanh toán", giao dịch vào mục **Cần kiểm tra** |
| `duplicate` | Gửi lại cùng mã giao dịch (`id` / `tid`) | Bỏ qua, không ghi lần 2 |
| `already_paid` | Đơn đã được thanh toán trước đó (mã giao dịch khác) | Không đổi, giao dịch vào **Cần kiểm tra** |
| `order_cancelled` | Tiền về sau khi đơn đã hủy | Không đổi, giao dịch vào **Cần kiểm tra** để admin hoàn tiền tay |
| `not_found` | Nội dung không có `LEGO<số>` hoặc mã đơn không tồn tại | Giao dịch ghi là **Không rõ đơn** |

Các lỗi khác:

| HTTP | Nguyên nhân |
|---|---|
| 401 `Unauthorized` | Sai hoặc thiếu API key |
| 400 `Invalid JSON` | Body không phải JSON |
| 405 | Gọi bằng GET thay vì POST |

### 4.5 Một số quy tắc cần biết

- **Mã đơn được đọc từ nội dung chuyển khoản**, không phân biệt hoa thường, cho phép có khoảng trắng: `LEGO35`, `lego35`, `... LEGO 35 ...` đều nhận ra đơn 35.
- **Chuyển dư tiền** vẫn được tính là đã thanh toán.
- **Giao dịch tiền ra bị bỏ qua:** `transferType` khác `"in"` (SePay) hoặc `amount <= 0` (Casso).
- **Mỗi lần test nên dùng một mã giao dịch mới** (`id` / `tid`), nếu không sẽ nhận `duplicate`.

---

## 5. Cách 3 – Webhook thật từ SePay / Casso về máy local

Dùng khi muốn kiểm tra lần cuối với ngân hàng thật. **Cách này chuyển tiền thật**, nên dùng số tiền nhỏ (ví dụ đơn có voucher, còn vài nghìn đồng).

1. **Mở đường hầm (tunnel) cho Apache** để có URL công khai, ví dụ:
   ```bash
   ngrok http 80
   # hoặc
   cloudflared tunnel --url http://localhost:80
   ```
   Lấy URL được cấp, ví dụ `https://abcd-1234.ngrok-free.app`.
2. **Cấu hình webhook ở SePay hoặc Casso:**
   - URL: `https://abcd-1234.ngrok-free.app/lego_shop_php/payment/webhook`
   - Xác thực: chọn kiểu **API Key** và dán API key của shop (`PAYMENT_WEBHOOK_KEY` trong `.env`). SePay sẽ gửi header `Authorization: Apikey <key>`.
   - Nếu dịch vụ không cho đặt header theo kiểu trên, thêm `?key=<api-key>` vào cuối URL.
3. **Đặt tài khoản nhận tiền** trong **Cài đặt hệ thống** trùng với tài khoản đã liên kết ở SePay/Casso.
4. **Đặt một đơn VietQR**, dùng app ngân hàng quét mã và chuyển khoản. Giữ nguyên số tiền và nội dung.
5. **Trang QR tự chuyển sang "Thanh toán thành công"** sau vài giây đến vài chục giây, tùy tốc độ của dịch vụ.

Nếu không thấy gì xảy ra:
- **Xem phía dịch vụ:** lịch sử gọi webhook trên trang quản lý của SePay/Casso.
- **Xem phía shop:** `/admin/payment` có giao dịch mới không.
- **Lỗi 401:** kiểm tra lại API key. File `.htaccess` đã có dòng chuyển tiếp header `Authorization`, không cần sửa.

---

## 6. Kịch bản test gợi ý

| # | Kịch bản | Cách làm | Kết quả mong đợi |
|---|---|---|---|
| 1 | Thanh toán thành công | Cách 1, hoặc cách 2 với đúng số tiền | Đơn "Đã thanh toán", trang QR tự chuyển trang |
| 2 | Chuyển thiếu tiền | Cách 2 với `transferAmount` nhỏ hơn tổng đơn | `amount_mismatch`; đơn vẫn chưa thanh toán |
| 3 | Webhook gửi trùng | Gửi lại đúng request của kịch bản 1 | `duplicate` |
| 4 | Hết hạn thanh toán | Đặt "Hết hạn thanh toán" = 5 phút, tạo đơn VietQR rồi chờ hơn 5 phút, sau đó mở lại trang QR hoặc "Đơn hàng của tôi" | Đơn tự **hủy**, trả lượt voucher, mã QR báo hết hạn |
| 5 | Tiền về sau khi đơn hủy | Sau kịch bản 4, gửi webhook cho đơn đó | `order_cancelled`; giao dịch vào "Cần kiểm tra" |
| 6 | Nội dung sai | Cách 2 với `content` không có `LEGO<số>` | `not_found`; giao dịch "Không rõ đơn" |
| 7 | Admin xác nhận tay | Admin → chi tiết đơn VietQR chưa trả → **Xác nhận đã nhận tiền** | Đơn "Đã thanh toán", giao dịch nguồn "Admin xác nhận" |
| 8 | Chặn giao hàng khi chưa trả tiền | Admin đổi đơn VietQR chưa thanh toán sang "Giao thành công" | Bị chặn, hiện thông báo "Đơn chưa được thanh toán" |
| 9 | Hủy đơn đã thanh toán → hoàn tiền | Sau kịch bản 1, khách hủy đơn (nhập tài khoản ngân hàng) **hoặc** admin hủy đơn | Tạo yêu cầu hoàn tiền; xử lý ở `/admin/refund` |
| 10 | Voucher giảm hết tiền | Áp mã có mức giảm ≥ tổng đơn, chọn VietQR | Đơn 0đ được ghi nhận đã thanh toán ngay, không qua trang QR |

> **Mẹo cho kịch bản 4, không cần chờ:** lùi thời gian tạo đơn bằng SQL rồi tải lại trang "Đơn hàng của tôi":
> ```sql
> UPDATE orders SET created_at = NOW() - INTERVAL 40 MINUTE WHERE id = <mã đơn>;
> ```
> Việc tự hủy chạy khi mở trang checkout, trang QR, "Đơn hàng của tôi" hoặc khi trang QR hỏi trạng thái. Không có cron chạy nền.

---

## 7. Xem kết quả ở đâu

| Nơi | Đường dẫn | Thấy gì |
|---|---|---|
| Giao dịch VietQR (admin) | `/admin/payment` | Mọi tiền vào: nguồn (SePay / Casso / Giả lập / Admin xác nhận), số tiền, nội dung, kết quả. Lọc **"Cần kiểm tra"** để xem các khoản lệch |
| Chi tiết đơn (admin) | `/admin/order/detail/<id>` | Khung **Thanh toán**: trạng thái, giờ nhận tiền, các giao dịch của đơn; mục **Lịch sử** |
| Đơn hàng của tôi (khách) | `/profile/orders` → Xem chi tiết | Trạng thái thanh toán, tiến trình đơn |
| Database | bảng `payment_transactions`, `orders` (`payment_status`, `paid_at`, `payment_code`), `order_history` | Dữ liệu gốc |

---

## 8. Dọn dữ liệu test

**Xóa giao dịch test** (đặt tiền tố riêng cho mã giao dịch khi test, ví dụ `TEST-`):
```sql
DELETE FROM payment_transactions WHERE transaction_ref LIKE 'TEST-%';
DELETE FROM payment_transactions WHERE provider = 'sandbox';
```

**Xóa toàn bộ đơn của một tài khoản test:**
```sql
SET @u := (SELECT u.id FROM users u JOIN accounts a ON a.id = u.account_id WHERE a.email = 'email-test@example.com');
DELETE FROM payment_transactions WHERE order_id IN (SELECT id FROM orders WHERE user_id = @u);
DELETE FROM order_history        WHERE order_id IN (SELECT id FROM orders WHERE user_id = @u);
DELETE FROM refund_requests      WHERE user_id = @u;
DELETE FROM voucher_usages       WHERE user_id = @u;
DELETE FROM orders               WHERE user_id = @u;
DELETE FROM accounts WHERE email = 'email-test@example.com';  -- users, địa chỉ, giỏ hàng tự xóa theo
```

**Lưu ý trước khi xóa:**
- **Đơn đã "Giao thành công" đã trừ kho thật.** Trước khi xóa đơn như vậy, cộng trả số lượng vào `products.stock_quantity`.
- **Bộ test tự động** (`php tests/run_tests.php`) chạy trên DB riêng `lego_shop_test` nên không cần dọn.

---

## 9. Trước khi chạy thật

- [ ] **Tắt** "Chế độ giả lập thanh toán" (`payment_sandbox = 0`). Nếu vẫn bật, khách tự xác nhận thanh toán được mà không cần chuyển tiền.
- [ ] Nhập đúng **ngân hàng / số tài khoản / chủ tài khoản** và quét QR mẫu trong trang Cài đặt để kiểm tra.
- [ ] Đổi API key: tạo key mới bằng `php -r "echo bin2hex(random_bytes(24));"`, sửa dòng `PAYMENT_WEBHOOK_KEY` trong `.env` trên server, rồi cập nhật key mới bên SePay/Casso.
- [ ] Trong `.env` trên server: `APP_ENV=production`, `APP_DEBUG=false`, đặt mật khẩu DB riêng (`DB_USER`, `DB_PASS`).
- [ ] Webhook URL dùng **HTTPS** và domain thật: `https://<domain>/lego_shop_php/payment/webhook`.
- [ ] Xóa giao dịch test (mục 8) để báo cáo ở `/admin/payment` không bị lẫn.
- [ ] Chọn thời gian **hết hạn thanh toán** phù hợp (mặc định 30 phút).
