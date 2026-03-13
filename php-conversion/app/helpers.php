<?php
/**
 * Global Helper Functions
 */

/**
 * Send JSON response
 */
function json_response($data, $status_code = 200)
{
    http_response_code($status_code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    die;
}

/**
 * Get JSON input from request body
 */
function get_json_input()
{
    $input = file_get_contents('php://input');
    return json_decode($input, true) ?? [];
}

/**
 * Get request method
 */
function get_request_method()
{
    return $_SERVER['REQUEST_METHOD'];
}

/**
 * Get request path
 */
function get_request_path()
{
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    // Remove public_html or webroot
    $path = preg_replace('|^/public|', '', $path);
    return $path;
}

/**
 * Get query parameters
 */
function get_query_params()
{
    return $_GET;
}

/**
 * Set CORS headers
 */
function set_cors_headers()
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
    
    if (get_request_method() === 'OPTIONS') {
        http_response_code(200);
        die;
    }
}

/**
 * Log errors to file
 */
function log_error($message)
{
    $log_file = dirname(dirname(__DIR__)) . '/storage/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

/**
 * Sanitize input
 */
function sanitize($value)
{
    if (is_array($value)) {
        return array_map('sanitize', $value);
    }
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
