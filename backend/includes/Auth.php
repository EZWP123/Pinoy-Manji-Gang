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
            // Regenerate session id to prevent fixation
            if (session_status() === PHP_SESSION_NONE) session_start();
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Session hardening: fingerprint + last activity
            $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            $_SESSION['fingerprint'] = hash('sha256', $ua . $ip);
            $_SESSION['last_activity'] = time();

            return true;
        }
        return false;
    }

    // Validate session lifetime and fingerprint
    public function validateSession($timeout = 3600) {
        if (!isset($_SESSION['user_id'])) return false;
        // Check timeout
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
            $this->logout();
            return false;
        }
        // Check fingerprint (user agent + ip)
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $fingerprint = hash('sha256', $ua . $ip);
        if (isset($_SESSION['fingerprint']) && $_SESSION['fingerprint'] !== $fingerprint) {
            $this->logout();
            return false;
        }
        // Refresh activity timestamp
        $_SESSION['last_activity'] = time();
        return true;
    }

    // Register user
    public function register($username, $email, $password, $full_name) {
        $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, SHA2(?, 256), ?, 'viewer')");
        $stmt->bind_param("ssss", $username, $email, $password, $full_name);
        return $stmt->execute();
    }

    // Check if user is logged in
    public function isLoggedIn() {
        if (!isset($_SESSION['user_id'])) return false;
        return $this->validateSession();
    }

    // Check user role
    public function hasRole($role) {
        return isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    // Logout user
    public function logout() {
        // Clear session data and destroy session cookie
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
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
