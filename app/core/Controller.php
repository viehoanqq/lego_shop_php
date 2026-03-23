<?php
class Controller {
    // Hàm nạp Model
    public function model($model) {
        require_once 'app/models/' . $model . '.php';
        return new $model();
    }

    // Hàm nạp View và truyền dữ liệu
    public function view($view, $data = []) {
    extract($data); 
    
    // 1. Tự động đắp cái đầu vào
    require_once 'app/views/components/header.php';

    // 2. Đổ nội dung thân bài (home, product, v.v.) vào giữa
    if (file_exists('app/views/' . $view . '.php')) {
        require_once 'app/views/' . $view . '.php';
    }

    // 3. Tự động đắp cái đuôi vào
    require_once 'app/views/components/footer.php';
}
}