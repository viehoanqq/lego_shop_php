<?php
class AdminCustomerController extends Controller {
    private $customerModel;

    public function __construct() {
        $this->customerModel = $this->model('CustomerModel');
    }

    public function index() {
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $status = isset($_GET['status']) ? trim($_GET['status']) : '';
        
        // Xử lý phân trang
        $limit = 2; // Số bản ghi mỗi trang
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $limit;

        // Lấy tổng số bản ghi để tính số trang
        $totalRecords = $this->customerModel->countAllCustomers($search, $status);
        $totalPages = ceil($totalRecords / $limit);

        // Lấy dữ liệu theo trang hiện tại
        $customers = $this->customerModel->getAllCustomers($search, $status, $limit, $offset);
        
        $this->view('admin/customer', [
            'customers'   => $customers,
            'title'       => 'Quản lý người dùng',
            'search'      => $search,
            'status'      => $status,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
            'totalRecords'=> $totalRecords
        ]);
    }

    public function toggleStatus($id) {
        // 1. Lấy thông tin tài khoản
        $account = $this->customerModel->getAccountById($id);

        if ($account) {
            // 2. Đảo ngược trạng thái
            $newStatus = ($account['status'] === 'active') ? 'locked' : 'active';
            
            // 3. Cập nhật
            $result = $this->customerModel->updateStatus($id, $newStatus);

            if ($result) {
                set_flash_message('success', 'Đã ' . ($newStatus == 'active' ? 'mở khóa' : 'khóa') . ' tài khoản thành công!');
            } else {
                set_flash_message('error', 'Lỗi hệ thống không thể cập nhật.');
            }
        } else {
            set_flash_message('error', 'Tài khoản không tồn tại.');
        }

        // 4. Quay lại trang trước đó
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }
}