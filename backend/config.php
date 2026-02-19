<?php
/**
 * Database Configuration
 * Subdivision Housing Availability and Mapping System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'subdi_housing_system');

// Establish connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8");

// Application settings
define('SITE_URL', 'http://localhost/subdi-housing/');
define('UPLOAD_PATH', './uploads/');
define('MAX_UPLOAD_SIZE', 5242880); // 5MB

// Session configuration
// Increase session lifetime and configure secure cookie params before starting session
ini_set('session.gc_maxlifetime', 3600); // 1 hour

$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => 3600,
    'path' => $cookieParams['path'] ?? '/',
    'domain' => $cookieParams['domain'] ?? '',
    // Set to true when using HTTPS in production
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
