<?php
/**
 * Admin Inquiries Management
 */

require_once '../config.php';
require_once '../includes/Auth.php';

$auth = new Auth($conn);

// Check authorization
if (!$auth->isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'agent'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

$user = $auth->getCurrentUser();

// Get all inquiries
$sql = "SELECT i.*, p.unit_number, s.name as subdivision_name
        FROM inquiries i
        JOIN properties p ON i.property_id = p.id
        JOIN subdivisions s ON p.subdivision_id = s.id
        ORDER BY i.created_at DESC";

$result = $conn->query($sql);
$inquiries = [];
while ($row = $result->fetch_assoc()) {
    $inquiries[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries Management - SubdiHousing</title>
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
                        <small class="text-light">Logged in as <?php echo htmlspecialchars($user['username']); ?></small>
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
                        <li class="nav-item">
                            <a class="nav-link" href="properties.php">
                                <i class="fas fa-home"></i> Properties
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="inquiries.php">
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
                <div class="mb-4">
                    <h1 class="h3">Inquiries Management</h1>
                </div>

                <!-- Inquiries Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Property</th>
                                        <th>Subdivision</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inquiries as $inquiry): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($inquiry['visitor_name']); ?></td>
                                        <td><a href="mailto:<?php echo htmlspecialchars($inquiry['visitor_email']); ?>"><?php echo htmlspecialchars($inquiry['visitor_email']); ?></a></td>
                                        <td><?php echo htmlspecialchars($inquiry['visitor_phone'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['unit_number']); ?></td>
                                        <td><?php echo htmlspecialchars($inquiry['subdivision_name']); ?></td>
                                        <td>
                                            <span class="badge bg-info"><?php echo ucfirst($inquiry['status']); ?></span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#viewModal" onclick="viewInquiry(<?php echo $inquiry['id']; ?>)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="../../frontend/js/auth.js"></script>
    <script>
        function viewInquiry(id) {
            console.log('View inquiry:', id);
            // Implementation for view modal
        }
    </script>
</body>
</html>
