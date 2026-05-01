<?php
// ============================================================
// ActivityLog.php — Tracks all admin actions
// ============================================================
namespace App\Models;

class ActivityLog extends BaseModel
{
    protected string $table = 'activity_logs';

    // Log an action
    public function log(int $adminId, string $action, ?int $leadId = null): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (admin_id, lead_id, action)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$adminId, $leadId, $action]);
    }

    // Get recent activity (for dashboard feed)
    public function recent(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT al.*, a.full_name AS admin_name, l.name AS lead_name
            FROM {$this->table} al
            LEFT JOIN admins a ON al.admin_id = a.id
            LEFT JOIN leads  l ON al.lead_id  = l.id
            ORDER BY al.created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
