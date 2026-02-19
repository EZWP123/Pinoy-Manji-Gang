<?php
/**
 * Listing Requests API
 * Homeowners can submit listing requests. Admins can view them.
 */

require_once '../config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Ensure table exists (lightweight migration)
$createSql = "CREATE TABLE IF NOT EXISTS listing_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    unit_number VARCHAR(50),
    property_type ENUM('house') DEFAULT 'house',
    price DECIMAL(12,2),
    description TEXT,
    contact_info VARCHAR(255),
    images JSON,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($createSql);

if ($method === 'GET') {
    // Admin: view requests
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $sql = "SELECT lr.*, u.username as homeowner_username FROM listing_requests lr LEFT JOIN users u ON lr.user_id = u.id ORDER BY lr.created_at DESC";
    $result = $conn->query($sql);
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

else if ($method === 'POST') {
    // Homeowner submit
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'homeowner') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }
    $images = [];

    // Support multipart/form-data (file uploads) or JSON body
    if (!empty($_FILES['images'])) {
        // multiple files
        foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
            $origName = basename($_FILES['images']['name'][$i]);
            $ext = pathinfo($origName, PATHINFO_EXTENSION);
            $newName = 'lr_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $target = __DIR__ . '/../../uploads/' . $newName;
            if (move_uploaded_file($tmp, $target)) {
                $images[] = 'uploads/' . $newName;
            }
        }

        // read other fields from $_POST
        $title = $_POST['title'] ?? '';
        $unit_number = $_POST['unit_number'] ?? null;
        $property_type = 'house'; // enforce
        $price = $_POST['price'] ?? null;
        $description = $_POST['description'] ?? null;
        $contact_info = $_POST['contact_info'] ?? null;
    } else {
        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $title = $data['title'] ?? '';
        $unit_number = $data['unit_number'] ?? null;
        $property_type = 'house';
        $price = $data['price'] ?? null;
        $description = $data['description'] ?? null;
        $contact_info = $data['contact_info'] ?? null;
    }

    $imagesJson = json_encode($images);

    $stmt = $conn->prepare("INSERT INTO listing_requests (user_id, title, unit_number, property_type, price, description, contact_info, images) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('isssdsss', $_SESSION['user_id'], $title, $unit_number, $property_type, $price, $description, $contact_info, $imagesJson);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    exit;
}

else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?>
