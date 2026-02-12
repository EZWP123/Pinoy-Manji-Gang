<?php
/**
 * Session Check Handler
 * Verifies if user is logged in (2.5 Session management)
 */

require_once '../config.php';

header('Content-Type: application/json');

$response = [
    'logged_in' => isset($_SESSION['user_id']),
    'user' => isset($_SESSION['user_id']) ? [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ] : null
];

echo json_encode($response);
?>
