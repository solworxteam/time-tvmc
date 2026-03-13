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
        </div>
    <?php else: ?>
        <?php
        $isFriday = date('N') == 5;
        $zuhrLabel = $isFriday ? 'Friday Prayer' : 'Zuhur';
        ?>
        <div id="location-status" class="mb-3"></div>

        <div class="d-none d-md-block">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle" id="prayer-table">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th style="width: 20%;">Mosque</th>
                        <th style="text-align: center;">Fajr</th>
                        <th style="text-align: center;"><?php echo $zuhrLabel; ?></th>
                        <th style="text-align: center;">Asr</th>
                        <th style="text-align: center;">Maghrib</th>
                        <th style="text-align: center;">Isha</th>
                        <th style="width: 10%; text-align: center;">Next Prayer</th>
                        <th style="width: 8%; text-align: center;">Direction</th>
                    </tr>
                </thead>
                <tbody>
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
                    
                    foreach ($allMosques as $mosque):
                        $mosqueId = $mosque['id'];
                        $prayers = isset($prayersByMosque[$mosqueId]) ? $prayersByMosque[$mosqueId][0] : null;
                        
                        if (!$prayers) continue;
                        
                        $prayerTimes = [
                            ['name' => 'Fajr', 'time' => $prayers['fajar_start']],
                            ['name' => $zuhrLabel, 'time' => $prayers['zuhr_start']],
                            ['name' => 'Asr', 'time' => $prayers['asr_start']],
                            ['name' => 'Maghrib', 'time' => $prayers['maghrib']],
                            ['name' => 'Isha', 'time' => $prayers['isha_start']],
                        ];
                        
                        $nextPrayer = null;
                        foreach ($prayerTimes as $prayer) {
                            $prayerTimeStr = date('H:i', strtotime($prayer['time']));
                            $prayerTimestamp = strtotime(date('Y-m-d') . ' ' . $prayerTimeStr);
                            
                            if ($prayerTimestamp > $currentTime) {
                                $nextPrayer = $prayer['name'];
                                break;
                            }
                        }
                        
                        if (!$nextPrayer) {
                            $nextPrayer = 'Fajr (Tomorrow)';
                        }
                    ?>
                    <tr class="prayer-row" data-mosque-id="<?php echo htmlspecialchars($mosque['id']); ?>" 
                        data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" 
                        data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                        data-address="<?php echo htmlspecialchars($mosque['address'] ?? ''); ?>"
                        data-postcode="<?php echo htmlspecialchars($mosque['postcode'] ?? ''); ?>">
                        <td>
                            <a href="/mosque.php?id=<?php echo urlencode($mosque['id']); ?>" 
                               class="text-decoration-none fw-bold text-dark">
                                <?php echo sanitize($mosque['name']); ?>
                            </a>
                            <br>
                            <small class="text-muted"><?php echo sanitize($mosque['postcode']); ?></small>
                        </td>
                        <td class="text-center">
                            <span><?php echo formatTime($prayers['fajar_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['fajar_jamaat']); ?></strong>
                        </td>
                        <td class="text-center">
                            <span><?php echo formatTime($prayers['zuhr_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['zuhr_jamaat']); ?></strong>
                        </td>
                        <td class="text-center">
                            <span><?php echo formatTime($prayers['asr_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['asr_jamaat']); ?></strong>
                        </td>
                        <td class="text-center">
                            <span><?php echo formatTime($prayers['maghrib']); ?></span>
                        </td>
                        <td class="text-center">
                            <span><?php echo formatTime($prayers['isha_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['isha_jamaat']); ?></strong>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success rounded-pill px-3 py-2">
                                <?php echo $nextPrayer; ?>
                            </span>
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
        </div><!-- /.d-none.d-md-block -->

        <!-- Mobile card view (phones / small tablets) -->
        <div class="d-md-none" id="mosque-cards">
            <?php foreach ($allMosques as $mosque):
                $cardMosqueId = $mosque['id'];
                $prayersCard  = isset($prayersByMosque[$cardMosqueId]) ? $prayersByMosque[$cardMosqueId][0] : null;
                if (!$prayersCard) continue;

                $cardPrayerTimes = [
                    ['name' => 'Fajr',        'time' => $prayersCard['fajar_start']],
                    ['name' => $zuhrLabel,    'time' => $prayersCard['zuhr_start']],
                    ['name' => 'Asr',         'time' => $prayersCard['asr_start']],
                    ['name' => 'Maghrib',     'time' => $prayersCard['maghrib']],
                    ['name' => 'Isha',        'time' => $prayersCard['isha_start']],
                ];
                $nextPrayerCard = null;
                foreach ($cardPrayerTimes as $cp) {
                    $cpTs = strtotime(date('Y-m-d') . ' ' . date('H:i', strtotime($cp['time'])));
                    if ($cpTs > $currentTime) { $nextPrayerCard = $cp['name']; break; }
                }
                if (!$nextPrayerCard) $nextPrayerCard = 'Fajr (Tomorrow)';
            ?>
            <div class="card mb-3 prayer-row"
                 data-mosque-id="<?php echo htmlspecialchars($mosque['id']); ?>"
                 data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                 data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                 data-address="<?php echo htmlspecialchars($mosque['address'] ?? ''); ?>"
                 data-postcode="<?php echo htmlspecialchars($mosque['postcode'] ?? ''); ?>">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-0">
                                <a href="/mosque.php?id=<?php echo urlencode($mosque['id']); ?>"
                                   class="text-decoration-none fw-bold text-dark">
                                    <?php echo sanitize($mosque['name']); ?>
                                </a>
                            </h6>
                            <small class="text-muted"><?php echo sanitize($mosque['postcode']); ?></small>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success mosque-next-badge"><?php echo $nextPrayerCard; ?></span>
                            <button class="btn btn-sm btn-info directions-btn" type="button"
                                    data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                                    data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                    title="Get Directions">&#128205;</button>
                        </div>
                    </div>
                    <div class="row row-cols-3 g-2 text-center small">
                        <div class="col">
                            <div class="text-muted prayer-col-label">Fajr</div>
                            <div><?php echo formatTime($prayersCard['fajar_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['fajar_jamaat']); ?></strong>
                        </div>
                        <div class="col">
                            <div class="text-muted prayer-col-label"><?php echo $zuhrLabel; ?></div>
                            <div><?php echo formatTime($prayersCard['zuhr_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['zuhr_jamaat']); ?></strong>
                        </div>
                        <div class="col">
                            <div class="text-muted prayer-col-label">Asr</div>
                            <div><?php echo formatTime($prayersCard['asr_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['asr_jamaat']); ?></strong>
                        </div>
                        <div class="col">
                            <div class="text-muted prayer-col-label">Maghrib</div>
                            <div><?php echo formatTime($prayersCard['maghrib']); ?></div>
                        </div>
                        <div class="col">
                            <div class="text-muted prayer-col-label">Isha</div>
                            <div><?php echo formatTime($prayersCard['isha_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['isha_jamaat']); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div><!-- /.d-md-none -->

        <style>
            .table-hover tbody tr:hover {
                background-color: #f5f5ff !important;
            }
            .badge {
                font-size: 0.85rem;
            }
            .mosque-next-badge {
                white-space: nowrap;
                font-size: 0.75rem;
            }
            .prayer-col-label {
                font-size: 0.7rem;
            }
            .sticky-top {
                top: 0;
                z-index: 100;
            }
            
            .prayer-row.nearest {
                background-color: #fff3cd !important;
                border-left: 5px solid #ffc107;
            }
            
            .prayer-row.nearest:hover {
                background-color: #ffe9a8 !important;
            }
            
            .distance-badge {
                font-size: 0.7rem;
                padding: 2px 6px;
            }
        </style>

        <script>
            // Get user location on page load
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const userLat = position.coords.latitude;
                    const userLon = position.coords.longitude;
                    
                    // Calculate distances and find nearest
                    let nearestRow = null;
                    let minDistance = Infinity;
                    
                    document.querySelectorAll('.prayer-row').forEach(row => {
                        const mosLat = parseFloat(row.dataset.lat);
                        const mosLon = parseFloat(row.dataset.lon);
                        
                        if (mosLat && mosLon) {
                            const distance = getDistanceFromLatLon(userLat, userLon, mosLat, mosLon);
                            row.dataset.distance = distance;
                            
                            if (distance < minDistance) {
                                minDistance = distance;
                                nearestRow = row;
                            }
                        }
                    });
                    
                    if (nearestRow) {
                        const nearestId  = nearestRow.dataset.mosqueId;
                        const distanceKm = (minDistance).toFixed(2);
                        // Highlight both table row + mobile card for the same mosque
                        document.querySelectorAll(`.prayer-row[data-mosque-id="${nearestId}"]`).forEach(el => {
                            el.classList.add('nearest');
                        });
                        const nameEl = nearestRow.querySelector('a');
                        const mosqueNameCell = nameEl ? nameEl.textContent.trim() : 'Nearest Mosque';
                        document.getElementById('location-status').innerHTML =
                            `<div class="alert alert-success" role="alert">
                                &#128205; <strong>Nearest Mosque:</strong> ${mosqueNameCell} (${distanceKm} km away) – Highlighted in yellow
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
    <?php endif; ?>

    <div class="row mt-5">
        <div class="col-md-12">
            <hr>
            <p class="text-center text-muted small">
                <strong>Start time:</strong> Main prayer time | <strong>Bold time:</strong> Congregation times | <strong>📍 Button:</strong> Get Directions | <strong>Mosque names:</strong> Click to view details
            </p>
        </div>
    </div>
</div>
