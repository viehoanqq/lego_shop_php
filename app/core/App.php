<?php
class App {
    protected $controller = "HomeController";
    protected $method = "index";
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // URL admin kiểu cũ (/adminorder/detail/5) -> chuyển sang kiểu mới (/admin/order/detail/5)
        if (isset($url[0]) && preg_match('/^admin([a-z]+)$/i', $url[0], $m) && $this->adminControllerExists($m[1])) {
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $query = $_GET;
                unset($query['url']);
                $target = '/lego_shop_php/admin/' . strtolower($m[1]) . (count($url) > 1 ? '/' . implode('/', array_slice($url, 1)) : '');
                header('Location: ' . $target . ($query ? '?' . http_build_query($query) : ''), true, 301);
                exit;
            }
            $url = array_merge(['admin', strtolower($m[1])], array_slice($url, 1));
        }

        // 1. XỬ LÝ CONTROLLER
        if (isset($url[0])) {
            if (strtolower($url[0]) === 'admin' && isset($url[1]) && $this->adminControllerExists($url[1])) {
                // /admin/order/detail/5 -> AdminOrderController::detail(5)
                $this->controller = 'Admin' . ucfirst(strtolower($url[1])) . 'Controller';
                $url = array_slice($url, 2);
            } elseif (file_exists('app/controllers/' . ucfirst($url[0]) . 'Controller.php')) {
                // /checkout/payment -> CheckoutController::payment
                // /admin/login      -> AdminController::login (không có AdminLoginController)
                $this->controller = ucfirst($url[0]) . 'Controller';
                $url = array_slice($url, 1);
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
        if (isset($url[0])) {
            // Ép method về chữ thường nếu bạn đặt tên hàm trong Controller là chữ thường
            $methodName = strtolower($url[0]);
            if (method_exists($this->controller, $methodName)) {
                $this->method = $methodName;
                $url = array_slice($url, 1);
            }
        }

        $this->params = array_values($url);
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function adminControllerExists($name) {
        return preg_match('/^[a-z]+$/i', $name) && file_exists('app/controllers/Admin' . ucfirst(strtolower($name)) . 'Controller.php');
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }
        return [];
    }
}
