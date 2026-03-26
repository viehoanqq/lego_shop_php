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
            // Truyền thêm $this->port vào vị trí thứ 5
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name, $this->port);
            $this->conn->set_charset("utf8");
        } catch(Exception $e) {
            echo "Lỗi kết nối: " . $e->getMessage();
        }
        return $this->conn;
    }
}