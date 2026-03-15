<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-2">Today's Prayer Times</h1>
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
                            ['name' => 'Fajr', 'key' => 'fajar_start', 'start' => $prayers['fajar_start'], 'jamaat' => $prayers['fajar_jamaat'] ?? null],
                            ['name' => $zuhrLabel, 'key' => 'zuhr_start', 'start' => $prayers['zuhr_start'], 'jamaat' => $prayers['zuhr_jamaat'] ?? null],
                            ['name' => 'Asr', 'key' => 'asr_start', 'start' => $prayers['asr_start'], 'jamaat' => $prayers['asr_jamaat'] ?? null],
                            ['name' => 'Maghrib', 'key' => 'maghrib', 'start' => $prayers['maghrib'], 'jamaat' => null],
                            ['name' => 'Isha', 'key' => 'isha_start', 'start' => $prayers['isha_start'], 'jamaat' => $prayers['isha_jamaat'] ?? null],
                        ];

                        $nextPrayerKey = null;
                        foreach ($prayerTimes as $prayer) {
                            $comparisonTime = !empty($prayer['jamaat']) ? $prayer['jamaat'] : $prayer['start'];
                            if (empty($comparisonTime)) {
                                continue;
                            }

                            $prayerTimeStr = date('H:i', strtotime($comparisonTime));
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
                        <td class="text-center">
                            <div class="<?php echo $nextPrayerKey === 'fajar_start' ? 'next-prayer-chip' : 'prayer-time-stack'; ?>">
                                <span><?php echo formatTime($prayers['fajar_start']); ?></span>
                                <strong class="d-block"><?php echo formatTime($prayers['fajar_jamaat']); ?></strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="<?php echo $nextPrayerKey === 'zuhr_start' ? 'next-prayer-chip' : 'prayer-time-stack'; ?>">
                                <span><?php echo formatTime($prayers['zuhr_start']); ?></span>
                                <strong class="d-block"><?php echo formatTime($prayers['zuhr_jamaat']); ?></strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="<?php echo $nextPrayerKey === 'asr_start' ? 'next-prayer-chip' : 'prayer-time-stack'; ?>">
                                <span><?php echo formatTime($prayers['asr_start']); ?></span>
                                <strong class="d-block"><?php echo formatTime($prayers['asr_jamaat']); ?></strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="<?php echo $nextPrayerKey === 'maghrib' ? 'next-prayer-chip next-prayer-chip-single' : 'prayer-time-stack'; ?>">
                                <span><?php echo formatTime($prayers['maghrib']); ?></span>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="<?php echo $nextPrayerKey === 'isha_start' ? 'next-prayer-chip' : 'prayer-time-stack'; ?>">
                                <span><?php echo formatTime($prayers['isha_start']); ?></span>
                                <strong class="d-block"><?php echo formatTime($prayers['isha_jamaat']); ?></strong>
                            </div>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm directions-btn" type="button" 
                                    data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" 
                                    data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                    title="Get Directions"
                                    aria-label="Get Directions">
                                <span class="dir-arrow" aria-hidden="true">↪</span>
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
                    ['name' => 'Fajr',        'key' => 'fajar_start', 'start' => $prayersCard['fajar_start'], 'jamaat' => $prayersCard['fajar_jamaat'] ?? null],
                    ['name' => $zuhrLabel,    'key' => 'zuhr_start', 'start' => $prayersCard['zuhr_start'], 'jamaat' => $prayersCard['zuhr_jamaat'] ?? null],
                    ['name' => 'Asr',         'key' => 'asr_start', 'start' => $prayersCard['asr_start'], 'jamaat' => $prayersCard['asr_jamaat'] ?? null],
                    ['name' => 'Maghrib',     'key' => 'maghrib', 'start' => $prayersCard['maghrib'], 'jamaat' => null],
                    ['name' => 'Isha',        'key' => 'isha_start', 'start' => $prayersCard['isha_start'], 'jamaat' => $prayersCard['isha_jamaat'] ?? null],
                ];
                $nextPrayerCardKey = null;
                foreach ($cardPrayerTimes as $cp) {
                    $comparisonTime = !empty($cp['jamaat']) ? $cp['jamaat'] : $cp['start'];
                    if (empty($comparisonTime)) {
                        continue;
                    }

                    $cardPrayerTimeStr = date('H:i', strtotime($comparisonTime));
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
                            <button class="btn btn-sm directions-btn" type="button"
                                    data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                                    data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                    title="Get Directions"
                                    aria-label="Get Directions">
                                <span class="dir-arrow" aria-hidden="true">↪</span>
                            </button>
                        </div>
                    </div>
                    <div class="row row-cols-3 g-2 text-center small">
                        <div class="col">
                            <div class="<?php echo $nextPrayerCardKey === 'fajar_start' ? 'next-prayer-card-chip' : 'prayer-card-stack'; ?>">
                                <div class="text-muted prayer-col-label">Fajr</div>
                                <div><?php echo formatTime($prayersCard['fajar_start']); ?></div>
                                <strong><?php echo formatTime($prayersCard['fajar_jamaat']); ?></strong>
                            </div>
                        </div>
                        <div class="col">
                            <div class="<?php echo $nextPrayerCardKey === 'zuhr_start' ? 'next-prayer-card-chip' : 'prayer-card-stack'; ?>">
                                <div class="text-muted prayer-col-label"><?php echo $zuhrLabel; ?></div>
                                <div><?php echo formatTime($prayersCard['zuhr_start']); ?></div>
                                <strong><?php echo formatTime($prayersCard['zuhr_jamaat']); ?></strong>
                            </div>
                        </div>
                        <div class="col">
                            <div class="<?php echo $nextPrayerCardKey === 'asr_start' ? 'next-prayer-card-chip' : 'prayer-card-stack'; ?>">
                                <div class="text-muted prayer-col-label">Asr</div>
                                <div><?php echo formatTime($prayersCard['asr_start']); ?></div>
                                <strong><?php echo formatTime($prayersCard['asr_jamaat']); ?></strong>
                            </div>
                        </div>
                        <div class="col">
                            <div class="<?php echo $nextPrayerCardKey === 'maghrib' ? 'next-prayer-card-chip next-prayer-card-chip-single' : 'prayer-card-stack'; ?>">
                                <div class="text-muted prayer-col-label">Maghrib</div>
                                <div><?php echo formatTime($prayersCard['maghrib']); ?></div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="<?php echo $nextPrayerCardKey === 'isha_start' ? 'next-prayer-card-chip' : 'prayer-card-stack'; ?>">
                                <div class="text-muted prayer-col-label">Isha</div>
                                <div><?php echo formatTime($prayersCard['isha_start']); ?></div>
                                <strong><?php echo formatTime($prayersCard['isha_jamaat']); ?></strong>
                            </div>
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
            .prayer-col-label {
                font-size: 0.7rem;
            }
            .prayer-time-stack {
                display: inline-flex;
                flex-direction: column;
                align-items: center;
                gap: 0.15rem;
            }
            .next-prayer-chip {
                display: inline-flex;
                flex-direction: column;
                align-items: center;
                gap: 0.15rem;
                min-width: 86px;
                padding: 0.45rem 0.9rem;
                border-radius: 999px;
                background: linear-gradient(135deg, #29b765 0%, #239656 100%);
                color: #ffffff;
                font-weight: 700;
                box-shadow: 0 10px 22px rgba(35, 150, 86, 0.2);
            }
            .next-prayer-chip span,
            .next-prayer-chip strong {
                color: inherit;
            }
            .next-prayer-chip-single {
                justify-content: center;
                min-height: 52px;
            }
            .prayer-card-stack {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.15rem;
            }
            .next-prayer-card-chip {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.15rem;
                height: 100%;
                padding: 0.5rem 0.35rem;
                border-radius: 16px;
                background: linear-gradient(135deg, #29b765 0%, #239656 100%);
                color: #ffffff;
                box-shadow: 0 10px 22px rgba(35, 150, 86, 0.2);
            }
            .next-prayer-card-chip .prayer-col-label,
            .next-prayer-card-chip div,
            .next-prayer-card-chip strong {
                color: inherit !important;
            }
            .next-prayer-card-chip-single {
                min-height: 100%;
            }
            .sticky-top {
                top: 0;
                z-index: 100;
            }
            
            .prayer-row.nearest td {
                background-color: #fff3cd !important;
            }
            
            .table-hover tbody tr.prayer-row.nearest:hover td {
                background-color: #ffe69c !important;
            }

            .card.prayer-row.nearest,
            .card.prayer-row.nearest .card-body {
                background-color: #fff3cd !important;
            }

            .card.prayer-row.nearest:hover,
            .card.prayer-row.nearest:hover .card-body {
                background-color: #ffe69c !important;
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

        <div class="modal fade" id="mapChoiceModal" tabindex="-1" aria-labelledby="mapChoiceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="mapChoiceModalLabel">Open Directions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Choose which maps app you want to use for directions.
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-outline-primary" id="openAppleMapsBtn">Apple Maps</button>
                        <button type="button" class="btn btn-primary" id="openGoogleMapsBtn">Google Maps</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const locationStatusEl = document.getElementById('location-status');
            const mapChoiceModalEl = document.getElementById('mapChoiceModal');
            const openAppleMapsBtn = document.getElementById('openAppleMapsBtn');
            const openGoogleMapsBtn = document.getElementById('openGoogleMapsBtn');

            function openMapLink(provider, lat, lon) {
                const targetUrl = provider === 'apple'
                    ? `https://maps.apple.com/?daddr=${lat},${lon}&dirflg=d`
                    : `https://maps.google.com/maps?daddr=${lat},${lon}`;

                window.open(targetUrl, '_blank');
            }

            openAppleMapsBtn.addEventListener('click', function() {
                openMapLink('apple', mapChoiceModalEl.dataset.lat, mapChoiceModalEl.dataset.lon);
                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(mapChoiceModalEl).hide();
                }
            });

            openGoogleMapsBtn.addEventListener('click', function() {
                openMapLink('google', mapChoiceModalEl.dataset.lat, mapChoiceModalEl.dataset.lon);
                if (typeof bootstrap !== 'undefined') {
                    bootstrap.Modal.getOrCreateInstance(mapChoiceModalEl).hide();
                }
            });

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
                    if (typeof bootstrap !== 'undefined') {
                        mapChoiceModalEl.dataset.lat = lat;
                        mapChoiceModalEl.dataset.lon = lon;
                        bootstrap.Modal.getOrCreateInstance(mapChoiceModalEl).show();
                    } else {
                        openMapLink('apple', lat, lon);
                    }
                    return;
                }

                openMapLink('google', lat, lon);
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
                <strong>Start time:</strong> Main prayer time | <strong>Bold time:</strong> Congregation times | <strong>Green prayer chip:</strong> Next upcoming prayer | <strong>Turn arrow:</strong> Get directions | <strong>Mosque names:</strong> Click to view details
            </p>
        </div>
    </div>
</div>
