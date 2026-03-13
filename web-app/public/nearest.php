<?php
$basePath = file_exists(__DIR__ . '/../bootstrap.php') ? dirname(__DIR__) : __DIR__;
require_once $basePath . '/bootstrap.php';

$pageTitle = "Find Nearest Mosque";

ob_start();
include $basePath . '/app/views/nearest.php';
$content = ob_get_clean();

include $basePath . '/app/views/layout.php';
