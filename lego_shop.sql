-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th3 24, 2026 lúc 06:08 AM
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
  `status` enum('active','locked') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `accounts`
--

INSERT INTO `accounts` (`id`, `phone`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(5, '0961589023', 'viethoang0101010@gmail.com', '$2y$10$piPSlwd3/PpeaaMhG4CwjuevsiaCvKEBzbbg7kWeZE2coii2VLsja', 'customer', 'active', '2026-03-23 21:44:10'),
(6, '', '', '$2y$10$NO6D2pBke7kylpXztZ9I9.7NFOBgWqoNDGh7nWve9otjRmGELGgsW', 'customer', 'active', '2026-03-23 21:54:32'),
(7, '0961589323', 'viethoang010s1010@gmail.com', '$2y$10$WF3..hq.Sxk6SfesEvxqgOhxODeeBp33xVI2zIUl0t8D9eeCEzG7m', 'customer', 'active', '2026-03-23 22:01:23');

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
(1, 'LEGO Technic', 'Dòng siêu xe và máy móc kỹ thuật phức tạp', 'category1.webp', 'active', 0),
(2, 'LEGO Harry Potter', 'Thế giới phù thủy Hogwarts', 'category3.webp', 'active', 0),
(3, 'LEGO Star Wars', 'Cuộc chiến giữa các vì sao', 'category7.webp', 'active', 0),
(4, 'LEGO City', 'Xây dựng thành phố mơ ước', 'category5.webp', 'active', 0),
(5, 'LEGO Architecture', 'Các công trình kiến trúc nổi tiếng thế giới', 'category8.webp', 'active', 0),
(6, 'LEGO Creator Expert', 'Các mẫu lắp ráp chi tiết cho người lớn', 'category6.webp', 'active', 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `total_amount` int(11) DEFAULT 0,
  `status` enum('draft','completed') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `inventory_transactions`
--

CREATE TABLE `inventory_transactions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `transaction_type` enum('IN','OUT') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','delivered','cancelled') DEFAULT 'pending',
  `payment_method` enum('cash','transfer','online') NOT NULL,
  `total_amount` int(11) NOT NULL,
  `shipping_fullname` varchar(100) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_street` varchar(255) NOT NULL,
  `shipping_ward` varchar(100) NOT NULL,
  `shipping_district` varchar(100) NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `unit` varchar(50) DEFAULT 'Hộp',
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

INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `description`, `unit`, `import_price`, `profit_margin`, `selling_price`, `stock_quantity`, `min_stock_level`, `status`, `created_at`) VALUES
(1, 1, 'TEC-42141', 'Siêu xe McLaren Formula 1', 'Mô hình xe đua F1 tỉ lệ thực tế', 'Hộp', 3500000, 0.2, 4200000, 15, 5, 1, '2026-03-23 19:47:56'),
(2, 1, 'TEC-42115', 'Lamborghini Sián FKP 37', 'Siêu xe thể thao màu xanh lá đặc trưng', 'Hộp', 8000000, 0.15, 9200000, 5, 5, 1, '2026-03-23 19:47:56'),
(3, 2, 'HP-71043', 'Lâu đài Hogwarts Castle', 'Mô hình lâu đài cực đại với hơn 6000 mảnh ghép', 'Hộp', 9000000, 0.2, 10800000, 3, 5, 1, '2026-03-23 19:47:56'),
(4, 3, 'SW-75192', 'Millennium Falcon', 'Phi thuyền biểu tượng của Han Solo', 'Hộp', 15000000, 0.1, 16500000, 2, 5, 1, '2026-03-23 19:47:56'),
(5, 3, 'SW-75300', 'Imperial TIE Fighter', 'Máy bay chiến đấu của Đế chế', 'Hộp', 900000, 0.25, 1125000, 20, 5, 1, '2026-03-23 19:47:56'),
(6, 5, 'ARC-21044', 'Paris Skyline', 'Bản đồ kiến trúc thành phố Paris', 'Hộp', 1200000, 0.3, 1560000, 12, 5, 1, '2026-03-23 19:47:56'),
(7, 5, 'ARC-21058', 'Đại kim tự tháp Giza', 'Kỳ quan thế giới cổ đại', 'Hộp', 2500000, 0.2, 3000000, 8, 5, 1, '2026-03-23 19:47:56'),
(8, 6, 'CRE-10281', 'Cây Bonsai Nhật Bản', 'Mẫu lắp ráp thư giãn cho người lớn', 'Hộp', 1100000, 0.2, 1320000, 0, 5, 1, '2026-03-23 19:47:56');

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
(1, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS cao cấp', 1.5, '65 x 27 x 13 cm', '18+', 1432, 2022, 'Lái xe vào thế giới đua xe thể thao đỉnh cao với mô hình McLaren Racing. Chiếc xe là sự hợp tác thiết kế chặt chẽ giữa LEGO và đội đua McLaren, tái hiện hoàn hảo các chi tiết khí động học.'),
(2, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS cao cấp', 3.2, '60 x 25 x 13 cm', '18+', 3696, 2020, 'Trải nghiệm sức mạnh và thiết kế tinh tế của siêu xe Lamborghini Sián FKP 37. Mô hình sở hữu động cơ V12 với các piston có thể chuyển động, hộp số 8 cấp và cửa cắt kéo trứ danh.'),
(3, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS an toàn', 5.8, '69 x 43 x 58 cm', '16+', 6020, 2018, 'Khám phá phép thuật tại Trường Phù thủy và Pháp sư Hogwarts. Bộ lắp ráp cực đại này bao gồm Đại sảnh đường, tháp học, Phòng chứa Bí mật, Cây Liễu Roi và túp lều của Hagrid.'),
(4, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS an toàn', 8.5, '84 x 56 x 21 cm', '16+', 7541, 2017, 'Gia nhập phi hành đoàn trên chiếc tàu vũ trụ nhanh nhất dải ngân hà. Millennium Falcon phiên bản Ultimate Collector Series là một trong những bộ LEGO lớn nhất từng được sản xuất với độ chi tiết kinh ngạc.'),
(5, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS an toàn', 0.5, '17 x 14 x 15 cm', '8+', 432, 2021, 'Tái hiện lại các trận chiến không gian kịch tính trong bộ ba phim Star Wars kinh điển với máy bay chiến đấu Imperial TIE Fighter, đi kèm khoang lái mở và súng bắn đạn lò xo.'),
(6, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS an toàn', 0.6, '28 x 11 x 22 cm', '12+', 649, 2019, 'Mang vẻ đẹp lãng mạn của thủ đô Paris nước Pháp vào không gian sống của bạn. Bức tranh toàn cảnh thu nhỏ bao gồm Khải Hoàn Môn, đại lộ Champs-Elysées, Tháp Eiffel và bảo tàng Louvre.'),
(7, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS an toàn', 1.8, '35 x 32 x 20 cm', '18+', 1476, 2022, 'Quay ngược thời gian về thế kỷ 26 TCN và khám phá cách người Ai Cập cổ đại xây dựng một trong Bảy Kỳ quan Thế giới Cổ đại với sa bàn sông Nile và cấu trúc cắt lớp độc đáo.'),
(8, 'Tập đoàn LEGO', 'Đan Mạch', 'Nhựa ABS an toàn', 0.8, '18 x 21 x 20 cm', '18+', 878, 2021, 'Tìm kiếm sự bình yên và tĩnh lặng với nghệ thuật cắt tỉa cây Bonsai. Bạn có thể tự do thay đổi giữa tán lá xanh tươi mát của mùa hè và sắc hồng rực rỡ của hoa anh đào mùa xuân.');

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
(3, 3, 'product7.webp', 1),
(4, 4, 'product1.webp', 1),
(5, 5, 'product16.webp', 1),
(6, 6, 'product17.webp', 1),
(7, 7, 'product11.webp', 1),
(8, 8, 'product13.webp', 1);

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
(1, 6, 5, 5, 'hihi', 'approved', '2026-03-24 09:54:51');

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
(5, 5, 'Hoàng Nguyễn'),
(6, 6, ''),
(7, 7, 'Hoàng Nguyễn');

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
(5, 5, 'Hoàng Nguyễn', '0961589023', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', 1),
(6, 6, '', '', '', '', '', '', 1),
(7, 7, 'Hoàng Nguyễn', '0961589323', '451 Phạm Thế Hiển', 'Phường Bến Nghé', 'Quận 1', 'Hồ Chí Minh', 1);

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
  ADD KEY `admin_id` (`admin_id`);

--
-- Chỉ mục cho bảng `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `receipt_id` (`receipt_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD PRIMARY KEY (`id`),
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
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  ADD CONSTRAINT `import_receipts_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `accounts` (`id`);

--
-- Các ràng buộc cho bảng `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  ADD CONSTRAINT `import_receipt_details_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `import_receipts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `import_receipt_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Các ràng buộc cho bảng `inventory_transactions`
--
ALTER TABLE `inventory_transactions`
  ADD CONSTRAINT `inventory_transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
