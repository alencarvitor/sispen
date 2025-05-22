<?php
/**
 * Classe para gerenciamento de sessões
 * Sistema de Leilão
 */

class Session {
    private $db;
    private $user_id;
    private $token;
    private $session_duration = 86400; // 24 horas em segundos
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        
     if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
    }
    
    /**
     * Inicia uma nova sessão para o usuário
     * @param int $user_id ID do usuário
     * @param array $user_data Dados do usuário para armazenar na sessão
     * @return bool Resultado da operação
     */
    public function create($user_id, $user_data) {
        try {
            // Gerar token único
            $this->token = bin2hex(random_bytes(32));
            $this->user_id = $user_id;
            
            // Armazenar dados na sessão PHP
            $_SESSION['user_id'] = $user_id;
            $_SESSION['token'] = $this->token;
            $_SESSION['user_data'] = $user_data;
            $_SESSION['logged_in'] = true;
            
            // Registrar sessão no banco de dados
            $expiration_date = date('Y-m-d H:i:s', time() + $this->session_duration);
            $ip = $_SERVER['REMOTE_ADDR'];
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
            
            $query = "INSERT INTO sessoes (PK_USER, TOKEN, DATA_EXPIRACAO, IP, USER_AGENT) 
                     VALUES (:user_id, :token, :expiration, :ip, :user_agent)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':token', $this->token);
            $stmt->bindParam(':expiration', $expiration_date);
            $stmt->bindParam(':ip', $ip);
            $stmt->bindParam(':user_agent', $user_agent);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erro ao criar sessão: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se o usuário está logado
     * @return bool Status do login
     */
    public function isLoggedIn() {
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            return $this->validateSession();
        }
        return false;
    }
    
    /**
     * Valida a sessão atual
     * @return bool Resultado da validação
     */
    private function validateSession() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['token'])) {
            return false;
        }
        
        try {
            $query = "SELECT * FROM sessoes 
                     WHERE PK_USER = :user_id 
                     AND TOKEN = :token 
                     AND ATIVA = 1 
                     AND DATA_EXPIRACAO > NOW()";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->bindParam(':token', $_SESSION['token']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                // Atualizar data de expiração
                $this->refreshSession();
                return true;
            } else {
                $this->destroy();
                return false;
            }
        } catch (Exception $e) {
            error_log("Erro ao validar sessão: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Atualiza o tempo de expiração da sessão
     */
    private function refreshSession() {
        $expiration_date = date('Y-m-d H:i:s', time() + $this->session_duration);
        
        $query = "UPDATE sessoes 
                 SET DATA_EXPIRACAO = :expiration 
                 WHERE PK_USER = :user_id 
                 AND TOKEN = :token";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':expiration', $expiration_date);
        $stmt->bindParam(':user_id', $_SESSION['user_id']);
        $stmt->bindParam(':token', $_SESSION['token']);
        $stmt->execute();
    }
    
    /**
     * Encerra a sessão atual
     * @return bool Resultado da operação
     */
    public function destroy() {
        try {
            if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
                $query = "UPDATE sessoes 
                         SET ATIVA = 0 
                         WHERE PK_USER = :user_id 
                         AND TOKEN = :token";
                
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->bindParam(':token', $_SESSION['token']);
                $stmt->execute();
            }
            
            // Limpar variáveis de sessão
            $_SESSION = array();
            
            // Destruir cookie de sessão
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            // Destruir sessão
            session_destroy();
            
            return true;
        } catch (Exception $e) {
            error_log("Erro ao destruir sessão: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtém dados do usuário da sessão atual
     * @return array|null Dados do usuário ou null se não estiver logado
     */
    public function getUserData() {
        if ($this->isLoggedIn() && isset($_SESSION['user_data'])) {
            return $_SESSION['user_data'];
        }
        return null;
    }
    
    /**
     * Obtém o ID do usuário da sessão atual
     * @return int|null ID do usuário ou null se não estiver logado
     */
    public function getUserId() {
        if ($this->isLoggedIn() && isset($_SESSION['user_id'])) {
            return $_SESSION['user_id'];
        }
        return null;
    }
    
    /**
     * Verifica se o usuário tem permissão para acessar determinada área
     * @param array $allowed_types Tipos de usuário permitidos
     * @return bool Resultado da verificação
     */
    public function hasPermission($allowed_types) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $user_data = $this->getUserData();
        if (!isset($user_data['tipo'])) {
            return false;
        }
        
        return in_array($user_data['tipo'], $allowed_types);
    }
}
?>
