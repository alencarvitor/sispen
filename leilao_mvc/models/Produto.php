<?php
// Modelo de produto
// models/Produto.php

class Produto extends Model {
    protected $table = 'produtos';
    protected $primaryKey = 'id';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Obtém produtos disponíveis para leilão
    public function getAvailableForAuction() {
        $sql = "SELECT * FROM {$this->table} WHERE em_leilao = 0";
        $result = $this->db->query($sql);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Obtém produtos em leilão
    public function getInAuction() {
        $sql = "SELECT * FROM {$this->table} WHERE em_leilao = 1";
        $result = $this->db->query($sql);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Obtém produtos com detalhes do leilão
    public function getWithAuctionDetails() {
        $sql = "SELECT p.*, l.id as leilao_id, l.status as leilao_status 
                FROM {$this->table} p 
                LEFT JOIN leilao l ON p.id = l.produto_id 
                WHERE p.em_leilao = 1";
        $result = $this->db->query($sql);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Obtém um produto com detalhes do leilão
    public function getWithAuctionDetailsById($id) {
        $sql = "SELECT p.*, l.id as leilao_id, l.status as leilao_status 
                FROM {$this->table} p 
                LEFT JOIN leilao l ON p.id = l.produto_id 
                WHERE p.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
}
