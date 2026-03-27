<?php
class AdminCategoryController extends Controller {

    private $categoryModel;
    private $limit = 10; // Đặt limit chung

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
        $this->categoryModel = $this->model('CategoryModel');
    }

    // Hàm helper để lấy dữ liệu phân trang
    private function getPaginationData($filters) {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        // Gọi hàm Model với offset và limit
        $categories = $this->categoryModel->getAdminCategoriesWithCount($filters['keyword'], $filters['status'], $this->limit, $offset);
        $totalItems = $this->categoryModel->countAdminCategories($filters['keyword'], $filters['status']);
        
        $totalPages = ceil($totalItems / $this->limit);

        return [
            'categories' => $categories,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }

    public function index() {
        // Lấy dữ liệu lọc từ URL
        $filters = [
            'keyword' => $_GET['keyword'] ?? '',
            'status'  => $_GET['status'] ?? 'all'
        ];
        // Lấy dữ liệu phân trang
        $pageData = $this->getPaginationData($filters);

        $this->view('admin/categories', array_merge($pageData, [
            'is_form' => false,
            'filters' => $filters
        ]));
    }

    public function add() {
        $filters = ['keyword' => '', 'status' => 'all'];
        $pageData = $this->getPaginationData($filters);

        $this->view('admin/categories', array_merge($pageData, [
            'is_form' => true,
            'filters' => $filters
        ]));
    }


    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $image = $this->handleUpload($_FILES['image_url']);
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'image_url' => $image ?: 'default.jpg'
            ];
            
            if ($this->categoryModel->insert($data)) {
                set_flash_message('msg', 'success'); 
            } else {
                set_flash_message('error', 'db');
            }
            header('Location: /lego_shop_php/admincategory');
            exit();
        }
    }

    public function edit($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            header('Location: /lego_shop_php/admincategory');
            exit();
        }

        $filters = ['keyword' => '', 'status' => 'all'];
        $pageData = $this->getPaginationData($filters);

        $this->view('admin/categories', array_merge($pageData, [
            'category' => $category,
            'is_form'  => true,
            'filters'  => $filters
        ]));
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $old_data = $this->categoryModel->getCategoryById($id);
            $image = $this->handleUpload($_FILES['image_url']) ?: $old_data['image_url'];
            
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'image_url' => $image
            ];

            if ($this->categoryModel->update($id, $data)) {
                set_flash_message('msg', 'updated'); 
            } else {
                set_flash_message('error', 'db');
            }
            header('Location: /lego_shop_php/admincategory');
            exit();
        }
    }

    // Hàm Mở khóa danh mục
    public function unlock($id) {
        $id = intval($id);
        // Sử dụng hàm updateStatusAdmin
        if ($this->categoryModel->updateStatusAdmin($id, 'active')) {
            // Thông báo: Chỉ mở danh mục, sản phẩm vẫn ẩn
            set_flash_message('msg', 'unlocked'); 
        } else {
            set_flash_message('error', 'db');
        }
        header('Location: /lego_shop_php/admincategory');
        exit();
    }

    // Hàm Khóa danh mục
    public function delete($id) {
        $id = intval($id);
        // Khi khóa danh mục -> Model sẽ tự khóa luôn sản phẩm bên trong
        if ($this->categoryModel->updateStatusAdmin($id, 'locked')) {
            set_flash_message('msg', 'hidden');
        } else {
            set_flash_message('error', 'db');
        }
        header('Location: /lego_shop_php/admincategory');
        exit();
    }

    private function handleUpload($file) {
        if (isset($file) && $file['error'] == 0) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = time() . '.' . $ext;
            move_uploaded_file($file['tmp_name'], 'public/assets/images/' . $filename);
            return $filename;
        }
        return null;
    }
}