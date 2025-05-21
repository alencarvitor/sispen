<?php
// Classe base para conexão com o banco de dados
// core/Database.php

class Database {
    private $host;
    private $user;
    private $pass;
    private $db;
    private $conn;
    private static $instance = null;

    private function __construct() {
        $config = require_once __DIR__ . '/../config/database.php';
        $this->host = $config['host'];
        $this->user = $config['user'];
        $this->pass = $config['pass'];
        $this->db = $config['db'];
        $this->connect();
    }

    // Padrão Singleton para garantir apenas uma instância da conexão
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Estabelece a conexão com o banco de dados
    private function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->conn->connect_error) {
            die("Falha na conexão: " . $this->conn->connect_error);
        }
        $this->conn->set_charset("utf8");
    }

    // Executa uma consulta SQL
    public function query($sql) {
        return $this->conn->query($sql);
    }

    // Prepara uma declaração SQL
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    // Escapa strings para evitar injeção SQL
    public function escape($string) {
        return $this->conn->real_escape_string($string);
    }

    // Obtém o ID do último registro inserido
    public function lastInsertId() {
        return $this->conn->insert_id;
    }

    // Fecha a conexão com o banco de dados
    public function close() {
        $this->conn->close();
    }
}
