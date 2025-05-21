<?php
// Classe para roteamento de URLs
// core/Router.php

class Router {
    private $routes = [];
    private $defaultController;
    private $defaultAction;
    
public function __construct() {
    $config = require_once __DIR__ . '/../config/config.php';
    if (is_array($config)) {
        $this->defaultController = $config['default_controller'] ?? 'Home';
        $this->defaultAction = $config['default_action'] ?? 'index';
    } else {
        $this->defaultController = 'Home';
        $this->defaultAction = 'index';
    }
}
    
    // Adiciona uma rota
    public function add($route, $controller, $action) {
        $this->routes[$route] = [
            'controller' => $controller,
            'action' => $action
        ];
    }
    
    // Processa a URL atual e direciona para o controlador correto
    public function dispatch() {
        // Obtém a URL atual
        $url = isset($_GET['url']) ? $_GET['url'] : '';
        $url = rtrim($url, '/');
        
        // Se a URL estiver vazia, usa o controlador e ação padrão
        if (empty($url)) {
            $controllerName = $this->defaultController;
            $actionName = $this->defaultAction;
        } else {
            // Verifica se a URL corresponde a alguma rota definida
            if (isset($this->routes[$url])) {
                $controllerName = $this->routes[$url]['controller'];
                $actionName = $this->routes[$url]['action'];
            } else {
                // Divide a URL em segmentos
                $segments = explode('/', $url);
                
                // O primeiro segmento é o controlador
                $controllerName = ucfirst($segments[0]);
                
                // O segundo segmento é a ação (ou usa a ação padrão)
                $actionName = isset($segments[1]) ? $segments[1] : $this->defaultAction;
                
                // Os segmentos restantes são parâmetros
                $params = array_slice($segments, 2);
            }
        }
        
        // Caminho para o arquivo do controlador
        $controllerFile = __DIR__ . '/../controllers/' . $controllerName . 'Controller.php';
        
        // Verifica se o arquivo do controlador existe
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            
            // Nome completo da classe do controlador
            $controllerClass = $controllerName . 'Controller';
            
            // Instancia o controlador
            $controller = new $controllerClass();
            
            // Verifica se o método da ação existe
            if (method_exists($controller, $actionName)) {
                // Chama o método da ação com os parâmetros
                call_user_func_array([$controller, $actionName], isset($params) ? $params : []);
            } else {
                // Ação não encontrada
                $this->notFound();
            }
        } else {
            // Controlador não encontrado
            $this->notFound();
        }
    }
    
    // Manipula rotas não encontradas
    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        require_once __DIR__ . '/../views/shared/404.php';
        exit;
    }
}
