<?php
require_once __DIR__ . '/../../app/helpers.php';

requireAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
    <h1>Welcome, Admin!</h1>

    <div class="row mt-4">
        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Prayer Times</h5>
                    <p class="h2 text-primary">📅</p>
                    <a href="/admin/prayer-times.php" class="btn btn-primary btn-sm">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Mosques</h5>
                    <p class="h2 text-success">🕌</p>
                    <a href="/admin/mosques.php" class="btn btn-success btn-sm">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Upload</h5>
                    <p class="h2 text-warning">📤</p>
                    <a href="/admin/upload.php" class="btn btn-warning btn-sm">Upload Files</a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Website</h5>
                    <p class="h2 text-info">🌐</p>
                    <a href="/" class="btn btn-info btn-sm">View Site</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Quick Start</h5>
            <ul>
                <li><a href="/admin/mosques.php">Manage mosque information</a> - Edit names, addresses, contact details</li>
                <li><a href="/admin/prayer-times.php">Update prayer times</a> - View and edit individual prayer times</li>
                <li><a href="/admin/upload.php">Bulk upload prayer times</a> - Upload CSV files with prayer schedules</li>
            </ul>
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
