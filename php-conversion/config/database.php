<?php
/**
 * Environment Configuration Loader
 * Loads .env file and provides access to configuration
 */

if (!function_exists('env_loader')) {
    function load_env()
    {
        $env_file = dirname(dirname(__DIR__)) . '/.env';
        
        if (!file_exists($env_file)) {
            throw new Exception('  .env file not found at: ' . $env_file);
        }
        
        $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) continue; // Skip comments
            if (!strpos($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\r\n\"\\'");

            $_ENV[$key] = $value;
        }
    }
}

load_env();

/**
 * Get environment variable
 */
function env($key, $default = null)
{
    return isset($_ENV[$key]) ? $_ENV[$key] : $default;
}

/**
 * Database Configuration
 */
return [
    'host' => env('DB_HOST', '127.0.0.1'),
    'user' => env('DB_USER', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'database' => env('DB_NAME', 'timetvmcorg_mosquesuk'),
    'charset' => 'utf8mb4',
];
