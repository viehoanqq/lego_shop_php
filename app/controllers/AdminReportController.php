<?php
class AdminReportController extends Controller {
    
    public function __construct() {
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
    }

    // TRANG CHỦ BÁO CÁO (DANH SÁCH)
    public function index() {
        $reportModel = $this->model('ReportModel');
        $categoryModel = $this->model('CategoryModel');
        
        $filters = [
            'category_id' => $_GET['category_id'] ?? 'all',
            'start_date'  => $_GET['start_date'] ?? date('Y-m-01'),
            'end_date'    => $_GET['end_date'] ?? date('Y-m-d'),
            'keyword'     => $_GET['keyword'] ?? ''
        ];
        
        $data['filters'] = $filters;
        $data['categories'] = $categoryModel->getAllCategories();
        $data['reports'] = $reportModel->getInventoryReport($filters);
        $data['title'] = "Báo cáo Nhập - Xuất - Tồn";

        $this->view('admin/reports', $data); 
    }

    // TRANG CHI TIẾT SẢN PHẨM
    public function productDetail($id) {
        $reportModel = $this->model('ReportModel');
        
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-d');
        
        $data['product'] = $this->model('ProductModel')->getProductById($id);
        $data['stats'] = $reportModel->getProductPerformanceStats($id, $start, $end);
        $data['history'] = $reportModel->getInventoryDetail($id, $start, $end);
        $data['chart_data'] = $reportModel->getChartData($id, $start, $end);
        
        $this->view('admin/report_product_detail', $data);
    }
}
?>