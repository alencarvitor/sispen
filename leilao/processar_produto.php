<?php
include 'session.php';
include 'db.php';

$nome_produto = $_POST['nome_produto'];
$nome_doador = $_POST['nome_doador'];
$valor_produto = !empty($_POST['valor_produto']) ? $_POST['valor_produto'] : null;
$valor_venda = !empty($_POST['valor_venda']) ? $_POST['valor_venda'] : null;

$imagem = null;
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $nome_arquivo = uniqid() . "_" . $_FILES['imagem']['name'];
    move_uploaded_file($_FILES['imagem']['tmp_name'], "uploads/" . $nome_arquivo);
    $imagem = $nome_arquivo;
}

$stmt = $conn->prepare("INSERT INTO produtos (nome_produto, nome_doador, imagem, valor_produto, valor_venda) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssdd", $nome_produto, $nome_doador, $imagem, $valor_produto, $valor_venda);

if ($stmt->execute()) {
    echo "Produto cadastrado com sucesso!";
} else {
    echo "Erro ao cadastrar produto: " . $stmt->error;
}

$stmt->close();
?>