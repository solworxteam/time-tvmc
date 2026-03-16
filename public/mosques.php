<?php
$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

try {
    $pageTitle = "All Mosques";

    $mosques = [];
    if (!empty($_GET['search'])) {
        $mosques = Mosque::search($_GET['search']);
    } else {
        $mosques = Mosque::getAll();
    }

    ob_start();
    include $basePath . '/app/views/mosques.php';
    $content = ob_get_clean();

    include $basePath . '/app/views/layout.php';
} catch (Throwable $e) {
    Logger::error("Error loading mosques page: " . $e->getMessage());
    http_response_code(500);
    die("<div class='container mt-5'><div class='alert alert-danger'>An error occurred while loading the mosques page.</div></div>");
}
