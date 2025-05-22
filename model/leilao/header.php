<?php
/**
 * Arquivo de cabeçalho para todas as páginas
 * Sistema de Leilão
 */

// Iniciar sessão e carregar configurações
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'utils/Session.php';
require_once 'utils/Auth.php';

// Inicializar sessão
$sessionManager = new Session();
$auth = new Auth();

// Verificar se o usuário está logado
$loggedIn = $sessionManager->isLoggedIn();
$userData = $sessionManager->getUserData();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Leilão</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">Sistema de Leilão</a>
                <ul class="nav-menu">
                    <li><a href="index.php">Início</a></li>
                    <?php if ($loggedIn): ?>
                        <?php if (isset($userData['tipo']) && ($userData['tipo'] == 'administrador' || $userData['tipo'] == 'leiloeiro')): ?>
                            <li><a href="gestao_itens.php">Gestão de Itens</a></li>
                        <?php endif; ?>
                        <li><a href="painel_lances.php">Painel de Lances</a></li>
                        <li><a href="logout.php">Sair</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="cadastro.php">Cadastro</a></li>
                    <?php endif; ?>
                </ul>
                <div class="hamburger">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>
            </nav>
        </div>
    </header>
    <main class="main-section">
        <div class="container">
