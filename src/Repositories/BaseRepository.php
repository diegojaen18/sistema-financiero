<?php
namespace SistemaFinanciero\Repositories;

use SistemaFinanciero\Database\Connection;
use SistemaFinanciero\Interfaces\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface {
    
    protected Connection $db;
    protected string $table;
    
    public function __construct() {
        $this->db = Connection::getInstance();
    }
    
    public function find(int $id): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        return $this->db->queryOne($sql, [$id]);
    }
    
    public function findAll(): array {
        $sql = "SELECT * FROM {$this->table} ORDER BY id DESC";
        return $this->db->query($sql);
    }
    
    public function save(array $data): int {
        $fields = array_keys($data);
        $placeholders = array_fill(0, count($fields), '?');
        
        $sql = sprintf(
            "INSERT INTO {$this->table} (%s) VALUES (%s)",
            implode(', ', $fields),
            implode(', ', $placeholders)
        );
        
        $this->db->execute($sql, array_values($data));
        return (int)$this->db->lastInsertId();
    }
    
    public function update(int $id, array $data): bool {
        $fields = [];
        foreach (array_keys($data) as $field) {
            $fields[] = "{$field} = ?";
        }
        
        $sql = sprintf(
            "UPDATE {$this->table} SET %s WHERE id = ?",
            implode(', ', $fields)
        );
        
        $values = array_values($data);
        $values[] = $id;
        
        return $this->db->execute($sql, $values) > 0;
    }
    
    public function delete(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        return $this->db->execute($sql, [$id]) > 0;
    }
    
    public function exists(int $id): bool {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE id = ?";
        return $this->db->queryScalar($sql, [$id]) > 0;
    }
}