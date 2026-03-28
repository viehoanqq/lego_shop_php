<?php
class Database {
    private $host = "localhost";
    private $port = 3306;
    private $db_name = "lego_shop";
    private $username = "root";
    private $password = "";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, $this->port);
            $this->conn->set_charset("utf8");

            // --- CHỈNH Ở ĐÂY ---
            // Ép MySQL luôn chạy múi giờ Việt Nam (+07:00)
            $this->conn->query("SET time_zone = '+07:00'");
            
        } catch(Exception $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
        }
        return $this->conn;
    }
}