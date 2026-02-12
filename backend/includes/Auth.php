<?php
/**
 * Authentication Handler
 */

class Auth {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Login user
    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, email, role, full_name FROM users WHERE username = ? AND password = SHA2(?, 256) AND is_active = TRUE");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            return true;
        }
        return false;
    }

    // Register user
    public function register($username, $email, $password, $full_name) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, SHA2(?, 256), ?, 'viewer')");
        $stmt->bind_param("ssss", $username, $email, $password, $full_name);
        return $stmt->execute();
    }

    // Check if user is logged in
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    // Check user role
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    // Logout user
    public function logout() {
        session_destroy();
        return true;
    }

    // Get current user
    public function getCurrentUser() {
        if ($this->isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $_SESSION['role'],
                'full_name' => $_SESSION['full_name']
            ];
        }
        return null;
    }
}
?>
