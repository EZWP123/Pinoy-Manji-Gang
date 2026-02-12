/**
 * Authentication Module
 * Handles login/logout functionality
 */

// Login form handler (2.2 Administrator login)
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errorAlert = document.getElementById('errorAlert');

    // Clear previous error
    errorAlert.classList.add('d-none');

    // Send login request
    fetch('../backend/auth/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Redirect to admin panel
            window.location.href = '../backend/admin/dashboard.php';
        } else {
            // Show error
            errorAlert.textContent = data.error || 'Login failed. Please try again.';
            errorAlert.classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Login error:', error);
        errorAlert.textContent = 'An error occurred. Please try again.';
        errorAlert.classList.remove('d-none');
    });
});

// Session check function
function checkSession() {
    fetch('../backend/auth/check-session.php')
        .then(response => response.json())
        .then(data => {
            if (!data.logged_in && window.location.pathname.includes('admin')) {
                window.location.href = '../login.html';
            }
        })
        .catch(error => console.error('Session check error:', error));
}

// Logout function (2.3 Administrator logout)
function logout() {
    fetch('../backend/auth/logout.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '../../frontend/index.html';
        }
    })
    .catch(error => console.error('Logout error:', error));
}

// Run session check on page load if on admin pages
if (window.location.pathname.includes('admin')) {
    document.addEventListener('DOMContentLoaded', checkSession);
}
