<?php
/**
 * Inquiries API Endpoints
 */

require_once '../config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Get all inquiries
if ($method === 'GET') {
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $sql = "SELECT i.*, p.unit_number FROM inquiries i 
            JOIN properties p ON i.property_id = p.id 
            ORDER BY i.created_at DESC 
            LIMIT $limit";

    $result = $conn->query($sql);

    if ($result) {
        $inquiries = [];
        while ($row = $result->fetch_assoc()) {
            $inquiries[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $inquiries]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Query failed']);
    }
}

// Add new inquiry
else if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $stmt = $conn->prepare("INSERT INTO inquiries (property_id, visitor_name, visitor_email, visitor_phone, message) VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "issss",
        $data['property_id'],
        $data['visitor_name'],
        $data['visitor_email'],
        $data['visitor_phone'],
        $data['message']
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}

// Update inquiry status
else if ($method === 'PUT') {
    if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'agent'])) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $data = json_decode(file_get_contents("php://input"), true);
    $id = intval($data['id']);
    $status = $conn->real_escape_string($data['status']);

    $sql = "UPDATE inquiries SET status = '$status', updated_at = NOW() WHERE id = $id";

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
