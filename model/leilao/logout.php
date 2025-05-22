<?php
/**
 * Página de logout do Sistema de Leilão
 */

// Incluir configurações
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'utils/Session.php';
require_once 'utils/Auth.php';

// Inicializar autenticação
$auth = new Auth();

// Realizar logout
$auth->logout();

// Redirecionar para a página inicial
redirect('index.php');
?>
