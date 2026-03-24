<?php
class Controller {
    
    // Nạp Model
    public function model($model) {
        require_once __DIR__ . '/../models/' . $model . '.php';
        return new $model();
    }

    // Nạp View
    public function view($view, $data = []) {
        // Giải nén mảng data thành các biến riêng lẻ
        extract($data); 
        
        // ==========================================
        // 1. XỬ LÝ GIAO DIỆN ADMIN
        // ==========================================
        if (strpos($view, 'admin/') !== false) {
            
            // TRƯỜNG HỢP ĐẶC BIỆT: Trang Login Admin (Không dùng layout chung)
            if ($view === 'admin/login') {
                if (file_exists(__DIR__ . '/../views/admin/login.php')) {
                    require_once __DIR__ . '/../views/admin/login.php';
                    return; 
                }
            }

            // CÁC TRANG ADMIN KHÁC (Dashboard, Products...): Dùng layout_admin.php
            if (file_exists(__DIR__ . '/../views/admin/admin_layout.php')) {
                // Biến này sẽ được dùng bên trong file layout_admin.php để nạp nội dung con
                $view_content = $view; 
                require_once __DIR__ . '/../views/admin/admin_layout.php';
            } else {
                die("Lỗi: Không tìm thấy file layout_admin.php tại views/admin/");
            }
        } 
        
        // ==========================================
        // 2. XỬ LÝ GIAO DIỆN NGƯỜI DÙNG (USER)
        // ==========================================
        else {
            // Đổ dữ liệu động lên Header cho User
            require_once __DIR__ . '/../models/CategoryModel.php';
            $categoryModel = new CategoryModel();
            $header_categories = $categoryModel->getAllCategories();

            // Ghép bộ khung User: Header -> Content -> Footer
            if (file_exists(__DIR__ . '/../views/components/header.php')) {
                require_once __DIR__ . '/../views/components/header.php';
            }

            if (file_exists(__DIR__ . '/../views/' . $view . '.php')) {
                require_once __DIR__ . '/../views/' . $view . '.php';
            } else {
                die("Lỗi: Không tìm thấy giao diện User tại " . $view);
            }

            if (file_exists(__DIR__ . '/../views/components/footer.php')) {
                require_once __DIR__ . '/../views/components/footer.php';
            }
        }
    }
}