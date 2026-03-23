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
        // Giải nén mảng data thành các biến riêng lẻ (rất tốt, giữ nguyên)
        extract($data); 
        
        // ==========================================
        // 1. ĐỔ DỮ LIỆU ĐỘNG LÊN HEADER TẠI ĐÂY
        // ==========================================
        require_once __DIR__ . '/../models/CategoryModel.php';
        $categoryModel = new CategoryModel();
        
        // Biến này sẽ tự động chạy sang file header.php
        $header_categories = $categoryModel->getAllCategories();

        // ==========================================
        // 2. GHÉP GIAO DIỆN (Dùng __DIR__ để chống lỗi)
        // ==========================================
        
        // Tự động ghép Header
        if (file_exists(__DIR__ . '/../views/components/header.php')) {
            require_once __DIR__ . '/../views/components/header.php';
        }

        // Nạp nội dung thân bài (home, login, register...)
        if (file_exists(__DIR__ . '/../views/' . $view . '.php')) {
            require_once __DIR__ . '/../views/' . $view . '.php';
        }

        // Tự động ghép Footer
        if (file_exists(__DIR__ . '/../views/components/footer.php')) {
            require_once __DIR__ . '/../views/components/footer.php';
        }
    }
}