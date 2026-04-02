<?php
class AdminProductController extends Controller {
    private $productModel;
    private $categoryModel;
    private $limit = 6; // Đặt limit chung

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
            header("Location: /lego_shop_php/admin/login"); 
            exit; 
        }
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

        // 1. Kiểm tra sản phẩm có tồn tại không
        if (!$product) {
            set_flash_message('error', 'db');
            header('Location: /lego_shop_php/adminproduct');
            exit();
        }

        // 2. Nếu sản phẩm đã ẩn rồi (status == 0) thì thông báo luôn
        if ($product['status'] == 0) {
            set_flash_message('error', 'already_hidden');
        } 
        // 3. Nếu chưa ẩn (status là 1 hoặc 2) thì tiến hành đưa về 0
        else {
            if ($this->productModel->updateStatus($id, 0)) {
                set_flash_message('msg', 'hidden');
            } else {
                set_flash_message('error', 'db');
            }
        }

        header('Location: /lego_shop_php/adminproduct');
        exit();
    }

    // Hàm Mở khóa sản phẩm (Chuyển từ 2 sang 1)
    // public function show($id) {
    //     $id = intval($id);
    //     $product = $this->productModel->getProductById($id);

    //     if (!$product) {
    //         set_flash_message('error', 'db');

    //     // Chỉ cho phép đổi từ 0 -> 2
    //     } elseif ($product['status'] != 0) {
    //         set_flash_message('error', 'invalid_status');

    //     // Update status = 2
    //     } elseif ($this->productModel->updateStatus($id, 2)) {
    //         set_flash_message('msg', 'updated_to_2');

    //     } else {
    //         set_flash_message('error', 'db');
    //     }

    //     header('Location: /lego_shop_php/adminproduct');
    //     exit();
    // }


    // public function toggleStatus($id) {
    //     // Đổi $this->productModel->find($id) thành hàm bạn đã có:
    //     $product = $this->productModel->getProductFullDetail($id);
        
    //     if (!$product) {
    //         set_flash_message('error', 'notfound');
    //         header("Location: /lego_shop_php/adminproduct");
    //         exit();
    //     }

    //     // Đảo ngược trạng thái
    //     $newStatus = ($product['status'] == 1) ? 2 : 1;
        
    //     // Gọi hàm updateStatus bạn đã có ở dòng 134 của Model
    //     $result = $this->productModel->updateStatus($id, $newStatus);
        
    //     if ($result) {
    //         set_flash_message('msg', ($newStatus == 2 ? 'hidden' : 'show'));
    //     } else {
    //         set_flash_message('error', 'db');
    //     }
        
    //     header("Location: /lego_shop_php/adminproduct");
    //     exit();
    // }

    public function toggleStatus($id) {
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            set_flash_message('error', 'notfound');
            header("Location: /lego_shop_php/adminproduct");
            exit();
        }

        // Đảo trạng thái: Nếu đang là 1 thì chuyển sang 2, ngược lại chuyển sang 1
        $newStatus = ($product['status'] == 1) ? 2 : 1;

        // Gọi hàm Model có tích hợp sẵn kiểm tra danh mục
        $result = $this->productModel->updateStatusWithTaskCheck($id, $newStatus);

        if ($result === 'success') {
            set_flash_message('msg', ($newStatus == 2 ? 'hidden' : 'show'));
        } elseif ($result === 'cat_locked') {
            set_flash_message('error', 'cat_is_locked');
        } else {
            set_flash_message('error', 'db');
        }

        header("Location: /lego_shop_php/adminproduct");
        exit();
    }


    // 3. Logic Lưu sản phẩm mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Xử lý upload ảnh an toàn
            $uploaded_image = null;
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $uploaded_image = $this->uploadFile($_FILES['main_image']);
            }

            $data = [
                'name'          => trim($_POST['name'] ?? ''),
                'sku'           => strtoupper(trim($_POST['sku'] ?? '')),
                'selling_price' => intval($_POST['selling_price'] ?? 0),
                'category_id'   => intval($_POST['category_id'] ?? 0),
                'description'   => trim($_POST['description'] ?? ''),
                'main_image'    => $uploaded_image, // Trả về Tên file hoặc Null
                
                'pieces'        => intval($_POST['pieces'] ?? 0),
                'manufacturer'  => trim($_POST['manufacturer'] ?? ''),
                'material'      => trim($_POST['material'] ?? ''),
                'dimensions'    => (!empty($_POST['length']) && !empty($_POST['width']) && !empty($_POST['height'])) 
                                   ? intval($_POST['length']) . ' x ' . intval($_POST['width']) . ' x ' . intval($_POST['height']) . ' cm' 
                                   : '',
                'age_range'     => !empty($_POST['age_range']) ? intval($_POST['age_range']) . '+' : '',
                'release_year'  => !empty($_POST['release_year']) ? intval($_POST['release_year']) : null,
                'theme_story'   => trim($_POST['theme_story'] ?? '')
            ];

            if ($data['name'] === '' || $data['sku'] === '') {
                set_flash_message('error', 'empty');
                header('Location: /lego_shop_php/adminproduct/add'); exit();
            }
            if ($this->productModel->isSkuExists($data['sku'])) {
                set_flash_message('error', 'sku_exists');
                header('Location: /lego_shop_php/adminproduct/add'); exit();
            }

            if ($this->productModel->insertProduct($data)) {
                set_flash_message('msg', 'success');
                header('Location: /lego_shop_php/adminproduct');
            } else {
                set_flash_message('error', 'db');
                header('Location: /lego_shop_php/adminproduct/add');
            }
            exit();
        }
    }   
    // ==========================================
    // CẬP NHẬT SẢN PHẨM (Cập nhật 2 bảng cùng lúc)
    // ==========================================
   public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Bắt file ảnh mới nếu Admin có tải lên
            $uploaded_image = null;
            if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
                $uploaded_image = $this->uploadFile($_FILES['main_image']);
            }

            $data = [
                'name'          => trim($_POST['name'] ?? ''),
                'sku'           => strtoupper(trim($_POST['sku'] ?? '')),
                'selling_price' => intval($_POST['selling_price'] ?? 0),
                'category_id'   => intval($_POST['category_id'] ?? 0),
                'description'   => trim($_POST['description'] ?? ''),
                'main_image'    => $uploaded_image,
                
                'pieces'        => intval($_POST['pieces'] ?? 0),
                'manufacturer'  => trim($_POST['manufacturer'] ?? ''),
                'material'      => trim($_POST['material'] ?? ''),
                'dimensions'    => (!empty($_POST['length']) && !empty($_POST['width']) && !empty($_POST['height'])) 
                                   ? intval($_POST['length']) . ' x ' . intval($_POST['width']) . ' x ' . intval($_POST['height']) . ' cm' 
                                   : '',
                'age_range'     => !empty($_POST['age_range']) ? intval($_POST['age_range']) . '+' : '',
                'release_year'  => !empty($_POST['release_year']) ? intval($_POST['release_year']) : null,
                'theme_story'   => trim($_POST['theme_story'] ?? '')
            ];

            if ($this->productModel->isSkuExists($data['sku'], $id)) {
                set_flash_message('error', 'sku_exists');
                header('Location: /lego_shop_php/adminproduct/edit/'.$id); exit();
            }

            if ($this->productModel->updateProduct($id, $data)) {
                set_flash_message('msg', 'updated');
                header('Location: /lego_shop_php/adminproduct');
            } else {
                set_flash_message('error', 'db');
                header('Location: /lego_shop_php/adminproduct/edit/'.$id);
            }
            exit();
        }
    }

    
    // public function delete($id) {
    //     $productModel = $this->model("ProductModel");

    //     // BƯỚC 1: KIỂM TRA RÀNG BUỘC
    //     if (!$productModel->canDeleteProduct($id)) {
    //         // Gửi mã 'has_history' để View hiển thị đúng câu thông báo
    //         set_flash_message('error', 'has_history'); 
    //         header("Location: /lego_shop_php/adminproduct");
    //         exit();
    //     }

    //     // BƯỚC 2: TIẾN HÀNH XÓA
    //     if ($productModel->deleteProduct($id)) {
    //         // Đồng nhất với View (View của bạn đang dùng 'deleted')
    //         set_flash_message('msg', 'deleted'); 
    //     } else {
    //         set_flash_message('error', 'db');
    //     }

    //     header("Location: /lego_shop_php/adminproduct");
    //     exit();
    // }

    public function delete($id) {
        $productModel = $this->model("ProductModel");
        $id = intval($id);

        // BƯỚC 1: KIỂM TRA XEM CÓ LỊCH SỬ GIAO DỊCH CHƯA
        if ($productModel->canDeleteProduct($id)) {
            // TRƯỜNG HỢP A: Chưa có lịch sử -> Xóa thẳng tay
            if ($productModel->deleteProduct($id)) {
                set_flash_message('msg', 'deleted');
            } else {
                set_flash_message('error', 'db');
            }
        } else {
            // TRƯỜNG HỢP B: Đã có lịch sử -> Không được xóa, chỉ được Khóa
            if ($productModel->hideProduct($id)) {
                set_flash_message('error', 'hidden_due_to_history');
            } else {
                set_flash_message('error', 'db');
            }
        }

        header("Location: /lego_shop_php/adminproduct");
        exit();
    }


    private function uploadFile($file) {
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/gif'];
        
        // Kiểm tra định dạng thực tế
        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($fileInfo, $file['tmp_name']);
        finfo_close($fileInfo);

        if (!in_array($mimeType, $allowed)) return 'default.jpg';
        if ($file['size'] > 2 * 1024 * 1024) return 'default.jpg';

        $targetDir = "public/assets/images/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . '_' . basename($file["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $fileName;
        }
        return 'default.jpg';
    }
    

    // Hàm hiển thị trang chi tiết kỹ thuật
    public function detail($id) {
        $product = $this->productModel->getProductFullDetail($id);
        if (!$product) {
            set_flash_message('error', 'notfound');
            header('Location: /lego_shop_php/adminproduct');
            exit();
        }
        
        $this->view('admin/product_detail', [
            'product' => $product,
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

            if ($this->productModel->updateProductDetail($id, $data)) {
                set_flash_message('msg', 'updated');
            } else {
                set_flash_message('error', 'db');
            }
            header("Location: /lego_shop_php/adminproduct/detail/" . $id);
            exit();
        }
    }


    public function lowstock() {
        // 1. Lấy keyword và type từ URL
        $type = $_GET['type'] ?? 'all';
        $keyword = $_GET['keyword'] ?? ''; 
        
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($page - 1) * $this->limit;

        // 2. Truyền thêm keyword vào Model
        $products = $this->productModel->getLowStockProducts($offset, $this->limit, $type, $keyword);
        $totalItems = $this->productModel->countLowStockProducts($type, $keyword);
        
        $totalPages = ceil($totalItems / $this->limit);

        // LẤY DANH SÁCH TẤT CẢ SẢN PHẨM ĐỂ TRUYỀN XUỐNG JAVASCRIPT (Làm Combo box)
        $all_products = $this->productModel->getAllProductsForDropdown();

        $this->view('admin/low_stock', [
            'products'     => $products,
            'all_products' => $all_products, // Đưa dữ liệu này xuống view
            'totalItems'   => $totalItems,
            'totalPages'   => $totalPages,
            'currentPage'  => $page,
            'currentType'  => $type,
            'keyword'      => $keyword 
        ]);
    }
    

    
}