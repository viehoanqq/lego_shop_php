<?php
class AdminCustomerController extends Controller {
    private $customerModel;
    private $limit = 6;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
        $this->customerModel = $this->model('CustomerModel');
    }

    // --- HÀM HELPER ĐỂ LẤY DỮ LIỆU PHÂN TRANG
    private function getPaginationData($filters, $action = 'index', $editId = null) {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $customers = $this->customerModel->getAllCustomers($filters['search'], $filters['status'], $this->limit, $offset);
        $totalRecords = $this->customerModel->countAllCustomers($filters['search'], $filters['status']);
        $totalPages = ceil($totalRecords / $this->limit);

        return [
            'customers'    => $customers,
            'totalRecords' => $totalRecords,
            'totalPages'   => $totalPages,
            'currentPage'  => $page,
            'currentAction'=> $action, // Thêm dòng này
            'editId'       => $editId  // Thêm dòng này
        ];
    }

    public function index() {
        // Gom filters từ URL
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        $pageData = $this->getPaginationData($filters, 'index');

        $this->view('admin/customer', array_merge($pageData, [
            'title'   => 'Quản lý người dùng',
            'is_form' => false,
            'search'  => $filters['search'],
            'status'  => $filters['status']
        ]));
    }
    // --- HÀM XÓA TÀI KHOẢN (CÓ KIỂM TRA LỊCH SỬ) ---
    public function delete($id) {
        $id = intval($id);
        
        // Tránh Admin tự xóa chính mình
        if ($id == $_SESSION['admin_id']) {
            set_flash_message('error', 'cannot_delete_self');
            header("Location: /lego_shop_php/admincustomer");
            exit();
        }

        // BƯỚC 1: KIỂM TRA GIAO DỊCH (Gồm cả Đơn mua và Phiếu nhập)
        if ($this->customerModel->hasTransactions($id)) {
            // Đã có giao dịch -> Bắt buộc Xóa mềm (Ẩn)
            if ($this->customerModel->softDeleteAccount($id)) {
                set_flash_message('msg', 'soft_deleted');
            } else {
                set_flash_message('error', 'db');
            }
        } else {
            // Chưa có bất kỳ giao dịch nào -> Xóa vĩnh viễn
            if ($this->customerModel->deleteAccountForever($id)) {
                set_flash_message('msg', 'deleted');
            } else {
                set_flash_message('error', 'db');
            }
        }

        header("Location: /lego_shop_php/admincustomer");
        exit();
    }
    // --- HÀM KHÔI PHỤC TÀI KHOẢN ---
    public function restore($id) {
        $id = intval($id);
        // Khôi phục về trạng thái 'locked' để an toàn
        if ($this->customerModel->updateStatus($id, 'locked')) {
            set_flash_message('msg', 'restored');
        } else {
            set_flash_message('error', 'db');
        }
        header("Location: /lego_shop_php/admincustomer");
        exit();
    }
    public function toggleStatus($id) {
        $account = $this->customerModel->getAccountById($id);
        if ($account) {
            $newStatus = ($account['status'] === 'active') ? 'locked' : 'active';
            if ($this->customerModel->updateStatus($id, $newStatus)) {
                // Dùng mã lỗi tương tự Category để hiện alert
                set_flash_message('msg', $newStatus == 'active' ? 'unlocked' : 'hidden');
            } else {
                set_flash_message('error', 'db');
            }
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }


    public function add() {
        // 1. Lấy filters để giữ trạng thái danh sách bên dưới form
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? ''
        ];

        // 2. Lấy dữ liệu phân trang
        $pageData = $this->getPaginationData($filters, 'add');

        // 3. Truyền đầy đủ dữ liệu vào View
        $this->view('admin/customer', array_merge($pageData, [
            'title'    => 'Thêm người dùng mới',
            'is_form'  => true,
            'customer' => null,
            'search'   => $filters['search'], // Quan trọng để link phân trang không lỗi
            'status'   => $filters['status']  // Quan trọng để link phân trang không lỗi
        ]));
    }


    // --- HÀM HIỂN THỊ FORM SỬA
    public function edit($id) {
        // Lấy dữ liệu từ Model
        $customer = $this->customerModel->getAccount($id); 

        // Kiểm tra nếu không có dữ liệu thì quay về danh sách
        if (!$customer) {
            header('Location: /lego_shop_php/admincustomer');
            exit();
        }

        $filters = ['search' => $_GET['search'] ?? '', 'status' => $_GET['status'] ?? ''];
        $pageData = $this->getPaginationData($filters, 'edit', $id);

        // TRUYỀN BIẾN $customer VÀO ĐÂY
        $this->view('admin/customer', array_merge($pageData, [
            'title'    => 'Chỉnh sửa thành viên',
            'is_form'  => true,
            'customer' => $customer, // BIẾN NÀY QUYẾT ĐỊNH DỮ LIỆU HIỆN RA
            'search'   => $filters['search'],
            'status'   => $filters['status']
        ]));
    }

    // --- HÀM XỬ LÝ CẬP NHẬT DỮ LIỆU ---
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'fullname' => trim($_POST['fullname']),
                'phone'    => trim($_POST['phone']),
                'email'    => trim($_POST['email']),
                'role'     => $_POST['role'],
                'status'   => $_POST['status'],
                'password' => $_POST['password']
            ];

            $existError = $this->customerModel->checkExist($data['email'], $data['phone'], $id);
            if ($existError) {
                set_flash_message('error', 'exists_' . $existError);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            if ($this->customerModel->updateCustomer($id, $data)) {
                set_flash_message('msg', 'updated');
                header('Location: /lego_shop_php/admincustomer');
            } else {
                set_flash_message('error', 'db');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
        }
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'fullname' => trim($_POST['fullname']),
                'phone'    => trim($_POST['phone']),
                'email'    => trim($_POST['email']),
                'password' => $_POST['password'],
                'role'     => $_POST['role'] ?? 'customer',
                'status'   => $_POST['status'] ?? 'active'
            ];

            // 1. Kiểm tra trống
            if (empty($data['email']) || empty($data['phone']) || empty($data['fullname'])) {
                set_flash_message('error', 'Vui lòng điền đầy đủ thông tin.');
                header('Location: /lego_shop_php/admincustomer/add');
                exit();
            }

            // 2. Kiểm tra trùng lặp (GỌI HÀM CHECK TẠI ĐÂY)
            $existError = $this->customerModel->checkExist($data['email'], $data['phone']);
            if ($existError) {
                $msg = ($existError === 'email') ? 'Email này đã được sử dụng!' : 'Số điện thoại này đã được sử dụng!';
                set_flash_message('error', 'exists_' . $existError);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            // 3. Tiến hành lưu
            if ($this->customerModel->addCustomer($data)) {
                set_flash_message('msg', 'success'); // Đã sửa thành 'msg' để giống Category
                header('Location: /lego_shop_php/admincustomer');
            } else {
                set_flash_message('error', 'db');
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            }
            exit();
        }
    }
}