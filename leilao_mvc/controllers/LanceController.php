<?php
// Controlador de lances
// controllers/LanceController.php

class LanceController extends Controller {
    private $leilaoModel;
    private $produtoModel;
    private $lanceModel;
    
    public function __construct() {
        $this->leilaoModel = new Leilao();
        $this->produtoModel = new Produto();
        $this->lanceModel = new Lance();
    }
    
    // Método para exibir a página inicial de lances
    public function index() {
        // Verifica se o usuário está logado
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
        
        // Carrega a view de lances
        $this->view('lance/index', [
            'title' => 'Lances - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'lance',
            'leiloes' => $leiloes
        ]);
    }
    
    // Método para exibir detalhes de um produto para dar lance
    public function produto($id) {
        // Verifica se o usuário está logado
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Obtém o produto com detalhes do leilão
        $produto = $this->produtoModel->getWithAuctionDetailsById($id);
        
        if (!$produto || !$produto['em_leilao'] || $produto['leilao_status'] !== 'em_andamento') {
            $this->redirect('/lance');
            return;
        }
        
        // Obtém os lances para o produto
        $lances = $this->lanceModel->getByProductId($produto['id']);
        
        // Obtém o maior lance
        $maior_lance = $this->lanceModel->getHighestBid($produto['id']);
        
        // Carrega a view de detalhes do produto para lance
        $this->view('lance/produto', [
            'title' => $produto['nome_produto'] . ' - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'lance',
            'produto' => $produto,
            'lances' => $lances,
            'maior_lance' => $maior_lance
        ]);
    }
    
    // Método para dar um lance
    public function dar_lance($produto_id) {
        // Verifica se o usuário está logado
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Obtém o produto com detalhes do leilão
        $produto = $this->produtoModel->getWithAuctionDetailsById($produto_id);
        
        if (!$produto || !$produto['em_leilao'] || $produto['leilao_status'] !== 'em_andamento') {
            $this->redirect('/lance');
            return;
        }
        
        // Se o formulário foi enviado
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $valor_lance = $_POST['valor_lance'] ?? 0;
            
            // Validação básica
            if (empty($valor_lance) || !is_numeric($valor_lance)) {
                $this->redirect('/lance/produto/' . $produto_id);
                return;
            }
            
            // Verifica se o lance é válido (maior que o lance atual)
            if (!$this->lanceModel->isValidBid($produto_id, $valor_lance)) {
                $this->view('lance/produto', [
                    'title' => $produto['nome_produto'] . ' - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'lance',
                    'produto' => $produto,
                    'lances' => $this->lanceModel->getByProductId($produto_id),
                    'maior_lance' => $this->lanceModel->getHighestBid($produto_id),
                    'error' => 'O valor do lance deve ser maior que o lance atual.'
                ]);
                return;
            }
            
            // Cria o lance
            $lanceData = [
                'produto_id' => $produto_id,
                'usuario_id' => $_SESSION['user_id'],
                'valor_lance' => $valor_lance,
                'data_lance' => date('Y-m-d H:i:s')
            ];
            
            $lanceId = $this->lanceModel->create($lanceData);
            
            if ($lanceId) {
                $this->redirect('/lance/produto/' . $produto_id);
            } else {
                $this->view('lance/produto', [
                    'title' => $produto['nome_produto'] . ' - Sistema de Leilão Beneficente São João Batista',
                    'active_page' => 'lance',
                    'produto' => $produto,
                    'lances' => $this->lanceModel->getByProductId($produto_id),
                    'maior_lance' => $this->lanceModel->getHighestBid($produto_id),
                    'error' => 'Erro ao dar lance. Por favor, tente novamente.'
                ]);
            }
        } else {
            // Redireciona para a página do produto
            $this->redirect('/lance/produto/' . $produto_id);
        }
    }
    
    // Método para exibir histórico de lances do usuário
    public function meus_lances() {
        // Verifica se o usuário está logado
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
            return;
        }
        
        // Obtém os lances do usuário
        $sql = "SELECT l.*, p.nome_produto, p.imagem 
                FROM lances l 
                JOIN produtos p ON l.produto_id = p.id 
                WHERE l.usuario_id = ? 
                ORDER BY l.data_lance DESC";
        
        $stmt = $this->lanceModel->db->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $lances = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $lances[] = $row;
            }
        }
        
        // Carrega a view de histórico de lances
        $this->view('lance/meus_lances', [
            'title' => 'Meus Lances - Sistema de Leilão Beneficente São João Batista',
            'active_page' => 'lance',
            'lances' => $lances
        ]);
    }
}
