<?php
/**
 * Admin Properties Management
 */

require_once '../config.php';
require_once '../includes/Auth.php';

$auth = new Auth($conn);

// Check authorization (2.6 Unauthorized access prevention, 2.4 Role-based access control)
if (!$auth->isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: ../../frontend/login.html');
    exit;
}

$user = $auth->getCurrentUser();

// Get all properties
$sql = "SELECT p.*, s.name as subdivision_name, u.full_name as homeowner_name
        FROM properties p
        JOIN subdivisions s ON p.subdivision_id = s.id
    LEFT JOIN users u ON p.homeowner_id = u.id
        ORDER BY p.created_at DESC";

$result = $conn->query($sql);
$properties = [];
while ($row = $result->fetch_assoc()) {
    $properties[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties Management - SubdiHousing</title>
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
                        <!-- Subdivisions removed (single subdivision) -->
                        <li class="nav-item">
                            <a class="nav-link active" href="properties.php">
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
                    <h1 class="h3">Properties Management</h1>
                    <a href="add-property.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Property
                    </a>
                </div>

                <!-- Properties Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Unit#</th>
                                        <th>Subdivision</th>
                                        <th>Status</th>
                                        <th>Price</th>
                                        <th>Homeowner</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($properties as $property): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($property['unit_number']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($property['subdivision_name']); ?></td>
                                        <!-- property_type removed (all houses) -->
                                        <td>
                                            <span class="badge badge-<?php echo $property['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $property['status'])); ?>
                                            </span>
                                        </td>
                                        <td>₱<?php echo number_format($property['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($property['homeowner_name'] ?? 'N/A'); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editModal" onclick="editProperty(<?php echo $property['id']; ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteProperty(<?php echo $property['id']; ?>)">
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
        function editProperty(id) {
            console.log('Edit property:', id);
            // Implementation for edit modal
        }

        function deleteProperty(id) {
            if (confirm('Are you sure you want to delete this property?')) {
                fetch(`../api/properties.php?id=${id}`, { method: 'DELETE' })
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
