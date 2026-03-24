<?php
class Controller {
    
    // Nạp Model
    public function model($model) {
        // __DIR__ là app/core, dùng '/../' để lùi ra app, rồi vào models
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
            
            // TRƯỜNG HỢP ĐẶC BIỆT: Trang Login Admin (Không load Sidebar/Header)
            if ($view === 'admin/login') {
                if (file_exists(__DIR__ . '/../views/admin/login.php')) {
                    require_once __DIR__ . '/../views/admin/login.php';
                    return; // Kết thúc luôn, không chạy xuống dưới
                }
            }

            // CÁC TRANG ADMIN CÒN LẠI: Tự động ghép bộ khung Sidebar + Header
            if (file_exists(__DIR__ . '/../views/' . $view . '.php')) {
                // Nạp Sidebar (Dùng chung thư mục components với user như bạn muốn)
                if (file_exists(__DIR__ . '/../views/components/admin_sidebar.php')) {
                    require_once __DIR__ . '/../views/components/sidebar.php';
                }

                echo '<div class="main-content">';
                    // Nạp Admin Header
                    if (file_exists(__DIR__ . '/../views/components/admin_header.php')) {
                        require_once __DIR__ . '/../views/components/admin_header.php';
                    }

                    echo '<section class="content">';
                        // Nạp nội dung chính của trang (dashboard, products,...)
                        require_once __DIR__ . '/../views/' . $view . '.php';
                    echo '</section>';
                echo '</div>';

            } else {
                die("Lỗi: Không tìm thấy giao diện Admin tại " . $view);
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