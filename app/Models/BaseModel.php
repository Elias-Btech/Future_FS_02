<?php
// ============================================================
// BaseModel.php — All models extend this
// Provides shared PDO access
// ============================================================
namespace App\Models;

use App\Core\Database;
use PDO;

abstract class BaseModel
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
}
