<?php
/**
 * Login Handler
 * Authenticates user credentials (2.2 Administrator login)
 */

require_once '../config.php';
require_once '../includes/Auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(['success' => false, 'error' => 'Username and password are required']);
    exit;
}

$auth = new Auth($conn);

// Attempt login
if ($auth->login($data['username'], $data['password'])) {
    // Regenerate session id again just after login call (extra safety)
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    // Return success with role so frontend can redirect appropriately
    echo json_encode(['success' => true, 'message' => 'Login successful', 'role' => $_SESSION['role']]);
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
}

$conn->close();
?>
