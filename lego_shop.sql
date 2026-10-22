-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: lego_shop
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `status` enum('active','locked','deleted') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accounts`
--

LOCK TABLES `accounts` WRITE;
/*!40000 ALTER TABLE `accounts` DISABLE KEYS */;
INSERT INTO `accounts` VALUES (1,'admin','admin','$2y$10$TqLA5OtVj1jud.cBAcIv4.3SuZ3WQ5OejmEEsKohM8fSPrPIGVxM.','admin','active','2026-04-03 17:34:49'),(2,'0999999999','viethoang0101010@gmail.com','$2y$10$VJ8zos7A6CFApDmS.nMiXuPxLCPgQAM0GxkaFI5juom80RP2OU.he','customer','active','2026-04-01 13:01:05'),(3,'0961519023','test@gmail.com','$2y$10$sFZ2n8Iv9f5kFXpatSgOWOfUnG6S28onMJD1nX7rMGzoqJT6ngt6S','customer','locked','2026-04-04 13:29:29'),(4,'0961589923','TESTORDER@gmail.com','$2y$10$q/T0oFKpj0GxX0umxLc4YOERaCi8Jo4qomeZ0g8ULLbBCQvMUKU6q','customer','deleted','2026-04-04 13:29:56'),(5,'test','test','$2y$10$k9vTyk1Dgt78pRmd/Qif6en2XDljhV6Uat5ygK3dQw8qBcoLB9A46','customer','active','2026-04-04 07:44:52');
/*!40000 ALTER TABLE `accounts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_id` (`cart_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (61,44,38,1);
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carts`
--

DROP TABLE IF EXISTS `carts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carts`
--

LOCK TABLES `carts` WRITE;
/*!40000 ALTER TABLE `carts` DISABLE KEYS */;
INSERT INTO `carts` VALUES (44,2);
/*!40000 ALTER TABLE `carts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `ordering` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'LEGO Technic','Khám phá thế giới cơ khí và xe cộ đỉnh cao.','category1.webp','active',0),(2,'LEGO Harry Potter','Lạc vào thế giới phép thuật Hogwarts huyền bí.','category3.webp','active',0),(3,'LEGO Star Wars','Cuộc chiến giữa các vì sao và những con tàu vũ trụ.','category8.webp','active',0),(4,'LEGO City','Xây dựng thành phố trong mơ của bạn.','category5.webp','active',0),(5,'LEGO Architecture','Tái hiện các công trình kiến trúc nổi tiếng thế giới.','category2.webp','hidden',0),(6,'zzz','Thử thách độ khó cao dành cho người lớn.','category6.webp','hidden',0),(7,'LEGO Ninjago','Sát cánh cùng các Ninja bảo vệ thế giới.','category4.webp','hidden',0),(8,'LEGO Super Heroes','Sưu tập các siêu anh hùng Marvel & DC.','category7.webp','hidden',0),(14,'DANH MỤC TEST CÓ SẢN PHẨM','DANH MỤC TEST CÓ SẢN PHẨM','1775230302.jpg','hidden',0),(15,'qqq','','default.jpg','active',0);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_receipt_details`
--

DROP TABLE IF EXISTS `import_receipt_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_receipt_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `calculated_average_price` int(11) DEFAULT 0,
  `calculated_selling_price` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `receipt_id` (`receipt_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `import_receipt_details_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `import_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `import_receipt_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_receipt_details`
--

LOCK TABLES `import_receipt_details` WRITE;
/*!40000 ALTER TABLE `import_receipt_details` DISABLE KEYS */;
INSERT INTO `import_receipt_details` VALUES (1,1,36,10,100000,100000,130000),(2,1,37,10,120000,120000,156000),(3,2,36,15,120000,112000,145600),(4,3,37,10,100000,107500,139750),(5,3,1,10,120000,120000,156000),(6,4,4,10,120000,120000,156000),(7,4,8,15,130000,130000,169000),(8,5,13,10,120000,120000,240000),(9,5,10,12,120000,120000,156000),(10,5,6,15,100000,100000,130000),(11,6,2,12,100000,100000,200000),(12,6,3,20,120000,120000,156000),(13,6,13,15,110000,110000,220000),(14,6,7,20,140000,140000,182000),(15,7,4,10,100000,103333,134333),(16,7,13,10,120000,114000,228000),(17,7,36,10,100000,106000,212000),(18,7,5,10,100000,100000,130000),(19,8,8,10,100000,0,0),(20,9,38,10,1000,1000,1300);
/*!40000 ALTER TABLE `import_receipt_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `import_receipts`
--

DROP TABLE IF EXISTS `import_receipts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `import_receipts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `total_amount` int(11) DEFAULT 0,
  `status` enum('draft','completed') DEFAULT 'draft',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `fk_import_supplier` (`supplier_id`),
  CONSTRAINT `fk_import_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `import_receipts_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `accounts` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `import_receipts`
--

LOCK TABLES `import_receipts` WRITE;
/*!40000 ALTER TABLE `import_receipts` DISABLE KEYS */;
INSERT INTO `import_receipts` VALUES (1,1,10,2200000,'completed','2026-04-01 22:52:00'),(2,1,10,1800000,'completed','2026-04-01 22:52:00'),(3,1,10,2200000,'completed','2026-04-01 22:54:00'),(4,1,10,3150000,'completed','2026-04-01 22:55:00'),(5,1,10,4140000,'completed','2026-04-02 22:58:00'),(6,1,11,8050000,'completed','2026-04-12 07:34:00'),(7,1,11,4200000,'completed','2026-04-14 07:38:00'),(8,1,11,1000000,'draft','2026-04-16 09:10:00'),(9,1,11,10000,'completed','2026-04-01 09:25:00');
/*!40000 ALTER TABLE `import_receipts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `cost_price` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (1,1,37,4,156000,120000.00),(2,1,36,6,145600,112000.00),(3,2,4,5,156000,120000.00),(4,2,1,4,156000,120000.00),(5,2,37,6,139750,107500.00),(6,3,13,5,240000,120000.00),(7,3,6,5,130000,100000.00),(8,4,6,3,130000,100000.00),(9,4,4,3,156000,120000.00),(10,4,37,2,139750,107500.00),(11,4,36,2,145600,112000.00),(12,5,36,7,224000,112000.00),(13,6,13,5,240000,120000.00),(14,7,37,1,139750,107500.00),(15,8,8,6,169000,130000.00),(16,8,4,2,156000,120000.00),(17,9,10,5,156000,120000.00),(18,10,10,1,156000,120000.00),(19,11,1,1,156000,120000.00),(20,12,1,2,156000,120000.00),(21,13,37,2,139750,107500.00),(22,14,3,10,156000,120000.00),(23,14,2,6,200000,100000.00),(24,15,13,6,220000,110000.00),(25,16,7,10,182000,140000.00),(26,17,3,5,156000,120000.00),(27,17,5,5,130000,100000.00),(28,18,5,3,130000,100000.00),(29,18,3,4,156000,120000.00),(30,19,1,3,156000,120000.00),(31,20,2,4,200000,100000.00),(32,21,1,3,156000,120000.00),(33,23,38,9,1300,1000.00);
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_history`
--

DROP TABLE IF EXISTS `order_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `changed_at` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_history`
--

LOCK TABLES `order_history` WRITE;
/*!40000 ALTER TABLE `order_history` DISABLE KEYS */;
INSERT INTO `order_history` VALUES (1,1,'confirmed','2026-04-01 22:53:58',''),(2,1,'shipping','2026-04-01 22:54:00',''),(3,1,'delivered','2026-04-01 22:54:02',''),(4,2,'confirmed','2026-04-02 22:56:49',''),(5,2,'shipping','2026-04-02 22:56:51',''),(6,2,'delivered','2026-04-02 22:56:53',''),(7,3,'confirmed','2026-04-02 22:59:13',''),(8,3,'shipping','2026-04-02 22:59:16',''),(9,3,'delivered','2026-04-02 22:59:18',''),(10,4,'confirmed','2026-04-03 23:02:09',''),(11,4,'shipping','2026-04-03 23:02:11',''),(12,4,'delivered','2026-04-03 23:02:13',''),(13,5,'confirmed','2026-04-03 23:03:26',''),(14,5,'shipping','2026-04-03 23:03:28',''),(15,5,'delivered','2026-04-03 23:03:30',''),(16,6,'confirmed','2026-04-04 11:18:52',''),(17,6,'shipping','2026-04-04 11:18:54',''),(18,6,'delivered','2026-04-04 11:18:56',''),(19,9,'confirmed','2026-04-04 11:40:30',''),(20,9,'shipping','2026-04-04 11:40:32',''),(21,9,'delivered','2026-04-04 11:40:34',''),(22,10,'confirmed','2026-04-15 07:02:27',''),(23,10,'shipping','2026-04-15 07:02:29',''),(24,10,'delivered','2026-04-15 07:02:31',''),(25,13,'confirmed','2026-04-15 18:01:05',''),(26,13,'shipping','2026-04-15 18:01:15',''),(27,13,'delivered','2026-04-15 18:01:24',''),(28,14,'confirmed','2026-04-12 07:35:43',''),(29,14,'shipping','2026-04-12 07:35:45',''),(30,14,'delivered','2026-04-12 07:35:47',''),(31,11,'confirmed','2026-04-12 07:35:55',''),(32,11,'shipping','2026-04-12 07:35:57',''),(33,11,'delivered','2026-04-12 07:35:59',''),(34,17,'confirmed','2026-04-16 07:39:30',''),(35,17,'shipping','2026-04-16 07:39:32',''),(36,17,'delivered','2026-04-16 07:39:33',''),(37,8,'confirmed','2026-04-16 07:39:41',''),(38,8,'shipping','2026-04-16 07:39:44',''),(39,8,'delivered','2026-04-16 07:39:46',''),(40,15,'confirmed','2026-04-16 07:39:53',''),(41,15,'shipping','2026-04-16 07:39:55',''),(42,15,'delivered','2026-04-16 07:39:57',''),(43,12,'confirmed','2026-04-16 07:40:03',''),(44,12,'shipping','2026-04-16 07:40:05',''),(45,12,'delivered','2026-04-16 07:40:07',''),(46,7,'cancelled','2026-04-16 07:40:15',''),(47,19,'cancelled','2026-04-16 07:41:01',''),(48,18,'confirmed','2026-04-16 07:41:08',''),(49,18,'shipping','2026-04-16 07:41:10',''),(50,18,'delivered','2026-04-16 07:41:12',''),(51,20,'confirmed','2026-04-16 07:41:28',''),(52,20,'shipping','2026-04-16 07:41:32',''),(53,20,'delivered','2026-04-16 07:41:52','');
/*!40000 ALTER TABLE `order_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','confirmed','shipping','delivered','cancelled','returned') DEFAULT 'pending',
  `payment_method` enum('cash','transfer','online','vietqr') NOT NULL,
  `payment_status` enum('unpaid','paid','refund_pending','refunded') DEFAULT 'unpaid',
  `subtotal_amount` int(11) NOT NULL DEFAULT 0 COMMENT 'Tổng tiền hàng trước giảm giá',
  `discount_amount` int(11) NOT NULL DEFAULT 0 COMMENT 'Số tiền được giảm bởi voucher',
  `voucher_id` int(11) DEFAULT NULL,
  `voucher_code` varchar(50) DEFAULT NULL,
  `payment_code` varchar(30) DEFAULT NULL COMMENT 'Nội dung chuyển khoản, VD: LEGO25',
  `paid_at` datetime DEFAULT NULL,
  `total_amount` int(11) NOT NULL,
  `shipping_fullname` varchar(100) NOT NULL,
  `shipping_phone` varchar(20) NOT NULL,
  `shipping_street` varchar(255) NOT NULL,
  `shipping_ward` varchar(100) NOT NULL,
  `shipping_district` varchar(100) NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_payment_code` (`payment_code`),
  KEY `user_id` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created_at` (`created_at`),
  KEY `idx_orders_voucher` (`voucher_id`),
  CONSTRAINT `fk_orders_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,2,'delivered','cash','unpaid',1497600,0,NULL,NULL,NULL,NULL,1497600,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-01 22:53:49'),(2,2,'delivered','transfer','paid',2242500,0,NULL,NULL,NULL,NULL,2242500,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-02 22:56:39'),(3,2,'delivered','cash','unpaid',1850000,0,NULL,NULL,NULL,NULL,1850000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-02 22:59:06'),(4,2,'delivered','transfer','paid',1428700,0,NULL,NULL,NULL,NULL,1428700,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-03 23:01:57'),(5,2,'delivered','cash','unpaid',1568000,0,NULL,NULL,NULL,NULL,1568000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-03 23:03:14'),(6,2,'delivered','transfer','paid',1200000,0,NULL,NULL,NULL,NULL,1200000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-04 11:18:38'),(7,2,'cancelled','cash','unpaid',139750,0,NULL,NULL,NULL,NULL,139750,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-04 11:38:59'),(8,2,'delivered','cash','unpaid',1326000,0,NULL,NULL,NULL,NULL,1326000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-04 11:39:20'),(9,2,'delivered','transfer','paid',780000,0,NULL,NULL,NULL,NULL,780000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-04 11:39:31'),(10,2,'delivered','cash','unpaid',156000,0,NULL,NULL,NULL,NULL,156000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-15 07:02:20'),(11,2,'delivered','cash','unpaid',156000,0,NULL,NULL,NULL,NULL,156000,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-15 09:12:11'),(12,5,'delivered','cash','unpaid',312000,0,NULL,NULL,NULL,NULL,312000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-15 16:11:22'),(13,2,'delivered','cash','unpaid',279500,0,NULL,NULL,NULL,NULL,279500,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-15 18:00:27'),(14,5,'delivered','cash','unpaid',2760000,0,NULL,NULL,NULL,NULL,2760000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-12 07:35:28'),(15,5,'delivered','cash','unpaid',1320000,0,NULL,NULL,NULL,NULL,1320000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-13 07:37:21'),(16,5,'pending','cash','unpaid',1820000,0,NULL,NULL,NULL,NULL,1820000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-13 07:37:44'),(17,5,'delivered','cash','unpaid',1430000,0,NULL,NULL,NULL,NULL,1430000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-14 07:38:56'),(18,5,'delivered','cash','unpaid',1014000,0,NULL,NULL,NULL,NULL,1014000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-16 07:40:34'),(19,5,'cancelled','cash','unpaid',468000,0,NULL,NULL,NULL,NULL,468000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-16 07:40:53'),(20,5,'delivered','cash','unpaid',800000,0,NULL,NULL,NULL,NULL,800000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-16 07:41:21'),(21,5,'pending','cash','unpaid',468000,0,NULL,NULL,NULL,NULL,468000,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-16 07:41:44'),(22,2,'pending','cash','unpaid',5200,0,NULL,NULL,NULL,NULL,5200,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-20 07:01:05'),(23,2,'pending','cash','unpaid',11700,0,NULL,NULL,NULL,NULL,11700,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh','2026-04-20 07:10:00');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_details`
--

DROP TABLE IF EXISTS `product_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `theme_story` text DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  CONSTRAINT `product_details_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_details`
--

LOCK TABLES `product_details` WRITE;
/*!40000 ALTER TABLE `product_details` DISABLE KEYS */;
INSERT INTO `product_details` VALUES (1,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',14.5,'84 x 56 x 21 cm','16+',7541,2017,'Chiếc phi thuyền Millennium Falcon nhanh nhất dải ngân hà, từng làm chủ Kessel Run dưới 12 parsec! Một siêu phẩm không thể thiếu của mọi fan cứng Star Wars.'),(2,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',4.8,'60 x 25 x 13 cm','18+',1000,2020,'Siêu xe thể thao lai điện đầu tiên của Lamborghini. Mô hình tái hiện chân thực động cơ V12, hộp số 8 cấp và cửa cắt kéo đặc trưng của dòng Sián.'),(3,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',7.2,'69 x 43 x 58 cm','16+',6020,2018,'Khám phá thế giới phép thuật tại Trường Phù thủy và Pháp sư Hogwarts. Bao gồm Đại sảnh đường, tháp nhọn, lớp học Độc dược và Phòng chứa Bí mật.'),(4,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',0.8,'27 x 27 x 23 cm','8+',563,2021,'Cùng Ninja lửa Kai bay lượn trên không trung và tung ra những quả cầu lửa thiêu rụi kẻ thù với chú rồng có thể cử động linh hoạt hàm, cánh và đuôi.'),(5,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',1.5,'86 x 5 x 10 cm','7+',764,2022,'Đoàn tàu cao tốc hiện đại tích hợp công nghệ điều khiển từ xa Powered Up. Bao gồm đầu máy có thể phát sáng, toa hành khách và cả toa căn tin sang trọng.'),(6,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',3.5,'55 x 38 x 60 cm','16+',2461,2015,'Tận hưởng niềm vui tại khu hội chợ với vòng đu quay khổng lồ. Thiết kế tinh xảo với các cabin rực rỡ và hệ thống xoay bằng tay cực êm ái.'),(7,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',0.6,'28 x 11 x 22 cm','12+',649,2019,'Bức tranh toàn cảnh về các công trình biểu tượng của thủ đô Paris, Pháp. Từ Khải Hoàn Môn, Tháp Eiffel đến Bảo tàng Louvre tráng lệ.'),(8,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',1.4,'34 x 32 x 31 cm','7+',766,2022,'Đội cứu hỏa LEGO City luôn sẵn sàng đối phó với mọi tình huống khẩn cấp. Trạm cứu hỏa 3 tầng trang bị đầy đủ xe thang, trực thăng và cầu trượt.'),(9,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',0.7,'13 x 11 x 31 cm','18+',590,2021,'Sức mạnh thao túng cả vũ trụ giờ đây nằm gọn trong bàn tay của bạn. Mô hình Găng tay vô cực mạ vàng sáng bóng đính kèm 6 viên đá vô cực quyền năng.'),(10,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',3.2,'57 x 25 x 17 cm','16+',2704,2016,'Huyền thoại xe đua đường phố với thiết kế khí động học tuyệt đỉnh. Sở hữu lớp sơn màu cam rực rỡ và các chi tiết cơ khí mô phỏng chính xác xe thật.'),(11,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',0.7,'21 x 20 x 18 cm','18+',878,2021,'Tận hưởng khoảnh khắc thiền định khi tự tay ghép và tạo dáng cho cây Bonsai tĩnh lặng. Đặc biệt có thể thay đổi tán lá xanh sang hoa anh đào hồng nở rộ.'),(13,'Tập đoàn LEGO','Đan Mạch','Nhựa ABS nguyên sinh',1.8,'65 x 27 x 13 cm','18+',1432,2022,'Sự hợp tác đỉnh cao giữa nhóm thiết kế LEGO và đội đua McLaren Racing. Mô phỏng chiếc F1 mùa giải 2022 với động cơ V6 có piston di chuyển được.'),(36,'The LEGO Group','Đan Mạch','Nhựa ABS an toàn',0,'10 x 10 x 10 cm','18+',1000,2026,'TEST'),(37,'The LEGO Group','Đan Mạch','Nhựa ABS an toàn',0,'10 x 10 x 10 cm','18+',1,2026,'TEST'),(38,'The LEGO Group','Đan Mạch','Nhựa ABS an toàn',0,'','11+',1111,2026,'');
/*!40000 ALTER TABLE `product_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `is_main` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (1,1,'product1.webp',1),(2,2,'product2.webp',1),(4,4,'product4.webp',1),(5,5,'product5.webp',1),(6,6,'product11.webp',1),(7,7,'product17.webp',1),(8,8,'product3.webp',1),(9,9,'product9.webp',1),(10,10,'product10.webp',1),(11,11,'product13.webp',1),(13,13,'product14.webp',1),(14,1,'product1-1.webp',0),(18,2,'product2-1.webp',0),(19,2,'product2-2.webp',0),(54,3,'product3.webp',1),(75,36,'1775230345_new-product.webp',1),(77,37,'1775230403_login-bgr.webp',1),(79,37,'1776214294_751_product1.webp',0),(82,4,'1776214605_556_product3.webp',0),(83,36,'1776304650_332_1776214419_279_banner2.webp',0),(84,36,'1776304650_437_product1.webp',0),(85,38,'1776306272_Screenshot 2026-04-02 153528.png',1);
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_reviews`
--

DROP TABLE IF EXISTS `product_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','hidden') DEFAULT 'approved',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_reviews`
--

LOCK TABLES `product_reviews` WRITE;
/*!40000 ALTER TABLE `product_reviews` DISABLE KEYS */;
INSERT INTO `product_reviews` VALUES (1,6,2,5,'ok','approved','2026-04-03 23:22:22'),(2,37,2,5,'okkk','approved','2026-04-03 23:22:31'),(3,36,2,4,'good','hidden','2026-04-03 23:22:41'),(4,10,2,5,'ok','approved','2026-04-04 11:41:46');
/*!40000 ALTER TABLE `product_reviews` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,3,'SW-75192','Siêu tàu vũ trụ Millennium Falcon','Phi thuyền biểu tượng của Han Solo với hơn 7500 mảnh ghép, to nhất lịch sử Star Wars.',120000,0.3,156000,3,10,1,'2026-03-27 14:09:27'),(2,1,'TEC-42115','Siêu xe Lamborghini Sián FKP 37','Mô hình siêu xe thể thao màu xanh lá tỉ lệ 1:8 chân thực đến từng chi tiết động cơ.',100000,1,200000,2,10,1,'2026-03-27 14:09:27'),(3,2,'HP-71043','Lâu đài Hogwarts Castle','Trường học phép thuật Hogwarts siêu to khổng lồ dành cho các fan Harry Potter.',120000,0.3,156000,1,10,1,'2026-03-27 14:09:27'),(4,7,'NJ-71753','Rồng Lửa Của Kai','Chiến đấu cùng Ninja lửa Kai cưỡi trên lưng con rồng đỏ rực phun lửa hung tợn.',103333,0.3,134333,10,10,2,'2026-03-27 14:09:27'),(5,4,'CT-60337','Tàu Chở Khách Tốc Hành','Hệ thống tàu điện thông minh điều khiển qua Bluetooth.',100000,0.3,130000,2,10,1,'2026-03-27 14:09:27'),(6,6,'CRE-10247','Vòng Đu Quay Ferris Wheel','Khu vui chơi giải trí tuyệt đẹp cho bộ sưu tập Creator.',100000,0.3,130000,7,10,2,'2026-03-27 14:09:27'),(7,5,'ARC-21044','Mô hình Paris Skyline','Bản đồ kiến trúc thu nhỏ của thủ đô ánh sáng Paris.',140000,0.3,182000,20,10,2,'2026-03-27 14:09:27'),(8,4,'CT-60321','Trạm Cứu Hỏa Trung Tâm','Xe cứu hỏa, trực thăng và đội cứu hộ thành phố City.',130000,0.3,169000,9,10,1,'2026-03-27 14:09:27'),(9,8,'SH-76191','Găng Tay Vô Cực Infinity Gauntlet','Mô hình Găng tay vô cực của Thanos mạ vàng sáng bóng.',0,0.3,0,0,10,2,'2026-03-27 14:09:27'),(10,1,'TEC-42056','Porsche 911 GT3 RS','Huyền thoại siêu xe Porsche màu cam rực rỡ.',120000,0.3,156000,6,10,2,'2026-03-27 14:09:27'),(11,6,'CRE-10281','Cây Bonsai Nhật Bản','Nghệ thuật trồng cây Bonsai thu nhỏ giúp thư giãn tĩnh tâm.',0,0.3,0,0,10,2,'2026-03-27 14:09:27'),(13,1,'TEC-42141','Siêu xe McLaren Formula 1','Xe đua F1 của đội McLaren với độ chi tiết khí động học cao.',114000,1,228000,19,10,1,'2026-03-27 14:09:27'),(36,14,'SP-TEST','SP TEST NEW','TEST',106000,1,212000,20,10,2,'2026-04-04 13:34:45'),(37,14,'TEST2','TEST SP2','TEST2',107500,0.3,139750,6,10,2,'2026-04-03 22:33:01'),(38,15,'QQQ','qqq','QQQ',1000,0.3,1300,10,5,1,'2026-04-16 09:24:32');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refund_requests`
--

DROP TABLE IF EXISTS `refund_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `fk_refund_admin` (`processed_by`),
  CONSTRAINT `fk_refund_admin` FOREIGN KEY (`processed_by`) REFERENCES `accounts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_refund_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_refund_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refund_requests`
--

LOCK TABLES `refund_requests` WRITE;
/*!40000 ALTER TABLE `refund_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `refund_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shop_settings`
--

DROP TABLE IF EXISTS `shop_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  `bank_code` varchar(20) DEFAULT 'MB' COMMENT 'Mã ngân hàng theo VietQR (MB, VCB, TCB, ACB...)',
  `bank_account_no` varchar(30) DEFAULT '0961589023',
  `bank_account_name` varchar(100) DEFAULT 'NGUYEN HOANG',
  `payment_webhook_key` varchar(100) DEFAULT NULL COMMENT 'API key webhook SePay/Casso',
  `payment_sandbox` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = hiện nút giả lập thanh toán (dùng khi demo/local)',
  `payment_timeout_minutes` int(11) NOT NULL DEFAULT 30 COMMENT 'Quá thời gian chưa thanh toán VietQR -> tự hủy đơn',
  `refund_days` int(11) NOT NULL DEFAULT 7 COMMENT 'Số ngày được yêu cầu trả hàng/hoàn tiền sau khi giao',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shop_settings`
--

LOCK TABLES `shop_settings` WRITE;
/*!40000 ALTER TABLE `shop_settings` DISABLE KEYS */;
INSERT INTO `shop_settings` VALUES (1,'LEGO WORLD STORE','Công ty cổ phần LEGO','0309132354','logo.png','1900 1208 000','hotro@legoworldstore.com.vn','273 An Dương Vương, Phường 1, Quận 5, TP. Hồ Chí Minh','Thứ 2 - Thứ 7: 8:00 - 17:00','Chủ nhật: 8:00 - 12:00','Miễn phí giao hàng đơn từ 500k','Giao hàng hỏa tốc 4 tiếng','Chương trình thành viên VIP','Hỗ trợ mua hàng trả góp 0%','Hệ thống 200 cửa hàng toàn quốc','#','#','#','#','#','MB','0961589023','NGUYEN HOANG',NULL,1,30,7,'2026-10-22 19:10:23');
/*!40000 ALTER TABLE `shop_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Tên công ty/nhà cung cấp',
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `status` enum('active','deleted','locked') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (8,'Công ty TNHH Phân phối Đồ chơi Lego Việt','02838445566','contact@legoviet.vn','123 Lê Lợi, Phường Bến Nghé, Quận 1, TP.HCM','locked','2026-04-01 13:06:51'),(10,'Lego Global Trading Co., Ltd','0243123456','info@legoglobal.com','45 Đại Cồ Việt, Quận Hai Bà Trưng, Hà Nội','locked','2026-04-01 13:06:51'),(11,'Xưởng Nhập Khẩu LEGO Chính Hãng Sài Gòn','0988776655','nhapkhaulego@gmail.com','789 Nguyễn Văn Linh, Phường Tân Phong, Quận 7, TP.HCM','active','2026-04-01 13:06:51'),(12,'Đại Lý Đồ Chơi Lắp Ráp Cao Cấp BrickStore','0912000111','support@brickstore.vn','12 Xuân Thủy, Quận Cầu Giấy, Hà Nội','deleted','2026-04-01 13:06:51');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `receiver_name` varchar(100) NOT NULL,
  `receiver_phone` varchar(20) NOT NULL,
  `street` varchar(255) NOT NULL,
  `ward` varchar(100) NOT NULL,
  `district` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `is_default` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_addresses`
--

LOCK TABLES `user_addresses` WRITE;
/*!40000 ALTER TABLE `user_addresses` DISABLE KEYS */;
INSERT INTO `user_addresses` VALUES (1,2,'Hoàng Nguyễn','0961589023','451 Phạm Thế Hiển','Phường Bến Nghé','Quận 1','Hồ Chí Minh',1),(2,4,'TEST ORDER','11111111111','TEST','Phường Bến Thành','Quận 1','Hồ Chí Minh',0),(3,5,'Kiều Hoài Nam','0900000000','273 An dương vương','Phường Bến Nghé','Quận 1','Hồ Chí Minh',1);
/*!40000 ALTER TABLE `user_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Administrator'),(2,2,'Nguyễn Việt Hoàng'),(3,3,'TEST'),(4,4,'TEST ORDER'),(5,5,'Kiều Hoài Nam');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher_usages`
--

DROP TABLE IF EXISTS `voucher_usages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
  KEY `fk_usage_user` (`user_id`),
  CONSTRAINT `fk_usage_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usage_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_usage_voucher` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher_usages`
--

LOCK TABLES `voucher_usages` WRITE;
/*!40000 ALTER TABLE `voucher_usages` DISABLE KEYS */;
/*!40000 ALTER TABLE `voucher_usages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (1,'WELCOME50K','Giảm 50.000đ cho đơn từ 300.000đ','fixed',50000,NULL,300000,100,0,1,'2026-01-01 00:00:00','2027-12-31 23:59:59','active','2026-10-22 19:10:23'),(2,'LEGO10','Giảm 10% tối đa 200.000đ cho đơn từ 500.000đ','percent',10,200000,500000,NULL,0,3,'2026-01-01 00:00:00','2027-12-31 23:59:59','active','2026-10-22 19:10:23'),(3,'FREESHIP30K','Giảm 30.000đ cho mọi đơn hàng','fixed',30000,NULL,0,500,0,5,'2026-01-01 00:00:00','2027-12-31 23:59:59','active','2026-10-22 19:10:23');
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL COMMENT 'ID người dùng',
  `product_id` int(11) NOT NULL COMMENT 'ID sản phẩm yêu thích',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_wishlist` (`account_id`,`product_id`),
  KEY `fk_wishlist_product` (`product_id`),
  CONSTRAINT `fk_wishlist_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlists`
--

LOCK TABLES `wishlists` WRITE;
/*!40000 ALTER TABLE `wishlists` DISABLE KEYS */;
INSERT INTO `wishlists` VALUES (1,2,4,'2026-04-03 23:37:45'),(2,2,6,'2026-04-03 23:37:47');
/*!40000 ALTER TABLE `wishlists` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
