<?php
/**
 * Main Entry Point
 * Initializes the application and handles requests
 */

// Start session
session_start();

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Get the root directory
$root_dir = dirname(dirname(__DIR__));

// Autoload classes
function autoload($class)
{
    $paths = [
        $root_dir . '/app/services/' . $class . '.php',
        $root_dir . '/app/models/' . $class . '.php',
        $root_dir . '/app/controllers/' . $class . '.php',
        $root_dir . '/app/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
}

spl_autoload_register('autoload');

// Load helpers
require_once $root_dir . '/app/helpers.php';

try {
    // Load configuration
    $db_config = require_once $root_dir . '/config/database.php';

    // Initialize database service
    $db = new DatabaseService($db_config);

    // Initialize models
    $mosque_model = new Mosque($db);
    $prayer_time_model = new PrayerTime($db);
    $parking_model = new Parking($db);
    $user_model = new User($db);

    // Initialize controllers
    $mosque_controller = new MosqueController($mosque_model);
    $prayer_time_controller = new PrayerTimeController($prayer_time_model);
    $parking_controller = new ParkingController($parking_model);
    $auth_controller = new AuthController($user_model);

    // Set CORS headers
    set_cors_headers();

    //Initialize router
    $router = new Router();

    // Define routes
    // Mosque routes
    $router->get('/api/mosques', function () use ($mosque_controller) {
        return $mosque_controller->getAll();
    });

    $router->get('/api/mosques/:id', function ($id) use ($mosque_controller) {
        return $mosque_controller->getById($id);
    });

    $router->put('/api/mosques/:id', function ($id) use ($mosque_controller) {
        return $mosque_controller->update($id);
    });

    // Prayer Times routes
    $router->get('/api/prayertimes/:date', function ($date) use ($prayer_time_controller) {
        return $prayer_time_controller->getByDate($date);
    });

    $router->post('/api/prayertimes/upload', function () use ($prayer_time_controller) {
        return $prayer_time_controller->upload();
    });

    $router->get('/api/prayertimes/by-mosque/:mosque_id', function ($mosque_id) use ($prayer_time_controller) {
        $month = get_query_params()['month'] ?? null;
        $year = get_query_params()['year'] ?? null;
        return $prayer_time_controller->getByMosqueAndMonth($mosque_id, $month, $year);
    });

    $router->put('/api/prayertimes/:id', function ($id) use ($prayer_time_controller) {
        return $prayer_time_controller->update($id);
    });

    // Parking routes
    $router->get('/api/parking/:mosque_id', function ($mosque_id) use ($parking_controller) {
        return $parking_controller->get($mosque_id);
    });

    $router->put('/api/parking/:mosque_id', function ($mosque_id) use ($parking_controller) {
        return $parking_controller->update($mosque_id);
    });

    // Authentication routes
    $router->post('/api/login', function () use ($auth_controller) {
        return $auth_controller->login();
    });

    $router->get('/api/auth/me', function () use ($auth_controller) {
        return $auth_controller->me();
    });

    $router->post('/api/logout', function () use ($auth_controller) {
        return $auth_controller->logout();
    });

    // Health check routes
    $router->get('/', function () {
        return json_response(['message' => 'API is working ✅']);
    });

    $router->get('/api', function () {
        return json_response(['message' => 'API is working ✅']);
    });

    // Dispatch request
    $method = get_request_method();
    $path = get_request_path();
    $router->dispatch($method, $path);

} catch (Exception $e) {
    log_error($e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Internal server error']);
    die;
}
