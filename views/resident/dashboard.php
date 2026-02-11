<?php
// Protect route
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: ?page=login");
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch request statistics
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'Pending') as pending,
        SUM(status = 'Approved') as approved,
        SUM(status = 'Completed') as completed,
        SUM(status = 'Rejected') as rejected
    FROM requests
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$stats = $stmt->fetch();

$pageTitle = 'Dashboard - Resident';
include __DIR__ . '/../layout/header.php';
?>

<!-- Welcome Section -->
<div class="mb-12">
    <h1 class="text-4xl font-bold text-gray-900 mb-2">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Resident') ?>! 👋</h1>
    <p class="text-gray-600 text-lg">Manage your certificate requests and appointments here</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-12">
    <!-- Total -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-medium">Total Requests</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= $stats['total'] ?? 0 ?></h3>
            </div>
            <div class="text-4xl">📋</div>
        </div>
    </div>

    <!-- Pending -->
    <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl shadow border border-amber-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-800 text-sm font-medium">Pending</p>
                <h3 class="text-3xl font-bold text-amber-700"><?= $stats['pending'] ?? 0 ?></h3>
            </div>
            <div class="text-4xl">⏳</div>
        </div>
    </div>

    <!-- Approved -->
    <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl shadow border border-teal-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-800 text-sm font-medium">Approved</p>
                <h3 class="text-3xl font-bold text-teal-700"><?= $stats['approved'] ?? 0 ?></h3>
            </div>
            <div class="text-4xl">✅</div>
        </div>
    </div>

    <!-- Completed -->
    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl shadow border border-cyan-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-cyan-800 text-sm font-medium">Completed</p>
                <h3 class="text-3xl font-bold text-cyan-700"><?= $stats['completed'] ?? 0 ?></h3>
            </div>
            <div class="text-4xl">🎉</div>
        </div>
    </div>

    <!-- Rejected -->
    <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl shadow border border-red-200 p-6 hover:shadow-lg transition">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-800 text-sm font-medium">Rejected</p>
                <h3 class="text-3xl font-bold text-red-700"><?= $stats['rejected'] ?? 0 ?></h3>
            </div>
            <div class="text-4xl">❌</div>
        </div>
    </div>
</div>

<!-- Quick Actions Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Request Certificate Card -->
    <a href="?page=create-request" class="bg-gradient-to-br from-teal-600 to-teal-700 text-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition hover:scale-105">
        <div class="text-5xl mb-4">📝</div>
        <h3 class="text-2xl font-bold mb-2">Request Certificate</h3>
        <p class="text-teal-100">Start a new certificate request and schedule your appointment</p>
        <div class="mt-6 inline-block px-6 py-2 bg-white text-teal-600 rounded-lg font-semibold hover:bg-gray-100 transition">
            Get Started →
        </div>
    </a>

    <!-- My Requests Card -->
    <a href="?page=my-requests" class="bg-gradient-to-br from-cyan-600 to-blue-600 text-white rounded-xl shadow-lg p-8 hover:shadow-2xl transition hover:scale-105">
        <div class="text-5xl mb-4">📊</div>
        <h3 class="text-2xl font-bold mb-2">View My Requests</h3>
        <p class="text-cyan-100">Track all your requests and their current status</p>
        <div class="mt-6 inline-block px-6 py-2 bg-white text-cyan-600 rounded-lg font-semibold hover:bg-gray-100 transition">
            View All →
        </div>
    </a>
</div>

<!-- Info Section -->
<div class="mt-12 bg-teal-50 border-2 border-teal-200 rounded-xl p-8">
    <h3 class="text-xl font-bold text-gray-900 mb-4">❓ How It Works</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <div class="text-3xl mb-2">1️⃣</div>
            <p class="font-semibold text-gray-900 mb-1">Submit Request</p>
            <p class="text-gray-600 text-sm">Choose the certificate type and schedule your preferred appointment date</p>
        </div>
        <div>
            <div class="text-3xl mb-2">2️⃣</div>
            <p class="font-semibold text-gray-900 mb-1">Wait for Review</p>
            <p class="text-gray-600 text-sm">Our admin team will review your request and update you on approval status</p>
        </div>
        <div>
            <div class="text-3xl mb-2">3️⃣</div>
            <p class="font-semibold text-gray-900 mb-1">Claim Certificate</p>
            <p class="text-gray-600 text-sm">Once ready, come to your appointment and receive your certificate</p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>