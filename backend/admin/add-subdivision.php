<?php
/**
 * Add Subdivision Form
 */

require_once '../config.php';
require_once '../includes/Auth.php';

$auth = new Auth($conn);

if (!$auth->isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../../frontend/login.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subdivision - SubdiHousing</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../frontend/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-2 sidebar">
                <div class="sticky-top pt-3">
                    <div class="text-white mb-4 ps-3">
                        <h5 class="mb-1"><i class="fas fa-user-shield"></i> Admin Panel</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="subdivisions.php">
                                <i class="fas fa-map"></i> Subdivisions
                            </a>
                        </li>
                        <li class="nav-item border-top mt-3 pt-3">
                            <a class="nav-link text-danger" href="javascript:logout()">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 main-content">
                <div class="mb-4">
                    <h1 class="h3">Add New Subdivision</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="subdivisionForm">
                            <div class="mb-3">
                                <label class="form-label">Subdivision Name *</label>
                                <input type="text" class="form-control" name="name" placeholder="Enter subdivision name" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Location *</label>
                                <input type="text" class="form-control" name="location" placeholder="City/Municipality" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="number" class="form-control" name="latitude" placeholder="14.5995" step="0.000001">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Longitude</label>
                                    <input type="number" class="form-control" name="longitude" placeholder="120.9842" step="0.000001">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Total Units</label>
                                <input type="number" class="form-control" name="total_units" min="0">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Developer Name</label>
                                    <input type="text" class="form-control" name="developer_name" placeholder="Developer company name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contact Info</label>
                                    <input type="text" class="form-control" name="contact_info" placeholder="Phone or email">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Subdivision description"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Subdivision
                                </button>
                                <a href="subdivisions.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/auth.js"></script>
    <script>
        document.getElementById('subdivisionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('../api/subdivisions.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Subdivision added successfully!');
                    window.location.href = 'subdivisions.php';
                } else {
                    alert('Error: ' + result.error);
                }
            });
        });
    </script>
</body>
</html>
