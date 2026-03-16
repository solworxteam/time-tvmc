<?php
/**
 * Home view – Figma-designed prayer times cards
 * Data: $todayPrayers from PrayerTime::getTodayPrayerTimes()
 */

$isFriday    = date('N') == 5;
$zuhrLabel   = $isFriday ? 'Friday' : 'Dhuhr';
$currentTime = time();

// Group prayer rows by mosque ID
$prayersByMosque = [];
foreach ((array)$todayPrayers as $row) {
    $prayersByMosque[$row['mosque_id']] = $row;
}
$allMosques = Mosque::getAll();

// Build JS mosque data array (no secrets)
$mosqueDataJs = [];
foreach ($allMosques as $m) {
    $mid = $m['id'];
    $p   = $prayersByMosque[$mid] ?? null;
    if (!$p) continue;
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

$prayerLabels = [
    'fajr'    => 'Fajr',
    'zuhr'    => $zuhrLabel,
    'asr'     => 'Asr',
    'maghrib' => 'Maghrib',
    'isha'    => 'Isha',
];

function getNextCurrentPrayer(array $p, array $defs, int $now): array {
    $next    = null;
    $current = null;
    foreach ($defs as $def) {
        $startVal   = $p[$def['start']] ?? '';
        $jamaatVal  = ($def['jamaat'] && !empty($p[$def['jamaat']])) ? $p[$def['jamaat']] : '';
        $compareVal = $jamaatVal ?: $startVal;
        if (empty($compareVal)) continue;
        $ts = strtotime(date('Y-m-d') . ' ' . date('H:i', strtotime($compareVal)));
        if ($ts > $now) {
            if ($next === null) $next = $def['key'];
        } else {
            $current = $def['key'];
        }
    }
    if ($next === null) $next = 'fajr';
    return ['next' => $next, 'current' => $current];
}
?>

<script>
window.MOSQUE_DATA = <?php echo json_encode($mosqueDataJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
window.ZUHR_LABEL  = <?php echo json_encode($zuhrLabel); ?>;
</script>

<style>
#pt-home { background: #f9fafb; }
.dark #pt-home { background: #111827; }
#pt-header { background: linear-gradient(to right, #16a34a, #15803d); color: #fff; padding: 1.5rem 1rem; }
.dark #pt-header { background: linear-gradient(to right, #15803d, #166534); }
#pt-countdown { background: linear-gradient(to right, #22c55e, #16a34a); border-radius: 1rem; color: #fff; padding: 1.5rem; margin-bottom: 1.5rem; display: none; }
.dark #pt-countdown { background: linear-gradient(to right, #16a34a, #15803d); }
#pt-filterbar { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,.07); }
.dark #pt-filterbar { background: #1f2937; border-color: #374151; }
#pt-search-wrap { position: relative; flex: 1; }
#pt-search-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; display: flex; }
#pt-search { width: 100%; padding: .5rem .75rem .5rem 2.5rem; border: 1px solid #d1d5db; border-radius: .5rem; font-size: .875rem; }
.dark #pt-search { background: #374151; border-color: #4b5563; color: #fff; }
#pt-search:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
.pt-select-wrap { position: relative; }
.pt-select-icon { position: absolute; left: .75rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; display: flex; }
.pt-select { height: 2.5rem; min-width: 160px; appearance: none; border: 1px solid #d1d5db; border-radius: .5rem; padding: 0 2rem 0 2.5rem; font-size: .875rem; background: #fff; }
.dark .pt-select { background: #374151; border-color: #4b5563; color: #fff; }
.pt-select:focus { outline: none; border-color: #22c55e; box-shadow: 0 0 0 3px rgba(34,197,94,.15); }
#pt-fmt-btn { height: 2.5rem; width: 2.5rem; border: 1px solid #d1d5db; border-radius: .5rem; background: #fff; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
.dark #pt-fmt-btn { background: #374151; border-color: #4b5563; color: #fff; }
#pt-fmt-btn:hover { background: #f3f4f6; }
.dark #pt-fmt-btn:hover { background: #4b5563; }
.pt-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 1.5rem; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.06); transition: box-shadow .2s; }
.dark .pt-card { background: #1f2937; border-color: #374151; }
.pt-card.is-nearest { border-left: 4px solid #facc15; }
.pt-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,.1); }
.pt-mosque-link { text-decoration: none; color: #111827; }
.dark .pt-mosque-link { color: #f9fafb; }
.pt-card-address { font-size: .82rem; color: #6b7280; margin: .2rem 0 0; }
.dark .pt-card-address { color: #9ca3af; }
.pt-card-meta { font-size: .75rem; color: #9ca3af; margin-top: .25rem; display: flex; flex-wrap: wrap; gap: .4rem .75rem; align-items: center; }
.dark .pt-card-meta { color: #6b7280; }
.pt-grid { display: grid; grid-template-columns: repeat(5,1fr); gap: .75rem; margin-top: 1rem; }
@media(max-width:600px){ .pt-grid { grid-template-columns: repeat(3,1fr); } }
@media(max-width:380px){ .pt-grid { grid-template-columns: repeat(2,1fr); } }
.pt-pcell { border: 1px solid #e5e7eb; border-radius: .75rem; padding: .625rem .5rem; text-align: center; transition: all .15s; }
.dark .pt-pcell { border-color: #4b5563; background: rgba(55,65,81,.4); }
.pt-pcell.is-next { border: 2px solid #22c55e; background: #dcfce7; }
.dark .pt-pcell.is-next { border-color: #16a34a; background: rgba(21,128,61,.25); }
.pt-pcell.is-current { border: 1px solid #93c5fd; background: #eff6ff; }
.dark .pt-pcell.is-current { border-color: #3b82f6; background: rgba(59,130,246,.15); }
.pt-pcell-label { font-size: .7rem; font-weight: 600; color: #6b7280; margin-bottom: .2rem; }
.dark .pt-pcell-label { color: #9ca3af; }
.pt-pcell.is-next .pt-pcell-label { color: #15803d; }
.dark .pt-pcell.is-next .pt-pcell-label { color: #4ade80; }
.pt-pcell.is-current .pt-pcell-label { color: #2563eb; }
.dark .pt-pcell.is-current .pt-pcell-label { color: #60a5fa; }
.pt-start { font-size: .7rem; color: #9ca3af; }
.dark .pt-start { color: #6b7280; }
.pt-pcell.is-next .pt-start { color: rgba(21,128,61,.7); }
.dark .pt-pcell.is-next .pt-start { color: rgba(74,222,128,.6); }
.pt-jamaat { font-size: 1rem; font-weight: 700; color: #111827; }
.dark .pt-jamaat { color: #f9fafb; }
.pt-pcell.is-next .pt-jamaat { color: #166534; }
.dark .pt-pcell.is-next .pt-jamaat { color: #4ade80; }
.pt-pcell.is-current .pt-jamaat { color: #1d4ed8; }
.dark .pt-pcell.is-current .pt-jamaat { color: #60a5fa; }
.pt-next-tag { font-size: .65rem; font-weight: 700; color: #16a34a; margin-top: .15rem; }
.dark .pt-next-tag { color: #4ade80; }
.pt-current-tag { font-size: .65rem; color: #2563eb; margin-top: .15rem; }
.dark .pt-current-tag { color: #60a5fa; }
.pt-fav { background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #facc15; line-height: 1; padding: 0 .1rem; }
.pt-fav:hover { color: #ca8a04; }
.pt-nearest-badge { display: inline-block; font-size: .7rem; font-weight: 600; border: 1px solid #fde68a; background: #fefce8; color: #92400e; border-radius: 999px; padding: .15rem .6rem; }
.dark .pt-nearest-badge { border-color: #b45309; background: rgba(180,83,9,.15); color: #fbbf24; }
.pt-geo-banner { border-radius: 1rem; padding: .75rem 1rem; margin-bottom: 1rem; font-size: .875rem; }
.pt-geo-loading { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.dark .pt-geo-loading { background: rgba(21,128,61,.15); border-color: #15803d; color: #4ade80; }
.pt-geo-error { background: #fefce8; border: 1px solid #fde68a; color: #854d0e; display: flex; gap: .75rem; align-items: flex-start; }
.dark .pt-geo-error { background: rgba(161,98,7,.1); border-color: #78350f; color: #fcd34d; }
#pt-no-results { text-align: center; color: #9ca3af; padding: 3rem 0; display: none; }
</style>

<?php if (empty($todayPrayers)): ?>
<div class="container mt-5">
    <div class="alert alert-info"><strong>No prayer times available for today.</strong></div>
</div>
<?php else: ?>

<div id="pt-home">
    <header id="pt-header">
        <div class="container d-flex align-items-center justify-content-between gap-3" style="max-width:1200px">
            <div>
                <h1 style="font-size:1.75rem;font-weight:700;margin:0">Prayer Times</h1>
                <p style="margin:.25rem 0 0;color:rgba(255,255,255,.85);font-size:.9rem"><?php echo date('l, d F Y'); ?></p>
            </div>
            <button id="pt-dark-btn" aria-label="Toggle dark mode"
                style="background:rgba(255,255,255,.15);border:none;border-radius:.5rem;width:2.25rem;height:2.25rem;
                       display:flex;align-items:center;justify-content:center;cursor:pointer;color:#fff">
                <i id="pt-dark-icon" data-lucide="moon" style="width:18px;height:18px"></i>
            </button>
        </div>
    </header>

    <div class="container py-4" style="max-width:1200px">

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
                        <button class="directions-btn btn btn-sm btn-outline-secondary"
                                data-lat="<?php echo htmlspecialchars($mosque['latitude'] ?? 0); ?>"
                                data-lon="<?php echo htmlspecialchars($mosque['longitude'] ?? 0); ?>"
                                title="Get Directions" aria-label="Get Directions"
                                style="padding:.2rem .5rem;font-size:.85rem">&#8635;</button>
                    </div>
                </div>

                <div class="pt-grid">
                    <?php foreach ($prayerDefs as $def):
                        $key     = $def['key'];
                        $label   = $prayerLabels[$key];
                        $start   = $p[$def['start']] ?? '';
                        $jamaat  = ($def['jamaat'] && !empty($p[$def['jamaat']])) ? $p[$def['jamaat']] : '';
                        $display = $jamaat ?: $start;
                        $isNext  = ($key === $nxt);
                        $isCurr  = ($key === $cur && !$isNext);
                        $cellCls = $isNext ? 'pt-pcell is-next' : ($isCurr ? 'pt-pcell is-current' : 'pt-pcell');
                    ?>
                    <div class="<?php echo $cellCls; ?>" data-prayer="<?php echo $key; ?>">
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

        <p class="text-center text-muted small mt-4" style="font-size:.75rem">
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