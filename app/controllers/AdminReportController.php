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
    // Lấy dữ liệu báo cáo từ Model
    $data['reports'] = $reportModel->getInventoryReport($filters); 
    $data['title'] = "Báo cáo Nhập - Xuất - Tồn";

    // =========================================================
    // NẾU CÓ PARAM action=export_excel THÌ XUẤT EXCEL VÀ DỪNG LẠI
    // =========================================================
    if (isset($_GET['action']) && $_GET['action'] === 'export_excel') {
        
        $filename = "BaoCao_TongHop_XuatNhapTon_" . date('Ymd_Hi') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Khai báo HTML chuẩn để Excel nhận diện font Tiếng Việt
        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head>
                <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
                <style>
                    body, table { font-family: "Times New Roman", Times, serif; font-size: 12pt; }
                    .table-data { border-collapse: collapse; width: 100%; }
                    .table-data th, .table-data td { border: 1px solid windowtext; padding: 5px; vertical-align: middle; }
                    .table-data th { font-weight: bold; text-align: center; }
                    .no-border { border: none !important; }
                    .text-center { text-align: center; }
                    .text-right { text-align: right; }
                    .font-bold { font-weight: bold; }
                </style>
              </head>';
        echo '<body>';

        // 1. HEADER: Thông tin đơn vị và Quốc hiệu
        echo '<table width="100%">
                <tr>
                    <td colspan="3" class="no-border font-bold">CỬA HÀNG LEGO SHOP</td>
                    <td colspan="5" class="no-border text-center font-bold">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border">Bộ phận: Quản lý Kho - Bán hàng</td>
                    <td colspan="5" class="no-border text-center font-bold" style="text-decoration: underline;">Độc lập - Tự do - Hạnh phúc</td>
                </tr>
                <tr><td colspan="8" class="no-border"></td></tr>';

        // 2. TÊN BÁO CÁO
        $start = date('d/m/Y', strtotime($filters['start_date']));
        $end = date('d/m/Y', strtotime($filters['end_date']));
        echo '<tr>
                <td colspan="8" class="no-border text-center font-bold" style="font-size: 16pt;">BÁO CÁO TỔNG HỢP XUẤT NHẬP TỒN VÀ DOANH THU</td>
              </tr>
              <tr>
                <td colspan="8" class="no-border text-center" style="font-style: italic;">Từ ngày: ' . $start . ' - Đến ngày: ' . $end . '</td>
              </tr>
              <tr><td colspan="8" class="no-border"></td></tr>
            </table>';

        // 3. BẢNG DỮ LIỆU CHÍNH (Đen trắng chuẩn)
        echo '<table class="table-data">';
        echo '<tr>
                <th>STT</th>
                <th>Mã SKU</th>
                <th style="width: 300px;">Tên sản phẩm, hàng hóa</th>
                <th>SL Nhập</th>
                <th>SL Bán (Xuất)</th>
                <th>Tổng vốn nhập</th>
                <th>Tổng doanh thu</th>
                <th>Dòng tiền (Chênh lệch)</th>
              </tr>';

        $sum_in = 0; $sum_out = 0; $sum_cost = 0; $sum_rev = 0; $sum_diff = 0;
        $stt = 1;

        if (!empty($data['reports'])) {
            foreach ($data['reports'] as $r) {
                $diff = $r['total_revenue'] - $r['total_import_cost'];
                $sum_in += $r['qty_in'];
                $sum_out += $r['qty_out'];
                $sum_cost += $r['total_import_cost'];
                $sum_rev += $r['total_revenue'];
                $sum_diff += $diff;

                // Ghi chú nếu ngừng kinh doanh
                $is_deleted = (isset($r['status']) && $r['status'] == 3) ? ' (Ngừng KD)' : '';

                echo '<tr>
                        <td class="text-center">' . $stt++ . '</td>
                        <td class="text-center">' . strtoupper($r['sku']) . '</td>
                        <td>' . htmlspecialchars($r['name']) . $is_deleted . '</td>
                        <td class="text-center">' . $r['qty_in'] . '</td>
                        <td class="text-center">' . $r['qty_out'] . '</td>
                        <td class="text-right">' . number_format($r['total_import_cost']) . '</td>
                        <td class="text-right">' . number_format($r['total_revenue']) . '</td>
                        <td class="text-right">' . number_format($diff) . '</td>
                      </tr>';
            }
        } else {
            echo '<tr><td colspan="8" class="text-center" style="padding: 20px;">Không có dữ liệu phát sinh trong kỳ báo cáo.</td></tr>';
        }

        // 4. DÒNG TỔNG CỘNG
        echo '<tr class="font-bold">
                <td colspan="3" class="text-center">TỔNG CỘNG</td>
                <td class="text-center">' . number_format($sum_in) . '</td>
                <td class="text-center">' . number_format($sum_out) . '</td>
                <td class="text-right">' . number_format($sum_cost) . '</td>
                <td class="text-right">' . number_format($sum_rev) . '</td>
                <td class="text-right">' . number_format($sum_diff) . '</td>
              </tr>';
        echo '</table>';

        // 5. CHỮ KÝ KẾ TOÁN (Ở cuối file Excel)
        echo '<table width="100%">
                <tr><td colspan="8" class="no-border"></td></tr>
                <tr>
                    <td colspan="5" class="no-border"></td>
                    <td colspan="3" class="no-border text-center" style="font-style: italic;">Ngày ' . date('d') . ' tháng ' . date('m') . ' năm ' . date('Y') . '</td>
                </tr>
                <tr class="font-bold text-center">
                    <td colspan="3" class="no-border">Người lập biểu</td>
                    <td colspan="2" class="no-border">Kế toán trưởng</td>
                    <td colspan="3" class="no-border">Giám đốc</td>
                </tr>
                <tr class="text-center" style="font-style: italic;">
                    <td colspan="3" class="no-border">(Ký, họ tên)</td>
                    <td colspan="2" class="no-border">(Ký, họ tên)</td>
                    <td colspan="3" class="no-border">(Ký, họ tên, đóng dấu)</td>
                </tr>
                <tr>
                    <td colspan="8" class="no-border" style="height: 80px;"></td> </tr>
              </table>';

        echo '</body></html>';
        
        exit; 
    }
    // =========================================================

    // Nếu không phải là lệnh xuất Excel, tiến hành render giao diện HTML bình thường
    $this->view('admin/reports', $data); 
}

    // TRANG CHI TIẾT SẢN PHẨM
    public function productDetail($id) {
        $reportModel = $this->model('ReportModel');
        
        $start = $_GET['start'] ?? date('Y-m-01');
        $end = $_GET['end'] ?? date('Y-m-d');
        $profit_details = $reportModel->getProfitBreakdown($id, $start, $end);
        $data['product'] = $this->model('ProductModel')->getProductById($id);
        $data['stats'] = $reportModel->getProductPerformanceStats($id, $start, $end);
        $data['history'] = $reportModel->getInventoryDetail($id, $start, $end);
        $data['chart_data'] = $reportModel->getChartData($id, $start, $end);
        $data['profit_details'] = $profit_details;
        
        $this->view('admin/report_product_detail', $data);
    }
}
?>