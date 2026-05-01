<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';
use App\Core\{Auth, Helper};

// Log the action before destroying session
if (Auth::check()) {
    $admin = Auth::admin();
    (new \App\Models\ActivityLog())->log($admin['id'], 'Logged out');
}

Auth::logout();
Helper::redirect(BASE_URL . '/login.php');
