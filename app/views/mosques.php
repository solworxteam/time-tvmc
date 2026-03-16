<div class="pt-inner-header">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-lg-end justify-content-between gap-3">
            <div>
                <h1>All Mosques</h1>
                <p>Browse prayer times, mosque details, and directions in the same green visual system as the homepage.</p>
            </div>
            <div class="text-white-50 fw-semibold"><?php echo count($mosques); ?> mosque(s) available</div>
        </div>
    </div>
</div>

<div class="container pb-4">
    <div class="pt-panel p-4 p-lg-5 mb-4">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-lg-9">
                <label for="mosque-search" class="form-label fw-semibold text-body-secondary">Search mosques</label>
                <input id="mosque-search" type="text" name="search" class="form-control form-control-lg" placeholder="Search by name, city, or address..." value="<?php echo isset($_GET['search']) ? sanitize($_GET['search']) : ''; ?>">
            </div>
            <div class="col-lg-3">
                <button type="submit" class="pt-btn-primary w-100">Search Mosques</button>
            </div>
        </form>
    </div>

    <?php if (empty($mosques)): ?>
        <div class="pt-panel p-5 text-center">
            <h2 class="h4 mb-2">No mosques found</h2>
            <p class="text-muted mb-0">Try broadening your search terms or clearing the filter.</p>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($mosques as $mosque): ?>
                <div class="col-lg-6">
                    <div class="pt-panel h-100 p-4">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-3 d-flex align-items-start gap-3">
                                <div class="pt-soft-panel d-inline-flex align-items-center justify-content-center" style="width:2.5rem;height:2.5rem;flex-shrink:0">
                                    <i data-lucide="building-2" style="width:18px;height:18px;color:#16a34a"></i>
                                </div>
                                <div>
                                    <h2 class="h5 mb-1" style="color:#166534"><?php echo sanitize($mosque['name']); ?></h2>
                                    <p class="mb-1 text-muted"><?php echo sanitize($mosque['address']); ?></p>
                                    <p class="mb-0 text-muted"><?php echo sanitize($mosque['postcode']); ?></p>
                                </div>
                            </div>

                            <div class="pt-soft-panel p-3 mb-4">
                                <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                                    <i data-lucide="map-pinned" style="width:14px;height:14px"></i>
                                    <span>Area</span>
                                </div>
                                <div class="fw-semibold"><?php echo !empty($mosque['area']) ? sanitize($mosque['area']) : 'Area not specified'; ?></div>
                            </div>

                            <div class="mt-auto d-flex flex-column flex-sm-row gap-2">
                                <a href="/mosque.php?id=<?php echo urlencode($mosque['id']); ?>" class="pt-btn-primary flex-grow-1">
                                    <i data-lucide="calendar-days" class="pt-inline-icon"></i>
                                    <span>View Prayer Times</span>
                                </a>
                                <a href="https://maps.google.com/maps?daddr=<?php echo urlencode($mosque['address'] . ' ' . $mosque['postcode']); ?>" target="_blank" rel="noopener noreferrer" class="pt-btn-secondary flex-grow-1">
                                    <i data-lucide="navigation" class="pt-inline-icon"></i>
                                    <span>Directions</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
