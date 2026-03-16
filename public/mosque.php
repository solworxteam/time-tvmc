<?php
$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

try {
    $mosqueId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if (!$mosqueId) {
        header('Location: /mosques.php');
        exit;
    }

    $mosque = Mosque::getById($mosqueId);
    $prayerTimes = null;
    $parking = null;

    if ($mosque) {
        $today = date('Y-m-d');
        $prayers = PrayerTime::getByDate($today, $mosqueId);
        $prayerTimes = $prayers[0] ?? null;
        $parking = Parking::getByMosqueId($mosqueId);
    }

    $pageTitle = $mosque ? sanitize($mosque['name']) : "Mosque";

    ob_start();
    include $basePath . '/app/views/mosque.php';
    $content = ob_get_clean();

    include $basePath . '/app/views/layout.php';
} catch (Throwable $e) {
    Logger::error("Error loading mosque page: " . $e->getMessage());
    http_response_code(500);
    die("<div class='container mt-5'><div class='alert alert-danger'>An error occurred while loading the mosque page.</div></div>");
}
