<?php
class App {
    protected $controller = "HomeController"; 
    protected $method = "index";
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. XỬ LÝ CONTROLLER
        if (isset($url[0])) {
    // Thử trường hợp viết hoa chữ đầu (chuẩn thường)
    $name = ucfirst($url[0]) . 'Controller';
    
    // MẸO: Nếu URL bắt đầu bằng 'admin', ta thử tìm file có chữ hoa ở từ thứ 2
    // Ví dụ: adminreport -> AdminReportController
    if (strpos($url[0], 'admin') === 0 && strlen($url[0]) > 5) {
        $sub = substr($url[0], 5); // lấy phần sau chữ 'admin'
        $name = 'Admin' . ucfirst($sub) . 'Controller';
    }

    $file = 'app/controllers/' . $name . '.php';

    if (file_exists($file)) {
        $this->controller = $name;
        unset($url[0]);
    }
}

        // Kiểm tra lại đường dẫn file trước khi require
        $fullPath = 'app/controllers/' . $this->controller . '.php';
        if(file_exists($fullPath)){
            require_once $fullPath;
        } else {
            // Nếu không tìm thấy cả HomeController, báo lỗi để debug
            die("Lỗi: Không tìm thấy file Controller tại " . $fullPath);
        }
        
        $this->controller = new $this->controller;

        // 2. XỬ LÝ METHOD
        if (isset($url[1])) {
            // Ép method về chữ thường nếu bạn đặt tên hàm trong Controller là chữ thường
            $methodName = strtolower($url[1]); 
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}