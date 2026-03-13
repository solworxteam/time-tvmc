<?php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/models/Mosque.php';
require_once __DIR__ . '/../../app/models/PrayerTime.php';

requireAdmin();

$mosques = Mosque::getAll();
$selectedMosqueId = $_GET['mosque'] ?? null;
$selectedMonth = $_GET['month'] ?? date('m');
$selectedYear = $_GET['year'] ?? date('Y');

$prayerTimes = [];
if ($selectedMosqueId) {
    $prayerTimes = PrayerTime::getByMosqueAndMonth($selectedMosqueId, $selectedMonth, $selectedYear);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Prayer Times</title>
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
                <li class="nav-item"><a class="nav-link" href="/admin/upload.php">Upload Times</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h1>Manage Prayer Times</h1>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <label class="form-label">Mosque</label>
                    <select name="mosque" class="form-select" onchange="this.form.submit()">
                        <option value="">-- Select Mosque --</option>
                        <?php foreach ($mosques as $mosque): ?>
                            <option value="<?php echo $mosque['id']; ?>" <?php echo $selectedMosqueId == $mosque['id'] ? 'selected' : ''; ?>>
                                <?php echo sanitize($mosque['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <input type="month" name="month" class="form-control" value="<?php echo $selectedYear . '-' . $selectedMonth; ?>" onchange="this.form.submit()">
                </div>

                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <?php if ($selectedMosqueId): ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Fajr</th>
                        <th>Zuhr</th>
                        <th>Asr</th>
                        <th>Maghrib</th>
                        <th>Isha</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prayerTimes)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No prayer times for this month</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($prayerTimes as $prayer): ?>
                            <tr>
                                <td><?php echo formatDate($prayer['date']); ?></td>
                                <td><?php echo formatTime($prayer['fajr']); ?></td>
                                <td><?php echo formatTime($prayer['zuhr']); ?></td>
                                <td><?php echo formatTime($prayer['asr']); ?></td>
                                <td><?php echo formatTime($prayer['maghrib']); ?></td>
                                <td><?php echo formatTime($prayer['isha']); ?></td>
                                <td>
                                    <a href="/admin/edit-prayer-time.php?id=<?php echo $prayer['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="mt-3">
            <a href="/admin/upload.php" class="btn btn-success">📤 Bulk Upload Prayer Times</a>
        </p>
    <?php else: ?>
        <div class="alert alert-info">Select a mosque to view and manage prayer times.</div>
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
