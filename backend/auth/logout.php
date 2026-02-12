<?php
/**
 * Logout Handler
 * Destroys user session (2.3 Administrator logout)
 */

require_once '../config.php';
require_once '../includes/Auth.php';

header('Content-Type: application/json');

$auth = new Auth($conn);
$auth->logout();

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);

$conn->close();
?>
