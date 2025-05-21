<?php
// Controlador de leilão
// controllers/LeilaoController.php

class LeilaoController extends Controller {
    private $leilaoModel;
    private $produtoModel;
    private $lanceModel;
    
    public function __construct() {
        $this->leilaoModel = new Leilao();
        $this->produtoModel = new Produto();
        $this->lanceModel = new Lance();
    }
    
    // Método para exibir a página inicial de leilões
    public function index() {
        // Verifica se o usuário tem permissão de leiloeiro ou admin
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Obtém leilões ativos
        $leiloes = $this->leilaoModel->getActive();
        
        // Para cada leilão, obtém o maior lance
        foreach ($leiloes as &$leilao) {
            $leilao['maior_lance'] = $this->lanceModel->getHighestBid($leilao['produto_id']);
        }
        
        // Carrega a view de leilões
        $this->view('leilao/index', [
            'title' => 'Leilões - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'leilao',
            'leiloes' => $leiloes
        ]);
    }
    
    // Método para exibir detalhes de um leilão
    public function detalhes($id) {
        // Verifica se o usuário está logado
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Obtém o leilão pelo ID
        $leilao = $this->leilaoModel->getWithProductDetails($id);
        
        if (!$leilao) {
            $this->redirect('/leilao');
            return;
        }
        
        // Obtém os lances para o produto
        $lances = $this->lanceModel->getByProductId($leilao['produto_id']);
        
        // Obtém o maior lance
        $maior_lance = $this->lanceModel->getHighestBid($leilao['produto_id']);
        
        // Carrega a view de detalhes do leilão
        $this->view('leilao/detalhes', [
            'title' => $leilao['nome_produto'] . ' - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'leilao',
            'leilao' => $leilao,
            'lances' => $lances,
            'maior_lance' => $maior_lance
        ]);
    }
    
    // Método para gerenciar um leilão (apenas para leiloeiros e admins)
    public function gerenciar($id) {
        // Verifica se o usuário tem permissão de leiloeiro ou admin
        if (!$this->checkPermission('leiloeiro')) {
            return;
        }
        
        // Obtém o leilão pelo ID
        $leilao = $this->leilaoModel->getWithProductDetails($id);
        
        if (!$leilao) {
            $this->redirect('/leilao');
            return;
        }
        
        // Obtém os lances para o produto
        $lances = $this->lanceModel->getByProductId($leilao['produto_id']);
        
        // Obtém o maior lance
        $maior_lance = $this->lanceModel->getHighestBid($leilao['produto_id']);
        
        // Carrega a view de gerenciamento do leilão
        $this->view('leilao/gerenciar', [
            'title' => 'Gerenciar ' . $leilao['nome_produto'] . ' - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'leilao',
            'leilao' => $leilao,
            'lances' => $lances,
            'maior_lance' => $maior_lance
        ]);
    }
    
    // Método para alterar o status de um leilão (apenas para leiloeiros e admins)
    public function alterar_status($id, $status) {
        // Verifica se o usuário tem permissão de leiloeiro ou admin
        if (!$this->checkPermission('leiloeiro')) {
            return;
        }
        
        // Obtém o leilão pelo ID
        $leilao = $this->leilaoModel->getById($id);
        
        if (!$leilao) {
            $this->redirect('/leilao');
            return;
        }
        
        // Verifica se o status é válido
        if ($status !== 'em_andamento' && $status !== 'pausado' && $status !== 'finalizado') {
            $this->redirect('/leilao/gerenciar/' . $id);
            return;
        }
        
        // Atualiza o status do leilão
        $success = $this->leilaoModel->update($id, ['status' => $status]);
        
        // Se o leilão foi finalizado, atualiza o status do produto
        if ($status === 'finalizado') {
            $this->produtoModel->update($leilao['produto_id'], ['em_leilao' => 0]);
        }
        
        // Redireciona para a página de gerenciamento do leilão
        $this->redirect('/leilao/gerenciar/' . $id);
    }
}
