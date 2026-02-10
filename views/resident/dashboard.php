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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 text-white flex justify-between">
        <h1 class="font-semibold text-lg">Barangay System</h1>
        <div class="space-x-4">
            <a href="?page=create-request" class="hover:underline">Create Request</a>
            <a href="?page=my-requests" class="hover:underline">My Requests</a>
            <a href="?page=logout" class="hover:underline">Logout</a>
        </div>
    </nav>

    <!-- Content -->
    <div class="max-w-6xl mx-auto mt-8 px-4">
        <h2 class="text-2xl font-bold mb-6">Resident Dashboard</h2>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">

            <div class="bg-white p-6 rounded-lg shadow">
                <p class="text-gray-500 text-sm">Total Requests</p>
                <h3 class="text-2xl font-bold"><?= $stats['total'] ?? 0 ?></h3>
            </div>

            <div class="bg-yellow-100 p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Pending</p>
                <h3 class="text-2xl font-bold text-yellow-600"><?= $stats['pending'] ?? 0 ?></h3>
            </div>

            <div class="bg-blue-100 p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Approved</p>
                <h3 class="text-2xl font-bold text-blue-600"><?= $stats['approved'] ?? 0 ?></h3>
            </div>

            <div class="bg-green-100 p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Completed</p>
                <h3 class="text-2xl font-bold text-green-600"><?= $stats['completed'] ?? 0 ?></h3>
            </div>

            <div class="bg-red-100 p-6 rounded-lg shadow">
                <p class="text-gray-600 text-sm">Rejected</p>
                <h3 class="text-2xl font-bold text-red-600"><?= $stats['rejected'] ?? 0 ?></h3>
            </div>

        </div>

        <!-- Quick Action Section -->
        <div class="mt-10 bg-white p-6 rounded-lg shadow">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
            <div class="flex flex-wrap gap-4">
                <a href="?page=create-request"
                   class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                    Request New Certificate
                </a>

                <a href="?page=my-requests"
                   class="bg-gray-600 text-white px-5 py-2 rounded hover:bg-gray-700 transition">
                    View My Requests
                </a>
            </div>
        </div>
    </div>

</body>
</html>