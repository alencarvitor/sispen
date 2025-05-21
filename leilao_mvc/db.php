<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$user = 'usb';
$pass = 'usbw';
$db = 'crud_bd';

// Cria a conexão
$conn = new mysqli($host, $user, $pass, $db);

// Verifica a conexão
if ($conn->connect_error) {
    error_log("Conexão falhou: " . $conn->connect_error);
    die("Erro de conexão com o banco de dados. Por favor, tente novamente mais tarde.");
}
?>