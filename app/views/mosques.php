
<div class="container mt-5">
    <h1>All Mosques</h1>
    
    <div class="mb-4">
        <form method="GET" class="row g-2">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Search by name, city, or address..." value="<?php echo isset($_GET['search']) ? sanitize($_GET['search']) : ''; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
        </form>
    </div>

    <?php if (empty($mosques)): ?>
        <div class="alert alert-info">No mosques found.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($mosques as $mosque): ?>
                <div class="col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary"><?php echo sanitize($mosque['name']); ?></h5>
                            <p class="card-text">
                                <strong>📍 Address:</strong> <?php echo sanitize($mosque['address']); ?><br>
                                <strong>📬 Postcode:</strong> <?php echo sanitize($mosque['postcode']); ?>
                            </p>
                            <div class="mt-3 d-grid gap-2 d-sm-flex">
                                <a href="/mosque.php?id=<?php echo urlencode($mosque['id']); ?>" class="btn btn-primary">
                                    🕌 View Prayer Times
                                </a>
                                <a href="https://maps.google.com/maps?daddr=<?php echo urlencode($mosque['address'] . ' ' . $mosque['postcode']); ?>" target="_blank" class="btn btn-outline-secondary directions-link">
                                    <svg class="dir-icon" viewBox="0 0 64 64" aria-hidden="true" focusable="false">
                                        <rect x="12" y="12" width="40" height="40" rx="4" transform="rotate(45 32 32)" fill="#2fa6d7"></rect>
                                        <path d="M22 38v-8c0-3.3 2.7-6 6-6h10v-6l12 10-12 10v-6h-8v6z" fill="#111"></path>
                                    </svg>
                                    Directions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-muted mt-3">Showing <?php echo count($mosques); ?> mosque(s)</p>
    <?php endif; ?>
</div>
