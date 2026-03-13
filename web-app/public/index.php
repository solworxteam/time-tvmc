<?php
/**
 * Home Page - Displays prayer times for all mosques
 */

$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

try {
    $pageTitle = "Home";
    $todayPrayers = PrayerTime::getTodayPrayerTimes();

    ob_start();
    include $basePath . '/app/views/home.php';
    $content = ob_get_clean();

    include $basePath . '/app/views/layout.php';
} catch (Exception $e) {
    Logger::error("Error loading home page: " . $e->getMessage());
    http_response_code(500);
    die("<div class='container mt-5'><div class='alert alert-danger'>An error occurred while loading the page.</div></div>");
}
