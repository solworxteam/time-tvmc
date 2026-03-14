
<div class="container-fluid mosque-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 20px; margin-bottom: 40px; border-radius: 0;">
    <?php if (!$mosque): ?>
        <div class="alert alert-danger">Mosque not found.</div>
    <?php else: ?>
        <div class="text-center">
            <h1 class="display-4 mosque-title mb-2"><?php echo sanitize($mosque['name']); ?></h1>
            <p class="lead mb-2">
                <strong>Current Time: </strong><?php echo date('g:i a'); ?>
            </p>
            <p class="lead">
                <?php echo sanitize($mosque['address']); ?> <?php echo sanitize($mosque['postcode']); ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<div class="container mt-4">
    <?php if (!$mosque): ?>
        <div class="row">
            <div class="col-12">
                <a href="/index.php" class="btn btn-secondary">← Back to All Mosques</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Parking Information Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4">🅿️ Parking Information</h2>
                <div class="card border-primary shadow-sm">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <div class="p-4 bg-light rounded">
                                    <h5 class="text-muted">Onsite Parking</h5>
                                    <h2 class="text-primary"><?php echo $parking && !empty($parking['onsite_parking']) ? htmlspecialchars($parking['onsite_parking']) : '—'; ?></h2>
                                    <p class="text-muted mb-0">available spaces</p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-4 bg-light rounded">
                                    <h5 class="text-muted">Disabled Bays</h5>
                                    <h2 class="text-primary"><?php echo $parking && strtolower($parking['disable_bays']) === 'yes' ? '✅' : '❌'; ?></h2>
                                    <p class="text-muted mb-0"><?php echo $parking && strtolower($parking['disable_bays']) === 'yes' ? 'Available' : 'Not available'; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="p-4 bg-light rounded">
                                    <h5 class="text-muted">Off Street Parking</h5>
                                    <h2 class="text-primary"><?php echo $parking && strtolower($parking['off_street_parking']) === 'yes' ? '✅' : '❌'; ?></h2>
                                    <p class="text-muted mb-0"><?php echo $parking && strtolower($parking['off_street_parking']) === 'yes' ? 'Available' : 'Not available'; ?></p>
                                </div>
                            </div>
                        </div>
                        <?php if ($parking && !empty($parking['road_name'])): ?>
                        <div class="mt-3 pt-3 border-top">
                            <p class="text-muted mb-0">
                                <strong>Parking Location:</strong> <?php echo htmlspecialchars($parking['road_name']); ?>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prayer Times Section -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="text-center mb-4">🕌 Prayer Times</h2>
                <p class="text-center text-muted mb-4">
                    <strong><?php echo date('l, d F Y'); ?></strong>
                </p>

                <?php if (!$prayerTimes): ?>
                    <div class="alert alert-info">
                        No prayer times available for today. <a href="/index.php" class="alert-link">View all mosques</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle prayer-times-table">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width: 20%;">Prayer</th>
                                    <th style="width: 20%;">Start Time</th>
                                    <th style="width: 20%;">Congregation Time</th>
                                    <th style="width: 20%; text-align: center;">Status</th>
                                    <th style="width: 20%; text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $isFriday = date('N') == 5;
                                $zuhrLabel = $isFriday ? 'Friday Prayer' : 'Zuhur';
                                $prayers = [
                                    ['name' => 'Fajr', 'start' => 'fajar_start', 'jamaat' => 'fajar_jamaat'],
                                    ['name' => $zuhrLabel, 'start' => 'zuhr_start', 'jamaat' => 'zuhr_jamaat'],
                                    ['name' => 'Asr', 'start' => 'asr_start', 'jamaat' => 'asr_jamaat'],
                                    ['name' => 'Maghrib', 'start' => 'maghrib', 'jamaat' => null],
                                    ['name' => 'Isha', 'start' => 'isha_start', 'jamaat' => 'isha_jamaat'],
                                ];
                                
                                $currentTime = time();
                                $nextPrayerName = null;
                                
                                // Find next prayer
                                foreach ($prayers as $p) {
                                    $prayerTimeStr = date('H:i', strtotime($prayerTimes[$p['start']]));
                                    $prayerTimestamp = strtotime(date('Y-m-d') . ' ' . $prayerTimeStr);
                                    if ($prayerTimestamp > $currentTime) {
                                        $nextPrayerName = $p['name'];
                                        break;
                                    }
                                }
                                if (!$nextPrayerName) {
                                    $nextPrayerName = 'Fajr (Tomorrow)';
                                }
                                
                                foreach ($prayers as $p):
                                ?>
                                <tr>
                                    <td><strong><?php echo $p['name']; ?></strong></td>
                                    <td><span><?php echo formatTime($prayerTimes[$p['start']]); ?></span></td>
                                    <td>
                                        <?php if ($p['jamaat'] && isset($prayerTimes[$p['jamaat']])): ?>
                                            <strong><?php echo formatTime($prayerTimes[$p['jamaat']]); ?></strong>
                                        <?php else: ?>
                                            <small class="text-muted">—</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($p['name'] === str_replace(' (Tomorrow)', '', $nextPrayerName)): ?>
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
                                            <svg class="dir-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                                                <rect x="12" y="12" width="40" height="40" rx="4" transform="rotate(45 32 32)" fill="#2fa6d7"></rect>
                                                <path d="M22 38v-8c0-3.3 2.7-6 6-6h10v-6l12 10-12 10v-6h-8v6z" fill="#111"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Mosque Details Section -->
        <div class="row mb-5">
            <div class="col-md-6">
                <h3 class="mb-4">📍 Mosque Details</h3>
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="mb-2"><strong>Name:</strong> <?php echo sanitize($mosque['name']); ?></p>
                        <p class="mb-2"><strong>Address:</strong> <?php echo sanitize($mosque['address']); ?></p>
                        <p class="mb-0"><strong>Postcode:</strong> <?php echo sanitize($mosque['postcode']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="mb-4">🗺️ Location</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="p-4 bg-light rounded mb-3" style="height: 200px; display: flex; align-items: center; justify-content: center;">
                            <div class="text-center">
                                <p class="text-muted mb-0">📍 <?php echo sanitize($mosque['address']); ?></p>
                                <p class="text-muted mb-0"><?php echo sanitize($mosque['postcode']); ?></p>
                            </div>
                        </div>
                        <p class="small text-muted mb-1">Latitude: <?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?></p>
                        <p class="small text-muted mb-0">Longitude: <?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="row mb-5">
            <div class="col-12">
                <a href="/index.php" class="btn btn-primary">← Back to All Mosques</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .directions-btn {
        transition: all 0.3s ease;
    }
    .directions-btn:hover {
        transform: scale(1.05);
    }
    
    table tbody tr:hover {
        background-color: #f8f9fa;
    }
</style>

<script>
    function openDirections(lat, lon) {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

        if (isIOS) {
            const useApple = window.confirm('Open in Apple Maps? Tap Cancel for Google Maps.');
            if (useApple) {
                window.open(`https://maps.apple.com/?daddr=${lat},${lon}&dirflg=d`, '_blank');
            } else {
                window.open(`https://maps.google.com/maps?daddr=${lat},${lon}`, '_blank');
            }
            return;
        }

        window.open(`https://maps.google.com/maps?daddr=${lat},${lon}`, '_blank');
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
            
            openDirections(lat, lon);
        });
    });
</script>
