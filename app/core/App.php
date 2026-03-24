<?php
class App {
    protected $controller = "HomeController"; 
    protected $method = "index";
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. XỬ LÝ CONTROLLER
        if (isset($url[0])) {
            // Chuyển chữ đầu thành hoa (ví dụ: product -> Product)
            $controllerName = ucfirst($url[0]) . 'Controller';
            $file = 'app/controllers/' . $controllerName . '.php';

            if (file_exists($file)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        // Gọi file Controller (Bắt buộc dùng require_once)
        require_once 'app/controllers/' . $this->controller . '.php';
        
        // Khởi tạo Class Controller
        $this->controller = new $this->controller;

        // 2. XỬ LÝ METHOD (Hàm)
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 3. XỬ LÝ PARAMS (Tham số)
        $this->params = $url ? array_values($url) : [];

        // CHẠY HÀM (Cực kỳ quan trọng: Biến đầu tiên phải là đối tượng $this->controller)
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            // Cắt chuỗi URL thành mảng, loại bỏ ký tự lạ và dấu / cuối cùng
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return []; // Trả về mảng rỗng nếu không có URL
    }
}