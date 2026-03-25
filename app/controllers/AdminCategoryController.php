<?php
class AdminCategoryController extends Controller {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = $this->model('CategoryModel');
    }

    public function index() {
    $categories = $this->categoryModel->getCategoriesWithCount(); 
    $this->view('admin/categories', [
        'categories' => $categories,
        'is_form' => false 
    ]);
}

    public function add() {
        echo "Đang chạy hàm ADD rồi nè!"; // Thêm dòng này
        $categories = $this->categoryModel->getCategoriesWithCount(); 
        $this->view('admin/categories', [
            'categories' => $categories,
            'is_form' => true
        ]);
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
                header('Location: /lego_shop_php/admincategory?msg=success');
            } else {
                header('Location: /lego_shop_php/admincategory?error=db');
            }
            exit();
        }
    }

    public function edit($id) {
        $categories = $this->categoryModel->getCategoriesWithCount();
        $category = $this->categoryModel->getCategoryById($id);
        
        $this->view('admin/categories', [
            'categories' => $categories,
            'category' => $category,
            'is_form' => true
        ]);
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
                header('Location: /lego_shop_php/admincategory?msg=updated');
            } else {
                header('Location: /lego_shop_php/admincategory?error=db');
            }
            exit();
        }
    }

    public function delete($id) {
        $db = $this->categoryModel->getConnection();
        $id = intval($id);
        $sql = "UPDATE categories SET status = 'locked' WHERE id = $id";
        if ($db->query($sql)) {
            header('Location: /lego_shop_php/admincategory?msg=hidden');
        }
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