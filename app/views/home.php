<?php
/**
 * Home view - Figma-designed prayer times cards
 * Data: $todayPrayers from PrayerTime::getTodayPrayerTimes()
 */

$isFriday    = date('N') == 5;
$zuhrLabel   = $isFriday ? 'Friday Prayer' : 'Dhuhr';
$currentTime = time();

// Group prayer rows by mosque ID
$prayersByMosque = [];
foreach ((array)$todayPrayers as $row) {
    $prayersByMosque[$row['mosque_id']] = $row;
}
$allMosques = Mosque::getAll();

// Build JS mosque data array (no secrets)
$mosqueDataJs = [];
$hasSunrise = false;
foreach ($allMosques as $m) {
    $mid = $m['id'];
    $p   = $prayersByMosque[$mid] ?? null;
    if (!$p) continue;
    $sunrise = $p['sunrise'] ?? '';
    if (!empty($sunrise)) {
        $hasSunrise = true;
    }
    $mosqueDataJs[] = [
        'id'      => (string)$mid,
        'name'    => $m['name'],
        'address' => $m['address'] ?? '',
        'postcode'=> $m['postcode'] ?? '',
        'area'    => $m['area'] ?? '',
        'lat'     => (float)($m['latitude'] ?? 0),
        'lon'     => (float)($m['longitude'] ?? 0),
        'prayers' => [
            'fajr'    => ['start' => $p['fajar_start'] ?? '',  'jamaat' => $p['fajar_jamaat'] ?? ''],
            'sunrise' => ['start' => $sunrise,                 'jamaat' => ''],
            'zuhr'    => ['start' => $p['zuhr_start'] ?? '',   'jamaat' => $p['zuhr_jamaat'] ?? ''],
            'asr'     => ['start' => $p['asr_start'] ?? '',    'jamaat' => $p['asr_jamaat'] ?? ''],
            'maghrib' => ['start' => $p['maghrib'] ?? '',      'jamaat' => ''],
            'isha'    => ['start' => $p['isha_start'] ?? '',   'jamaat' => $p['isha_jamaat'] ?? ''],
        ],
    ];
}

// Prayer definitions
$prayerDefs = [
    ['key' => 'fajr',    'start' => 'fajar_start',  'jamaat' => 'fajar_jamaat'],
    ['key' => 'zuhr',    'start' => 'zuhr_start',   'jamaat' => 'zuhr_jamaat'],
    ['key' => 'asr',     'start' => 'asr_start',    'jamaat' => 'asr_jamaat'],
    ['key' => 'maghrib', 'start' => 'maghrib',       'jamaat' => null],
    ['key' => 'isha',    'start' => 'isha_start',   'jamaat' => 'isha_jamaat'],
];

$displayPrayerDefs = [
    ['key' => 'fajr', 'start' => 'fajar_start', 'jamaat' => 'fajar_jamaat'],
];

if ($hasSunrise) {
    $displayPrayerDefs[] = ['key' => 'sunrise', 'start' => 'sunrise', 'jamaat' => null];
}

$displayPrayerDefs[] = ['key' => 'zuhr', 'start' => 'zuhr_start', 'jamaat' => 'zuhr_jamaat'];
$displayPrayerDefs[] = ['key' => 'asr', 'start' => 'asr_start', 'jamaat' => 'asr_jamaat'];
$displayPrayerDefs[] = ['key' => 'maghrib', 'start' => 'maghrib', 'jamaat' => null];
$displayPrayerDefs[] = ['key' => 'isha', 'start' => 'isha_start', 'jamaat' => 'isha_jamaat'];

$prayerLabels = [
    'fajr'    => 'Fajr',
    'sunrise' => 'Sunrise',
    'zuhr'    => $zuhrLabel,
    'asr'     => 'Asr',
    'maghrib' => 'Maghrib',
    'isha'    => 'Isha',
];

$prayerIcons = [
    'fajr' => 'moon-star',
    'sunrise' => 'sunrise',
    'zuhr' => 'sun',
    'asr' => 'cloud-sun',
    'maghrib' => 'sunset',
    'isha' => 'moon',
];
?>

<script>
window.MOSQUE_DATA = <?php echo json_encode($mosqueDataJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
window.ZUHR_LABEL  = <?php echo json_encode($zuhrLabel); ?>;
</script>

<?php if (empty($todayPrayers)): ?>
<div class="container mt-5">
    <div class="alert alert-info"><strong>No prayer times available for today.</strong></div>
</div>
<?php else: ?>

<div id="pt-home">
    <header id="pt-header">
        <div class="container">
            <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div>
                    <div class="d-flex align-items-center gap-2" style="font-size:1.05rem;font-weight:700">
                        <span class="pt-brand-mark" aria-hidden="true"><i data-lucide="landmark" style="width:16px;height:16px"></i></span>
                        <span>Local Prayer Times</span>
                    </div>
                    <p style="margin:.4rem 0 0;color:rgba(255,255,255,.85);font-size:.9rem">Live prayer times and mosque directions across your area.</p>
                </div>
                <div class="d-flex align-items-center gap-2 ms-auto">
                    <nav class="pt-header-nav" aria-label="Primary navigation">
                        <a class="pt-header-link is-active" href="/" aria-current="page">Home</a>
                        <a class="pt-header-link" href="/mosques.php">Mosques</a>
                        <a class="pt-header-link" href="/nearest.php">Find Nearest</a>
                    </nav>
                    <button id="pt-dark-btn" class="pt-dark-toggle" type="button" aria-label="Toggle dark mode">
                        <i id="pt-dark-icon" data-lucide="moon" style="width:18px;height:18px"></i>
                    </button>
                </div>
            </div>
            <div class="d-flex d-md-none gap-2 flex-wrap mt-3">
                <a class="pt-header-link is-active" href="/" aria-current="page">Home</a>
                <a class="pt-header-link" href="/mosques.php">Mosques</a>
                <a class="pt-header-link" href="/nearest.php">Find Nearest</a>
            </div>
            <div class="mt-4">
                <h1 style="font-size:1.9rem;font-weight:700;margin:0">Prayer Times</h1>
                <p style="margin:.25rem 0 0;color:rgba(255,255,255,.85);font-size:.95rem"><?php echo date('l, d F Y'); ?></p>
            </div>
        </div>
    </header>

    <div class="container py-4">

        <div id="pt-countdown">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3">
                <div>
                    <div style="font-size:.8rem;font-weight:500;opacity:.9;margin-bottom:.15rem">Next Prayer</div>
                    <div id="pt-cd-name" style="font-size:1.75rem;font-weight:700"></div>
                </div>
                <div class="text-start text-sm-end">
                    <div style="font-size:.8rem;font-weight:500;opacity:.9;margin-bottom:.15rem;display:flex;align-items:center;gap:.35rem">
                        <i data-lucide="clock" style="width:14px;height:14px"></i> Time Remaining
                    </div>
                    <div id="pt-cd-time" style="font-size:1.75rem;font-weight:700"></div>
                </div>
            </div>
        </div>

        <div id="pt-geo-loading" class="pt-geo-banner pt-geo-loading" style="display:none">
            Detecting your location for accurate mosque distances&hellip;
        </div>
        <div id="pt-geo-error" class="pt-geo-banner pt-geo-error" style="display:none">
            <i data-lucide="map-pin-off" style="width:18px;height:18px;flex-shrink:0;margin-top:1px"></i>
            <div><strong>Location access denied.</strong> Distances are approximate. Enable location access for accurate sorting.</div>
        </div>

        <div id="pt-fav-count" style="font-size:.85rem;color:#6b7280;margin-bottom:.75rem;display:none">
            <i data-lucide="star" style="width:14px;height:14px;fill:#eab308;color:#eab308;vertical-align:middle;margin-right:.3rem"></i>
            <span id="pt-fav-count-text"></span>
        </div>

        <div id="pt-filterbar">
            <div class="d-flex flex-column flex-md-row gap-3">
                <div id="pt-search-wrap">
                    <span id="pt-search-icon"><i data-lucide="search" style="width:16px;height:16px"></i></span>
                    <input id="pt-search" type="text" placeholder="Search by mosque name or postcode&hellip;" autocomplete="off">
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 flex-wrap align-items-start">
                    <div class="pt-select-wrap">
                        <span class="pt-select-icon"><i data-lucide="map-pin" style="width:16px;height:16px"></i></span>
                        <select id="pt-area" class="pt-select">
                            <option value="all">All Areas</option>
                            <?php
                            $areas = array_unique(array_filter(array_column($allMosques, 'area')));
                            sort($areas);
                            foreach ($areas as $area):
                            ?>
                            <option value="<?php echo htmlspecialchars($area, ENT_QUOTES); ?>"><?php echo htmlspecialchars($area); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="pt-select-wrap">
                        <span class="pt-select-icon"><i data-lucide="sliders-horizontal" style="width:16px;height:16px"></i></span>
                        <select id="pt-sort" class="pt-select">
                            <option value="distance">Sort by Distance</option>
                            <option value="name">Sort by Name</option>
                        </select>
                    </div>
                    <button id="pt-fmt-btn" title="Toggle time format (12h / 24h)">
                        <i id="pt-fmt-icon" data-lucide="clock" style="width:16px;height:16px"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="pt-cards">
            <?php foreach ($allMosques as $mosque):
                $mid  = $mosque['id'];
                $p    = $prayersByMosque[$mid] ?? null;
                if (!$p) continue;
                $nc      = getNextCurrentPrayer($p, $prayerDefs, $currentTime);
                $nxt     = $nc['next'];
                $cur     = $nc['current'];
            ?>
            <div class="pt-card"
                 data-mosque-id="<?php echo htmlspecialchars($mid); ?>"
                 data-name="<?php echo htmlspecialchars(strtolower($mosque['name'])); ?>"
                 data-address="<?php echo htmlspecialchars(strtolower($mosque['address'] ?? '')); ?>"
                 data-postcode="<?php echo htmlspecialchars(strtolower($mosque['postcode'] ?? '')); ?>"
                 data-area="<?php echo htmlspecialchars($mosque['area'] ?? ''); ?>"
                 data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                 data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                 data-distance="99999"
                 data-next="<?php echo htmlspecialchars($nxt); ?>"
                 data-current="<?php echo htmlspecialchars($cur ?? ''); ?>">

                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div style="flex:1;min-width:0">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h3 style="font-size:1rem;font-weight:600;margin:0">
                                <a href="/mosque.php?id=<?php echo urlencode($mid); ?>" class="pt-mosque-link">
                                    <?php echo sanitize($mosque['name']); ?>
                                </a>
                            </h3>
                            <button class="pt-fav" data-mosque-id="<?php echo htmlspecialchars($mid); ?>" aria-label="Toggle favourite">&#9734;</button>
                        </div>
                        <p class="pt-card-address"><?php echo sanitize($mosque['address'] ?? ''); ?></p>
                        <div class="pt-card-meta">
                            <span><?php echo htmlspecialchars($mosque['postcode'] ?? ''); ?></span>
                            <span>&bull;</span>
                            <span>
                                <i data-lucide="map-pin" style="width:11px;height:11px;vertical-align:middle"></i>
                                <span class="pt-dist-km" data-mosque-id="<?php echo htmlspecialchars($mid); ?>">&ndash;</span> km
                            </span>
                            <?php if (!empty($mosque['direction'])): ?>
                            <span>&bull;</span>
                            <span><?php echo htmlspecialchars($mosque['direction']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        <span class="pt-nearest-badge" style="display:none">Nearest</span>
                        <button class="directions-btn pt-icon-btn"
                                data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                                data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                title="Get Directions" aria-label="Get Directions"
                                type="button">
                            <i data-lucide="navigation" style="width:14px;height:14px"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-grid">
                    <?php foreach ($displayPrayerDefs as $def):
                        $key     = $def['key'];
                        $label   = $prayerLabels[$key];
                        $start   = $p[$def['start']] ?? '';
                        $jamaat  = ($def['jamaat'] && !empty($p[$def['jamaat']])) ? $p[$def['jamaat']] : '';
                        $display = $jamaat ?: $start;
                        $isNext  = ($key === $nxt);
                        $isCurr  = ($key === $cur && !$isNext);
                        $cellCls = $isNext ? 'pt-pcell is-next' : ($isCurr ? 'pt-pcell is-current' : 'pt-pcell');
                        $icon    = $prayerIcons[$key] ?? 'clock-3';
                    ?>
                    <div class="<?php echo $cellCls; ?>" data-prayer="<?php echo $key; ?>">
                        <div class="pt-pcell-icon"><i data-lucide="<?php echo htmlspecialchars($icon); ?>" style="width:14px;height:14px"></i></div>
                        <div class="pt-pcell-label"><?php echo htmlspecialchars($label); ?></div>
                        <?php if ($start && $jamaat && $start !== $jamaat): ?>
                        <div class="pt-start"><?php echo htmlspecialchars(substr($start, 0, 5)); ?></div>
                        <?php endif; ?>
                        <div class="pt-jamaat"><?php echo htmlspecialchars(substr($display, 0, 5) ?: chr(8211)); ?></div>
                        <?php if ($isNext): ?>
                        <div class="pt-next-tag">Next</div>
                        <?php elseif ($isCurr): ?>
                        <div class="pt-current-tag">Current</div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>
            <?php endforeach; ?>
        </div>

        <div id="pt-no-results">
            <p style="font-size:1.1rem">No mosques found matching your criteria.</p>
            <p style="font-size:.85rem;margin-top:.5rem">Try adjusting your search or filters.</p>
        </div>

        <p class="pt-legend-note text-center small mt-4" style="font-size:.75rem">
            Small time = Start &nbsp;|&nbsp; <strong>Bold time</strong> = Congregation &nbsp;|&nbsp;
            <span style="color:#16a34a;font-weight:600">Green</span> = Next prayer &nbsp;|&nbsp;
            <span style="color:#2563eb">Blue</span> = Current prayer &nbsp;|&nbsp;
            <span style="color:#92400e">Yellow border</span> = Nearest mosque
        </p>
    </div>

    <div class="modal fade" id="mapChoiceModal" tabindex="-1" aria-labelledby="mapChoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mapChoiceModalLabel">Open Directions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Choose which maps app to use for directions.</div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-primary" id="openAppleMapsBtn">Apple Maps</button>
                    <button type="button" class="btn btn-primary"         id="openGoogleMapsBtn">Google Maps</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>