<?php
/**
 * Admin Dashboard
 * Main admin panel interface
 */

require_once '../config.php';
require_once '../includes/Auth.php';

$auth = new Auth($conn);

// Check if user is logged in and has admin role (2.6 Unauthorized access prevention)
if (!$auth->isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../../frontend/login.html');
    exit;
}

$user = $auth->getCurrentUser();

// Get statistics (2.4 Role-based access control)
$statsQuery = "SELECT 
    (SELECT COUNT(*) FROM properties WHERE status = 'available') as available_properties,
    (SELECT COUNT(*) FROM properties WHERE status = 'sold') as sold_properties,
    (SELECT COUNT(*) FROM inquiries WHERE status = 'new') as new_inquiries";

$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SubdiHousing</title>
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
                        <small class="text-light">Welcome, <?php echo htmlspecialchars($user['full_name']); ?></small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-chart-line"></i> Dashboard
                            </a>
                        </li>
                        <!-- Subdivisions link removed: focusing on single subdivision -->
                        <li class="nav-item">
                            <a class="nav-link" href="properties.php">
                                <i class="fas fa-home"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="inquiries.php">
                                <i class="fas fa-envelope"></i> Inquiries
                            </a>
                        </li>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <?php endif; ?>
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
                <!-- Top Bar / Header -->
                <header class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">Dashboard</h1>
                        <small class="text-muted">Villa Purita — Admin Panel</small>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-end me-2">
                            <div style="font-weight:700"><?php echo htmlspecialchars($user['full_name']); ?></div>
                            <div class="text-muted" style="font-size:0.85rem;"><i class="fas fa-clock"></i> <span id="currentDate"></span></div>
                        </div>
                        <button class="btn btn-outline-danger" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
                    </div>
                </header>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Available Properties</h6>
                                        <h3 class="mb-0"><?php echo $stats['available_properties']; ?></h3>
                                    </div>
                                    <div class="text-primary" style="font-size: 2rem;">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">Sold Properties</h6>
                                        <h3 class="mb-0"><?php echo $stats['sold_properties']; ?></h3>
                                    </div>
                                    <div class="text-success" style="font-size: 2rem;">
                                        <i class="fas fa-home"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Subdivisions card removed (single subdivision focus) -->

                    <div class="col-md-3 mb-3">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-2">New Inquiries</h6>
                                        <h3 class="mb-0"><?php echo $stats['new_inquiries']; ?></h3>
                                    </div>
                                    <div class="text-warning" style="font-size: 2rem;">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Inquiries</h5>
                    </div>
                    <div class="card-body">
                        <div id="recentInquiries" class="table-responsive">
                            <p class="text-muted">Loading...</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Add New Property</h5>
                                <p class="text-muted small">Create a new property listing</p>
                                <a href="add-property.php" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Property
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- Add New Subdivision removed (single subdivision focus) -->
                </div>
            </main>
        </div>
    </div>

    <style>
        .border-left-primary { border-left: 4px solid #0d6efd; }
        .border-left-success { border-left: 4px solid #198754; }
        .border-left-info { border-left: 4px solid #0dcaf0; }
        .border-left-warning { border-left: 4px solid #ffc107; }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/auth.js"></script>
    <script>
        // Display current date
        document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        // Load recent inquiries
        fetch('../api/inquiries.php?limit=5')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    let html = '<table class="table table-sm"><thead><tr><th>Name</th><th>Property</th><th>Status</th><th>Date</th></tr></thead><tbody>';
                    data.data.forEach(inquiry => {
                        html += `<tr><td>${inquiry.visitor_name}</td><td>#${inquiry.property_id}</td><td><span class="badge bg-info">${inquiry.status}</span></td><td>${new Date(inquiry.created_at).toLocaleDateString()}</td></tr>`;
                    });
                    html += '</tbody></table>';
                    document.getElementById('recentInquiries').innerHTML = html;
                } else {
                    document.getElementById('recentInquiries').innerHTML = '<p class="text-muted">No inquiries yet</p>';
                }
            })
            .catch(error => console.error('Error loading inquiries:', error));
    </script>
</body>
</html>
