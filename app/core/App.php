<?php
class App {
    protected $controller = "HomeController"; 
    protected $method = "index";
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. XỬ LÝ CONTROLLER
        if (isset($url[0])) {
            // Ép tất cả URL về chữ thường, sau đó mới viết hoa chữ cái đầu
            // Ví dụ: pRoFiLe -> profile -> Profile
            $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
            
            // QUAN TRỌNG: Hãy kiểm tra file vật lý của bạn tên là gì (ví dụ: ProfileController.php)
            $file = 'app/controllers/' . $controllerName . '.php';

            if (file_exists($file)) {
                $this->controller = $controllerName;
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