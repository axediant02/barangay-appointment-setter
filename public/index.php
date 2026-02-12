<?php
session_start();
require_once '../config/database.php';

function redirect($url) {
    header("Location: $url");
    exit;
}
$isLoggedIn = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? null;

$page = $_GET['page'] ?? 'home';

//routing
switch ($page) {
    case 'login':
        if ($isLoggedIn) {
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
        require_once '../controllers/RequestController.php';
        $controller = new RequestController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->createForm();
        }
        break;

    case 'my-requests':
        if (!$isLoggedIn || $role !== 'resident') redirect('?page=login');
        require_once '../controllers/RequestController.php';
        $controller = new RequestController($pdo);
        $controller->myRequests();
        break;

    case 'cancel-request':
        if (!$isLoggedIn || $role !== 'resident') redirect('?page=login');
        require_once '../controllers/RequestController.php';
        $controller = new RequestController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->cancel();
        } else {
            redirect('?page=my-requests');
        }
        break;

    case 'admin-dashboard':
        if (!$isLoggedIn || $role !== 'admin') redirect('?page=login');
        include '../views/admin/dashboard.php';
        break;

    case 'manage-requests':
        if (!$isLoggedIn || $role !== 'admin') redirect('?page=login');
        require_once '../controllers/AdminController.php';
        $controller = new AdminController($pdo);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->updateRequest();
        } else {
            $controller->manageRequests();
        }
        break;

    case 'manage-certificates':
        if (!$isLoggedIn || $role !== 'admin') redirect('?page=login');
        include '../views/admin/manage-certificate.php';
        break;

    case 'home':
    default:
        if ($isLoggedIn) {
            if ($role === 'admin') redirect('?page=admin-dashboard');
            else redirect('?page=resident-dashboard');
        } else {
            include '../views/landing-page.php';
        }
        break;
}