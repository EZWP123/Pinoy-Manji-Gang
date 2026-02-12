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
    // Check if user is admin
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'agent') {
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        session_destroy();
        echo json_encode(['success' => false, 'error' => 'Insufficient permissions']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid username or password']);
}

$conn->close();
?>
