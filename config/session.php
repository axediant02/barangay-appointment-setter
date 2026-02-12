<?php
/**
 * Session Configuration and Helper Functions
 * This file ensures consistent session handling across the application
 */

// Configure sessions for production stability
ini_set('session.gc_maxlifetime', 86400); // 24 hours
ini_set('session.use_only_cookies', 1);
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Lax');

// Ensure session save path is writable (critical for live deployment)
$sessionPath = sys_get_temp_dir() . '/barangay_sessions';
if (!is_dir($sessionPath)) {
    @mkdir($sessionPath, 0700, true);
}

// Verify the path is writable
if (!is_writable($sessionPath)) {
    // Fallback to default temp directory
    $sessionPath = sys_get_temp_dir();
}

session_save_path($sessionPath);

// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    if (!session_start()) {
        die("ERROR: Failed to start session. Server configuration issue. Contact administrator.");
    }
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user has valid session
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user has specific role
 * 
 * @param string $role The role to check (admin, resident)
 * @return bool True if user has the specified role
 */
function hasRole($role) {
    return isLoggedIn() && ($_SESSION['role'] ?? null) === $role;
}

/**
 * Get current user ID
 * 
 * @return int|null User ID or null if not logged in
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 * 
 * @return string|null User role or null if not logged in
 */
function getCurrentRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user username
 * 
 * @return string|null User username or null if not logged in
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * Require authentication - redirects to login if not logged in
 * 
 * @param string $requiredRole (optional) Specific role required
 * @return void
 */
function requireLogin($requiredRole = null) {
    if (!isLoggedIn()) {
        header("Location: ?page=login");
        exit;
    }
    
    if ($requiredRole && getCurrentRole() !== $requiredRole) {
        header("Location: ?page=login");
        exit;
    }
}

/**
 * Require admin role
 * 
 * @return void
 */
function requireAdmin() {
    requireLogin('admin');
}

/**
 * Require resident role
 * 
 * @return void
 */
function requireResident() {
    requireLogin('resident');
}

/**
 * Logout user
 * 
 * @return void
 */
function logout() {
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    session_unset();
    session_destroy();
}

/**
 * Set session data after login
 * 
 * @param array $userData User data from database
 * @return bool True if session was set successfully
 */
function setSessionData($userData) {
    if (!isset($userData['id']) || empty($userData['id'])) {
        return false;
    }
    
    $_SESSION['user_id'] = (int)$userData['id'];
    $_SESSION['role'] = (string)($userData['role'] ?? '');
    $_SESSION['username'] = (string)($userData['name'] ?? '');
    $_SESSION['email'] = (string)($userData['email'] ?? '');
    
    // Force session to write to disk
    session_write_close();
    session_start();
    
    // Verify session was saved
    return !empty($_SESSION['user_id']);
}
