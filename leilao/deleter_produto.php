<?php
include 'session.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    $stmt = $conn->prepare("DELETE FROM produtos WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Produto deletado com sucesso!";
    } else {
        http_response_code(500);
        echo "Erro ao deletar produto: " . $stmt->error;
    }

    $stmt->close();
} else {
    http_response_code(400);
    echo "Requisição inválida.";
}
?>