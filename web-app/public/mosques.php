<?php
$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

$pageTitle = "All Mosques";

// Handle search
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
