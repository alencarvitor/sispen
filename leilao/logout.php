<?php
session_start();
include 'db.php';

if (isset($_SESSION['user'])) {
    // Limpa o token de sessão no banco
    $stmt = $conn->prepare("UPDATE usuarios SET session_token = NULL WHERE username = ?");
    $stmt->bind_param("s", $_SESSION['user']);
    $stmt->execute();
    $stmt->close();
}

session_destroy();
header("Location: login.php");
exit();
?>