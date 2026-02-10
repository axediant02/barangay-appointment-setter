<?php
// Route protection
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}

// Total residents
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users WHERE role = 'resident'");
$totalResidents = $stmt->fetch()['total'] ?? 0;

// Request statistics
$stmt = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(status = 'Pending') as pending,
        SUM(status = 'Approved') as approved,
        SUM(status = 'Completed') as completed,
        SUM(status = 'Rejected') as rejected
    FROM requests
");
$stats = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<nav class="bg-gray-800 p-4 text-white flex justify-between">
    <h1 class="font-semibold text-lg">Admin Dashboard</h1>
    <div class="space-x-4">
        <a href="?page=manage-requests" class="hover:underline">Manage Requests</a>
        <a href="?page=manage-certificates" class="hover:underline">Certificates</a>
        <a href="?page=logout" class="hover:underline">Logout</a>
    </div>
</nav>

<div class="max-w-7xl mx-auto mt-8 px-4">

    <h2 class="text-2xl font-bold mb-6">System Overview</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">

        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-gray-500 text-sm">Residents</p>
            <h3 class="text-2xl font-bold"><?= $totalResidents ?></h3>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-gray-500 text-sm">Total Requests</p>
            <h3 class="text-2xl font-bold"><?= $stats['total'] ?? 0 ?></h3>
        </div>

        <div class="bg-yellow-100 p-6 rounded-lg shadow">
            <p class="text-sm">Pending</p>
            <h3 class="text-2xl font-bold text-yellow-600"><?= $stats['pending'] ?? 0 ?></h3>
        </div>

        <div class="bg-blue-100 p-6 rounded-lg shadow">
            <p class="text-sm">Approved</p>
            <h3 class="text-2xl font-bold text-blue-600"><?= $stats['approved'] ?? 0 ?></h3>
        </div>

        <div class="bg-green-100 p-6 rounded-lg shadow">
            <p class="text-sm">Completed</p>
            <h3 class="text-2xl font-bold text-green-600"><?= $stats['completed'] ?? 0 ?></h3>
        </div>

        <div class="bg-red-100 p-6 rounded-lg shadow">
            <p class="text-sm">Rejected</p>
            <h3 class="text-2xl font-bold text-red-600"><?= $stats['rejected'] ?? 0 ?></h3>
        </div>

    </div>

    <div class="mt-10 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Requests</h3>

        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Resident</th>
                    <th class="p-2 text-left">Certificate</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("
                    SELECT r.*, u.name as resident_name, c.name as certificate_name
                    FROM requests r
                    JOIN users u ON r.user_id = u.id
                    JOIN certificates c ON r.certificate_id = c.id
                    ORDER BY r.created_at DESC
                    LIMIT 5
                ");

                foreach ($stmt as $row):
                ?>
                <tr class="border-b">
                    <td class="p-2"><?= htmlspecialchars($row['resident_name']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['certificate_name']) ?></td>
                    <td class="p-2"><?= $row['status'] ?></td>
                    <td class="p-2"><?= $row['appointment_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>