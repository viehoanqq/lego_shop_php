<?php
class AdminCategoryController extends Controller {

    private $categoryModel;
    private $limit = 8; // Đặt limit chung

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
            'filters' => $filters,
            'title'   => 'Quản lý danh mục',
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
            $name = trim($_POST['name'] ?? '');
            
            if (empty($name)) {
                set_flash_message('error', 'empty');
                header('Location: /lego_shop_php/admincategory/add');
                exit();
            }

            // Kiểm tra trùng tên
            if ($this->categoryModel->isNameExists($name)) {
                set_flash_message('error', 'name_exists');
                header('Location: /lego_shop_php/admincategory/add');
                exit();
            }

            $image = $this->handleUpload($_FILES['image_url']);
            $data = [
                'name' => $name,
                'description' => trim($_POST['description'] ?? ''),
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
            $id = intval($id);
            $name = trim($_POST['name'] ?? '');

            if (empty($name)) {
                set_flash_message('error', 'empty');
                header('Location: /lego_shop_php/admincategory/edit/'.$id);
                exit();
            }

            // Kiểm tra trùng tên (loại trừ chính nó)
            if ($this->categoryModel->isNameExists($name, $id)) {
                set_flash_message('error', 'name_exists');
                header('Location: /lego_shop_php/admincategory/edit/'.$id);
                exit();
            }

            $old_data = $this->categoryModel->getCategoryById($id);
            $image = $this->handleUpload($_FILES['image_url']) ?: $old_data['image_url'];
            
            $data = [
                'name' => $name,
                'description' => trim($_POST['description'] ?? ''),
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

    // Hàm KHÓA danh mục (Đổi tên từ delete -> lock)
    public function lock($id) {
        $id = intval($id);
        if ($this->categoryModel->updateStatusAdmin($id, 'locked')) {
            set_flash_message('msg', 'hidden');
        } else {
            set_flash_message('error', 'db');
        }
        header('Location: /lego_shop_php/admincategory');
        exit();
    }

    // Hàm XÓA MỀM danh mục (MỚI)
    public function delete($id) {
        $id = intval($id);
        
        // Kiểm tra xem danh mục này có chứa sản phẩm nào không
        if ($this->categoryModel->hasProducts($id)) {
            // Đã có sản phẩm -> Chỉ được phép XÓA MỀM (Ẩn đi để bảo toàn dữ liệu sản phẩm)
            if ($this->categoryModel->softDeleteCategory($id)) {
                set_flash_message('msg', 'soft_deleted');
            } else {
                set_flash_message('error', 'db');
            }
        } else {
            // Hoàn toàn trống (chưa có sản phẩm) -> XÓA VĨNH VIỄN khỏi DB
            if ($this->categoryModel->deleteCategoryForever($id)) {
                set_flash_message('msg', 'deleted');
            } else {
                set_flash_message('error', 'db');
            }
        }
        
        header('Location: /lego_shop_php/admincategory');
        exit();
    }
    public function restore($id) {
        $id = intval($id);
        // Khi khôi phục, đưa danh mục về trạng thái 'locked' để an toàn, Admin muốn mở bán thì phải bấm nút Mở khóa sau.
        if ($this->categoryModel->updateStatus($id, 'locked')) {
            set_flash_message('msg', 'restored');
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