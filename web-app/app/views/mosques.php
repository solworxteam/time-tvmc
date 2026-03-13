
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
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo sanitize($mosque['name']); ?></h5>
                            <p class="card-text">
                                <strong>City:</strong> <?php echo sanitize($mosque['city']); ?><br>
                                <strong>Address:</strong> <?php echo sanitize($mosque['address']); ?><br>
                                <strong>Imam:</strong> <?php echo sanitize($mosque['imam']); ?><br>
                                <strong>Contact:</strong> <?php echo sanitize($mosque['contact']); ?>
                            </p>
                            <a href="/mosque.php?id=<?php echo $mosque['id']; ?>" class="btn btn-primary">View Prayer Times</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="text-muted mt-3">Showing <?php echo count($mosques); ?> mosque(s)</p>
    <?php endif; ?>
</div>
