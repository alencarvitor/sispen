<?php
// Modelo de leilão
// models/Leilao.php

class Leilao extends Model {
    protected $table = 'leilao';
    protected $primaryKey = 'id';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Obtém leilões ativos
    public function getActive() {
        $sql = "SELECT l.*, p.nome_produto, p.nome_doador, p.imagem, p.valor_produto, p.descricao 
                FROM {$this->table} l 
                JOIN produtos p ON l.produto_id = p.id 
                WHERE l.status = 'em_andamento'";
        $result = $this->db->query($sql);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Obtém um leilão com detalhes do produto
    public function getWithProductDetails($id) {
        $sql = "SELECT l.*, p.nome_produto, p.nome_doador, p.imagem, p.valor_produto, p.descricao 
                FROM {$this->table} l 
                JOIN produtos p ON l.produto_id = p.id 
                WHERE l.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Obtém o leilão ativo para um produto específico
    public function getActiveByProductId($produto_id) {
        $sql = "SELECT * FROM {$this->table} WHERE produto_id = ? AND status = 'em_andamento'";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}
