<?php
// Classe base para todos os modelos
// core/Model.php

class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // Busca todos os registros da tabela
    public function getAll() {
        $sql = "SELECT * FROM {$this->table}";
        $result = $this->db->query($sql);
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        
        return $items;
    }
    
    // Busca um registro pelo ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        
        return null;
    }
    
    // Cria um novo registro
    public function create($data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        
        $types = "";
        $values = [];
        
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= "i";
            } elseif (is_float($value)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
            $values[] = $value;
        }
        
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    // Atualiza um registro existente
    public function update($id, $data) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = "{$column} = ?";
        }
        $set = implode(", ", $set);
        
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        
        $types = "";
        $values = [];
        
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= "i";
            } elseif (is_float($value)) {
                $types .= "d";
            } else {
                $types .= "s";
            }
            $values[] = $value;
        }
        
        // Adiciona o ID ao final dos valores e tipos
        $types .= "i";
        $values[] = $id;
        
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    // Exclui um registro
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        
        return $stmt->execute();
    }
    
    // Busca registros com base em condições
    public function where($conditions, $values = []) {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->db->prepare($sql);
        
        if (!empty($values)) {
            $types = "";
            foreach ($values as $value) {
                if (is_int($value)) {
                    $types .= "i";
                } elseif (is_float($value)) {
                    $types .= "d";
                } else {
                    $types .= "s";
                }
            }
            
            $stmt->bind_param($types, ...$values);
        }
        
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
}
