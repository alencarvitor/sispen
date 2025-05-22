<?php
/**
 * Classe para autenticação de usuários
 * Sistema de Leilão
 */

class Auth {
    private $db;
    private $session;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->session = new Session();
    }
    
    /**
     * Realiza o login do usuário
     * @param string $cpf CPF do usuário
     * @param string $senha Senha do usuário
     * @return bool|array Dados do usuário em caso de sucesso, false em caso de falha
     */
    public function login($cpf, $senha) {
        try {
            $query = "SELECT u.*, t.TIPO as tipo_nome 
                     FROM usuario u
                     JOIN tipo_usuario t ON u.PK_TIPO_USUARIO = t.ID_TIPO_USER
                     WHERE u.CPF = :cpf";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Verificar senha (em um sistema real, usar password_verify)
                if ($senha === $user['SENHA']) {
                    // Preparar dados do usuário para a sessão
                    $user_data = [
                        'id' => $user['ID_USER'],
                        'nome' => $user['NOME_USER'],
                        'sobrenome' => $user['SOBRENOME_USER'],
                        'tipo' => $user['tipo_nome'],
                        'tipo_id' => $user['PK_TIPO_USUARIO']
                    ];
                    
                    // Criar sessão
                    $this->session->create($user['ID_USER'], $user_data);
                    
                    return $user_data;
                }
            }
            
            return false;
        } catch (Exception $e) {
            error_log("Erro no login: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Realiza o logout do usuário
     * @return bool Resultado da operação
     */
    public function logout() {
        return $this->session->destroy();
    }
    
    /**
     * Registra um novo usuário
     * @param array $user_data Dados do usuário
     * @return bool|int ID do usuário em caso de sucesso, false em caso de falha
     */
    public function register($user_data) {
        try {
            // Verificar se o CPF já está cadastrado
            $query = "SELECT ID_USER FROM usuario WHERE CPF = :cpf";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cpf', $user_data['cpf']);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                return false; // CPF já cadastrado
            }
            
            // Inserir novo usuário
            $query = "INSERT INTO usuario (PK_TIPO_USUARIO, NOME_USER, SOBRENOME_USER, CPF, TELEFONE, ENDERECO, SENHA) 
                     VALUES (:tipo_usuario, :nome, :sobrenome, :cpf, :telefone, :endereco, :senha)";
            
            $stmt = $this->db->prepare($query);
            
            // Por padrão, novos usuários são do tipo "comprador" (ID 3)
            $tipo_usuario = 3; // Comprador
            
            $stmt->bindParam(':tipo_usuario', $tipo_usuario);
            $stmt->bindParam(':nome', $user_data['nome']);
            $stmt->bindParam(':sobrenome', $user_data['sobrenome']);
            $stmt->bindParam(':cpf', $user_data['cpf']);
            $stmt->bindParam(':telefone', $user_data['telefone']);
            $stmt->bindParam(':endereco', $user_data['endereco']);
            $stmt->bindParam(':senha', $user_data['senha']);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Erro no registro: " . $e->getMessage());
            echo "Erro detalhado: " . $e->getMessage();

            return false;

        }
    }
    
    /**
     * Recupera a senha do usuário
     * @param string $cpf CPF do usuário
     * @param string $nova_senha Nova senha
     * @return bool Resultado da operação
     */
    public function recuperarSenha($cpf, $nova_senha) {
        try {
            $query = "UPDATE usuario SET SENHA = :senha WHERE CPF = :cpf";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':senha', $nova_senha);
            $stmt->bindParam(':cpf', $cpf);
            
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Erro na recuperação de senha: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verifica se o CPF existe no sistema
     * @param string $cpf CPF a verificar
     * @return bool Resultado da verificação
     */
    public function cpfExiste($cpf) {
        try {
            $query = "SELECT ID_USER FROM usuario WHERE CPF = :cpf";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cpf', $cpf);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Erro ao verificar CPF: " . $e->getMessage());
            return false;
        }
    }
}
?>
