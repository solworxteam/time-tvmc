<?php
require_once __DIR__ . '/../bootstrap.php';

$pageTitle = "All Mosques";

// Handle search
$mosques = [];
if (!empty($_GET['search'])) {
    $mosques = Mosque::search($_GET['search']);
} else {
    $mosques = Mosque::getAll();
}

ob_start();
include __DIR__ . '/../app/views/mosques.php';
$content = ob_get_clean();

include __DIR__ . '/../app/views/layout.php';
