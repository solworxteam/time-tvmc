<?php
require_once __DIR__ . '/../../bootstrap.php';

header('Content-Type: application/json');

$userLat = $_GET['lat'] ?? null;
$userLon = $_GET['lon'] ?? null;

if (!$userLat || !$userLon) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing coordinates']);
    exit;
}

$mosques = Mosque::getAll();

// Calculate distance for each mosque
$mosquesWithDistance = [];
foreach ($mosques as $mosque) {
    $distance = getDistance(
        (float)$userLat, 
        (float)$userLon,
        (float)$mosque['latitude'],
        (float)$mosque['longitude']
    );
    
    $mosque['distance'] = $distance;
    $mosquesWithDistance[] = $mosque;
}

// Sort by distance
usort($mosquesWithDistance, function($a, $b) {
    return $a['distance'] <=> $b['distance'];
});

echo json_encode($mosquesWithDistance);
