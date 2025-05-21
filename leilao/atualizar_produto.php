<?php
include 'session.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("SELECT id, nome_produto, nome_doador, valor_produto, valor_venda FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nome_produto, $nome_doador, $valor_produto, $valor_venda);
        $stmt->fetch();
        header('Content-Type: application/json');
        echo json_encode([
            'id' => $id,
            'nome_produto' => $nome_produto,
            'nome_doador' => $nome_doador,
            'valor_produto' => $valor_produto,
            'valor_venda' => $valor_venda
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Produto não encontrado']);
    }

    $stmt->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nome_produto = $_POST['nome_produto'];
    $nome_doador = $_POST['nome_doador'];
    $valor_produto = !empty($_POST['valor_produto']) ? $_POST['valor_produto'] : null;
    $valor_venda = !empty($_POST['valor_venda']) ? $_POST['valor_venda'] : null;

    $imagem = null;
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $nome_arquivo = uniqid() . "_" . $_FILES['imagem']['name'];
        move_uploaded_file($_FILES['imagem']['tmp_name'], "Uploads/" . $nome_arquivo);
        $imagem = $nome_arquivo;
    }

    if ($imagem) {
        $stmt = $conn->prepare("UPDATE produtos SET nome_produto = ?, nome_doador = ?, imagem = ?, valor_produto = ?, valor_venda = ? WHERE id = ?");
        $stmt->bind_param("sssddi", $nome_produto, $nome_doador, $imagem, $valor_produto, $valor_venda, $id);
    } else {
        $stmt = $conn->prepare("UPDATE produtos SET nome_produto = ?, nome_doador = ?, valor_produto = ?, valor_venda = ? WHERE id = ?");
        $stmt->bind_param("ssddi", $nome_produto, $nome_doador, $valor_produto, $valor_venda, $id);
    }

    if ($stmt->execute()) {
        echo "Produto atualizado com sucesso!";
    } else {
        http_response_code(500);
        echo "Erro ao atualizar produto: " . $stmt->error;
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Requisição inválida.";
}
?>