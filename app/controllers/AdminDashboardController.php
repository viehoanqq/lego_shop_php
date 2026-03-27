<?php
class AdminDashboardController extends Controller {

    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    public function index() {
        $dashboardModel = $this->model('DashboardModel');

        // 3. Lấy dữ liệu HÔM NAY
        $data['tong_don_hom_nay'] = (int)$dashboardModel->getOrdersToday();
        $data['tong_thu_nhap']    = (int)$dashboardModel->getTodayRevenue();
        $data['don_huy_tra']      = (int)$dashboardModel->getCancelledOrdersToday();

        // 4. Xử lý biểu đồ 7 ngày
        $raw_7_days = $dashboardModel->getRevenueLast7Days();
        $doanh_thu_7_ngay = [];
        $max_revenue = 1;

        foreach ($raw_7_days as $rev) {
            if ($rev > $max_revenue) $max_revenue = $rev;
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $day_name = date('d/m', strtotime($date)); 
            
            $amount = isset($raw_7_days[$date]) ? $raw_7_days[$date] : 0;
            $percent = ($max_revenue > 1) ? round(($amount / $max_revenue) * 100) : 0;
            if ($amount == 0) $percent = 2; 

            $doanh_thu_7_ngay[] = [
                'day'     => $day_name,
                'amount'  => $amount,
                'percent' => $percent
            ];
        }
        $data['doanh_thu_7_ngay'] = $doanh_thu_7_ngay;

        // 5. Tính tỷ lệ trạng thái HÔM NAY
        // 5. Tính tỷ lệ trạng thái HÔM NAY
$status_stats = $dashboardModel->getOrderStatusStatsToday();
// Tổng đơn thực tế từ Database (cộng tất cả các trạng thái lại)
$total_real = array_sum($status_stats); 

if ($total_real > 0) {
    $count_delivered = $status_stats['delivered'] ?? 0;
    $count_shipping  = $status_stats['shipping'] ?? 0;
    $count_pending   = ($status_stats['pending'] ?? 0) + ($status_stats['confirmed'] ?? 0);
    $count_cancelled = $status_stats['cancelled'] ?? 0;

    $data['ty_le_da_giao']   = round(($count_delivered / $total_real) * 100);
    $data['ty_le_dang_giao'] = round(($count_shipping / $total_real) * 100);
    $data['ty_le_cho_xu_ly'] = round(($count_pending / $total_real) * 100);
    $data['ty_le_da_huy']    = round(($count_cancelled / $total_real) * 100);
} else {
    $data['ty_le_da_giao'] = $data['ty_le_dang_giao'] = $data['ty_le_cho_xu_ly'] = $data['ty_le_da_huy'] = 0;
}
$data['tong_don_trang_thai'] = $total_real;

        // 6. Danh sách đơn mới nhất
        $don_moi_nhat_raw = $dashboardModel->getRecentOrders(5);
        $don_moi_nhat = [];
        foreach ($don_moi_nhat_raw as $don) {
            $don['thoi_gian'] = date('H:i d/m', strtotime($don['thoi_gian']));
            $don_moi_nhat[] = $don;
        }
        $data['don_moi_nhat'] = $don_moi_nhat;

        // 7. Gọi View
        $this->view('admin/dashboard', $data);
    }
}
?>