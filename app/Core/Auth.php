<?php
// ============================================================
// Auth.php — Authentication guard
// Protects admin pages from unauthenticated access
// ============================================================
namespace App\Core;

class Auth
{
    // Check if admin is logged in
    public static function check(): bool
    {
        Session::start();
        return Session::has('admin_id');
    }

    // Redirect to login if not authenticated
    public static function require(): void
    {
        if (!self::check()) {
            header('Location: ' . BASE_URL . '/login.php');
            exit;
        }
    }

    // Get the currently logged-in admin's data
    public static function admin(): ?array
    {
        if (!self::check()) return null;
        return [
            'id'       => Session::get('admin_id'),
            'username' => Session::get('admin_username'),
            'name'     => Session::get('admin_name'),
            'email'    => Session::get('admin_email'),
        ];
    }

    // Log in an admin (store in session)
    public static function login(array $admin): void
    {
        session_regenerate_id(true); // Prevent session fixation
        Session::set('admin_id',       $admin['id']);
        Session::set('admin_username', $admin['username']);
        Session::set('admin_name',     $admin['full_name']);
        Session::set('admin_email',    $admin['email']);
    }

    // Log out
    public static function logout(): void
    {
        Session::destroy();
    }
}
