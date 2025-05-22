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

// Obter ID do produto
$produto_id = isset($_GET['produto_id']) ? (int)$_GET['produto_id'] : 0;

if ($produto_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID do produto inválido']);
    exit;
}

// Buscar lances do produto
$query = "SELECT l.ID_LANCE, u.NOME_USER, u.SOBRENOME_USER, l.VALOR_LANCE, l.DATA_HORA
         FROM lance l
         JOIN usuario u ON l.PK_USUARIO = u.ID_USER
         WHERE l.PK_PRODUTO = :produto_id
         ORDER BY l.VALOR_LANCE DESC
         LIMIT 10";

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

// Obter valor atual do produto
$valor_atual = 0;
if (count($lances) > 0) {
    $valor_atual = number_format($lances[0]['VALOR_LANCE'], 2, ',', '.');
}

// Retornar resposta
echo json_encode([
    'success' => true,
    'lances' => $lances_formatados,
    'valor_atual' => $valor_atual
]);
