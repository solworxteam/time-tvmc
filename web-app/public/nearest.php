<?php
require_once __DIR__ . '/../app/helpers.php';
require_once __DIR__ . '/../app/models/Mosque.php';

$pageTitle = "Find Nearest Mosque";

ob_start();
include __DIR__ . '/../app/views/nearest.php';
$content = ob_get_clean();

include __DIR__ . '/../app/views/layout.php';
