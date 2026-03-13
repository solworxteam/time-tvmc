<?php
$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

$mosqueId = $_GET['id'] ?? null;

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
