<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-2">Local Prayer Times</h1>
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
                            ['name' => 'Fajr', 'key' => 'fajar_start', 'time' => $prayers['fajar_start']],
                            ['name' => $zuhrLabel, 'key' => 'zuhr_start', 'time' => $prayers['zuhr_start']],
                            ['name' => 'Asr', 'key' => 'asr_start', 'time' => $prayers['asr_start']],
                            ['name' => 'Maghrib', 'key' => 'maghrib', 'time' => $prayers['maghrib']],
                            ['name' => 'Isha', 'key' => 'isha_start', 'time' => $prayers['isha_start']],
                        ];

                        $nextPrayerKey = null;
                        foreach ($prayerTimes as $prayer) {
                            $prayerTimeStr = date('H:i', strtotime($prayer['time']));
                            $prayerTimestamp = strtotime(date('Y-m-d') . ' ' . $prayerTimeStr);
                            
                            if ($prayerTimestamp > $currentTime) {
                                $nextPrayerKey = $prayer['key'];
                                break;
                            }
                        }

                        if (!$nextPrayerKey) {
                            $nextPrayerKey = 'fajar_start';
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
                        <td class="text-center <?php echo $nextPrayerKey === 'fajar_start' ? 'next-prayer-cell' : ''; ?>">
                            <span><?php echo formatTime($prayers['fajar_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['fajar_jamaat']); ?></strong>
                        </td>
                        <td class="text-center <?php echo $nextPrayerKey === 'zuhr_start' ? 'next-prayer-cell' : ''; ?>">
                            <span><?php echo formatTime($prayers['zuhr_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['zuhr_jamaat']); ?></strong>
                        </td>
                        <td class="text-center <?php echo $nextPrayerKey === 'asr_start' ? 'next-prayer-cell' : ''; ?>">
                            <span><?php echo formatTime($prayers['asr_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['asr_jamaat']); ?></strong>
                        </td>
                        <td class="text-center <?php echo $nextPrayerKey === 'maghrib' ? 'next-prayer-cell' : ''; ?>">
                            <span><?php echo formatTime($prayers['maghrib']); ?></span>
                        </td>
                        <td class="text-center <?php echo $nextPrayerKey === 'isha_start' ? 'next-prayer-cell' : ''; ?>">
                            <span><?php echo formatTime($prayers['isha_start']); ?></span>
                            <br>
                            <strong class="d-block"><?php echo formatTime($prayers['isha_jamaat']); ?></strong>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info directions-btn" type="button" 
                                    data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" 
                                    data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                    title="Get Directions">
                                &rarr;
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
                    ['name' => 'Fajr',        'key' => 'fajar_start', 'time' => $prayersCard['fajar_start']],
                    ['name' => $zuhrLabel,    'key' => 'zuhr_start', 'time' => $prayersCard['zuhr_start']],
                    ['name' => 'Asr',         'key' => 'asr_start', 'time' => $prayersCard['asr_start']],
                    ['name' => 'Maghrib',     'key' => 'maghrib', 'time' => $prayersCard['maghrib']],
                    ['name' => 'Isha',        'key' => 'isha_start', 'time' => $prayersCard['isha_start']],
                ];
                $nextPrayerCardKey = null;
                foreach ($cardPrayerTimes as $cp) {
                    $cardPrayerTimeStr = date('H:i', strtotime($cp['time']));
                    $cpTs = strtotime(date('Y-m-d') . ' ' . $cardPrayerTimeStr);
                    if ($cpTs > $currentTime) { $nextPrayerCardKey = $cp['key']; break; }
                }
                if (!$nextPrayerCardKey) { $nextPrayerCardKey = 'fajar_start'; }
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
                        <div>
                            <button class="btn btn-sm btn-info directions-btn" type="button"
                                    data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                                    data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                    title="Get Directions">&rarr;</button>
                        </div>
                    </div>
                    <div class="row row-cols-3 g-2 text-center small">
                        <div class="col <?php echo $nextPrayerCardKey === 'fajar_start' ? 'next-prayer-cell next-prayer-card-cell' : ''; ?>">
                            <div class="text-muted prayer-col-label">Fajr</div>
                            <div><?php echo formatTime($prayersCard['fajar_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['fajar_jamaat']); ?></strong>
                        </div>
                        <div class="col <?php echo $nextPrayerCardKey === 'zuhr_start' ? 'next-prayer-cell next-prayer-card-cell' : ''; ?>">
                            <div class="text-muted prayer-col-label"><?php echo $zuhrLabel; ?></div>
                            <div><?php echo formatTime($prayersCard['zuhr_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['zuhr_jamaat']); ?></strong>
                        </div>
                        <div class="col <?php echo $nextPrayerCardKey === 'asr_start' ? 'next-prayer-cell next-prayer-card-cell' : ''; ?>">
                            <div class="text-muted prayer-col-label">Asr</div>
                            <div><?php echo formatTime($prayersCard['asr_start']); ?></div>
                            <strong><?php echo formatTime($prayersCard['asr_jamaat']); ?></strong>
                        </div>
                        <div class="col <?php echo $nextPrayerCardKey === 'maghrib' ? 'next-prayer-cell next-prayer-card-cell' : ''; ?>">
                            <div class="text-muted prayer-col-label">Maghrib</div>
                            <div><?php echo formatTime($prayersCard['maghrib']); ?></div>
                        </div>
                        <div class="col <?php echo $nextPrayerCardKey === 'isha_start' ? 'next-prayer-cell next-prayer-card-cell' : ''; ?>">
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
            .prayer-col-label {
                font-size: 0.7rem;
            }
            .next-prayer-cell {
                background-color: #d1f7df !important;
                border: 1px solid #5ec27b;
                border-radius: 6px;
                font-weight: 700;
            }
            .next-prayer-card-cell {
                border-radius: 8px;
                padding: 6px 2px;
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
            .location-actions {
                margin-top: 0.75rem;
                display: flex;
                gap: 0.5rem;
                flex-wrap: wrap;
            }
            .map-choice-actions {
                display: flex;
                gap: 0.5rem;
                margin-top: 0.75rem;
            }
        </style>

        <script>
            const locationStatusEl = document.getElementById('location-status');

            function clearNearestHighlight() {
                document.querySelectorAll('.prayer-row.nearest').forEach(row => {
                    row.classList.remove('nearest');
                });
            }

            function showLocationMessage(type, messageHtml, showRetry = false) {
                const retryButton = showRetry
                    ? '<div class="location-actions"><button type="button" class="btn btn-sm btn-primary" id="retry-location-btn">Try Location Again</button></div>'
                    : '';
                locationStatusEl.innerHTML = `<div class="alert alert-${type}" role="alert">${messageHtml}${retryButton}</div>`;
                if (showRetry) {
                    const retryBtn = document.getElementById('retry-location-btn');
                    if (retryBtn) {
                        retryBtn.addEventListener('click', function() {
                            requestAndHighlightNearest(true);
                        });
                    }
                }
            }

            function updateNearestHighlight(userLat, userLon) {
                clearNearestHighlight();

                let nearestRow = null;
                let minDistance = Infinity;

                document.querySelectorAll('.prayer-row').forEach(row => {
                    const mosLat = parseFloat(row.dataset.lat);
                    const mosLon = parseFloat(row.dataset.lon);

                    if (!Number.isFinite(mosLat) || !Number.isFinite(mosLon) || mosLat === 0 || mosLon === 0) {
                        return;
                    }

                    const distance = getDistanceFromLatLon(userLat, userLon, mosLat, mosLon);
                    row.dataset.distance = distance;

                    if (distance < minDistance) {
                        minDistance = distance;
                        nearestRow = row;
                    }
                });

                if (!nearestRow) {
                    showLocationMessage('warning', '<strong>Location found.</strong> No mosque coordinates are available to calculate nearest mosque.');
                    return;
                }

                const nearestId = nearestRow.dataset.mosqueId;
                const distanceKm = minDistance.toFixed(2);
                document.querySelectorAll(`.prayer-row[data-mosque-id="${nearestId}"]`).forEach(el => {
                    el.classList.add('nearest');
                });
                const nameEl = nearestRow.querySelector('a');
                const mosqueName = nameEl ? nameEl.textContent.trim() : 'Nearest Mosque';
                showLocationMessage('success', `&#128205; <strong>Nearest Mosque:</strong> ${mosqueName} (${distanceKm} km away) - highlighted in yellow.`);
            }

            function handleLocationError(error) {
                if (error.code === 1) {
                    showLocationMessage(
                        'danger',
                        '<strong>Permission denied.</strong> Please allow location access to highlight your nearest mosque.<br><small>iPhone Safari: aA menu > Website Settings > Location > Allow.</small>',
                        true
                    );
                    return;
                }
                if (error.code === 2) {
                    showLocationMessage('warning', '<strong>Location unavailable.</strong> Please check GPS/network and try again.', true);
                    return;
                }
                if (error.code === 3) {
                    showLocationMessage('warning', '<strong>Location request timed out.</strong> Please try again.', true);
                    return;
                }
                showLocationMessage('warning', '<strong>Unable to get location.</strong> Please try again.', true);
            }

            function requestAndHighlightNearest(isUserInitiated) {
                if (!navigator.geolocation) {
                    showLocationMessage('danger', '<strong>Geolocation is not supported by this browser.</strong>');
                    return;
                }

                if (isUserInitiated) {
                    showLocationMessage('info', 'Requesting your location...');
                }

                navigator.geolocation.getCurrentPosition(function(position) {
                    updateNearestHighlight(position.coords.latitude, position.coords.longitude);
                }, handleLocationError, {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                });
            }

            function shouldAutoPromptLocation() {
                if (!navigator.permissions || !navigator.permissions.query) {
                    requestAndHighlightNearest(false);
                    return;
                }

                navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
                    if (result.state === 'granted' || result.state === 'prompt') {
                        requestAndHighlightNearest(false);
                        return;
                    }

                    showLocationMessage(
                        'warning',
                        '<strong>Location access is blocked.</strong> Enable it in browser settings, then tap "Try Location Again".',
                        true
                    );
                }).catch(function() {
                    requestAndHighlightNearest(false);
                });
            }

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

            shouldAutoPromptLocation();
            
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
    <?php endif; ?>

    <div class="row mt-5">
        <div class="col-md-12">
            <hr>
            <p class="text-center text-muted small">
                <strong>Start time:</strong> Main prayer time | <strong>Bold time:</strong> Congregation times | <strong>Green prayer block:</strong> Next upcoming prayer | <strong>Arrow button:</strong> Get directions | <strong>Mosque names:</strong> Click to view details
            </p>
        </div>
    </div>
</div>
