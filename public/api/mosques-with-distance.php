<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $basePath = file_exists(__DIR__ . '/../../bootstrap.php') ? dirname(dirname(__DIR__)) : dirname(__DIR__);
    require_once $basePath . '/bootstrap.php';

    $userLat = $_GET['lat'] ?? null;
    $userLon = $_GET['lon'] ?? null;

    if ($userLat === null || $userLon === null || $userLat === '' || $userLon === '') {
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

    http_response_code(200);
    echo json_encode($mosquesWithDistance);
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Unable to fetch nearest mosques right now.'
    ]);
    exit;
}
