<?php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/models/PrayerTime.php';
require_once __DIR__ . '/../../app/models/Mosque.php';

requireAdmin();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file']['tmp_name'];
        
        if (($handle = fopen($file, 'r')) !== FALSE) {
            $header = fgetcsv($handle);
            $count = 0;
            
            while (($row = fgetcsv($handle)) !== FALSE) {
                if (count($row) >= 7) {
                    $mosqueId = $row[0];
                    $date = $row[1];
                    $fajr = $row[2];
                    $zuhr = $row[3];
                    $asr = $row[4];
                    $maghrib = $row[5];
                    $isha = $row[6];
                    
                    try {
                        PrayerTime::insertOrUpdate($mosqueId, $date, [
                            'fajr' => $fajr,
                            'zuhr' => $zuhr,
                            'asr' => $asr,
                            'maghrib' => $maghrib,
                            'isha' => $isha
                        ]);
                        $count++;
                    } catch (Exception $e) {
                        error_log("Error uploading prayer time: " . $e->getMessage());
                    }
                }
            }
            
            fclose($handle);
            $message = "Successfully uploaded $count prayer times!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Upload Prayer Times</title>
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
    <h1>Upload Prayer Times</h1>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">📤 Upload CSV File</h5>
                    <p class="small text-muted">Upload a CSV file with prayer times in the format below:</p>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Select CSV File</label>
                            <input type="file" class="form-control" name="csv_file" accept=".csv" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Upload File</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CSV Format</h5>
                    <p class="small">Your CSV file should have these columns:</p>
                    <pre class="small">mosque_id,date,fajr,zuhr,asr,maghrib,isha
1,2025-05-01,05:30,12:45,15:30,19:00,20:15
2,2025-05-01,05:32,12:47,15:35,19:05,20:20</pre>
                    <p class="small text-muted mt-2">
                        <strong>Note:</strong> Times should be in 24-hour format (HH:MM)
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
        <p>&copy; 2025 UK Prayer Times Admin</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
