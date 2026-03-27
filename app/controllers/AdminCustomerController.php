<?php
class AdminCustomerController extends Controller {
    private $customerModel;

    public function __construct() {
        // Nạp model CustomerModel
        $this->customerModel = $this->model('CustomerModel');
        
        // Kiểm tra quyền Admin (Tùy chọn nhưng nên có)
        if (!isset($_SESSION['admin_id'])) {
            // header("Location: /admin/login");
            // exit();
        }
    }

    // Hiển thị danh sách khách hàng
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

    // Xử lý Khóa/Mở tài khoản
    public function toggleStatus($id) {
        // 1. Lấy trạng thái hiện tại của tài khoản
        $db = $this->customerModel->getConnection();
        $id = intval($id);
        $res = $db->query("SELECT status FROM accounts WHERE id = $id AND role = 'customer'");
        $account = $res->fetch_assoc();

        if ($account) {
            // 2. Đảo ngược trạng thái
            $newStatus = ($account['status'] === 'active') ? 'locked' : 'active';
            
            // 3. Cập nhật vào DB thông qua Model
            $result = $this->customerModel->updateStatus($id, $newStatus);

            if ($result) {
                set_flash_message('success', 'Cập nhật trạng thái khách hàng thành công!');
            } else {
                set_flash_message('error', 'Không thể cập nhật trạng thái.');
            }
        } else {
            set_flash_message('error', 'Tài khoản không tồn tại hoặc không phải khách hàng.');
        }

        // 4. Quay lại trang danh sách
        header("Location: " . $_SERVER['HTTP_REFERER']); 
        exit();
    }
}