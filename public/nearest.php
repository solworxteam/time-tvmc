<?php
$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

try {
    $pageTitle = "Find Nearest Mosque";

    ob_start();
    include $basePath . '/app/views/nearest.php';
    $content = ob_get_clean();

    include $basePath . '/app/views/layout.php';
} catch (Throwable $e) {
    Logger::error("Error loading nearest page: " . $e->getMessage());
    http_response_code(500);
    die("<div class='container mt-5'><div class='alert alert-danger'>An error occurred while loading the nearest page.</div></div>");
}
