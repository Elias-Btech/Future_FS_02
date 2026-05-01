<?php
// ============================================================
// Lead.php — Lead management model
// ============================================================
namespace App\Models;

class Lead extends BaseModel
{
    protected string $table = 'leads';

    // Get all leads with optional filters + pagination
    public function getAll(array $filters = [], int $limit = 15, int $offset = 0): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[]          = "(name LIKE :search OR email LIKE :search OR company LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status'])) {
            $where[]           = "status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['priority'])) {
            $where[]             = "priority = :priority";
            $params[':priority'] = $filters['priority'];
        }
        if (!empty($filters['source'])) {
            $where[]           = "source = :source";
            $params[':source'] = $filters['source'];
        }
        if (!empty($filters['date_from'])) {
            $where[]              = "DATE(created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[]            = "DATE(created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $sql = "SELECT * FROM {$this->table}
                WHERE " . implode(' AND ', $where) . "
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':limit',  $limit,  \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Count leads with same filters (for pagination)
    public function count(array $filters = []): int
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filters['search'])) {
            $where[]          = "(name LIKE :search OR email LIKE :search OR company LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['status']))   { $where[] = "status = :status";     $params[':status']   = $filters['status']; }
        if (!empty($filters['priority'])) { $where[] = "priority = :priority"; $params[':priority'] = $filters['priority']; }
        if (!empty($filters['source']))   { $where[] = "source = :source";     $params[':source']   = $filters['source']; }

        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM {$this->table} WHERE " . implode(' AND ', $where)
        );
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    // Find single lead by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Create new lead
    public function create(array $d): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (name, email, phone, company, source, status, priority, notes, next_followup_date, assigned_to)
            VALUES
                (:name,:email,:phone,:company,:source,:status,:priority,:notes,:next_followup_date,:assigned_to)
        ");
        $stmt->execute([
            ':name'              => $d['name'],
            ':email'             => $d['email'],
            ':phone'             => $d['phone']             ?? null,
            ':company'           => $d['company']           ?? null,
            ':source'            => $d['source']            ?? null,
            ':status'            => $d['status']            ?? 'New',
            ':priority'          => $d['priority']          ?? 'Medium',
            ':notes'             => $d['notes']             ?? null,
            ':next_followup_date'=> $d['next_followup_date'] ?? null,
            ':assigned_to'       => $d['assigned_to']       ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    // Update lead
    public function update(int $id, array $d): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET
                name               = :name,
                email              = :email,
                phone              = :phone,
                company            = :company,
                source             = :source,
                status             = :status,
                priority           = :priority,
                notes              = :notes,
                next_followup_date = :next_followup_date,
                updated_at         = NOW()
            WHERE id = :id
        ");
        return $stmt->execute([
            ':name'              => $d['name'],
            ':email'             => $d['email'],
            ':phone'             => $d['phone']             ?? null,
            ':company'           => $d['company']           ?? null,
            ':source'            => $d['source']            ?? null,
            ':status'            => $d['status'],
            ':priority'          => $d['priority'],
            ':notes'             => $d['notes']             ?? null,
            ':next_followup_date'=> $d['next_followup_date'] ?? null,
            ':id'                => $id,
        ]);
    }

    // Delete lead
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Dashboard stats
    public function stats(): array
    {
        $row = $this->db->query("
            SELECT
                COUNT(*)                                      AS total,
                SUM(status = 'New')                           AS new_leads,
                SUM(status = 'Contacted')                     AS contacted,
                SUM(status = 'Follow-up')                     AS followups,
                SUM(status = 'Converted')                     AS converted,
                SUM(status = 'Closed')                        AS closed,
                SUM(priority = 'High')                        AS high_priority_count,
                SUM(
                    next_followup_date < CURDATE()
                    AND next_followup_date IS NOT NULL
                    AND status NOT IN ('Converted','Closed')
                )                                             AS overdue
            FROM {$this->table}
        ")->fetch();
        $row['conversion_rate'] = $row['total'] > 0
            ? round($row['converted'] / $row['total'] * 100, 1)
            : 0;
        // Alias for views that use 'high_priority'
        $row['high_priority'] = $row['high_priority_count'];
        return $row;
    }

    // Source breakdown
    public function bySource(): array
    {
        return $this->db->query("
            SELECT source, COUNT(*) AS total
            FROM {$this->table}
            WHERE source IS NOT NULL AND source != ''
            GROUP BY source ORDER BY total DESC
        ")->fetchAll();
    }

    // Recent leads (for dashboard)
    public function recent(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ?"
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Export all leads as array (for CSV)
    public function exportAll(): array
    {
        return $this->db->query(
            "SELECT id,name,email,phone,company,source,status,priority,notes,
                    next_followup_date,created_at,updated_at
             FROM {$this->table} ORDER BY created_at DESC"
        )->fetchAll();
    }
}
