<?php
/**
 * Application Configuration File
 * 
 * Loads environment variables and sets up application constants
 */

date_default_timezone_set('Europe/London');

// Load environment variables from .env file
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

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'timetvmcorg_mosquesuk');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASSWORD') ?: (getenv('DB_PASS') ?: ''));
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('SESSION_LIFETIME', 3600); // 1 hour

// Security Headers
define('SESSION_COOKIE_SECURE', true);
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SAMESITE', 'Strict');

// Application Paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', BASE_PATH . '/public');
define('LOG_PATH', BASE_PATH . '/storage/logs');

// Ensure log directory exists
if (!is_dir(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}
