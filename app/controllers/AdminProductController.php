<?php
class AdminProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $limit = 6; // Đặt limit chung

    public function __construct() {
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }

    // Hàm helper để lấy dữ liệu phân trang dùng chung cho index, add, edit
    private function getPaginationData($filters) {
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        //Gọi hàm Admin thay vì hàm Filter mặc định
        $products = $this->productModel->getAdminProducts($filters, $offset, $this->limit);
        $totalProducts = $this->productModel->countAdminProducts($filters);
        
        $totalPages = ceil($totalProducts / $this->limit);

        return [
            'products' => $products,
            'totalItems' => $totalProducts,
            'totalPages' => $totalPages,
            'currentPage' => $page
        ];
    }

    public function index() {
        // Lấy dữ liệu lọc từ URL (Nếu không có thì dùng mặc định)
        $filters = [
            'keyword'  => $_GET['keyword'] ?? '',
            'category' => $_GET['category'] ?? 'all',
            'status'   => $_GET['status'] ?? '1,2' // Mặc định hiện cả Đang bán và Tạm ẩn
        ];

        //Lấy dữ liệu
        $pageData = $this->getPaginationData($filters);
        $categories = $this->categoryModel->getAllCategories();


        $this->view('admin/products', array_merge($pageData, [
            'categories' => $categories,
            'is_form'    => false,
            'filters'    => $filters
        ]));
    }

    // 2. Form Thêm mới
    public function add() {
        // Vẫn dùng bộ lọc mặc định để hiển thị danh sách bên dưới form
        $filters = ['keyword' => '', 'category' => 'all', 'status' => '1,2'];
        $pageData = $this->getPaginationData($filters);
        $categories = $this->categoryModel->getAllCategories();

        $this->view('admin/products', array_merge($pageData, [
            'categories' => $categories,
            'is_form'    => true,
            'filters'    => $filters
        ]));
    }

    // Hàm Edit 
    public function edit($id) {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            header('Location: /lego_shop_php/adminproduct');
            exit();
        }

        $filters = ['keyword' => '', 'category' => 'all', 'status' => '1,2'];
        $pageData = $this->getPaginationData($filters);
        $categories = $this->categoryModel->getAllCategories();

        $this->view('admin/products', array_merge($pageData, [
            'product'    => $product,
            'categories' => $categories,
            'is_form'    => true,
            'filters'    => $filters
        ]));
    }

    // Hàm Khóa sản phẩm (Chuyển từ 1 sang 2)
    public function hide($id) {
        $id = intval($id);
        $product = $this->productModel->getProductById($id);

        // Kiểm tra xem sản phẩm có tồn tại không
        if (!$product) {
            header('Location: /lego_shop_php/adminproduct?error=db');
            exit();
        }

        // Nếu sản phẩm ĐÃ BỊ KHÓA RỒI (status == 2)
        if ($product['status'] == 2) {
            header('Location: /lego_shop_php/adminproduct?error=already_hidden');
            exit();
        }

        // Nếu chưa khóa thì mới tiến hành khóa
        if ($this->productModel->updateStatus($id, 2)) {
            header('Location: /lego_shop_php/adminproduct?msg=hidden');
        } else {
            header('Location: /lego_shop_php/adminproduct?error=db');
        }
        exit();
    }

// Hàm Mở khóa sản phẩm (Chuyển từ 2 sang 1)
    public function show($id) {
        $id = intval($id);
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            header('Location: /lego_shop_php/adminproduct?error=db');
            exit();
        }

        // Nếu sản phẩm ĐANG MỞ RỒI (status == 1)
        if ($product['status'] == 1) {
            header('Location: /lego_shop_php/adminproduct?error=already_shown');
            exit();
        }

        // Nếu đang khóa thì mới mở
        if ($this->productModel->updateStatus($id, 1)) {
            header('Location: /lego_shop_php/adminproduct?msg=show');
        } else {
            header('Location: /lego_shop_php/adminproduct?error=db');
        }
        exit();
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

    public function delete($id) {
        if ($this->productModel->updateStatus($id, 0)) {
            header('Location: /lego_shop_php/adminproduct?msg=deleted');
        } else {
            header('Location: /lego_shop_php/adminproduct?error=db');
        }
        exit();
    }


    private function uploadFile($file) {
        $targetDir = "public/assets/images/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . '_' . basename($file["name"]);
        $targetFile = $targetDir . $fileName;
        return move_uploaded_file($file["tmp_name"], $targetFile) ? $fileName : 'default.jpg';
    }



    // Hàm hiển thị trang chi tiết kỹ thuật
    public function detail($id) {
        $product = $this->productModel->getProductFullDetail($id);
        if (!$product) {
            header('Location: /lego_shop_php/adminproduct?error=notfound');
            exit();
        }
        
        $this->view('admin/product_detail', [
            'product' => $product,
            'msg'     => $_GET['msg'] ?? null,    
            'error'   => $_GET['error'] ?? null   
        ]);
    }

    // Hàm xử lý LƯU dữ liệu từ trang chi tiết kỹ thuật
    public function updateDetail($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'manufacturer' => $_POST['manufacturer'] ?? '',
                'material'     => $_POST['material'] ?? '',
                'dimensions'   => $_POST['dimensions'] ?? '',
                'age_range'    => $_POST['age_range'] ?? '',
                'pieces'       => intval($_POST['pieces'] ?? 0),
                'theme_story'  => $_POST['theme_story'] ?? '',
                'release_year'  => $_POST['release_year'] ?? ''
            ];

            $result = $this->productModel->updateProductDetail($id, $data);

            if ($result) {
                // Redirect về trang chi tiết kèm thông báo thành công
                header("Location: /lego_shop_php/adminproduct/detail/" . $id . "?msg=updated");
            } else {
                // Redirect về trang chi tiết kèm thông báo lỗi
                header("Location: /lego_shop_php/adminproduct/detail/" . $id . "?error=db");
            }
            exit();
        }
    }
}