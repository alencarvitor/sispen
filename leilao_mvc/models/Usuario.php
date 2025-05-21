<?php
// Modelo de usuário
// models/Usuario.php

class Usuario extends Model {
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Autentica um usuário
    public function authenticate($username, $password) {
        $sql = "SELECT id, username, password, tipo FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifica se a senha está correta
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
    }
    
    // Verifica se um nome de usuário já existe
    public function usernameExists($username) {
        $sql = "SELECT id FROM {$this->table} WHERE username = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result && $result->num_rows > 0;
    }
    
    // Atualiza o token de sessão de um usuário
    public function updateSessionToken($userId, $token) {
        $sql = "UPDATE {$this->table} SET session_token = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $token, $userId);
        
        return $stmt->execute();
    }
    
    // Obtém um usuário pelo token de sessão
    public function getBySessionToken($token) {
        $sql = "SELECT * FROM {$this->table} WHERE session_token = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}
