<?php
/**
 * Database Service - PDO Connection Handler
 * Manages database connections and query execution
 */

class DatabaseService
{
    private static $pdo = null;
    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Get or create PDO connection
     */
    public function getPDO()
    {
        if (self::$pdo === null) {
            try {
                $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
                
                self::$pdo = new PDO(
                    $dsn,
                    $this->config['user'],
                    $this->config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
            } catch (PDOException $e) {
                throw new Exception('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    /**
     * Execute a prepared statement
     */
    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->getPDO()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception('Database query error: ' . $e->getMessage());
        }
    }

    /**
     * Fetch one row
     */
    public function fetchOne($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Insert and get last insert ID
     */
    public function insert($sql, $params = [])
    {
        $this->query($sql, $params);
        return $this->getPDO()->lastInsertId();
    }

    /**
     * Get affected rows from last query
     */
    public function lastInsertId()
    {
        return $this->getPDO()->lastInsertId();
    }

    /**
     * Execute raw query (for debugging only - not in production)
     */
    public function exec($sql)
    {
        return $this->getPDO()->exec($sql);
    }
}
