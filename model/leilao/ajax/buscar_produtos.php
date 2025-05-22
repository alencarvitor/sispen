<?php
/**
 * Arquivo para processamento AJAX de busca de produtos
 * Sistema de Leilão
 */

// Incluir configurações
require_once '../config/config.php';
require_once '../config/database.php';

// Inicializar conexão com o banco de dados
$database = new Database();
$db = $database->getConnection();

// Obter termo de busca
$termo = isset($_GET['termo']) ? sanitize($_GET['termo']) : '';

// Buscar produtos
$query = "SELECT p.ID_PRODUTO, p.NOME_PRODUTO, p.NOME_DOADOR, p.IMAGEM, 
         (SELECT MAX(l.VALOR_LANCE) FROM lance l WHERE l.PK_PRODUTO = p.ID_PRODUTO) as VALOR_ATUAL
         FROM produtos p
         WHERE p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido)
         AND p.ID_PRODUTO NOT IN (SELECT PK_PRODUTO FROM produto_vendido_lance_direto)
         AND (p.NOME_PRODUTO LIKE :termo OR p.NOME_DOADOR LIKE :termo)
         ORDER BY p.ID_PRODUTO DESC";

$termo_busca = "%$termo%";
$stmt = $db->prepare($query);
$stmt->bindParam(':termo', $termo_busca);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Formatar produtos para resposta JSON
$produtos_formatados = [];
foreach ($produtos as $produto) {
    $produtos_formatados[] = [
        'id' => $produto['ID_PRODUTO'],
        'nome' => $produto['NOME_PRODUTO'],
        'doador' => $produto['NOME_DOADOR'],
        'imagem' => !empty($produto['IMAGEM']) ? $produto['IMAGEM'] : 'assets/img/no-image.jpg',
        'valor_atual' => number_format($produto['VALOR_ATUAL'] ?? 0, 2, ',', '.')
    ];
}

// Retornar resposta
echo json_encode([
    'success' => true,
    'produtos' => $produtos_formatados
]);
