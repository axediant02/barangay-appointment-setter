<?php
session_start();
require_once '../config/database.php';

function redirect($url) {
    header("Location: $url");
    exit;
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;

// Get requested page from query param
$page = $_GET['page'] ?? 'home';

// Routing logic
switch ($page) {
    case 'login':
        if ($isLoggedIn) {
            // Redirect based on role
            if ($role === 'admin') redirect('?page=admin-dashboard');
            else redirect('?page=resident-dashboard');
        }
        include '../views/auth/login.php';
        break;

    case 'register':
        if ($isLoggedIn) redirect('?page=resident-dashboard');
        include '../views/auth/register.php';
        break;

    case 'logout':
        session_unset();
        session_destroy();
        redirect('?page=home');
        break;

    case 'resident-dashboard':
        if (!$isLoggedIn || $role !== 'resident') redirect('?page=login');
        include '../views/resident/dashboard.php';
        break;

    case 'create-request':
        if (!$isLoggedIn || $role !== 'resident') redirect('?page=login');
        include '../views/resident/create-request.php';
        break;

    case 'my-requests':
        if (!$isLoggedIn || $role !== 'resident') redirect('?page=login');
        include '../views/resident/my-requests.php';
        break;

    case 'admin-dashboard':
        if (!$isLoggedIn || $role !== 'admin') redirect('?page=login');
        include '../views/admin/dashboard.php';
        break;

    case 'manage-requests':
        if (!$isLoggedIn || $role !== 'admin') redirect('?page=login');
        include '../views/admin/manage-requests.php';
        break;

    case 'manage-certificates':
        if (!$isLoggedIn || $role !== 'admin') redirect('?page=login');
        include '../views/admin/manage-certificates.php';
        break;

    case 'home':
    default:
        if ($isLoggedIn) {
            // Redirect logged-in users to their dashboard
            if ($role === 'admin') redirect('?page=admin-dashboard');
            else redirect('?page=resident-dashboard');
        } else {
            // Show landing page to non-logged-in users
            include '../views/landing-page.php';
        }
        break;
}