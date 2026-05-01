<?php
// ============================================================
// Session.php — Manages PHP sessions cleanly
// ============================================================
namespace App\Core;

class Session
{
    // Start session if not already started
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Store a value in the session
    public static function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    // Get a value from the session
    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    // Check if a session key exists
    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    // Remove a specific session key
    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    // Set a flash message (shown once, then deleted)
    public static function flash(string $key, string $message): void
    {
        $_SESSION['_flash'][$key] = $message;
    }

    // Get and clear a flash message
    public static function getFlash(string $key): ?string
    {
        $msg = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $msg;
    }

    // Destroy the entire session (logout)
    public static function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }
}
