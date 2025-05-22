<?php
/**
 * Arquivo para processamento AJAX de gerenciamento de itens
 * Sistema de Leilão
 */

// Incluir configurações
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../utils/Session.php';

// Inicializar sessão
$sessionManager = new Session();

// Verificar se o usuário está logado e tem permissão
if (!$sessionManager->isLoggedIn() || !$sessionManager->hasPermission(['administrador', 'leiloeiro'])) {
    echo json_encode(['success' => false, 'message' => 'Acesso não autorizado']);
    exit;
}

// Inicializar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Obter dados da requisição
$produto_id = isset($_POST['produto_id']) ? (int)$_POST['produto_id'] : 0;
$acao = isset($_POST['acao']) ? sanitize($_POST['acao']) : '';
$lance_id = isset($_POST['lance_id']) ? (int)$_POST['lance_id'] : 0;

if ($produto_id <= 0 || empty($acao)) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Processar ação
switch ($acao) {
    case 'retirar':
        // Excluir lances do produto
        $query = "DELETE FROM lance WHERE PK_PRODUTO = :produto_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':produto_id', $produto_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item retirado do leilão com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao retirar item do leilão']);
        }
        break;
        
    case 'vendido':
        // Buscar lance mais alto
        $query = "SELECT l.PK_USUARIO, l.VALOR_LANCE 
                 FROM lance l 
                 WHERE l.PK_PRODUTO = :produto_id 
                 ORDER BY l.VALOR_LANCE DESC 
                 LIMIT 1";
        
        $stmt = $db->prepare($query);
        $stmt->bindParam(':produto_id', $produto_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $lance = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Registrar venda
            $query = "INSERT INTO produto_vendido (PK_USER, PK_PRODUTO, VALOR_LANCE, DATA_HORA) 
                     VALUES (:user_id, :produto_id, :valor_lance, NOW())";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':user_id', $lance['PK_USUARIO']);
            $stmt->bindParam(':produto_id', $produto_id);
            $stmt->bindParam(':valor_lance', $lance['VALOR_LANCE']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Item marcado como vendido com sucesso']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao marcar item como vendido']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Não há lances para este item']);
        }
        break;
        
    case 'apagar_lance':
        if ($lance_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID do lance inválido']);
            exit;
        }
        
        $query = "DELETE FROM lance WHERE ID_LANCE = :lance_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':lance_id', $lance_id);
        
        if ($stmt->execute()) {
            // Buscar lances atualizados
            $query = "SELECT l.ID_LANCE, u.NOME_USER, u.SOBRENOME_USER, l.VALOR_LANCE, l.DATA_HORA
                     FROM lance l
                     JOIN usuario u ON l.PK_USUARIO = u.ID_USER
                     WHERE l.PK_PRODUTO = :produto_id
                     ORDER BY l.VALOR_LANCE DESC
                     LIMIT 5";
            
            $stmt = $db->prepare($query);
            $stmt->bindParam(':produto_id', $produto_id);
            $stmt->execute();
            $lances = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Formatar lances para resposta JSON
            $lances_formatados = [];
            foreach ($lances as $lance) {
                $lances_formatados[] = [
                    'id' => $lance['ID_LANCE'],
                    'usuario' => $lance['NOME_USER'] . ' ' . $lance['SOBRENOME_USER'],
                    'valor' => number_format($lance['VALOR_LANCE'], 2, ',', '.'),
                    'data_hora' => date('d/m/Y H:i:s', strtotime($lance['DATA_HORA']))
                ];
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Lance apagado com sucesso',
                'lances' => $lances_formatados
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao apagar lance']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
        break;
}
