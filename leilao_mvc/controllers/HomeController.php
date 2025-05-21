<?php
// Controlador da página inicial
// controllers/HomeController.php

class HomeController extends Controller {
    
    // Método para exibir a página inicial
    public function index() {
        // Carrega a view da página inicial
        $this->view('home/index', [
            'title' => 'Início - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'home'
        ]);
    }
    
    // Método para exibir a página de acesso negado
    public function acesso_negado() {
        // Carrega a view de acesso negado
        $this->view('home/acesso_negado', [
            'title' => 'Acesso Negado - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'home'
        ]);
    }
}
