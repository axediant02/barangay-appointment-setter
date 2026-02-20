<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'Barangay Certificate System';
$userName = $_SESSION['username'] ?? 'User';
$userRole = $_SESSION['role'] ?? null;
$currentPage = $_GET['page'] ?? 'home';
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Modern Barangay Certificate System for efficient request and appointment management">
    <title><?= htmlspecialchars($pageTitle) ?> | Barangay Certificate System</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script>
        window.APP_BASE = <?= json_encode(rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/') . '/') ?>;
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style type="text/tailwindcss">
        @layer base {
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                @apply text-slate-900 antialiased;
            }
        }
        @layer components {
            .nav-link {
                @apply relative py-2 text-sm font-semibold transition-colors hover:text-white/90 text-white/80;
            }
            .nav-link-active {
                @apply text-white after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-full after:bg-white after:rounded-full;
            }
            .glass-header {
                @apply sticky top-0 z-50 border-b border-teal-500/30 bg-teal-600/90 backdrop-blur-md;
            }
            .user-dropdown-item {
                @apply flex items-center gap-2 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 transition-colors;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

<header class="glass-header text-white">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Top">
        <div class="flex h-16 items-center justify-between">
            
            <div class="flex items-center gap-8">
                <a href="?page=home" class="flex items-center gap-2.5 group transition-transform active:scale-95">
                    <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white shadow-sm transition-group hover:rotate-6">
                        <span class="text-xl">🏛️</span>
                    </div>
                    <span class="text-lg font-bold tracking-tight hidden md:block">
                        Barangay<span class="text-teal-200">Connect</span>
                    </span>
                </a>

                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="hidden lg:flex lg:items-center lg:gap-6">
                    <?php if ($userRole === 'admin'): ?>
                        <a href="?page=admin-dashboard" class="nav-link <?= $currentPage === 'admin-dashboard' ? 'nav-link-active' : '' ?>">Dashboard</a>
                        <a href="?page=manage-requests" class="nav-link <?= $currentPage === 'manage-requests' ? 'nav-link-active' : '' ?>">Requests</a>
                        <a href="?page=manage-certificates" class="nav-link <?= $currentPage === 'manage-certificates' ? 'nav-link-active' : '' ?>">Certificates</a>
                    <?php else: ?>
                        <a href="?page=resident-dashboard" class="nav-link <?= $currentPage === 'resident-dashboard' ? 'nav-link-active' : '' ?>">Dashboard</a>
                        <a href="?page=create-request" class="nav-link <?= $currentPage === 'create-request' ? 'nav-link-active' : '' ?>">Request Certificate</a>
                        <a href="?page=my-requests" class="nav-link <?= $currentPage === 'my-requests' ? 'nav-link-active' : '' ?>">My History</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="relative rounded-full p-2 text-teal-100 hover:bg-teal-500/50 hover:text-white transition-all">
                        <span class="sr-only">View notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                        </svg>
                        <span class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-red-400 ring-2 ring-teal-600"></span>
                    </button>

                    <div class="relative ml-3" id="userMenu">
                        <button type="button" onclick="toggleDropdown()" class="flex items-center gap-3 rounded-full bg-teal-700/50 pl-3 pr-1 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-white/50 transition-all hover:bg-teal-700/80 border border-white/10">
                            <span class="hidden sm:block font-medium"><?= htmlspecialchars($userName) ?></span>
                            <div class="h-8 w-8 rounded-full bg-teal-500 flex items-center justify-center font-bold border border-white/20">
                                <?= strtoupper(substr($userName, 0, 1)) ?>
                            </div>
                        </button>
                        
                        <div id="dropdownContent" class="hidden absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-xl bg-white py-1 shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden animate-in fade-in zoom-in duration-100">
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?= $userRole ?></p>
                            </div>
                            <a href="?page=profile" class="user-dropdown-item">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                                Account Profile
                            </a>
                            <form id="logoutForm" action="?page=logout" method="POST">
                                <button type="submit" class="user-dropdown-item w-full text-red-600 hover:bg-red-50">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                    Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="flex items-center gap-3">
                        <a href="?page=login" class="px-4 py-2 text-sm font-semibold hover:text-teal-100 transition">Log in</a>
                        <a href="?page=register" class="rounded-lg bg-white px-4 py-2 text-sm font-bold text-teal-600 shadow-sm hover:bg-teal-50 transition-all active:scale-95">
                            Join System
                        </a>
                    </div>
                <?php endif; ?>

                <button onclick="toggleMobileMenu()" class="lg:hidden rounded-lg p-2 hover:bg-teal-500/50 transition-colors">
                    <svg id="mobileMenuIcon" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    <div id="mobileMenu" class="hidden lg:hidden border-t border-teal-500/30 bg-teal-700/50 px-4 pb-6 pt-4">
        <div class="space-y-2">
            <?php if ($userRole === 'admin'): ?>
                <a href="?page=admin-dashboard" class="block rounded-lg px-3 py-2 font-medium hover:bg-teal-500/50">Dashboard</a>
                <a href="?page=manage-requests" class="block rounded-lg px-3 py-2 font-medium hover:bg-teal-500/50">Requests</a>
                <a href="?page=manage-certificates" class="block rounded-lg px-3 py-2 font-medium hover:bg-teal-500/50">Certificates</a>
            <?php else: ?>
                <a href="?page=resident-dashboard" class="block rounded-lg px-3 py-2 font-medium hover:bg-teal-500/50">Dashboard</a>
                <a href="?page=create-request" class="block rounded-lg px-3 py-2 font-medium hover:bg-teal-500/50">Request Certificate</a>
                <a href="?page=my-requests" class="block rounded-lg px-3 py-2 font-medium hover:bg-teal-500/50">My History</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    function toggleDropdown() {
        const content = document.getElementById('dropdownContent');
        content.classList.toggle('hidden');
    }

    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        menu.classList.toggle('hidden');
    }

    // Close dropdown when clicking outside
    window.addEventListener('click', function(e) {
        const menu = document.getElementById('userMenu');
        const content = document.getElementById('dropdownContent');
        if (menu && !menu.contains(e.target)) {
            content.classList.add('hidden');
        }
    });
</script>

<main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">