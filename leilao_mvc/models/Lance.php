<?php
// Modelo de lance
// models/Lance.php

class Lance extends Model {
    protected $table = 'lances';
    protected $primaryKey = 'id';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Obtém lances para um produto específico
    public function getByProductId($produto_id) {
        $sql = "SELECT l.*, u.username 
                FROM {$this->table} l 
                JOIN usuarios u ON l.usuario_id = u.id 
                WHERE l.produto_id = ? 
                ORDER BY l.data_lance DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Obtém o maior lance para um produto
    public function getHighestBid($produto_id) {
        $sql = "SELECT MAX(valor_lance) as maior_lance FROM {$this->table} WHERE produto_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['maior_lance'];
        }
        
        return 0;
    }
    
    // Verifica se um lance é válido (maior que o lance atual)
    public function isValidBid($produto_id, $valor_lance) {
        $maior_lance = $this->getHighestBid($produto_id);
        return $valor_lance > $maior_lance;
    }
}
