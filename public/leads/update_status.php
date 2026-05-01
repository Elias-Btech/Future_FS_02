<?php
require_once dirname(dirname(__DIR__)) . '/app/bootstrap.php';
use App\Core\{Auth, Helper, Session};
use App\Models\{Lead, ActivityLog};

Auth::require();
$admin   = Auth::admin();
$id      = (int)($_GET['id'] ?? 0);
$status  = trim($_GET['status'] ?? '');
$allowed = ['New','Contacted','Follow-up','Converted','Closed'];

if ($id && in_array($status, $allowed, true)) {
    $lead = (new Lead())->findById($id);
    if ($lead) {
        (new Lead())->update($id, array_merge($lead, ['status' => $status]));
        (new ActivityLog())->log($admin['id'], "Changed status to $status", $id);
        Session::flash('success', 'Status updated to ' . $status . '.');
    }
}

Helper::redirect(BASE_URL . '/leads/view.php?id=' . $id);
