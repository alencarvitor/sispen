<?php
// Classe principal da aplicação
// core/App.php

class App {
    private $router;
    
    public function __construct() {
        // Inicia a sessão
        session_start();
        
        // Carrega as configurações
        $config = require_once __DIR__ . '/../config/config.php';
        
        // Define o fuso horário
        date_default_timezone_set($config['timezone']);
        
        // Cria o roteador
        $this->router = new Router();
        
        // Registra as rotas padrão
        $this->registerRoutes();
    }
    
    // Registra as rotas da aplicação
    private function registerRoutes() {
        // Rotas específicas podem ser adicionadas aqui
        $this->router->add('', 'Home', 'index');
        $this->router->add('login', 'Auth', 'login');
        $this->router->add('logout', 'Auth', 'logout');
        $this->router->add('register', 'Auth', 'register');
        $this->router->add('admin', 'Admin', 'index');
        $this->router->add('leilao', 'Leilao', 'index');
        $this->router->add('lance', 'Lance', 'index');
    }
    
    // Inicia a aplicação
    public function run() {
        // Despacha a requisição para o controlador apropriado
        $this->router->dispatch();
    }
}
