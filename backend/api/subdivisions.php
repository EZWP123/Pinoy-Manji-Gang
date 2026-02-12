<?php
/**
 * Subdivisions API Endpoints
 */

require_once '../config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Get all subdivisions
if ($method === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;

    if ($id) {
        // Get single subdivision with properties count
        $sql = "SELECT s.*, COUNT(p.id) as property_count, SUM(CASE WHEN p.status = 'available' THEN 1 ELSE 0 END) as available_count 
                FROM subdivisions s 
                LEFT JOIN properties p ON s.id = p.subdivision_id 
                WHERE s.id = $id 
                GROUP BY s.id";
    } else {
        // Get all subdivisions with stats
        $sql = "SELECT s.*, COUNT(p.id) as property_count, SUM(CASE WHEN p.status = 'available' THEN 1 ELSE 0 END) as available_count 
                FROM subdivisions s 
                LEFT JOIN properties p ON s.id = p.subdivision_id 
                GROUP BY s.id 
                ORDER BY s.created_at DESC";
    }

    $result = $conn->query($sql);

    if ($result) {
        $subdivisions = [];
        while ($row = $result->fetch_assoc()) {
            $subdivisions[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $subdivisions]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Query failed']);
    }
}

// Add new subdivision
else if ($method === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("INSERT INTO subdivisions (name, description, location, latitude, longitude, total_units, developer_name, contact_info, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sssddiiss",
        $data['name'],
        $data['description'],
        $data['location'],
        $data['latitude'],
        $data['longitude'],
        $data['total_units'],
        $data['developer_name'],
        $data['contact_info'],
        $_SESSION['user_id']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}

else {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?>
