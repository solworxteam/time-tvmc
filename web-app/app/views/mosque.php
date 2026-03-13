
<div class="container mt-5">
    <?php if (!$mosque): ?>
        <div class="alert alert-danger">Mosque not found.</div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-8">
                <h1><?php echo sanitize($mosque['name']); ?></h1>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Information</h5>
                        <p><strong>City:</strong> <?php echo sanitize($mosque['city']); ?></p>
                        <p><strong>Address:</strong> <?php echo sanitize($mosque['address']); ?></p>
                        <p><strong>Imam:</strong> <?php echo sanitize($mosque['imam']); ?></p>
                        <p><strong>Contact:</strong> <?php echo sanitize($mosque['contact']); ?></p>
                        <p><strong>Coordinates:</strong> <?php echo sanitize($mosque['latitude']); ?>, <?php echo sanitize($mosque['longitude']); ?></p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Prayer Times - <?php echo formatDate(date('Y-m-d')); ?></h5>
                        
                        <?php if (!$prayerTimes): ?>
                            <p class="text-muted">No prayer times available for today.</p>
                        <?php else: ?>
                            <table class="table">
                                <tr>
                                    <td><strong>Fajr (Dawn):</strong></td>
                                    <td><?php echo formatTime($prayerTimes['fajr']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Zuhr (Midday):</strong></td>
                                    <td><?php echo formatTime($prayerTimes['zuhr']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Asr (Afternoon):</strong></td>
                                    <td><?php echo formatTime($prayerTimes['asr']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Maghrib (Sunset):</strong></td>
                                    <td><?php echo formatTime($prayerTimes['maghrib']); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Isha (Night):</strong></td>
                                    <td><?php echo formatTime($prayerTimes['isha']); ?></td>
                                </tr>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($parking): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Parking</h5>
                            <p><strong>Spaces:</strong> <?php echo $parking['spaces']; ?></p>
                            <p><strong>Accessible Parking:</strong> <?php echo $parking['has_accessible'] ? 'Yes' : 'No'; ?></p>
                            <p><strong>Cost:</strong> <?php echo $parking['price'] ? '£' . $parking['price'] : 'Free'; ?></p>
                            <p><strong>Notes:</strong> <?php echo sanitize($parking['notes']); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Location</h5>
                        <div id="map" style="height: 300px; background: #e9ecef; display: flex; align-items: center; justify-content: center; border-radius: 0.25rem;">
                            <p class="text-muted">📍 <?php echo sanitize($mosque['city']); ?></p>
                        </div>
                        <p class="small text-muted mt-2">Latitude: <?php echo sanitize($mosque['latitude']); ?></p>
                        <p class="small text-muted">Longitude: <?php echo sanitize($mosque['longitude']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
