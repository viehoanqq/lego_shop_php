<?php
class AdminSupplierController extends Controller {
    private $supplierModel;
    private $limit = 10;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); exit; 
        }
        $this->supplierModel = $this->model('SupplierModel');
    }

    private function getPaginationData($filters) {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        $suppliers = $this->supplierModel->getSuppliers($filters['keyword'], $filters['status'], $this->limit, $offset);
        $totalItems = $this->supplierModel->countSuppliers($filters['keyword'], $filters['status']);
        $totalPages = ceil($totalItems / $this->limit);

        return [
            'suppliers'   => $suppliers,
            'totalItems'  => $totalItems,
            'totalPages'  => $totalPages,
            'currentPage' => $page
        ];
    }

    public function index() {
        $filters = [
            'keyword' => trim($_GET['keyword'] ?? ''),
            'status'  => $_GET['status'] ?? 'all'
        ];
        $pageData = $this->getPaginationData($filters);
        
        $this->view('admin/suppliers', array_merge($pageData, [
            'is_form' => false,
            'filters' => $filters
        ]));
    }

    public function add() {
        $filters = ['keyword' => '', 'status' => 'all'];
        $pageData = $this->getPaginationData($filters);
        
        $this->view('admin/suppliers', array_merge($pageData, [
            'is_form' => true,
            'filters' => $filters
        ]));
    }

    public function edit($id) {
        $supplier = $this->supplierModel->getSupplierById($id);
        if (!$supplier) {
            header('Location: /lego_shop_php/adminsupplier'); exit;
        }

        $filters = ['keyword' => '', 'status' => 'all'];
        $pageData = $this->getPaginationData($filters);
        
        $this->view('admin/suppliers', array_merge($pageData, [
            'supplier'=> $supplier,
            'is_form' => true,
            'filters' => $filters
        ]));
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? '')
            ];

            if (empty($data['name']) || empty($data['phone'])) {
                set_flash_message('error', 'empty');
            } else {
                if ($this->supplierModel->insertSupplier($data)) {
                    set_flash_message('msg', 'success');
                } else {
                    set_flash_message('error', 'db');
                }
            }
            header('Location: /lego_shop_php/adminsupplier'); exit;
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name'    => trim($_POST['name'] ?? ''),
                'phone'   => trim($_POST['phone'] ?? ''),
                'email'   => trim($_POST['email'] ?? ''),
                'address' => trim($_POST['address'] ?? '')
            ];

            if (empty($data['name']) || empty($data['phone'])) {
                set_flash_message('error', 'empty');
                header('Location: /lego_shop_php/adminsupplier/edit/'.$id); exit;
            }

            if ($this->supplierModel->updateSupplier($id, $data)) {
                set_flash_message('msg', 'updated');
            } else {
                set_flash_message('error', 'db');
            }
            header('Location: /lego_shop_php/adminsupplier'); exit;
        }
    }

    public function toggleStatus($id) {
        $supplier = $this->supplierModel->getSupplierById($id);
        if ($supplier) {
            if ($this->supplierModel->toggleStatus($id, $supplier['status'])) {
                set_flash_message('msg', 'status_changed');
            } else {
                set_flash_message('error', 'db');
            }
        }
        header('Location: /lego_shop_php/adminsupplier'); exit;
    }

    public function delete($id) {
        $id = intval($id);
        
        // Kiểm tra xem có lịch sử nhập hàng không
        if ($this->supplierModel->hasImportHistory($id)) {
            // Đã có lịch sử -> Chỉ được phép XÓA MỀM (Ẩn)
            if ($this->supplierModel->softDeleteSupplier($id)) {
                set_flash_message('msg', 'soft_deleted');
            } else {
                set_flash_message('error', 'db');
            }
        } else {
            // Chưa có lịch sử -> XÓA VĨNH VIỄN
            if ($this->supplierModel->deleteSupplierForever($id)) {
                set_flash_message('msg', 'deleted');
            } else {
                set_flash_message('error', 'db');
            }
        }
        
        header('Location: /lego_shop_php/adminsupplier');
        exit;
    }
}
?>