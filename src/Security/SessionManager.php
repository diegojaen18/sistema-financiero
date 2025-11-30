<?php
/**
 * SessionManager - Gestión de sesiones
 * Sistema Financiero - UTP
 */

namespace SistemaFinanciero\Security;

class SessionManager {
    
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function createSession(array $userData): void {
        self::start();
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['username'] = $userData['username'];
        $_SESSION['full_name'] = $userData['full_name'];
        $_SESSION['roles'] = explode(',', $userData['roles'] ?? '');
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    }
    
    public static function isActive(): bool {
        self::start();
        
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            return false;
        }
        
        $timeout = 1800;
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::destroy();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    public static function destroy(): void {
        self::start();
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
    }
    
    public static function getUserId(): ?int {
        self::start();
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getUsername(): ?string {
        self::start();
        return $_SESSION['username'] ?? null;
    }
    
    public static function getFullName(): ?string {
        self::start();
        return $_SESSION['full_name'] ?? null;
    }
    
    public static function getRoles(): array {
        self::start();
        return $_SESSION['roles'] ?? [];
    }
    
    public static function hasRole(string $roleName): bool {
        $roles = self::getRoles();
        return in_array($roleName, $roles, true);
    }
}