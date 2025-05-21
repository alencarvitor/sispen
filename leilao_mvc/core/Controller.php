<?php
// Classe base para todos os controladores
// core/Controller.php

class Controller {
    protected $viewData = [];
    
    // Carrega uma view com os dados fornecidos
    protected function view($view, $data = []) {
        // Mescla os dados existentes com os novos
        $this->viewData = array_merge($this->viewData, $data);
        
        // Extrai os dados para que fiquem disponíveis como variáveis na view
        extract($this->viewData);
        
        // Caminho para o arquivo de template principal
        $templatePath = __DIR__ . '/../views/templates/main.php';
        
        // Caminho para o arquivo de view específico
        $viewPath = __DIR__ . '/../views/' . $view . '.php';
        
        // Verifica se o arquivo de view existe
        if (!file_exists($viewPath)) {
            die("View não encontrada: " . $view);
        }
        
        // Inicia o buffer de saída
        ob_start();
        
        // Inclui o arquivo de view
        include $viewPath;
        
        // Captura o conteúdo do buffer
        $content = ob_get_clean();
        
        // Verifica se existe um template principal
        if (file_exists($templatePath)) {
            // Inclui o template principal, que terá acesso à variável $content
            include $templatePath;
        } else {
            // Se não houver template, exibe diretamente o conteúdo
            echo $content;
        }
    }
    
    // Redireciona para outra URL
    protected function redirect($url) {
        header("Location: " . $url);
        exit;
    }
    
    // Verifica se o usuário está logado
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    // Verifica se o usuário tem permissão para acessar
    protected function checkPermission($requiredType) {
        if (!$this->isLoggedIn()) {
            $this->redirect('auth/login');
            return false;
        }
        
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != $requiredType && $_SESSION['user_type'] != 'admin') {
            $this->redirect('home/acesso_negado');
            return false;
        }
        
        return true;
    }
}
