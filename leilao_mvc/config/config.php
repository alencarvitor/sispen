<?php
// Arquivo de configuração geral
// config/config.php

return [
    'app_name' => 'Sistema de Leilão Beneficente São João Batista',
    'app_url' => 'http://localhost',
    'debug' => true,
    'timezone' => 'America/Sao_Paulo',
    'default_controller' => 'Home',
    'default_action' => 'index',
    'session_time' => 3600, // 1 hora
    'upload_path' => 'public/uploads/',
    'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif']
];
