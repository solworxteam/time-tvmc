<?php
/**
 * Database Connection Manager - Singleton Pattern
 * 
 * This file maintains backward compatibility while using modern patterns.
 */

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function connect() {
        try {
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;charset=%s",
                DB_HOST,
                DB_NAME,
                DB_CHARSET
            );

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            Logger::info("Database connected successfully");
        } catch (PDOException $e) {
            Logger::error("Database connection failed: " . $e->getMessage());
            throw new Exception("Unable to connect to database");
        }
    }

    public function getConnection() {
        return $this->connection;
    }

    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            Logger::error("Query execution failed: " . $e->getMessage());
            throw new Exception("Database error occurred");
        }
    }

    public function fetchAll($sql, $params = []) {
        return $this->execute($sql, $params)->fetchAll();
    }

    public function fetch($sql, $params = []) {
        return $this->execute($sql, $params)->fetch();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        $this->connection->beginTransaction();
    }

    public function commit() {
        $this->connection->commit();
    }

    public function rollback() {
        $this->connection->rollBack();
    }

    private function __clone() {}

    public function __wakeup() {
        throw new Exception('Cannot unserialize singleton');
    }
}

// Backward compatibility functions
function getDatabase() {
    return Database::getInstance()->getConnection();
}

function queryDb($sql, $params = []) {
    return Database::getInstance()->execute($sql, $params);
}

function getAllDB($sql, $params = []) {
    return Database::getInstance()->fetchAll($sql, $params);
}

function getOneDB($sql, $params = []) {
    return Database::getInstance()->fetch($sql, $params);
}
