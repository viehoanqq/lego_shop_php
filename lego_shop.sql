-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 15, 2026 lúc 06:24 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `lego_shop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','locked','deleted') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `accounts`
--

INSERT INTO `accounts` (`id`, `phone`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'admin', 'admin', '123456', 'admin', 'active', '2026-04-03 17:34:49'),
(2, '0999999999', 'viethoang0101010@gmail.com', '$2y$10$VJ8zos7A6CFApDmS.nMiXuPxLCPgQAM0GxkaFI5juom80RP2OU.he', 'customer', 'active', '2026-04-01 13:01:05'),
(3, '0961519023', 'test@gmail.com', '$2y$10$sFZ2n8Iv9f5kFXpatSgOWOfUnG6S28onMJD1nX7rMGzoqJT6ngt6S', 'customer', 'locked', '2026-04-04 13:29:29'),
(4, '0961589923', 'TESTORDER@gmail.com', '$2y$10$q/T0oFKpj0GxX0umxLc4YOERaCi8Jo4qomeZ0g8ULLbBCQvMUKU6q', 'customer', 'deleted', '2026-04-04 13:29:56'),
(5, '0900000000', 'test1@gmail.com', '$2y$10$DAcQheS2vUE0y6L3BQ4Ksej9VaJhDQyS7RibyIksr1CQKyrXT4rae', 'customer', 'locked', '2026-04-04 07:44:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `ordering` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image_url`, `status`, `ordering`) VALUES
(1, 'LEGO Technic', 'Khám phá thế giới cơ khí và xe cộ đỉnh cao.', 'category1.webp', 'active', 0),
(2, 'LEGO Harry Potter', 'Lạc vào thế giới phép thuật Hogwarts huyền bí.', 'category3.webp', 'active', 0),
(3, 'LEGO Star Wars', 'Cuộc chiến giữa các vì sao và những con tàu vũ trụ.', 'category8.webp', 'active', 0),
(4, 'LEGO City', 'Xây dựng thành phố trong mơ của bạn.', 'category5.webp', 'active', 0),
(5, 'LEGO Architecture', 'Tái hiện các công trình kiến trúc nổi tiếng thế giới.', 'category2.webp', 'active', 0),
(6, 'zzz', 'Thử thách độ khó cao dành cho người lớn.', 'category6.webp', 'locked', 0),
(7, 'LEGO Ninjago', 'Sát cánh cùng các Ninja bảo vệ thế giới.', 'category4.webp', 'active', 0),
(8, 'LEGO Super Heroes', 'Sưu tập các siêu anh hùng Marvel & DC.', 'category7.webp', 'active', 0),
(13, 'DANH MỤC TEST', 'TEST THÊM DANH MỤC', '1775230316.webp', 'locked', 0),
(14, 'DANH MỤC TEST CÓ SẢN PHẨM', 'DANH MỤC TEST CÓ SẢN PHẨM', '1775230302.jpg', 'active', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `total_amount` int(11) DEFAULT 0,
  `status` enum('draft','completed') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_receipts`
--

INSERT INTO `import_receipts` (`id`, `admin_id`, `supplier_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 10, 2200000, 'completed', '2026-04-01 22:52:00'),
(2, 1, 10, 1800000, 'completed', '2026-04-01 22:52:00'),
(3, 1, 10, 2200000, 'completed', '2026-04-01 22:54:00'),
(4, 1, 10, 3150000, 'completed', '2026-04-01 22:55:00'),
(5, 1, 10, 4140000, 'completed', '2026-04-02 22:58:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipt_details`
--

CREATE TABLE `import_receipt_details` (
  `id` int(11) NOT NULL,
  `receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `calculated_average_price` int(11) DEFAULT 0,
  `calculated_selling_price` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `import_receipt_details`
--

INSERT INTO `import_receipt_details` (`id`, `receipt_id`, `product_id`, `quantity`, `price`, `calculated_average_price`, `calculated_selling_price`) VALUES
(1, 1, 36, 10, 100000, 100000, 130000),
(2, 1, 37, 10, 120000, 120000, 156000),
(3, 2, 36, 15, 120000, 112000, 145600),
(4, 3, 37, 10, 100000, 107500, 139750),
(5, 3, 1, 10, 120000, 120000, 156000),
(6, 4, 4, 10, 120000, 120000, 156000),
(7, 4, 8, 15, 130000, 130000, 169000),
(8, 5, 13, 10, 120000, 120000, 240000),
(9, 5, 10, 12, 120000, 120000, 156000),
(10, 5, 6, 15, 100000, 100000, 130000);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','shipping','delivered','cancelled') DEFAULT 'pending',
  `payment_method` enum('cash','transfer','online') NOT NULL,
  `payment_status` enum('unpaid','paid','refunded') DEFAULT 'unpaid',
  `total_amount` int(11) NOT NULL,
  `shipping_fullname` varchar(100) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_street` varchar(255) NOT NULL,
  `shipping_ward` varchar(100) NOT NULL,
  `shipping_district` varchar(100) NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `payment_method`, `payment_status`, `total_amount`, `shipping_fullname`, `shipping_phone`, `shipping_street`, `shipping_ward`, `shipping_district`, `shipping_city`, `created_at`) VALUES
(1, 2, 'delivered', 'cash', 'unpaid', 1497600, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-01 22:53:49'),
(2, 2, 'delivered', 'transfer', 'paid', 2242500, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-02 22:56:39'),
(3, 2, 'delivered', 'cash', 'unpaid', 1850000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-02 22:59:06'),
(4, 2, 'delivered', 'transfer', 'paid', 1428700, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-03 23:01:57'),
(5, 2, 'delivered', 'cash', 'unpaid', 1568000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-03 23:03:14'),
(6, 2, 'delivered', 'transfer', 'paid', 1200000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-04 11:18:38'),
(7, 2, 'pending', 'cash', 'unpaid', 139750, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-04 11:38:59'),
(8, 2, 'pending', 'cash', 'unpaid', 1326000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-04 11:39:20'),
(9, 2, 'delivered', 'transfer', 'paid', 780000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-04 11:39:31'),
(10, 2, 'delivered', 'cash', 'unpaid', 156000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-15 07:02:20'),
(11, 2, 'pending', 'cash', 'unpaid', 156000, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', '2026-04-15 09:12:11');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `cost_price` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `cost_price`) VALUES
(1, 1, 37, 4, 156000, 120000.00),
(2, 1, 36, 6, 145600, 112000.00),
(3, 2, 4, 5, 156000, 120000.00),
(4, 2, 1, 4, 156000, 120000.00),
(5, 2, 37, 6, 139750, 107500.00),
(6, 3, 13, 5, 240000, 120000.00),
(7, 3, 6, 5, 130000, 100000.00),
(8, 4, 6, 3, 130000, 100000.00),
(9, 4, 4, 3, 156000, 120000.00),
(10, 4, 37, 2, 139750, 107500.00),
(11, 4, 36, 2, 145600, 112000.00),
(12, 5, 36, 7, 224000, 112000.00),
(13, 6, 13, 5, 240000, 120000.00),
(14, 7, 37, 1, 139750, 107500.00),
(15, 8, 8, 6, 169000, 130000.00),
(16, 8, 4, 2, 156000, 120000.00),
(17, 9, 10, 5, 156000, 120000.00),
(18, 10, 10, 1, 156000, 120000.00),
(19, 11, 1, 1, 156000, 120000.00);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `order_history`
--

INSERT INTO `order_history` (`id`, `order_id`, `status`, `changed_at`, `note`) VALUES
(1, 1, 'confirmed', '2026-04-01 22:53:58', ''),
(2, 1, 'shipping', '2026-04-01 22:54:00', ''),
(3, 1, 'delivered', '2026-04-01 22:54:02', ''),
(4, 2, 'confirmed', '2026-04-02 22:56:49', ''),
(5, 2, 'shipping', '2026-04-02 22:56:51', ''),
(6, 2, 'delivered', '2026-04-02 22:56:53', ''),
(7, 3, 'confirmed', '2026-04-02 22:59:13', ''),
(8, 3, 'shipping', '2026-04-02 22:59:16', ''),
(9, 3, 'delivered', '2026-04-02 22:59:18', ''),
(10, 4, 'confirmed', '2026-04-03 23:02:09', ''),
(11, 4, 'shipping', '2026-04-03 23:02:11', ''),
(12, 4, 'delivered', '2026-04-03 23:02:13', ''),
(13, 5, 'confirmed', '2026-04-03 23:03:26', ''),
(14, 5, 'shipping', '2026-04-03 23:03:28', ''),
(15, 5, 'delivered', '2026-04-03 23:03:30', ''),
(16, 6, 'confirmed', '2026-04-04 11:18:52', ''),
(17, 6, 'shipping', '2026-04-04 11:18:54', ''),
(18, 6, 'delivered', '2026-04-04 11:18:56', ''),
(19, 9, 'confirmed', '2026-04-04 11:40:30', ''),
(20, 9, 'shipping', '2026-04-04 11:40:32', ''),
(21, 9, 'delivered', '2026-04-04 11:40:34', ''),
(22, 10, 'confirmed', '2026-04-15 07:02:27', ''),
(23, 10, 'shipping', '2026-04-15 07:02:29', ''),
(24, 10, 'delivered', '2026-04-15 07:02:31', '');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `import_price` int(11) DEFAULT 0,
  `profit_margin` float DEFAULT 0,
  `selling_price` int(11) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5,
  `status` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `description`, `import_price`, `profit_margin`, `selling_price`, `stock_quantity`, `min_stock_level`, `status`, `created_at`) VALUES
(1, 3, 'SW-75192', 'Siêu tàu vũ trụ Millennium Falcon', 'Phi thuyền biểu tượng của Han Solo với hơn 7500 mảnh ghép, to nhất lịch sử Star Wars.', 120000, 0.3, 156000, 6, 20, 1, '2026-03-27 14:09:27'),
(2, 1, 'TEC-42115', 'Siêu xe Lamborghini Sián FKP 37', 'Mô hình siêu xe thể thao màu xanh lá tỉ lệ 1:8 chân thực đến từng chi tiết động cơ.', 0, 1, 0, 0, 20, 3, '2026-03-27 14:09:27'),
(3, 2, 'HP-71043', 'Lâu đài Hogwarts Castle', 'Trường học phép thuật Hogwarts siêu to khổng lồ dành cho các fan Harry Potter.', 0, 0.3, 0, 0, 20, 3, '2026-03-27 14:09:27'),
(4, 7, 'NJ-71753', 'Rồng Lửa Của Kai', 'Chiến đấu cùng Ninja lửa Kai cưỡi trên lưng con rồng đỏ rực phun lửa hung tợn.', 120000, 0.3, 156000, 2, 20, 1, '2026-03-27 14:09:27'),
(5, 4, 'CT-60337', 'Tàu Chở Khách Tốc Hành', 'Hệ thống tàu điện thông minh điều khiển qua Bluetooth.', 0, 0.3, 0, 0, 20, 2, '2026-03-27 14:09:27'),
(6, 6, 'CRE-10247', 'Vòng Đu Quay Ferris Wheel', 'Khu vui chơi giải trí tuyệt đẹp cho bộ sưu tập Creator.', 100000, 0.3, 130000, 7, 20, 2, '2026-03-27 14:09:27'),
(7, 5, 'ARC-21044', 'Mô hình Paris Skyline', 'Bản đồ kiến trúc thu nhỏ của thủ đô ánh sáng Paris.', 0, 0.3, 0, 0, 20, 2, '2026-03-27 14:09:27'),
(8, 4, 'CT-60321', 'Trạm Cứu Hỏa Trung Tâm', 'Xe cứu hỏa, trực thăng và đội cứu hộ thành phố City.', 130000, 0.3, 169000, 15, 20, 1, '2026-03-27 14:09:27'),
(9, 8, 'SH-76191', 'Găng Tay Vô Cực Infinity Gauntlet', 'Mô hình Găng tay vô cực của Thanos mạ vàng sáng bóng.', 0, 0.3, 0, 0, 20, 2, '2026-03-27 14:09:27'),
(10, 1, 'TEC-42056', 'Porsche 911 GT3 RS', 'Huyền thoại siêu xe Porsche màu cam rực rỡ.', 120000, 0.3, 156000, 6, 20, 1, '2026-03-27 14:09:27'),
(11, 6, 'CRE-10281', 'Cây Bonsai Nhật Bản', 'Nghệ thuật trồng cây Bonsai thu nhỏ giúp thư giãn tĩnh tâm.', 0, 0.3, 0, 0, 20, 3, '2026-03-27 14:09:27'),
(13, 1, 'TEC-42141', 'Siêu xe McLaren Formula 1', 'Xe đua F1 của đội McLaren với độ chi tiết khí động học cao.', 120000, 1, 240000, 0, 20, 1, '2026-03-27 14:09:27'),
(36, 14, 'SP-TEST', 'SP TEST NEW', 'TEST', 112000, 1, 224000, 10, 20, 2, '2026-04-04 13:34:45'),
(37, 14, 'TEST2', 'TEST SP2', 'TEST2', 107500, 0.3, 139750, 8, 20, 1, '2026-04-03 22:33:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_details`
--

CREATE TABLE `product_details` (
  `product_id` int(11) NOT NULL,
  `manufacturer` varchar(150) DEFAULT 'Tập đoàn LEGO',
  `country_of_origin` varchar(100) DEFAULT 'Đan Mạch',
  `material` varchar(100) DEFAULT 'Nhựa ABS an toàn',
  `weight` float DEFAULT 0,
  `dimensions` varchar(100) DEFAULT NULL,
  `age_range` varchar(20) DEFAULT '6+',
  `pieces` int(11) DEFAULT 0,
  `release_year` int(11) DEFAULT NULL,
  `theme_story` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_details`
--

INSERT INTO `product_details` (`product_id`, `manufacturer`, `country_of_origin`, `material`, `weight`, `dimensions`, `age_range`, `pieces`, `release_year`, `theme_story`) VALUES
(1, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 14.5, '84 x 56 x 21 cm', '16+', 7541, 2017, 'Chiếc phi thuyền Millennium Falcon nhanh nhất dải ngân hà, từng làm chủ Kessel Run dưới 12 parsec! Một siêu phẩm không thể thiếu của mọi fan cứng Star Wars.'),
(2, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 4.8, '60 x 25 x 13 cm', '18+', 1000, 2020, 'Siêu xe thể thao lai điện đầu tiên của Lamborghini. Mô hình tái hiện chân thực động cơ V12, hộp số 8 cấp và cửa cắt kéo đặc trưng của dòng Sián.'),
(3, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 7.2, '69 x 43 x 58 cm', '16+', 6020, 2018, 'Khám phá thế giới phép thuật tại Trường Phù thủy và Pháp sư Hogwarts. Bao gồm Đại sảnh đường, tháp nhọn, lớp học Độc dược và Phòng chứa Bí mật.'),
(4, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 0.8, '27 x 27 x 23 cm', '8+', 563, 2021, 'Cùng Ninja lửa Kai bay lượn trên không trung và tung ra những quả cầu lửa thiêu rụi kẻ thù với chú rồng có thể cử động linh hoạt hàm, cánh và đuôi.'),
(5, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 1.5, '86 x 5 x 10 cm', '7+', 764, 2022, 'Đoàn tàu cao tốc hiện đại tích hợp công nghệ điều khiển từ xa Powered Up. Bao gồm đầu máy có thể phát sáng, toa hành khách và cả toa căn tin sang trọng.'),
(6, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 3.5, '55 x 38 x 60 cm', '16+', 2461, 2015, 'Tận hưởng niềm vui tại khu hội chợ với vòng đu quay khổng lồ. Thiết kế tinh xảo với các cabin rực rỡ và hệ thống xoay bằng tay cực êm ái.'),
(7, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 0.6, '28 x 11 x 22 cm', '12+', 649, 2019, 'Bức tranh toàn cảnh về các công trình biểu tượng của thủ đô Paris, Pháp. Từ Khải Hoàn Môn, Tháp Eiffel đến Bảo tàng Louvre tráng lệ.'),
(8, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 1.4, '34 x 32 x 31 cm', '7+', 766, 2022, 'Đội cứu hỏa LEGO City luôn sẵn sàng đối phó với mọi tình huống khẩn cấp. Trạm cứu hỏa 3 tầng trang bị đầy đủ xe thang, trực thăng và cầu trượt.'),
(9, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 0.7, '13 x 11 x 31 cm', '18+', 590, 2021, 'Sức mạnh thao túng cả vũ trụ giờ đây nằm gọn trong bàn tay của bạn. Mô hình Găng tay vô cực mạ vàng sáng bóng đính kèm 6 viên đá vô cực quyền năng.'),
(10, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 3.2, '57 x 25 x 17 cm', '16+', 2704, 2016, 'Huyền thoại xe đua đường phố với thiết kế khí động học tuyệt đỉnh. Sở hữu lớp sơn màu cam rực rỡ và các chi tiết cơ khí mô phỏng chính xác xe thật.'),
(11, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 0.7, '21 x 20 x 18 cm', '18+', 878, 2021, 'Tận hưởng khoảnh khắc thiền định khi tự tay ghép và tạo dáng cho cây Bonsai tĩnh lặng. Đặc biệt có thể thay đổi tán lá xanh sang hoa anh đào hồng nở rộ.'),
(13, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS nguyên sinh', 1.8, '65 x 27 x 13 cm', '18+', 1432, 2022, 'Sự hợp tác đỉnh cao giữa nhóm thiết kế LEGO và đội đua McLaren Racing. Mô phỏng chiếc F1 mùa giải 2022 với động cơ V6 có piston di chuyển được.'),
(36, 'The LEGO Group', 'Đan Mạch', 'Nhựa ABS an toàn', 0, '10 x 10 x 10 cm', '18+', 1000, 2026, 'TEST'),
(37, 'The LEGO Group', 'Đan Mạch', 'Nhựa ABS an toàn', 0, '10 x 10 x 10 cm', '18+', 1, 2026, 'TEST');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_main` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`, `is_main`) VALUES
(1, 1, 'product1.webp', 1),
(2, 2, 'product2.webp', 1),
(4, 4, 'product4.webp', 1),
(5, 5, 'product5.webp', 1),
(6, 6, 'product11.webp', 1),
(7, 7, 'product17.webp', 1),
(8, 8, 'product3.webp', 1),
(9, 9, 'product9.webp', 1),
(10, 10, 'product10.webp', 1),
(11, 11, 'product13.webp', 1),
(13, 13, 'product14.webp', 1),
(14, 1, 'product1-1.webp', 0),
(18, 2, 'product2-1.webp', 0),
(19, 2, 'product2-2.webp', 0),
(54, 3, 'product3.webp', 1),
(73, 36, 'default.jpg', 0),
(74, 36, '1775284495_HAUTRUONG.jpg', 0),
(75, 36, '1775230345_new-product.webp', 1),
(76, 37, '1775230381_new-product.webp', 0),
(77, 37, '1775230403_login-bgr.webp', 1),
(78, 37, '1776214283_282_news2.webp', 0),
(79, 37, '1776214294_751_product1.webp', 0),
(82, 4, '1776214605_556_product3.webp', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','hidden') DEFAULT 'approved',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `status`, `created_at`) VALUES
(1, 6, 2, 5, 'ok', 'approved', '2026-04-03 23:22:22'),
(2, 37, 2, 5, 'okkk', 'approved', '2026-04-03 23:22:31'),
(3, 36, 2, 4, 'good', 'hidden', '2026-04-03 23:22:41'),
(4, 10, 2, 5, 'ok', 'approved', '2026-04-04 11:41:46');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `shop_settings`
--

CREATE TABLE `shop_settings` (
  `id` int(11) NOT NULL DEFAULT 1,
  `shop_name` varchar(255) DEFAULT 'LEGO WORLD STORE',
  `company_name` varchar(255) DEFAULT 'Công ty cổ phần LEGO',
  `business_license` varchar(255) DEFAULT '0309132354',
  `logo_url` varchar(255) DEFAULT 'logo.png',
  `phone` varchar(50) DEFAULT '1900 1208',
  `email` varchar(100) DEFAULT 'hotro@legoworldstore.com.vn',
  `address` varchar(255) DEFAULT '273 An Dương Vương, Phường 1, Quận 5, TP. Hồ Chí Minh',
  `working_hours_1` varchar(255) DEFAULT 'Thứ 2 - Thứ 7: 8:00 - 17:00',
  `working_hours_2` varchar(255) DEFAULT 'Chủ nhật: 8:00 - 12:00',
  `policy_1` varchar(255) DEFAULT 'Miễn phí giao hàng đơn từ 500k',
  `policy_2` varchar(255) DEFAULT 'Giao hàng hỏa tốc 4 tiếng',
  `policy_3` varchar(255) DEFAULT 'Chương trình thành viên',
  `policy_4` varchar(255) DEFAULT 'Mua hàng trả góp',
  `policy_5` varchar(255) DEFAULT 'Hệ thống 200 cửa hàng',
  `facebook_url` varchar(255) DEFAULT '#',
  `instagram_url` varchar(255) DEFAULT '#',
  `youtube_url` varchar(255) DEFAULT '#',
  `tiktok_url` varchar(255) DEFAULT '#',
  `zalo_url` varchar(255) DEFAULT '#',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `shop_settings`
--

INSERT INTO `shop_settings` (`id`, `shop_name`, `company_name`, `business_license`, `logo_url`, `phone`, `email`, `address`, `working_hours_1`, `working_hours_2`, `policy_1`, `policy_2`, `policy_3`, `policy_4`, `policy_5`, `facebook_url`, `instagram_url`, `youtube_url`, `tiktok_url`, `zalo_url`, `updated_at`) VALUES
(1, 'LEGO WORLD STORE', 'Công ty cổ phần LEGO', '0309132354', 'logo.png', '1900 1208 000', 'hotro@legoworldstore.com.vn', '273 An Dương Vương, Phường 1, Quận 5, TP. Hồ Chí Minh', 'Thứ 2 - Thứ 7: 8:00 - 17:00', 'Chủ nhật: 8:00 - 12:00', 'Miễn phí giao hàng đơn từ 500k', 'Giao hàng hỏa tốc 4 tiếng', 'Chương trình thành viên VIP', 'Hỗ trợ mua hàng trả góp 0%', 'Hệ thống 200 cửa hàng toàn quốc', '#', '#', '#', '#', '#', '2026-04-04 11:08:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT 'Tên công ty/nhà cung cấp',
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('active','deleted','locked') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `status`, `created_at`) VALUES
(8, 'Công ty TNHH Phân phối Đồ chơi Lego Việt', '02838445566', 'contact@legoviet.vn', '123 Lê Lợi, Phường Bến Nghé, Quận 1, TP.HCM', 'locked', '2026-04-01 13:06:51'),
(10, 'Lego Global Trading Co., Ltd', '0243123456', 'info@legoglobal.com', '45 Đại Cồ Việt, Quận Hai Bà Trưng, Hà Nội', 'locked', '2026-04-01 13:06:51'),
(11, 'Xưởng Nhập Khẩu LEGO Chính Hãng Sài Gòn', '0988776655', 'nhapkhaulego@gmail.com', '789 Nguyễn Văn Linh, Phường Tân Phong, Quận 7, TP.HCM', 'active', '2026-04-01 13:06:51'),
(12, 'Đại Lý Đồ Chơi Lắp Ráp Cao Cấp BrickStore', '0912000111', 'support@brickstore.vn', '12 Xuân Thủy, Quận Cầu Giấy, Hà Nội', 'deleted', '2026-04-01 13:06:51');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `account_id`, `fullname`) VALUES
(1, 1, 'Administrator'),
(2, 2, 'Nguyễn Việt Hoàng'),
(3, 3, 'TEST'),
(4, 4, 'TEST ORDER'),
(5, 5, 'Kiều Hoài Nam');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `street` varchar(255) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `is_default` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `receiver_name`, `receiver_phone`, `street`, `ward`, `district`, `city`, `is_default`) VALUES
(1, 2, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', 1),
(2, 4, 'TEST ORDER', '11111111111', 'TEST', 'Phường Bến Thành', 'Quận 1', 'Hồ Chí Minh', 0),
(3, 5, 'Kiều Hoài Nam', '0900000000', '273 An dương vương', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `product_id` int(11) NOT NULL COMMENT 'ID sản phẩm yêu thích',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `wishlists`
--

INSERT INTO `wishlists` (`id`, `account_id`, `product_id`, `created_at`) VALUES
(1, 2, 4, '2026-04-03 23:37:45'),
(2, 2, 6, '2026-04-03 23:37:47');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Chỉ mục cho bảng `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `fk_import_supplier` (`supplier_id`);

--
-- Chỉ mục cho bảng `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`);

--
-- Chỉ mục cho bảng `product_details`
--
ALTER TABLE `product_details`
  ADD PRIMARY KEY (`product_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `shop_settings`
--
ALTER TABLE `shop_settings`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account_id` (`account_id`);

--
-- Chỉ mục cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`account_id`,`product_id`),
  ADD KEY `fk_wishlist_product` (`product_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD CONSTRAINT `fk_import_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `import_receipts_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `accounts` (`id`);

--
-- Các ràng buộc cho bảng `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  ADD CONSTRAINT `import_receipt_details_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `import_receipts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_receipt_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Các ràng buộc cho bảng `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Các ràng buộc cho bảng `product_details`
--
ALTER TABLE `product_details`
  ADD CONSTRAINT `product_details_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlist_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
