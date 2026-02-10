<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine page title
$pageTitle = $pageTitle ?? 'Barangay Certificate System';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Barangay Certificate System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .transition-colors {
            transition: background-color 0.3s ease, color 0.3s ease;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

<!-- Navigation Bar -->
<nav class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-md">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            
            <!-- Logo/Branding -->
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center font-bold text-blue-600">
                    BC
                </div>
                <a href="?page=landing" class="text-xl font-bold hover:text-blue-100 transition">
                    Barangay Certificate System
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="flex items-center gap-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Logged In User Navigation -->
                    <div class="flex items-center gap-4">
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <!-- Admin Links -->
                            <a href="?page=admin-dashboard" class="hover:text-blue-100 transition">
                                Dashboard
                            </a>
                            <a href="?page=manage-certificate" class="hover:text-blue-100 transition">
                                Certificates
                            </a>
                            <a href="?page=manage-request" class="hover:text-blue-100 transition">
                                Requests
                            </a>
                        <?php else: ?>
                            <!-- Resident Links -->
                            <a href="?page=resident-dashboard" class="hover:text-blue-100 transition">
                                Dashboard
                            </a>
                            <a href="?page=create-request" class="hover:text-blue-100 transition">
                                New Request
                            </a>
                            <a href="?page=my-request" class="hover:text-blue-100 transition">
                                My Requests
                            </a>
                        <?php endif; ?>
                    </div>

                    <!-- User Info & Logout -->
                    <div class="flex items-center gap-4 pl-4 border-l border-blue-400">
                        <div class="text-sm">
                            <div class="font-semibold"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></div>
                            <div class="text-blue-100 text-xs uppercase"><?= htmlspecialchars($_SESSION['role'] ?? 'User') ?></div>
                        </div>
                        <button onclick="document.getElementById('logoutForm').submit()" class="px-3 py-1 bg-red-500 hover:bg-red-600 rounded transition-colors">
                            Logout
                        </button>
                        <form id="logoutForm" action="?page=logout" method="POST" style="display:none;"></form>
                    </div>

                <?php else: ?>
                    <!-- Not Logged In Navigation -->
                    <div class="flex items-center gap-4">
                        <a href="?page=login" class="hover:text-blue-100 transition">
                            Login
                        </a>
                        <a href="?page=register" class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-blue-50 transition-colors font-semibold">
                            Register
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content Wrapper -->
<main class="flex-grow container mx-auto px-4 py-8">
