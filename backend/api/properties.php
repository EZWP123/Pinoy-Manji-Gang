<?php
/**
 * Properties API Endpoints
 */

require_once '../config.php';
require_once '../includes/Database.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Get all properties or by subdivision
if ($method === 'GET') {
    $subdivision_id = isset($_GET['subdivision_id']) ? intval($_GET['subdivision_id']) : null;
    $status = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : null;

    $sql = "SELECT * FROM properties WHERE 1=1";

    if ($subdivision_id) {
        $sql .= " AND subdivision_id = $subdivision_id";
    }

    if ($status) {
        $sql .= " AND status = '$status'";
    }

    $sql .= " ORDER BY created_at DESC";

    $result = $conn->query($sql);

    if ($result) {
        $properties = [];
        while ($row = $result->fetch_assoc()) {
            $properties[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $properties]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Query failed']);
    }
}

// Add new property
else if ($method === 'POST') {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'agent'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("INSERT INTO properties (subdivision_id, unit_number, property_type, status, price, area_sqm, bedrooms, bathrooms, description, latitude, longitude, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "isssdddiiids",
        $data['subdivision_id'],
        $data['unit_number'],
        $data['property_type'],
        $data['status'],
        $data['price'],
        $data['area_sqm'],
        $data['bedrooms'],
        $data['bathrooms'],
        $data['description'],
        $data['latitude'],
        $data['longitude'],
        $_SESSION['user_id']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}

// Update property
else if ($method === 'PUT') {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'agent'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);

    $sql = "UPDATE properties SET ";
    $updates = [];

    if (isset($data['status'])) $updates[] = "status = '" . $conn->real_escape_string($data['status']) . "'";
    if (isset($data['price'])) $updates[] = "price = " . floatval($data['price']);
    if (isset($data['description'])) $updates[] = "description = '" . $conn->real_escape_string($data['description']) . "'";

    if (empty($updates)) {
        echo json_encode(['success' => false, 'error' => 'No data to update']);
        exit;
    }

    $sql .= implode(', ', $updates) . " WHERE id = $id";

    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
}

else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?>
