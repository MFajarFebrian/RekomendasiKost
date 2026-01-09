<?php
/**
 * Database Configuration
 * Sistem Rekomendasi Kost
 */

class Database
{
    private static $instance = null;
    private $conn;

    // Database credentials (defaults for local development)
    private $host = '127.0.0.1';
    private $db_name = 'spk_kost';
    private $username = 'root';
    private $password = '';
    private $port = '3306';
    private $charset = 'utf8mb4';

    private function __construct()
    {
        try {
            // Priority: Environment Variables (Vercel/Cloud) > Class Defaults
            $db_type = getenv('DB_TYPE') ?: 'mysql';
            $host = getenv('DB_HOST') ?: $this->host;
            $port = getenv('DB_PORT') ?: $this->port;
            $db_name = getenv('DB_NAME') ?: $this->db_name;
            $username = getenv('DB_USER') ?: $this->username;
            $password = getenv('DB_PASSWORD') ?: $this->password;

            if ($db_type === 'pgsql') {
                $dsn = "pgsql:host={$host};port={$port};dbname={$db_name}";
            } else {
                $dsn = "mysql:host={$host};port={$port};dbname={$db_name};charset={$this->charset}";
            }

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    /**
     * Get database instance (Singleton)
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Execute a query with prepared statement
     */
    public function query($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all results
     */
    public function fetchAll($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single row
     */
    public function fetch($sql, $params = [])
    {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId()
    {
        return $this->conn->lastInsertId();
    }

    /**
     * Prevent cloning
     */
    private function __clone()
    {
    }

    /**
     * Prevent unserialization
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
