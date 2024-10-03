<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'api_ transacciones'; // Cambia esto al nombre de tu base de datos
    private $username = 'root'; // Cambia esto a tu usuario
    private $password = ''; // Cambia esto a tu contraseña
    public $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
