<?php
/**
 * Add Property Form
 */

require_once '../config.php';
require_once '../includes/Auth.php';

$auth = new Auth($conn);

if (!$auth->isLoggedIn() || !in_array($_SESSION['role'], ['admin', 'agent'])) {
    header('Location: ../../frontend/login.html');
    exit;
}

// Get subdivisions for dropdown
$subdivisions_result = $conn->query("SELECT id, name FROM subdivisions ORDER BY name");
$subdivisions = [];
while ($row = $subdivisions_result->fetch_assoc()) {
    $subdivisions[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - SubdiHousing</title>
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
                            <a class="nav-link" href="properties.php">
                                <i class="fas fa-home"></i> Properties
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
                    <h1 class="h3">Add New Property</h1>
                </div>

                <div class="card">
                    <div class="card-body">
                        <form id="propertyForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Subdivision *</label>
                                    <select class="form-select" name="subdivision_id" required>
                                        <option value="">Select Subdivision</option>
                                        <?php foreach ($subdivisions as $sub): ?>
                                        <option value="<?php echo $sub['id']; ?>"><?php echo htmlspecialchars($sub['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Unit Number *</label>
                                    <input type="text" class="form-control" name="unit_number" placeholder="e.g., A-101" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Property Type *</label>
                                    <select class="form-select" name="property_type" required>
                                        <option value="">Select Type</option>
                                        <option value="house">House</option>
                                        <option value="lot">Lot</option>
                                        <option value="condo">Condo</option>
                                        <option value="apartment">Apartment</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status *</label>
                                    <select class="form-select" name="status" required>
                                        <option value="">Select Status</option>
                                        <option value="occupied">Occupied</option>
                                        <option value="vacant">Vacant</option>
                                        <option value="for_sale">For Sale</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (₱)</label>
                                    <input type="number" class="form-control" name="price" placeholder="0.00" step="0.01">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Area (sqm)</label>
                                    <input type="number" class="form-control" name="area_sqm" placeholder="0.00" step="0.01">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Bedrooms</label>
                                    <input type="number" class="form-control" name="bedrooms" min="0">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Bathrooms</label>
                                    <input type="number" class="form-control" name="bathrooms" min="0">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Latitude</label>
                                    <input type="number" class="form-control" name="latitude" placeholder="14.5995" step="0.000001">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="number" class="form-control" name="longitude" placeholder="120.9842" step="0.000001">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="4" placeholder="Property description"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Property
                                </button>
                                <a href="properties.php" class="btn btn-secondary">
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
        document.getElementById('propertyForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('../api/properties.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('Property added successfully!');
                    window.location.href = 'properties.php';
                } else {
                    alert('Error: ' + result.error);
                }
            });
        });
    </script>
</body>
</html>
