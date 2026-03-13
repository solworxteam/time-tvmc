<?php
// Database configuration
function getDatabase() {
    // Load .env if it exists
    $env_file = __DIR__ . '/.env';
    if (!file_exists($env_file)) {
        $env_file = __DIR__ . '/../.env';
    }
    
    if (file_exists($env_file)) {
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && $line[0] !== '#') {
                list($key, $value) = explode('=', $line, 2);
                putenv(trim($key) . '=' . trim($value));
            }
        }
    }
    
    $db_host = getenv('DB_HOST') ?: '127.0.0.1';
    $db_name = getenv('DB_NAME') ?: 'timetvmcorg_mosquesuk';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_pass = getenv('DB_PASSWORD') ?: '';
    
    try {
        $pdo = new PDO(
            "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4",
            $db_user,
            $db_pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_TIMEOUT => 5
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("DB Error: " . $e->getMessage());
        die("<div style='color:red;padding:20px;font-family:Arial;'>Database connection error: " . htmlspecialchars($e->getMessage()) . "</div>");
    }
}

// Helper function to safely execute queries
function queryDb($sql, $params = []) {
    $pdo = getDatabase();
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// Get all results
function getAllDB($sql, $params = []) {
    $stmt = queryDb($sql, $params);
    return $stmt->fetchAll();
}

// Get single result
function getOneDB($sql, $params = []) {
    $stmt = queryDb($sql, $params);
    return $stmt->fetch();
}
