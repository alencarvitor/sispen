<?php
// Arquivo de entrada principal
// index.php

// Carrega os arquivos necessários
require_once 'core/Database.php';
require_once 'core/Model.php';
require_once 'core/Controller.php';
require_once 'core/Router.php';
require_once 'core/App.php';

// Carrega os modelos
require_once 'models/Usuario.php';
require_once 'models/Produto.php';
require_once 'models/Leilao.php';
require_once 'models/Lance.php';

// Inicia a aplicação
$app = new App();
$app->run();
