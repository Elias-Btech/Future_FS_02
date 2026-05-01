<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\Auth;

// Logged in → dashboard, else → public homepage
if (Auth::check()) {
    header('Location: ' . BASE_URL . '/dashboard.php');
} else {
    header('Location: ' . BASE_URL . '/home.php');
}
exit;
