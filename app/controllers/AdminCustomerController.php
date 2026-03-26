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
        // Lấy dữ liệu từ Model
        $customers = $this->customerModel->getAllCustomers();
        
        // Gọi view admin/customer.php
        // Lưu ý: Tên biến truyền vào mảng ['customers' => $customers] 
        // sẽ trở thành biến $customers trong file view nhờ hàm extract($data)
        $this->view('admin/customer', [
            'customers' => $customers,
            'title' => 'Quản lý khách hàng'
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