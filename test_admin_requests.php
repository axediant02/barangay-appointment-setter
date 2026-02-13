<?php
// Simple CLI helper to debug the admin-requests API action.
// Usage: php test_admin_requests.php

session_start();

// Pretend to be an admin user (adjust ID if needed)
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

// Simulate GET parameter
$_GET['action'] = 'admin-requests';

require __DIR__ . '/config/database.php';
require __DIR__ . '/public/api.php';

