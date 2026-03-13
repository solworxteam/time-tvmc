<?php
require_once __DIR__ . '/../bootstrap.php';

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
include __DIR__ . '/../app/views/mosque.php';
$content = ob_get_clean();

include __DIR__ . '/../app/views/layout.php';
