<?php
require_once __DIR__ . '/Env.php';
Env::load(__DIR__ . '/../../.env');

class Database {
    public $conn;

    // Dùng chung 1 kết nối cho toàn bộ request để transaction hoạt động xuyên suốt giữa các Model
    private static $sharedConn = null;

    public function getConnection() {
        if (self::$sharedConn === null) {
            // Thông tin kết nối lấy từ file .env (bộ test tự đặt DB_NAME=lego_shop_test)
            try {
                self::$sharedConn = new mysqli(
                    env('DB_HOST', 'localhost'),
                    env('DB_USER', 'root'),
                    (string)env('DB_PASS', ''),
                    env('DB_NAME', 'lego_shop'),
                    (int)env('DB_PORT', 3306)
                );
                self::$sharedConn->set_charset("utf8mb4");

                // Ép MySQL luôn chạy múi giờ Việt Nam (+07:00)
                self::$sharedConn->query("SET time_zone = '+07:00'");

            } catch (Exception $e) {
                // Không in chi tiết lỗi (lộ host/user DB) khi chạy thật
                error_log('DB connection error: ' . $e->getMessage());
                http_response_code(500);
                exit(env('APP_DEBUG', false) ? 'Lỗi kết nối CSDL: ' . $e->getMessage() : 'Hệ thống đang bảo trì, vui lòng thử lại sau.');
            }
        }
        $this->conn = self::$sharedConn;
        return $this->conn;
    }
}
