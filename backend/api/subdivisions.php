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

else {
    // Only GET is allowed for subdivisions in single-subdivision mode
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}

$conn->close();
?>
