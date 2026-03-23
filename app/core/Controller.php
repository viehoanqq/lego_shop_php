<?php
class Controller {
    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }

    public function view($view, $data = []) {
        extract($data); 
        
        // Tự động ghép Header
        if (file_exists('app/views/components/header.php')) {
            require_once 'app/views/components/header.php';
        }

        // Nạp nội dung thân bài
        if (file_exists('app/views/' . $view . '.php')) {
            require_once 'app/views/' . $view . '.php';
        }

        // Tự động ghép Footer
        if (file_exists('app/views/components/footer.php')) {
            require_once 'app/views/components/footer.php';
        }
    }
}