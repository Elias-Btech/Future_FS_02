<?php
// ============================================================
// Database.php — PDO Singleton
// Ensures only ONE database connection exists at a time.
// ============================================================
namespace App\Core;

use PDO;
use PDOException;

class Database
{
    // Holds the single instance of this class
    private static ?Database $instance = null;

    // The PDO connection object
    private PDO $pdo;

    // Private constructor — prevents direct instantiation
    private function __construct()
    {
        $config = require ROOT_PATH . '/config/database.php';

        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['dbname'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // In production, log this — never expose DB errors to users
            die('Database connection failed. Please try again later.');
        }
    }

    // Returns the single instance (creates it if it doesn't exist)
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Returns the PDO object for running queries
    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    // Prevent cloning of the instance
    private function __clone() {}
}
