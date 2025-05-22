<?php
/**
 * Arquivo de configuração global
 * Sistema de Leilão
 */

// Definição de constantes do sistema
define('BASE_URL', 'http://localhost/leilao');
define('ROOT_PATH', dirname(__FILE__));

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Alterar para 1 em produção com HTTPS

// Configurações de timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de exibição de erros (desativar em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Funções globais
function redirect($url) {
    header("Location: $url");
    exit();
}

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Carregamento automático de classes
spl_autoload_register(function($class_name) {
    $directories = [
        'models/',
        'controllers/',
        'utils/',
        'config/'
    ];
    
    foreach ($directories as $directory) {
        $file = ROOT_PATH . '/' . $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once($file);
            return;
        }
    }
});
?>
