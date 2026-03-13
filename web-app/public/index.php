<?php
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/models/Mosque.php';
require_once __DIR__ . '/../app/models/PrayerTime.php';

$pageTitle = "Home";
$todayPrayers = PrayerTime::getTodayPrayerTimes();

ob_start();
include __DIR__ . '/../app/views/home.php';
$content = ob_get_clean();

include __DIR__ . '/../app/views/layout.php';
