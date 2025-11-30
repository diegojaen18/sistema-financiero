<?php
/**
 * CSRF - Protección contra Cross-Site Request Forgery
 * Sistema Financiero - UTP
 */

namespace SistemaFinanciero\Security;

class CSRF {
    
    public static function generateToken(): string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public static function getToken(): ?string {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return $_SESSION['csrf_token'] ?? null;
    }
    
    public static function validateToken(string $token): bool {
        $sessionToken = self::getToken();
        
        if ($sessionToken === null) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }
    
    public static function getHiddenInput(): string {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    public static function validateFromPost(): bool {
        if (!isset($_POST['csrf_token'])) {
            return false;
        }
        
        return self::validateToken($_POST['csrf_token']);
    }
}