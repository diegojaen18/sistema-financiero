<?php
namespace src\Database;

use PDO;
use PDOException;

class Connection {
    private static ?Connection $instance = null;
    private PDO $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost;port=8889;dbname=sistema_financiero;charset=utf8mb4", "root", "root", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Connection {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance;
    }

    public function getPDO(): PDO {
        return $this->pdo;
    }
}
