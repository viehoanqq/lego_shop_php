<?php
class AdminReportController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    public function index() {
        $reportModel = $this->model('ReportModel');
        $categoryModel = $this->model('CategoryModel'); // Lấy danh mục để hiện trong bộ lọc
        
        // Nhận tham số lọc từ GET, nếu không có thì set giá trị mặc định
        $category_id = $_GET['category_id'] ?? 'all';
        $start_date = $_GET['start_date'] ?? date('Y-m-01'); // Ngày 1 của tháng hiện tại
        $end_date = $_GET['end_date'] ?? date('Y-m-d'); // Ngày hôm nay
        
        $data['filters'] = [
            'category_id' => $category_id, 
            'start_date' => $start_date, 
            'end_date' => $end_date
        ];
        
        $data['categories'] = $categoryModel->getAllCategories();
        $data['reports'] = $reportModel->getInventoryReport($category_id, $start_date, $end_date);
        $data['title'] = "Báo cáo Nhập - Xuất - Tồn";

        $this->view('admin/reports', $data); 
    }
}