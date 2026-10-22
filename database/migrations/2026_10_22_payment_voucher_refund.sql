-- =====================================================================
-- MIGRATION: Thanh toán VietQR + Mã giảm giá + Hoàn tiền + Sửa lỗi DB
-- Chạy 1 lần trên database `lego_shop` (phpMyAdmin > Import hoặc mysql CLI)
-- =====================================================================
SET NAMES utf8mb4;
START TRANSACTION;

-- ---------------------------------------------------------------------
-- 1. SỬA LỖI DỮ LIỆU CŨ
-- ---------------------------------------------------------------------

-- Mật khẩu admin đang lưu dạng chữ thường ('123456') -> băm bcrypt (mật khẩu vẫn là 123456)
UPDATE `accounts`
SET `password` = '$2y$10$TqLA5OtVj1jud.cBAcIv4.3SuZ3WQ5OejmEEsKohM8fSPrPIGVxM.'
WHERE `id` = 1 AND `password` = '123456';

-- Index cho các cột lọc thường xuyên trên bảng orders
ALTER TABLE `orders`
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created_at` (`created_at`);

-- ---------------------------------------------------------------------
-- 2. BẢNG ORDERS: thêm thông tin giảm giá + thanh toán online
-- ---------------------------------------------------------------------
ALTER TABLE `orders`
  MODIFY `payment_method` enum('cash','transfer','online','vietqr') NOT NULL,
  MODIFY `payment_status` enum('unpaid','paid','refund_pending','refunded') DEFAULT 'unpaid',
  ADD `subtotal_amount` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng tiền hàng trước giảm giá' AFTER `payment_status`,
  ADD `discount_amount` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền được giảm bởi voucher' AFTER `subtotal_amount`,
  ADD `voucher_id` int(11) DEFAULT NULL AFTER `discount_amount`,
  ADD `voucher_code` varchar(50) DEFAULT NULL AFTER `voucher_id`,
  ADD `payment_code` varchar(30) DEFAULT NULL COMMENT 'Nội dung chuyển khoản, VD: LEGO25' AFTER `voucher_code`,
  ADD `paid_at` datetime DEFAULT NULL AFTER `payment_code`,
  ADD KEY `idx_orders_voucher` (`voucher_id`),
  ADD UNIQUE KEY `uq_orders_payment_code` (`payment_code`);

-- Đơn cũ: tạm tính = tổng tiền (chưa có giảm giá)
UPDATE `orders` SET `subtotal_amount` = `total_amount` WHERE `subtotal_amount` = 0;

-- ---------------------------------------------------------------------
-- 3. MÃ GIẢM GIÁ
-- ---------------------------------------------------------------------
CREATE TABLE `vouchers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `discount_type` enum('percent','fixed') NOT NULL DEFAULT 'fixed',
  `discount_value` int(11) NOT NULL COMMENT 'percent: 1-100 | fixed: số tiền VNĐ',
  `max_discount` int(11) DEFAULT NULL COMMENT 'Giảm tối đa (chỉ áp dụng cho percent), NULL = không giới hạn',
  `min_order_value` int(11) NOT NULL DEFAULT 0,
  `usage_limit` int(11) DEFAULT NULL COMMENT 'Tổng lượt dùng, NULL = không giới hạn',
  `used_count` int(11) NOT NULL DEFAULT 0,
  `per_user_limit` int(11) NOT NULL DEFAULT 1,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_voucher_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `voucher_usages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `voucher_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `discount_amount` int(11) NOT NULL DEFAULT 0,
  `used_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usage_order` (`order_id`),
  KEY `idx_usage_voucher_user` (`voucher_id`,`user_id`),
  CONSTRAINT `fk_usage_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usage_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL;

-- Dữ liệu mẫu
INSERT INTO `vouchers` (`code`, `description`, `discount_type`, `discount_value`, `max_discount`, `min_order_value`, `usage_limit`, `per_user_limit`, `start_date`, `end_date`, `status`) VALUES
('WELCOME50K', 'Giảm 50.000đ cho đơn từ 300.000đ', 'fixed', 50000, NULL, 300000, 100, 1, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 'active'),
('LEGO10', 'Giảm 10% tối đa 200.000đ cho đơn từ 500.000đ', 'percent', 10, 200000, 500000, NULL, 3, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 'active'),
('FREESHIP30K', 'Giảm 30.000đ cho mọi đơn hàng', 'fixed', 30000, NULL, 0, 500, 5, '2026-01-01 00:00:00', '2027-12-31 23:59:59', 'active');

-- ---------------------------------------------------------------------
-- 4. GIAO DỊCH THANH TOÁN (lưu log webhook ngân hàng / xác nhận thủ công)
-- ---------------------------------------------------------------------
CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `provider` varchar(30) NOT NULL DEFAULT 'vietqr' COMMENT 'sepay | casso | sandbox | manual',
  `transaction_ref` varchar(100) DEFAULT NULL COMMENT 'Mã giao dịch phía ngân hàng (chống xử lý trùng)',
  `amount` int(11) NOT NULL DEFAULT 0,
  `content` varchar(255) DEFAULT NULL,
  `status` enum('success','mismatch','unmatched') NOT NULL DEFAULT 'success',
  `raw_data` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_provider_ref` (`provider`,`transaction_ref`),
  KEY `idx_tx_order` (`order_id`),
  CONSTRAINT `fk_tx_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- 5. YÊU CẦU HOÀN TIỀN
-- ---------------------------------------------------------------------
CREATE TABLE `refund_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('cancel','return') NOT NULL COMMENT 'cancel: hủy đơn đã thanh toán | return: trả hàng sau khi nhận',
  `amount` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `evidence_image` varchar(255) DEFAULT NULL,
  `bank_name` varchar(100) NOT NULL,
  `bank_account_number` varchar(30) NOT NULL,
  `bank_account_name` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected','completed') NOT NULL DEFAULT 'pending',
  `admin_note` text DEFAULT NULL,
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_refund_order` (`order_id`),
  KEY `idx_refund_user` (`user_id`),
  KEY `idx_refund_status` (`status`),
  CONSTRAINT `fk_refund_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_refund_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_refund_admin` FOREIGN KEY (`processed_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ---------------------------------------------------------------------
-- 6. CẤU HÌNH CỬA HÀNG: tài khoản nhận tiền VietQR + chính sách hoàn tiền
-- ---------------------------------------------------------------------
ALTER TABLE `shop_settings`
  ADD `bank_code` varchar(20) DEFAULT 'MB' COMMENT 'Mã ngân hàng theo VietQR (MB, VCB, TCB, ACB...)' AFTER `zalo_url`,
  ADD `bank_account_no` varchar(30) DEFAULT '0961589023' AFTER `bank_code`,
  ADD `bank_account_name` varchar(100) DEFAULT 'NGUYEN HOANG' AFTER `bank_account_no`,
  ADD `payment_webhook_key` varchar(100) DEFAULT NULL COMMENT 'API key webhook SePay/Casso' AFTER `bank_account_name`,
  ADD `payment_sandbox` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = hiện nút giả lập thanh toán (dùng khi demo/local)' AFTER `payment_webhook_key`,
  ADD `payment_timeout_minutes` int(11) NOT NULL DEFAULT 30 COMMENT 'Quá thời gian chưa thanh toán VietQR -> tự hủy đơn' AFTER `payment_sandbox`,
  ADD `refund_days` int(11) NOT NULL DEFAULT 7 COMMENT 'Số ngày được yêu cầu trả hàng/hoàn tiền sau khi giao' AFTER `payment_timeout_minutes`;

UPDATE `shop_settings`
SET `bank_code` = 'MB', `bank_account_no` = '0961589023', `bank_account_name` = 'NGUYEN HOANG',
    `payment_webhook_key` = MD5(CONCAT(RAND(), NOW()))
WHERE `id` = 1;

COMMIT;
