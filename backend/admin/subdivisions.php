<?php
/**
 * Admin Subdivisions Management
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

// Get all subdivisions with property counts
$sql = "SELECT s.*, COUNT(p.id) as property_count, u.full_name as created_by_name
        FROM subdivisions s
        LEFT JOIN properties p ON s.id = p.subdivision_id
        LEFT JOIN users u ON s.created_by = u.id
        GROUP BY s.id
        ORDER BY s.created_at DESC";

$result = $conn->query($sql);
$subdivisions = [];
while ($row = $result->fetch_assoc()) {
    $subdivisions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subdivisions Management - SubdiHousing</title>
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
                            <a class="nav-link active" href="subdivisions.php">
                                <i class="fas fa-map"></i> Subdivisions
                            </a>
                        </li>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Subdivisions Management</h1>
                    <a href="add-subdivision.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Subdivision
                    </a>
                </div>

                <!-- Subdivisions Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Total Units</th>
                                        <th>Properties</th>
                                        <th>Developer</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subdivisions as $subdivision): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($subdivision['name']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($subdivision['location'] ?? 'N/A'); ?></td>
                                        <td><?php echo $subdivision['total_units'] ?? 'N/A'; ?></td>
                                        <td><span class="badge bg-info"><?php echo $subdivision['property_count']; ?></span></td>
                                        <td><?php echo htmlspecialchars($subdivision['developer_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editSubdivision(<?php echo $subdivision['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteSubdivision(<?php echo $subdivision['id']; ?>)">
                                                <i class="fas fa-trash"></i>
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
        function editSubdivision(id) {
            console.log('Edit subdivision:', id);
            // Implementation for edit modal
        }

        function deleteSubdivision(id) {
            if (confirm('Are you sure you want to delete this subdivision?')) {
                fetch(`../api/subdivisions.php?id=${id}`, { method: 'DELETE' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    });
            }
        }
    </script>
</body>
</html>
