<?php
class AdminReportController extends Controller {
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
        $this->view('admin/reports', $data); 
    }

    public function getDetailAjax() {
        header('Content-Type: application/json');
        $reportModel = $this->model('ReportModel');
        echo json_encode($reportModel->getInventoryDetail($_GET['id'], $_GET['start'], $_GET['end']));
    }
    public function productDetail($id) {
    $reportModel = $this->model('ReportModel');
    
    // Lấy khoảng thời gian từ GET để đồng bộ với trang báo cáo tổng
    $start = $_GET['start'] ?? date('Y-m-01');
    $end = $_GET['end'] ?? date('Y-m-d');
    
    $data['product'] = $this->model('ProductModel')->getProductById($id);
    $data['stats'] = $reportModel->getProductPerformanceStats($id, $start, $end);
    $data['history'] = $reportModel->getInventoryDetail($id, $start, $end);
    $data['chart_data'] = $reportModel->getChartData($id, $start, $end);
    
    $this->view('admin/report_product_detail', $data);
}
}