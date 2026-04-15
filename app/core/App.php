<?php
class App {
    protected $controller = "HomeController";
    protected $method = "index";
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. XỬ LÝ CONTROLLER
        if (isset($url[0])) {
            $name = ucfirst($url[0]) . 'Controller';

            if (strpos($url[0], 'admin') === 0 && strlen($url[0]) > 5) {
                $sub = substr($url[0], 5); 
                $name = 'Admin' . ucfirst($sub) . 'Controller';
            }

            // BỎ DẤU ../ Ở ĐÂY
            $file = 'app/controllers/' . $name . '.php';

            if (file_exists($file)) {
                $this->controller = $name;
                unset($url[0]);
            }
        }

        // 2. REQUIRE CONTROLLER - BỎ DẤU ../ Ở ĐÂY LUÔN
        $fullPath = 'app/controllers/' . $this->controller . '.php';
        if (file_exists($fullPath)) {
            require_once $fullPath;
        } else {
            // Backup nếu không tìm thấy file
            require_once 'app/controllers/HomeController.php';
            $this->controller = "HomeController";
        }

        $this->controller = new $this->controller;

        // 3. XỬ LÝ METHOD
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        // 4. XỬ LÝ PARAMS
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
