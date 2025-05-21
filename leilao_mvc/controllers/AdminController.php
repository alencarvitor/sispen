<?php
// Controlador de administração
// controllers/AdminController.php

class AdminController extends Controller {
    private $produtoModel;
    private $leilaoModel;
    private $usuarioModel;
    
    public function __construct() {
        $this->produtoModel = new Produto();
        $this->leilaoModel = new Leilao();
        $this->usuarioModel = new Usuario();
    }
    
    // Método para exibir a página inicial da administração
    public function index() {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém estatísticas para o dashboard
        $totalProdutos = count($this->produtoModel->getAll());
        $totalLeiloes = count($this->leilaoModel->getAll());
        $totalUsuarios = count($this->usuarioModel->getAll());
        
        // Obtém produtos para listagem
        $produtos = $this->produtoModel->getAll();
        
        // Carrega a view do painel administrativo
        $this->view('admin/index', [
            'title' => 'Administração - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'admin',
            'totalProdutos' => $totalProdutos,
            'totalLeiloes' => $totalLeiloes,
            'totalUsuarios' => $totalUsuarios,
            'produtos' => $produtos
        ]);
    }
    
    // Método para exibir a página de gerenciamento de usuários
    public function usuarios() {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém todos os usuários
        $usuarios = $this->usuarioModel->getAll();
        
        // Carrega a view de gerenciamento de usuários
        $this->view('admin/usuarios', [
            'title' => 'Gerenciar Usuários - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'admin',
            'usuarios' => $usuarios
        ]);
    }
    
    // Método para exibir a página de gerenciamento de produtos
    public function produtos() {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém todos os produtos
        $produtos = $this->produtoModel->getAll();
        
        // Carrega a view de gerenciamento de produtos
        $this->view('admin/produtos', [
            'title' => 'Gerenciar Produtos - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'admin',
            'produtos' => $produtos
        ]);
    }
    
    // Método para exibir a página de gerenciamento de leilões
    public function leiloes() {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém todos os leilões
        $leiloes = $this->leilaoModel->getAll();
        
        // Obtém todos os produtos disponíveis para leilão
        $produtos = $this->produtoModel->getAvailableForAuction();
        
        // Carrega a view de gerenciamento de leilões
        $this->view('admin/leiloes', [
            'title' => 'Gerenciar Leilões - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'admin',
            'leiloes' => $leiloes,
            'produtos' => $produtos
        ]);
    }
    
    // Método para adicionar um novo produto
    public function adicionar_produto() {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Se o formulário foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome_produto = $_POST['nome_produto'] ?? '';
            $nome_doador = $_POST['nome_doador'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $valor_produto = $_POST['valor_produto'] ?? null;
            
            // Validação básica
            if (empty($nome_produto) || empty($nome_doador)) {
                $this->view('admin/adicionar_produto', [
                    'title' => 'Adicionar Produto - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'admin',
                    'error' => 'Por favor, preencha todos os campos obrigatórios.'
                ]);
                return;
            }
            
            // Processa o upload da imagem, se houver
            $imagem = null;
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads/';
                $fileExtension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $uniqueName = uniqid() . '_' . $_FILES['imagem']['name'];
                $uploadFile = $uploadDir . $uniqueName;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
                    $imagem = $uniqueName;
                }
            }
            
            // Cria o produto
            $produtoData = [
                'nome_produto' => $nome_produto,
                'nome_doador' => $nome_doador,
                'descricao' => $descricao,
                'valor_produto' => $valor_produto,
                'imagem' => $imagem,
                'em_leilao' => 0
            ];
            
            $produtoId = $this->produtoModel->create($produtoData);
            
            if ($produtoId) {
                $this->redirect('/admin/produtos');
            } else {
                $this->view('admin/adicionar_produto', [
                    'title' => 'Adicionar Produto - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'admin',
                    'error' => 'Erro ao adicionar produto. Por favor, tente novamente.'
                ]);
            }
        } else {
            // Exibe o formulário para adicionar produto
            $this->view('admin/adicionar_produto', [
                'title' => 'Adicionar Produto - Sistema de Leilão Beneficente São João Batista',
                'active_page' => 'admin'
            ]);
        }
    }
    
    // Método para editar um produto existente
    public function editar_produto($id) {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém o produto pelo ID
        $produto = $this->produtoModel->getById($id);
        
        if (!$produto) {
            $this->redirect('/admin/produtos');
            return;
        }
        
        // Se o formulário foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome_produto = $_POST['nome_produto'] ?? '';
            $nome_doador = $_POST['nome_doador'] ?? '';
            $descricao = $_POST['descricao'] ?? '';
            $valor_produto = $_POST['valor_produto'] ?? null;
            
            // Validação básica
            if (empty($nome_produto) || empty($nome_doador)) {
                $this->view('admin/editar_produto', [
                    'title' => 'Editar Produto - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'admin',
                    'error' => 'Por favor, preencha todos os campos obrigatórios.',
                    'produto' => $produto
                ]);
                return;
            }
            
            // Processa o upload da imagem, se houver
            $imagem = $produto['imagem'];
            if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../public/uploads/';
                $fileExtension = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
                $uniqueName = uniqid() . '_' . $_FILES['imagem']['name'];
                $uploadFile = $uploadDir . $uniqueName;
                
                if (move_uploaded_file($_FILES['imagem']['tmp_name'], $uploadFile)) {
                    // Remove a imagem antiga, se existir
                    if ($produto['imagem'] && file_exists($uploadDir . $produto['imagem'])) {
                        unlink($uploadDir . $produto['imagem']);
                    }
                    
                    $imagem = $uniqueName;
                }
            }
            
            // Atualiza o produto
            $produtoData = [
                'nome_produto' => $nome_produto,
                'nome_doador' => $nome_doador,
                'descricao' => $descricao,
                'valor_produto' => $valor_produto,
                'imagem' => $imagem
            ];
            
            $success = $this->produtoModel->update($id, $produtoData);
            
            if ($success) {
                $this->redirect('/admin/produtos');
            } else {
                $this->view('admin/editar_produto', [
                    'title' => 'Editar Produto - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'admin',
                    'error' => 'Erro ao atualizar produto. Por favor, tente novamente.',
                    'produto' => $produto
                ]);
            }
        } else {
            // Exibe o formulário para editar produto
            $this->view('admin/editar_produto', [
                'title' => 'Editar Produto - Sistema de Leilão Beneficente São João Batista',
                'active_page' => 'admin',
                'produto' => $produto
            ]);
        }
    }
    
    // Método para excluir um produto
    public function excluir_produto($id) {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém o produto pelo ID
        $produto = $this->produtoModel->getById($id);
        
        if (!$produto) {
            $this->redirect('/admin/produtos');
            return;
        }
        
        // Verifica se o produto está em leilão
        if ($produto['em_leilao']) {
            $this->redirect('/admin/produtos');
            return;
        }
        
        // Exclui o produto
        $success = $this->produtoModel->delete($id);
        
        // Redireciona para a lista de produtos
        $this->redirect('/admin/produtos');
    }
    
    // Método para iniciar um leilão
    public function iniciar_leilao() {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Se o formulário foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $produto_id = $_POST['produto_id'] ?? null;
            
            // Validação básica
            if (empty($produto_id)) {
                $this->redirect('/admin/leiloes');
                return;
            }
            
            // Verifica se o produto existe e está disponível
            $produto = $this->produtoModel->getById($produto_id);
            
            if (!$produto || $produto['em_leilao']) {
                $this->redirect('/admin/leiloes');
                return;
            }
            
            // Cria o leilão
            $leilaoData = [
                'produto_id' => $produto_id,
                'status' => 'em_andamento'
            ];
            
            $leilaoId = $this->leilaoModel->create($leilaoData);
            
            if ($leilaoId) {
                // Atualiza o status do produto
                $this->produtoModel->update($produto_id, ['em_leilao' => 1]);
                
                $this->redirect('/admin/leiloes');
            } else {
                $this->redirect('/admin/leiloes');
            }
        } else {
            // Redireciona para a lista de leilões
            $this->redirect('/admin/leiloes');
        }
    }
    
    // Método para pausar ou retomar um leilão
    public function alterar_status_leilao($id, $status) {
        // Verifica se o usuário tem permissão de administrador
        if (!$this->checkPermission('admin')) {
            return;
        }
        
        // Obtém o leilão pelo ID
        $leilao = $this->leilaoModel->getById($id);
        
        if (!$leilao) {
            $this->redirect('/admin/leiloes');
            return;
        }
        
        // Verifica se o status é válido
        if ($status !== 'em_andamento' && $status !== 'pausado' && $status !== 'finalizado') {
            $this->redirect('/admin/leiloes');
            return;
        }
        
        // Atualiza o status do leilão
        $success = $this->leilaoModel->update($id, ['status' => $status]);
        
        // Se o leilão foi finalizado, atualiza o status do produto
        if ($status === 'finalizado') {
            $this->produtoModel->update($leilao['produto_id'], ['em_leilao' => 0]);
        }
        
        // Redireciona para a lista de leilões
        $this->redirect('/admin/leiloes');
    }
}
