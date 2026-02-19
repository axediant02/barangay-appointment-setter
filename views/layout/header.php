<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'Barangay Certificate System';

$userName = $_SESSION['username'] ?? 'User';
$userRole = $_SESSION['role'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Modern Barangay Certificate System for efficient request and appointment management">
    <title><?= htmlspecialchars($pageTitle) ?> | Barangay Certificate System</title>
    <script>
        window.APP_BASE = <?= json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/') . '/') ?>;
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --primary: #0D9488;
            --primary-light: #ECFDF5;
            --accent: #F97316;
            --pending: #CA8A04;
            --approved: #059669;
            --completed: #0891B2;
            --rejected: #DC2626;
        }
        .btn-primary {
            @apply bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition font-medium;
        }
        .btn-secondary {
            @apply bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-lg transition font-medium;
        }
        .card {
            @apply bg-white rounded-lg shadow hover:shadow-md transition border border-gray-100;
        }
        .input-field {
            @apply w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition;
        }
        .focus-visible:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<nav class="bg-gradient-to-r from-teal-600 to-teal-700 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center font-bold text-teal-600">
                    📋
                </div>
                <a href="?page=home" class="text-xl font-bold hover:text-teal-100 transition">
                    Certificate System
                </a>
            </div>

            <div class="flex items-center gap-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-6">
                        <?php if ($userRole === 'admin'): ?>
                            <a href="?page=admin-dashboard" class="hover:text-teal-100 transition font-medium">Dashboard</a>
                            <a href="?page=manage-certificates" class="hover:text-teal-100 transition font-medium">Certificates</a>
                        <?php else: ?>
                            <a href="?page=resident-dashboard" class="hover:text-teal-100 transition font-medium">Dashboard</a>
                            <a href="?page=create-request" class="hover:text-teal-100 transition font-medium">Request Certificate</a>
                            <a href="?page=my-requests" class="hover:text-teal-100 transition font-medium">My Requests</a>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center gap-4 pl-4 border-l border-teal-400">
                        <div class="text-sm">
                            <div class="font-semibold text-right"><?= htmlspecialchars($userName) ?></div>
                            <div class="text-teal-100 text-xs uppercase text-right"><?= htmlspecialchars($userRole) ?></div>
                        </div>
                        <button onclick="document.getElementById('logoutForm').submit()" class="px-3 py-1 bg-red-500 hover:bg-red-600 rounded transition-colors text-sm font-medium">
                            Logout
                        </button>
                        <form id="logoutForm" action="?page=logout" method="POST" style="display:none;"></form>
                    </div>

                <?php else: ?>
                    <div class="flex items-center gap-4">
                        <a href="?page=login" class="hover:text-teal-100 transition font-medium">Login</a>
                        <a href="?page=register" class="px-4 py-2 bg-white text-teal-600 rounded-lg hover:bg-teal-50 transition-colors font-semibold">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<main class="flex-grow max-w-7xl mx-auto px-4 py-8 w-full">