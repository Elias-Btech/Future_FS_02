<?php
// ============================================================
// bootstrap.php — Loaded by every public page
// Sets up constants, autoloader, session, config
// ============================================================

// Ensure UTF-8 is declared for every response
header('Content-Type: text/html; charset=UTF-8');

// Root path of the project (parent of /public)
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');

// Load app config
$appConfig = require ROOT_PATH . '/config/app.php';
define('APP_NAME', $appConfig['name']);
define('BASE_URL',  $appConfig['base_url']);
define('DEBUG',     $appConfig['debug']);

// Set timezone
date_default_timezone_set($appConfig['timezone']);

// Simple PSR-4-style autoloader
spl_autoload_register(function (string $class): void {
    // Convert namespace to file path
    // App\Core\Database → app/Core/Database.php
    $path = ROOT_PATH . '/' . str_replace(['App\\', '\\'], ['app/', '/'], $class) . '.php';
    if (file_exists($path)) {
        require_once $path;
    }
});

// Start session
use App\Core\Session;
Session::start();
