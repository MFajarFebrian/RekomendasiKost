<?php
/**
 * Database Configuration
 * Sistem Rekomendasi Kost
 */

class Database
{
    private static $instance = null;
    private $conn;

    // Database credentials
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $port;
    private $connection; // mysql or pgsql

    private function __construct()
    {
        // Load from environment variables (for Vercel/Supabase) or default to local
        $this->connection = getenv('DB_CONNECTION') ?: 'mysql';
        $env_host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->host = gethostbyname($env_host);
        $this->db_name = getenv('DB_NAME') ?: 'spk_kost';
        $this->username = getenv('DB_USER') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->port = getenv('DB_PORT') ?: ($this->connection === 'pgsql' ? '5432' : '3306');
        
        try {
            if ($this->connection === 'pgsql') {
                $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=require";
            } else {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
            }
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
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
