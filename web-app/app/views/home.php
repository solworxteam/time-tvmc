
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-2">🕌 Today's Prayer Times</h1>
            <p class="text-muted small mb-4">
                <strong><?php echo date('l, d F Y'); ?></strong> | 
                <em>Total Mosques: <?php echo count(Mosque::getAll()); ?></em>
            </p>
        </div>
    </div>

    <?php if (empty($todayPrayers)): ?>
        <div class="alert alert-info">
            <strong>No prayer times available for today.</strong>
            <a href="/admin/login.php" class="alert-link">Login to add prayer times</a>
        </div>
    <?php else: ?>
        <div id="location-status" class="mb-3"></div>
        
        <?php 
        $currentTime = time();
        $prayersByMosque = [];
        foreach ($todayPrayers as $prayer) {
            $mosqueId = $prayer['mosque_id'];
            if (!isset($prayersByMosque[$mosqueId])) {
                $prayersByMosque[$mosqueId] = [];
            }
            $prayersByMosque[$mosqueId][] = $prayer;
        }
        
        $allMosques = Mosque::getAll();
        $displayedMosques = [];
        foreach ($allMosques as $mosque) {
            if (isset($prayersByMosque[$mosque['id']])) {
                $displayedMosques[] = $mosque;
            }
        }
        ?>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="mosqueTabs" role="tablist" style="border-bottom: 2px solid #dee2e6;">
            <?php foreach ($displayedMosques as $index => $mosque): ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link mosque-tab <?php echo $index === 0 ? 'active' : ''; ?>" 
                            id="tab-<?php echo htmlspecialchars($mosque['id']); ?>" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-<?php echo htmlspecialchars($mosque['id']); ?>" 
                            type="button" 
                            role="tab" 
                            aria-controls="content-<?php echo htmlspecialchars($mosque['id']); ?>"
                            data-mosque-id="<?php echo htmlspecialchars($mosque['id']); ?>"
                            data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" 
                            data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>">
                        <?php echo sanitize($mosque['name']); ?>
                        <br>
                        <small><?php echo sanitize($mosque['postcode']); ?></small>
                    </button>
                </li>
            <?php endforeach; ?>
        </ul>
        
        
        <!-- Tab Content -->
        <div class="tab-content" id="mosqueTabContent" style="margin-top: 20px;">
            <?php foreach ($displayedMosques as $index => $mosque):
                $mosqueId = $mosque['id'];
                $prayers = isset($prayersByMosque[$mosqueId]) ? $prayersByMosque[$mosqueId][0] : null;
                
                if (!$prayers) continue;
                
                $prayerTimes = [
                    ['name' => 'Fajr', 'time' => $prayers['fajar_start'], 'jamaat' => $prayers['fajar_jamaat']],
                    ['name' => 'Zuhr', 'time' => $prayers['zuhr_start'], 'jamaat' => $prayers['zuhr_jamaat']],
                    ['name' => 'Asr', 'time' => $prayers['asr_start'], 'jamaat' => $prayers['asr_jamaat']],
                    ['name' => 'Maghrib', 'time' => $prayers['maghrib'], 'jamaat' => null],
                    ['name' => 'Isha', 'time' => $prayers['isha_start'], 'jamaat' => $prayers['isha_jamaat']],
                ];
                
                $nextPrayer = null;
                foreach ($prayerTimes as $prayer) {
                    $prayerTimeStr = date('H:i', strtotime($prayer['time']));
                    $prayerTimestamp = strtotime($prayerTimeStr);
                    
                    if ($prayerTimestamp > $currentTime) {
                        $nextPrayer = $prayer['name'];
                        break;
                    }
                }
                
                if (!$nextPrayer) {
                    $nextPrayer = 'Fajr (Tomorrow)';
                }
            ?>
                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" 
                     id="content-<?php echo htmlspecialchars($mosque['id']); ?>" 
                     role="tabpanel" 
                     aria-labelledby="tab-<?php echo htmlspecialchars($mosque['id']); ?>"
                     data-mosque-id="<?php echo htmlspecialchars($mosque['id']); ?>"
                     data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" 
                     data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4><?php echo sanitize($mosque['name']); ?></h4>
                                <a href="/mosque.php?id=<?php echo urlencode($mosque['id']); ?>" class="btn btn-sm btn-outline-primary">
                                    View Full Details →
                                </a>
                            </div>
                            <small class="text-muted d-block mb-4"><?php echo sanitize($mosque['address'] ?? ''); ?></small>
                            
                            <!-- Prayer Times Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 20%;">Prayer</th>
                                            <th style="width: 20%; text-align: center;">Start Time</th>
                                            <th style="width: 20%; text-align: center;">Congregation Time</th>
                                            <th style="width: 20%; text-align: center;">Next Prayer</th>
                                            <th style="width: 20%; text-align: center;">Direction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($prayerTimes as $p): ?>
                                        <tr>
                                            <td class="fw-bold"><?php echo $p['name']; ?></td>
                                            <td class="text-center"><?php echo formatTime($p['time']); ?></td>
                                            <td class="text-center">
                                                <?php echo $p['jamaat'] ? formatTime($p['jamaat']) : '—'; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ($p['name'] === $nextPrayer): ?>
                                                    <span class="badge bg-success">Next</span>
                                                <?php else: ?>
                                                    <span class="text-muted">—</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info directions-btn" type="button" 
                                                        data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" 
                                                        data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                                        title="Get Directions">
                                                    📍
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <!-- Parking Information -->
                        <div class="col-md-4">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    🅿️ Parking Information
                                </div>
                                <div class="card-body">
                                    <?php 
                                    require_once __DIR__ . '/../../app/models/Parking.php';
                                    $parking = Parking::getByMosqueId($mosqueId);
                                    
                                    if ($parking):
                                    ?>
                                        <p class="mb-2">
                                            <strong>Available Spaces:</strong><br>
                                            <span class="badge bg-info"><?php echo $parking['spaces'] ?? 'N/A'; ?> spaces</span>
                                        </p>
                                        <p class="mb-2">
                                            <strong>Accessible Parking:</strong><br>
                                            <?php echo ($parking['has_accessible'] ?? 0) ? '✅ Available' : '❌ Not available'; ?>
                                        </p>
                                        <p class="mb-2">
                                            <strong>Price:</strong><br>
                                            <?php echo $parking['price'] ? '£' . htmlspecialchars($parking['price']) : 'Free'; ?>
                                        </p>
                                        <?php if ($parking['notes']): ?>
                                        <p class="mb-0">
                                            <strong>Notes:</strong><br>
                                            <small><?php echo htmlspecialchars($parking['notes']); ?></small>
                                        </p>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No parking information available.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="text-center mt-3">
                                <a href="/mosque.php?id=<?php echo urlencode($mosque['id']); ?>" class="text-muted small">
                                    More details ↳
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

        
        <style>
            .mosque-tab {
                color: #495057;
                font-weight: 500;
                padding: 12px 24px;
                border: none;
                border-bottom: 3px solid transparent;
                transition: all 0.3s ease;
            }
            
            .mosque-tab:hover {
                background-color: #f8f9fa;
                border-bottom-color: #0d6efd;
                color: #0d6efd;
            }
            
            .mosque-tab.active {
                background-color: #f8f9fa;
                border-bottom-color: #0d6efd !important;
                color: #0d6efd;
            }
            
            .mosque-tab.nearest {
                background-color: #fff3cd !important;
                border-bottom-color: #ffc107 !important;
                color: #856404;
            }
            
            .mosque-tab.nearest:hover {
                background-color: #ffe9a8 !important;
            }
            
            .tab-pane {
                animation: fadeIn 0.3s ease;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            
            .card {
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
        </style>

        <script>
            // Get user location on page load
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;
                    
                    // Calculate distances and find nearest
                    let nearestTab = null;
                    let minDistance = Infinity;
                    
                    document.querySelectorAll('.mosque-tab').forEach(tab => {
                        const mosLat = parseFloat(tab.dataset.lat);
                        const mosLon = parseFloat(tab.dataset.lon);
                        
                        if (mosLat && mosLon) {
                            const distance = getDistanceFromLatLon(userLat, userLon, mosLat, mosLon);
                            tab.dataset.distance = distance;
                            
                            if (distance < minDistance) {
                                minDistance = distance;
                                nearestTab = tab;
                            }
                        }
                    });
                    
                    if (nearestTab) {
                        nearestTab.classList.add('nearest');
                        const mosqueNameText = nearestTab.textContent.split('\n')[0];
                        const distanceKm = (minDistance).toFixed(2);
                        document.getElementById('location-status').innerHTML = 
                            `<div class="alert alert-success" role="alert">
                                📍 <strong>Nearest Mosque:</strong> ${mosqueNameText} (${distanceKm} km away) - Highlighted in yellow
                            </div>`;
                    }
                }, function(error) {
                    // Silently fail if location not available
                });
            }
            
            // Haversine formula for distance calculation
            function getDistanceFromLatLon(lat1, lon1, lat2, lon2) {
                const R = 6371; // Radius of the earth in km
                const dLat = deg2rad(lat2 - lat1);
                const dLon = deg2rad(lon2 - lon1);
                const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                          Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                          Math.sin(dLon/2) * Math.sin(dLon/2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                return R * c;
            }
            
            function deg2rad(deg) {
                return deg * (Math.PI/180);
            }
            
            // Directions button handler
            document.querySelectorAll('.directions-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const lat = this.dataset.lat;
                    const lon = this.dataset.lon;
                    
                    if (!lat || !lon || lat == '0' || lon == '0') {
                        alert('Location data not available for this mosque');
                        return;
                    }
                    
                    // Detect device and open appropriate maps
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                    const isAndroid = /Android/.test(navigator.userAgent);
                    
                    if (isIOS) {
                        // Apple Maps
                        window.open(`maps://maps.apple.com/?daddr=${lat},${lon}&dirflg=d`);
                    } else if (isAndroid) {
                        // Google Maps on Android
                        window.open(`https://maps.google.com/maps?daddr=${lat},${lon}`);
                    } else {
                        // Google Maps (default for desktop/other)
                        window.open(`https://maps.google.com/maps?daddr=${lat},${lon}`);
                    }
                });
            });
        </script>

    <div class="row mt-5">
        <div class="col-md-12">
            <hr>
            <p class="text-center text-muted small">
                <strong>Start time:</strong> Main prayer time | <strong>Grey text:</strong> Congregation times | <strong>?? Button:</strong> Get Directions | <strong>View Full Details:</strong> Individual mosque pages
            </p>
        </div>
    </div>
</div>
