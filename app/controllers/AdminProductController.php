<?php
class AdminProductController extends Controller {
    private $productModel;
    private $categoryModel;

    public function __construct() {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    // 1. Hiển thị danh sách: Lấy cả status 1 và 2 cho Admin
    public function index() {
        $products = $this->productModel->getFilteredProducts(['status' => '1,2'], 0, 100); 

        $this->view('admin/products', [
            'products' => $products,
            'is_form' => false
        ]);
    }

    // 2. Form Thêm mới
    public function add() {
        $products = $this->productModel->getFilteredProducts(['status' => '1,2'], 0, 100);
        $categories = $this->categoryModel->getAllCategories();
        $this->view('admin/products', [
            'products' => $products,
            'categories' => $categories,
            'is_form' => true
        ]);
    }

    // 3. Logic Lưu sản phẩm mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'sku' => $_POST['sku'] ?? '',
                'selling_price' => intval($_POST['selling_price'] ?? 0),
                'category_id' => intval($_POST['category_id'] ?? 0),
                'pieces' => intval($_POST['pieces'] ?? 0),
                'description' => $_POST['description'] ?? '',
                'main_image' => !empty($_FILES['main_image']['name']) ? $this->uploadFile($_FILES['main_image']) : null
            ];

            if (empty($data['name']) || empty($data['sku'])) {
                header('Location: /lego_shop_php/adminproduct/add?error=empty'); exit();
            }

            if ($this->productModel->isSkuExists($data['sku'])) {
                header('Location: /lego_shop_php/adminproduct/add?error=sku_exists'); exit();
            }

            if ($this->productModel->insertProduct($data)) {
                header('Location: /lego_shop_php/adminproduct?msg=success');
            } else {
                header('Location: /lego_shop_php/adminproduct/add?error=db');
            }
            exit();
        }
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => $_POST['name'],
                'sku' => $_POST['sku'],
                'selling_price' => intval($_POST['selling_price']),
                'category_id' => intval($_POST['category_id']),
                'pieces' => intval($_POST['pieces']),
                'main_image' => !empty($_FILES['main_image']['name']) ? $this->uploadFile($_FILES['main_image']) : null
            ];

            if ($this->productModel->isSkuExists($data['sku'], $id)) {
                header('Location: /lego_shop_php/adminproduct/edit/'.$id.'?error=sku_exists'); exit();
            }

            if ($this->productModel->updateProduct($id, $data)) {
                header('Location: /lego_shop_php/adminproduct?msg=updated');
            } else {
                header('Location: /lego_shop_php/adminproduct/edit/'.$id.'?error=db');
            }
            exit();
        }
    }

    public function hide($id) {
        if ($this->productModel->updateStatus($id, 2)) {
            header('Location: /lego_shop_php/adminproduct?msg=hidden');
        } else {
            header('Location: /lego_shop_php/adminproduct?error=db');
        }
        exit();
    }

    public function delete($id) {
        if ($this->productModel->updateStatus($id, 0)) {
            header('Location: /lego_shop_php/adminproduct?msg=deleted');
        } else {
            header('Location: /lego_shop_php/adminproduct?error=db');
        }
        exit();
    }

    // Hàm Edit (Cần thiết để bổ sung tham số cho View)
    public function edit($id) {
        $products = $this->productModel->getFilteredProducts(['status' => '1,2'], 0, 100);
        $categories = $this->categoryModel->getAllCategories();
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            header('Location: /lego_shop_php/adminproduct');
            exit();
        }

        $this->view('admin/products', [
            'products' => $products,
            'categories' => $categories,
            'product' => $product,
            'is_form' => true
        ]);
    }

    private function uploadFile($file) {
        $targetDir = "public/assets/images/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . '_' . basename($file["name"]);
        $targetFile = $targetDir . $fileName;
        return move_uploaded_file($file["tmp_name"], $targetFile) ? $fileName : 'default.jpg';
    }
}