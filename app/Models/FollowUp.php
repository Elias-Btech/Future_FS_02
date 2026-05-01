<?php
// ============================================================
// FollowUp.php — Follow-up notes model
// ============================================================
namespace App\Models;

class FollowUp extends BaseModel
{
    protected string $table = 'follow_ups';

    // Get all follow-ups for a lead
    public function getByLead(int $leadId): array
    {
        $stmt = $this->db->prepare("
            SELECT f.*, a.full_name AS admin_name
            FROM {$this->table} f
            LEFT JOIN admins a ON f.admin_id = a.id
            WHERE f.lead_id = ?
            ORDER BY f.created_at DESC
        ");
        $stmt->execute([$leadId]);
        return $stmt->fetchAll();
    }

    // Create follow-up note
    public function create(array $d): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (lead_id, admin_id, note, followup_date)
            VALUES (:lead_id, :admin_id, :note, :followup_date)
        ");
        $stmt->execute([
            ':lead_id'      => $d['lead_id'],
            ':admin_id'     => $d['admin_id'],
            ':note'         => $d['note'],
            ':followup_date'=> $d['followup_date'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    // Find by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Update note
    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET note = :note, followup_date = :followup_date
            WHERE id = :id
        ");
        return $stmt->execute([
            ':note'         => $d['note'],
            ':followup_date'=> $d['followup_date'] ?? null,
            ':id'           => $id,
        ]);
    }

    // Delete note
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
