<?php
class DashboardModel extends Database {

   public function getOrdersToday() {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURDATE()";
        $result = $db->query($sql);
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // 2. Tổng thu nhập HÔM NAY (Đã sửa)
    public function getTodayRevenue() {
        $db = $this->getConnection();
        $sql = "SELECT SUM(total_amount) as total FROM orders 
                WHERE status = 'delivered' 
                AND DATE(created_at) = CURDATE()";
        $result = $db->query($sql);
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // 3. Số đơn Hủy / Trả hàng HÔM NAY (Đã sửa)
    public function getCancelledOrdersToday() {
        $db = $this->getConnection();
        $sql = "SELECT COUNT(*) as total FROM orders 
                WHERE status = 'cancelled' 
                AND DATE(created_at) = CURDATE()";
        $result = $db->query($sql);
        return $result->fetch_assoc()['total'] ?? 0;
    }

    // 4. Doanh thu 7 ngày qua (Giữ nguyên cho biểu đồ)
    public function getRevenueLast7Days() {
        $db = $this->getConnection();
        $sql = "SELECT DATE(created_at) as order_date, SUM(total_amount) as daily_revenue 
                FROM orders 
                WHERE status = 'delivered' 
                AND created_at >= DATE(NOW() - INTERVAL 6 DAY) 
                GROUP BY DATE(created_at) 
                ORDER BY order_date ASC";
        $result = $db->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[$row['order_date']] = $row['daily_revenue'];
            }
        }
        return $data;
    }

    // 5. Thống kê tỷ lệ trạng thái HÔM NAY (Đã sửa)
    public function getOrderStatusStatsToday() {
        $db = $this->getConnection();
        $sql = "SELECT status, COUNT(*) as count FROM orders 
                WHERE DATE(created_at) = CURDATE() 
                GROUP BY status";
        $result = $db->query($sql);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[$row['status']] = (int)$row['count'];
            }
        }
        return $data;
    }

    // 6. Danh sách 5 đơn hàng mới nhất
    public function getRecentOrders($limit = 5) {
        $db = $this->getConnection();
        $sql = "SELECT id, shipping_fullname as khach_hang, total_amount as tong_tien, status as trang_thai, created_at as thoi_gian, payment_method, payment_status 
                FROM orders 
                ORDER BY created_at DESC LIMIT " . (int)$limit;
        $result = $db->query($sql);
        
        $orders = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $orders[] = $row;
            }
        }
        return $orders;
    }
}