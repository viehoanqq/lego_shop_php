-- =====================================================================
-- MIGRATION 2: Trạng thái đơn "returned" (đã trả hàng & hoàn tiền)
-- Đơn trả hàng xong chuyển sang 'returned' để báo cáo doanh thu / xuất kho
-- (vốn chỉ tính đơn 'delivered') không còn tính đơn này nữa.
-- =====================================================================
SET NAMES utf8mb4;

ALTER TABLE `orders`
  MODIFY `status` enum('pending','confirmed','shipping','delivered','cancelled','returned') DEFAULT 'pending';
