<?php
require_once "db.php";

$nome = $_POST['nome'] ?? '';
$descricao = $_POST['descricao'] ?? '';
$valor = $_POST['valor'] ?? '';

if (empty($nome) || empty($descricao) || empty($valor)) {
    echo "Todos os campos são obrigatórios.";
    exit;
}

$sql = "INSERT INTO produtos (nome, descricao, valor) VALUES (?, ?, ?)";

if ($stmt = $conexao->prepare($sql)) {
    $stmt->bind_param("ssd", $nome, $descricao, $valor);
    if ($stmt->execute()) {
        echo "Produto cadastrado com sucesso!";
    } else {
        echo "Erro na execução: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "Erro na preparação da query: " . $conexao->error;
}
?>
