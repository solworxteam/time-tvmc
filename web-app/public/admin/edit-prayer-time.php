<?php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/models/PrayerTime.php';
require_once __DIR__ . '/../../app/models/Mosque.php';

requireAdmin();

$success = '';
$prayerId = $_GET['id'] ?? null;
$prayer = null;

if ($prayerId) {
    $prayer = PrayerTime::getById($prayerId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $prayerId) {
    PrayerTime::update($prayerId, [
        'fajr' => $_POST['fajr'] ?? '',
        'zuhr' => $_POST['zuhr'] ?? '',
        'asr' => $_POST['asr'] ?? '',
        'maghrib' => $_POST['maghrib'] ?? '',
        'isha' => $_POST['isha'] ?? ''
    ]);
    
    $prayer = PrayerTime::getById($prayerId);
    $success = 'Prayer times updated successfully!';
}

$mosque = null;
if ($prayer) {
    $mosque = Mosque::getById($prayer['mosque_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Prayer Time</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="/">🕌 Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/admin/dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/prayer-times.php">Prayer Times</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/mosques.php">Mosques</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!$prayer): ?>
        <div class="alert alert-danger">Prayer time not found.</div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5><?php echo sanitize($mosque['name']); ?> - <?php echo formatDate($prayer['date']); ?></h5>
                    </div>

                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Fajr (Dawn)</label>
                                <input type="time" class="form-control" name="fajr" value="<?php echo substr($prayer['fajr'], 0, 5); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Zuhr (Midday)</label>
                                <input type="time" class="form-control" name="zuhr" value="<?php echo substr($prayer['zuhr'], 0, 5); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Asr (Afternoon)</label>
                                <input type="time" class="form-control" name="asr" value="<?php echo substr($prayer['asr'], 0, 5); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Maghrib (Sunset)</label>
                                <input type="time" class="form-control" name="maghrib" value="<?php echo substr($prayer['maghrib'], 0, 5); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Isha (Night)</label>
                                <input type="time" class="form-control" name="isha" value="<?php echo substr($prayer['isha'], 0, 5); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-success">✓ Save Changes</button>
                            <a href="/admin/prayer-times.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p>&copy; 2025 UK Prayer Times Admin</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
