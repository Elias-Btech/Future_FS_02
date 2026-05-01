<?php
// ============================================================
// Admin.php — Admin user model
// ============================================================
namespace App\Models;

class Admin extends BaseModel
{
    protected string $table = 'admins';

    // Find admin by username
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE username = ? LIMIT 1"
        );
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    // Find admin by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    // Create new admin
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (full_name, username, email, password)
             VALUES (:full_name, :username, :email, :password)"
        );
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':username'  => $data['username'],
            ':email'     => $data['email'],
            ':password'  => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
        return (int) $this->db->lastInsertId();
    }

    // Check if username or email already exists
    public function exists(string $username, string $email): bool
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM {$this->table} WHERE username = ? OR email = ? LIMIT 1"
        );
        $stmt->execute([$username, $email]);
        return (bool) $stmt->fetch();
    }

    // Verify password
    public function verifyPassword(string $plain, string $hashed): bool
    {
        return password_verify($plain, $hashed);
    }
}
