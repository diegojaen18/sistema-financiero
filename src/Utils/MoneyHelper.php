<?php
namespace SistemaFinanciero\Database;

use PDO;
use PDOException;

class Connection {
    
    private static ?Connection $instance = null;
    private ?PDO $pdo = null;
    
    private const PDO_OPTIONS = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ];
    
    private function __construct() {
        $this->connect();
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
    
    public static function getInstance(): Connection {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect(): void {
        try {
            $dsn = sprintf(
                "mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4",
                DB_HOST,
                DB_PORT,
                DB_NAME
            );
            
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, self::PDO_OPTIONS);
            
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new PDOException("Unable to connect to database");
        }
    }
    
    public function getPDO(): PDO {
        if ($this->pdo === null) {
            $this->connect();
        }
        return $this->pdo;
    }
    
    public function query(string $sql, array $params = []): array {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Query Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function execute(string $sql, array $params = []): int {
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log("Execute Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function lastInsertId(): string {
        return $this->pdo->lastInsertId();
    }
    
    public function beginTransaction(): bool {
        return $this->pdo->beginTransaction();
    }
    
    public function commit(): bool {
        return $this->pdo->commit();
    }
    
    public function rollBack(): bool {
        return $this->pdo->rollBack();
    }
    
    public function inTransaction(): bool {
        return $this->pdo->inTransaction();
    }
    
    public function queryOne(string $sql, array $params = []) {
        $result = $this->query($sql, $params);
        return $result[0] ?? null;
    }
    
    public function queryScalar(string $sql, array $params = []) {
        $result = $this->queryOne($sql, $params);
        return $result ? array_values($result)[0] : null;
    }
    
    public function exists(string $sql, array $params = []): bool {
        return $this->queryOne($sql, $params) !== null;
    }
}