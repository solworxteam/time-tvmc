<?php
$isFriday = date('N') == 5;
$zuhrLabel = $isFriday ? 'Friday Prayer' : 'Zuhr';
$hasCoords = !empty($mosque['latitude']) && !empty($mosque['longitude']) && $mosque['latitude'] != '0' && $mosque['longitude'] != '0';
$mapQuery = $hasCoords
    ? ($mosque['latitude'] . ',' . $mosque['longitude'])
    : trim(($mosque['address'] ?? '') . ' ' . ($mosque['postcode'] ?? ''));
$mapHref = 'https://maps.google.com/?q=' . urlencode($mapQuery);

$prayers = [
    ['key' => 'fajr', 'name' => 'Fajr', 'icon' => 'moon-star', 'start' => 'fajar_start', 'jamaat' => 'fajar_jamaat'],
];

if (!empty($prayerTimes['sunrise'])) {
    $prayers[] = ['key' => 'sunrise', 'name' => 'Sunrise', 'icon' => 'sunrise', 'start' => 'sunrise', 'jamaat' => null];
}

$prayers[] = ['key' => 'zuhr', 'name' => $zuhrLabel, 'icon' => 'sun', 'start' => 'zuhr_start', 'jamaat' => 'zuhr_jamaat'];
$prayers[] = ['key' => 'asr', 'name' => 'Asr', 'icon' => 'cloud-sun', 'start' => 'asr_start', 'jamaat' => 'asr_jamaat'];
$prayers[] = ['key' => 'maghrib', 'name' => 'Maghrib', 'icon' => 'sunset', 'start' => 'maghrib', 'jamaat' => null];
$prayers[] = ['key' => 'isha', 'name' => 'Isha', 'icon' => 'moon', 'start' => 'isha_start', 'jamaat' => 'isha_jamaat'];

$currentTime = time();
$nextPrayerKey = null;
foreach ($prayers as $prayer) {
    if ($prayer['key'] === 'sunrise') {
        continue;
    }
    $compareField = ($prayer['jamaat'] && !empty($prayerTimes[$prayer['jamaat']])) ? $prayer['jamaat'] : $prayer['start'];
    $compareTime = $prayerTimes[$compareField] ?? null;
    if (empty($compareTime)) {
        continue;
    }
    $prayerTimestamp = strtotime(date('Y-m-d') . ' ' . date('H:i', strtotime($compareTime)));
    if ($prayerTimestamp > $currentTime) {
        $nextPrayerKey = $prayer['key'];
        break;
    }
}
if ($nextPrayerKey === null) {
    $nextPrayerKey = 'fajr';
}
?>

<?php if (!$mosque): ?>
    <div class="container py-5">
        <div class="pt-panel p-5 text-center">
            <h1 class="h3 mb-2">Mosque not found</h1>
            <p class="text-muted mb-4">The mosque you requested could not be loaded.</p>
            <a href="/mosques.php" class="pt-btn-secondary">Back to Mosques</a>
        </div>
    </div>
<?php else: ?>
    <div class="pt-inner-header">
        <div class="container">
            <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 align-items-xl-end">
                <div>
                    <h1><?php echo sanitize($mosque['name']); ?></h1>
                    <p><?php echo sanitize($mosque['address']); ?> <?php echo sanitize($mosque['postcode']); ?></p>
                    <p class="mb-0 text-white-50 fw-semibold">Current time: <?php echo date('g:i a'); ?></p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2">
                    <button class="pt-btn-secondary directions-btn" type="button" data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>" data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>" title="Get Directions" aria-label="Get Directions">
                        <i data-lucide="navigation" class="pt-inline-icon"></i>
                        <span>Get Directions</span>
                    </button>
                    <a href="/mosques.php" class="pt-btn-primary">
                        <i data-lucide="arrow-left" class="pt-inline-icon"></i>
                        <span>Back to Mosques</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-4">
        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="pt-panel p-4 p-lg-5 h-100">
                    <div class="d-flex align-items-center justify-content-between gap-3 mb-4 flex-wrap">
                        <div>
                            <h2 class="h4 mb-1">Prayer Times</h2>
                            <p class="text-muted mb-0"><?php echo date('l, d F Y'); ?></p>
                        </div>
                        <span class="badge rounded-pill text-bg-success px-3 py-2">Next prayer highlighted in green</span>
                    </div>

                    <?php if (!$prayerTimes): ?>
                        <div class="pt-soft-panel p-4 text-center">
                            <p class="mb-0 text-muted">No prayer times available for today.</p>
                        </div>
                    <?php else: ?>
                        <div class="pt-mosque-grid">
                            <?php foreach ($prayers as $prayer): ?>
                                <?php
                                $startTime = $prayerTimes[$prayer['start']] ?? '';
                                $jamaatTime = ($prayer['jamaat'] && !empty($prayerTimes[$prayer['jamaat']])) ? $prayerTimes[$prayer['jamaat']] : '';
                                $displayTime = $jamaatTime ?: $startTime;
                                $isNext = $prayer['key'] === $nextPrayerKey;
                                ?>
                                <div class="pt-mosque-prayer<?php echo $isNext ? ' is-next' : ''; ?>">
                                    <div class="pt-mosque-stat">
                                        <i data-lucide="<?php echo htmlspecialchars($prayer['icon']); ?>" style="width:18px;height:18px;color:<?php echo $isNext ? '#166534' : '#16a34a'; ?>"></i>
                                        <div class="pt-mosque-prayer-label"><?php echo htmlspecialchars($prayer['name']); ?></div>
                                        <?php if ($jamaatTime && $startTime && $jamaatTime !== $startTime): ?>
                                            <div class="pt-mosque-subtime">Start: <?php echo formatTime($startTime); ?></div>
                                        <?php endif; ?>
                                        <div class="pt-mosque-prayer-time"><?php echo !empty($displayTime) ? formatTime($displayTime) : '—'; ?></div>
                                        <div class="pt-mosque-subtime"><?php echo $jamaatTime ? 'Jamaat time' : 'Primary time'; ?></div>
                                        <?php if ($isNext): ?>
                                            <div class="mt-2"><span class="badge rounded-pill text-bg-success">Next</span></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="pt-panel p-4 h-100">
                    <h2 class="h4 mb-4">Parking Information</h2>
                    <div class="row g-3">
                        <div class="col-sm-4 col-xl-12">
                            <div class="pt-soft-panel p-4 pt-mosque-stat">
                                <i data-lucide="car-front" style="width:18px;height:18px;color:#16a34a"></i>
                                <div class="pt-mosque-prayer-label">Onsite Parking</div>
                                <div class="pt-mosque-prayer-time"><?php echo $parking && !empty($parking['onsite_parking']) ? htmlspecialchars($parking['onsite_parking']) : '—'; ?></div>
                                <div class="pt-mosque-subtime">available spaces</div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-xl-12">
                            <div class="pt-soft-panel p-4 pt-mosque-stat">
                                <i data-lucide="accessibility" style="width:18px;height:18px;color:#16a34a"></i>
                                <div class="pt-mosque-prayer-label">Disabled Bays</div>
                                <div class="pt-mosque-prayer-time"><?php echo $parking && strtolower($parking['disable_bays'] ?? '') === 'yes' ? 'Available' : 'Not available'; ?></div>
                                <div class="pt-mosque-subtime">Accessibility support</div>
                            </div>
                        </div>
                        <div class="col-sm-4 col-xl-12">
                            <div class="pt-soft-panel p-4 pt-mosque-stat">
                                <i data-lucide="square-parking" style="width:18px;height:18px;color:#16a34a"></i>
                                <div class="pt-mosque-prayer-label">Off Street Parking</div>
                                <div class="pt-mosque-prayer-time"><?php echo $parking && strtolower($parking['off_street_parking'] ?? '') === 'yes' ? 'Available' : 'Not available'; ?></div>
                                <div class="pt-mosque-subtime"><?php echo !empty($parking['road_name']) ? htmlspecialchars($parking['road_name']) : 'Check nearby roads'; ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="pt-panel p-4 h-100">
                    <h2 class="h4 mb-3">Mosque Details</h2>
                    <div class="pt-soft-panel p-4">
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Name</div>
                            <div class="fw-semibold"><?php echo sanitize($mosque['name']); ?></div>
                        </div>
                        <div class="mb-3">
                            <div class="text-muted small mb-1">Address</div>
                            <div class="fw-semibold"><?php echo sanitize($mosque['address']); ?></div>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Postcode</div>
                            <div class="fw-semibold"><?php echo sanitize($mosque['postcode']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="pt-panel p-4 h-100">
                    <h2 class="h4 mb-3">Location</h2>
                    <a href="<?php echo $mapHref; ?>" target="_blank" rel="noopener noreferrer" class="pt-map-card">
                        <div class="pt-soft-panel p-4 pt-location-card">
                            <div class="pt-soft-panel d-inline-flex align-items-center justify-content-center" style="width:3rem;height:3rem;flex-shrink:0">
                                <i data-lucide="map-pinned" style="width:20px;height:20px;color:#16a34a"></i>
                            </div>
                            <div>
                                <div class="fw-semibold mb-1"><?php echo sanitize($mosque['address']); ?></div>
                                <div class="text-muted"><?php echo sanitize($mosque['postcode']); ?></div>
                                <?php if ($hasCoords): ?>
                                    <div class="text-muted small mt-2">Coordinates: <?php echo htmlspecialchars($mosque['latitude']); ?>, <?php echo htmlspecialchars($mosque['longitude']); ?></div>
                                <?php endif; ?>
                                <div class="pt-location-cta">
                                    <i data-lucide="navigation" class="pt-inline-icon"></i>
                                    <span>Open in maps</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mapChoiceModal" tabindex="-1" aria-labelledby="mapChoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapChoiceModalLabel">Open Directions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Choose which maps app you want to use for directions.</div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-success" id="openAppleMapsBtn">Apple Maps</button>
                    <button type="button" class="btn btn-success" id="openGoogleMapsBtn">Google Maps</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var modalEl = document.getElementById('mapChoiceModal');
            var appleBtn = document.getElementById('openAppleMapsBtn');
            var googleBtn = document.getElementById('openGoogleMapsBtn');

            function openMap(provider, lat, lon) {
                var url = provider === 'apple'
                    ? 'https://maps.apple.com/?daddr=' + lat + ',' + lon + '&dirflg=d'
                    : 'https://maps.google.com/maps?daddr=' + lat + ',' + lon;
                window.open(url, '_blank', 'noopener,noreferrer');
            }

            if (appleBtn && modalEl) {
                appleBtn.addEventListener('click', function () {
                    openMap('apple', modalEl.dataset.lat, modalEl.dataset.lon);
                    if (window.bootstrap) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    }
                });
            }

            if (googleBtn && modalEl) {
                googleBtn.addEventListener('click', function () {
                    openMap('google', modalEl.dataset.lat, modalEl.dataset.lon);
                    if (window.bootstrap) {
                        bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    }
                });
            }

            document.querySelectorAll('.directions-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var lat = this.dataset.lat;
                    var lon = this.dataset.lon;
                    if (!lat || !lon || lat === '0' || lon === '0') {
                        window.open('<?php echo $mapHref; ?>', '_blank', 'noopener,noreferrer');
                        return;
                    }
                    var isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                    if (isIOS && modalEl && window.bootstrap) {
                        modalEl.dataset.lat = lat;
                        modalEl.dataset.lon = lon;
                        bootstrap.Modal.getOrCreateInstance(modalEl).show();
                    } else {
                        openMap('google', lat, lon);
                    }
                });
            });
        })();
    </script>
<?php endif; ?>
