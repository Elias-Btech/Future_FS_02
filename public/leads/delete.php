<?php
require_once dirname(dirname(__DIR__)) . '/app/bootstrap.php';
use App\Core\{Auth, Helper, Session};
use App\Models\{Lead, ActivityLog};

Auth::require();
$admin = Auth::admin();

$id   = (int)($_GET['id'] ?? 0);
$lead = (new Lead())->findById($id);

if (!$lead) {
    Session::flash('error', 'Lead not found.');
} else {
    (new Lead())->delete($id);
    (new ActivityLog())->log($admin['id'], 'Deleted lead: ' . $lead['name']);
    Session::flash('success', 'Lead "' . $lead['name'] . '" deleted.');
}

Helper::redirect(BASE_URL . '/leads/index.php');
