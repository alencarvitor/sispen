<?php
session_start();
include 'db.php';

// Verificar se a sessão existe
if (!isset($_SESSION['user']) || !isset($_SESSION['session_token'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Buscar dados do usuário no banco
$stmt = $conn->prepare("SELECT id, username, session_token FROM usuarios WHERE username = ?");
$stmt->bind_param("s", $_SESSION['user']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && $user['session_token'] === $_SESSION['session_token']) {
    // Atualizar $_SESSION['user'] para conter todos os dados do usuário
    $_SESSION['user'] = [
        'id' => $user['id'],
        'username' => $user['username']
    ];
} else {
    // Token inválido ou usuário não encontrado
    session_destroy();
    header("Location: login.php");
    exit();
}
?>