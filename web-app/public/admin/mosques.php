<?php
require_once __DIR__ . '/../../app/helpers.php';
require_once __DIR__ . '/../../app/models/Mosque.php';
require_once __DIR__ . '/../../app/models/Parking.php';

requireAdmin();

$mosques = Mosque::getAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $mosqueId = $_POST['mosque_id'] ?? null;

    if ($action === 'update' && $mosqueId) {
        Mosque::update($mosqueId, [
            'name' => $_POST['name'] ?? '',
            'address' => $_POST['address'] ?? '',
            'city' => $_POST['city'] ?? '',
            'latitude' => $_POST['latitude'] ?? '',
            'longitude' => $_POST['longitude'] ?? '',
            'imam' => $_POST['imam'] ?? '',
            'contact' => $_POST['contact'] ?? ''
        ]);

        Parking::update($mosqueId, [
            'spaces' => $_POST['parking_spaces'] ?? 0,
            'has_accessible' => isset($_POST['parking_accessible']) ? 1 : 0,
            'price' => $_POST['parking_price'] ?? 0,
            'notes' => $_POST['parking_notes'] ?? ''
        ]);

        $_SESSION['success'] = 'Mosque updated successfully!';
        header('Location: /admin/mosques.php');
        exit;
    }
}

$editId = $_GET['edit'] ?? null;
$editMosque = null;
$editParking = null;

if ($editId) {
    $editMosque = Mosque::getById($editId);
    $editParking = Parking::getByMosqueId($editId);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Mosques</title>
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
    <h1>Manage Mosques</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>

    <?php if ($editMosque): ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5>Edit: <?php echo sanitize($editMosque['name']); ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="mosque_id" value="<?php echo $editMosque['id']; ?>">

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" value="<?php echo sanitize($editMosque['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Imam</label>
                                    <input type="text" class="form-control" name="imam" value="<?php echo sanitize($editMosque['imam']); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" name="address" value="<?php echo sanitize($editMosque['address']); ?>" required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">City</label>
                                    <input type="text" class="form-control" name="city" value="<?php echo sanitize($editMosque['city']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact</label>
                                    <input type="text" class="form-control" name="contact" value="<?php echo sanitize($editMosque['contact']); ?>">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Latitude</label>
                                    <input type="number" step="0.0001" class="form-control" name="latitude" value="<?php echo sanitize($editMosque['latitude']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Longitude</label>
                                    <input type="number" step="0.0001" class="form-control" name="longitude" value="<?php echo sanitize($editMosque['longitude']); ?>" required>
                                </div>
                            </div>

                            <hr>

                            <h5>Parking Information</h5>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Parking Spaces</label>
                                    <input type="number" class="form-control" name="parking_spaces" value="<?php echo $editParking['spaces'] ?? 0; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Parking Fee (£)</label>
                                    <input type="number" step="0.01" class="form-control" name="parking_price" value="<?php echo $editParking['price'] ?? 0; ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="accessible" name="parking_accessible" <?php echo ($editParking['has_accessible'] ?? 0) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="accessible">Has Accessible Parking</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Parking Notes</label>
                                <textarea class="form-control" name="parking_notes" rows="3"><?php echo $editParking['notes'] ?? ''; ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-success">✓ Save Changes</button>
                            <a href="/admin/mosques.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>City</th>
                        <th>Imam</th>
                        <th>Contact</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mosques as $mosque): ?>
                        <tr>
                            <td><?php echo sanitize($mosque['name']); ?></td>
                            <td><?php echo sanitize($mosque['city']); ?></td>
                            <td><?php echo sanitize($mosque['imam']); ?></td>
                            <td><?php echo sanitize($mosque['contact']); ?></td>
                            <td>
                                <a href="/admin/mosques.php?edit=<?php echo $mosque['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
