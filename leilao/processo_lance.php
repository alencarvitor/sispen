<?php
include 'session.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método inválido']);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
    exit();
}

$produto_id = (int)($_POST['produto_id'] ?? 0);
$valor_lance = (float)($_POST['valor_lance'] ?? 0);

if ($produto_id <= 0 || $valor_lance <= 0) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit();
}

// Verificar status do leilão
$stmt = $conn->prepare("SELECT status FROM leilao WHERE id = 1");
$stmt->execute();
$status = $stmt->get_result()->fetch_assoc()['status'];
$stmt->close();

if ($status !== 'em_andamento') {
    echo json_encode(['success' => false, 'error' => 'Leilão não está em andamento']);
    exit();
}

// Verificar maior lance atual
$stmt = $conn->prepare("SELECT MAX(valor_lance) as maior_lance FROM lances WHERE produto_id = ?");
$stmt->bind_param("i", $produto_id);
$stmt->execute();
$maior_lance = $stmt->get_result()->fetch_assoc()['maior_lance'] ?? 0;
$stmt->close();

if ($valor_lance <= $maior_lance) {
    echo json_encode(['success' => false, 'error' => 'O lance deve ser maior que o atual (R$ ' . number_format($maior_lance, 2, ',', '.') . ')']);
    exit();
}

// Inserir novo lance
$stmt = $conn->prepare("INSERT INTO lances (produto_id, usuario_id, valor_lance, data_lance) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iid", $produto_id, $_SESSION['user_id'], $valor_lance);
$success = $stmt->execute();
$stmt->close();

echo json_encode([
    'success' => $success,
    'error' => $success ? '' : 'Erro ao registrar lance'
]);
?>