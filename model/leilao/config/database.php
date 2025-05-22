<?php
/**
 * Arquivo de configuração da conexão com o banco de dados
 * Sistema de Leilão
 */

class Database {
    private $host = 'localhost';
    private $db_name = 'leilao';
    private $username = 'usb';
    private $password = 'usbw';
    private $conn;

    /**
     * Método para obter a conexão com o banco de dados
     * @return PDO Objeto de conexão com o banco de dados
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Erro de conexão: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>
