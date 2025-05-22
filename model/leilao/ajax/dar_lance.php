<?php
/**
 * Arquivo para processamento AJAX de lances
 * Sistema de Leilão
 */

// Incluir configurações
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../utils/Session.php';

// Inicializar sessão
$sessionManager = new Session();

// Verificar se o usuário está logado
if (!$sessionManager->isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Usuário não autenticado']);
    exit;
}

// Inicializar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Obter dados do lance
$produto_id = isset($_POST['produto_id']) ? (int)$_POST['produto_id'] : 0;
$valor = isset($_POST['valor']) ? (float)$_POST['valor'] : 0;
$user_id = $sessionManager->getUserId();

if ($produto_id <= 0 || $valor <= 0) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Verificar valor atual do produto
$query = "SELECT MAX(VALOR_LANCE) as valor_atual FROM lance WHERE PK_PRODUTO = :produto_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':produto_id', $produto_id);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);

$valor_atual = $resultado['valor_atual'] ?? 0;

if ($valor <= $valor_atual) {
    echo json_encode(['success' => false, 'message' => 'O valor do lance deve ser maior que o valor atual']);
    exit;
}

// Registrar lance
$query = "INSERT INTO lance (PK_USUARIO, PK_PRODUTO, VALOR_LANCE, DATA_HORA) 
         VALUES (:user_id, :produto_id, :valor, NOW())";

$stmt = $db->prepare($query);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':produto_id', $produto_id);
$stmt->bindParam(':valor', $valor);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Lance registrado com sucesso',
        'novo_valor' => number_format($valor, 2, ',', '.')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao registrar lance']);
}
