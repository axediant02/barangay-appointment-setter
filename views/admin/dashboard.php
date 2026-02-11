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

<nav class="bg-teal-600 p-4 text-white flex justify-between sticky top-0 z-40">
    <h1 class="font-semibold text-lg">Admin Dashboard</h1>
    <div class="space-x-4">
        <a href="?page=manage-requests" class="hover:text-teal-100 transition">Manage Requests</a>
        <a href="?page=manage-certificates" class="hover:text-teal-100 transition">Certificates</a>
        <a href="?page=logout" class="hover:text-teal-100 transition">Logout</a>
    </div>
</nav>

<div class="max-w-7xl mx-auto mt-8 px-4">

    <h2 class="text-2xl font-bold mb-6">System Overview</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4" id="stats-container">

        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-gray-500 text-sm">Residents</p>
            <h3 class="text-2xl font-bold" id="stat-residents"><?= $totalResidents ?></h3>
        </div>

        <div class="bg-white p-6 rounded-lg shadow">
            <p class="text-gray-500 text-sm">Total Requests</p>
            <h3 class="text-2xl font-bold" id="stat-total"><?= $stats['total'] ?? 0 ?></h3>
        </div>

        <div class="bg-yellow-100 p-6 rounded-lg shadow">
            <p class="text-sm">Pending</p>
            <h3 class="text-2xl font-bold text-yellow-600" id="stat-pending"><?= $stats['pending'] ?? 0 ?></h3>
        </div>

        <div class="bg-blue-100 p-6 rounded-lg shadow">
            <p class="text-sm">Approved</p>
            <h3 class="text-2xl font-bold text-blue-600" id="stat-approved"><?= $stats['approved'] ?? 0 ?></h3>
        </div>

        <div class="bg-green-100 p-6 rounded-lg shadow">
            <p class="text-sm">Completed</p>
            <h3 class="text-2xl font-bold text-green-600" id="stat-completed"><?= $stats['completed'] ?? 0 ?></h3>
        </div>

        <div class="bg-red-100 p-6 rounded-lg shadow">
            <p class="text-sm">Rejected</p>
            <h3 class="text-2xl font-bold text-red-600" id="stat-rejected"><?= $stats['rejected'] ?? 0 ?></h3>
        </div>

    </div>

    <div class="mt-10 bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold mb-4">Recent Requests</h3>

        <table class="w-full border-collapse" id="recent-requests-table">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 text-left">Resident</th>
                    <th class="p-2 text-left">Certificate</th>
                    <th class="p-2 text-left">Status</th>
                    <th class="p-2 text-left">Date</th>
                </tr>
            </thead>
            <tbody id="recent-requests-body">
                <?php
                $stmt = $pdo->query("
                    SELECT r.*, u.username as resident_name, c.name as certificate_name
                    FROM requests r
                    JOIN users u ON r.user_id = u.id
                    JOIN certificates c ON r.certificate_id = c.id
                    ORDER BY r.created_at DESC
                    LIMIT 5
                ");

                foreach ($stmt as $row):
                ?>
                <tr class="border-b" data-request-id="<?= $row['id'] ?>">
                    <td class="p-2"><?= htmlspecialchars($row['resident_name']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($row['certificate_name']) ?></td>
                    <td class="p-2 status-cell"><?= $row['status'] ?></td>
                    <td class="p-2"><?= $row['appointment_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<!-- Real-time Update Script -->
<script>
    function updateAdminStats() {
        fetch('api.php?action=admin-stats')
            .then(response => response.json())
            .then(data => {
                document.getElementById('stat-residents').textContent = data.residents || 0;
                document.getElementById('stat-total').textContent = data.total || 0;
                document.getElementById('stat-pending').textContent = data.pending || 0;
                document.getElementById('stat-approved').textContent = data.approved || 0;
                document.getElementById('stat-completed').textContent = data.completed || 0;
                document.getElementById('stat-rejected').textContent = data.rejected || 0;
            })
            .catch(error => console.log('Stats update error:', error));
    }

    function updateRecentRequests() {
        fetch('api.php?action=admin-recent-requests')
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('recent-requests-body');
                if (!tbody) return;

                // Update status for existing rows
                data.forEach(req => {
                    const row = document.querySelector(`[data-request-id="${req.id}"]`);
                    if (row) {
                        row.querySelector('.status-cell').textContent = req.status;
                    }
                });
            })
            .catch(error => console.log('Requests update error:', error));
    }

    // Poll every 5 seconds
    setInterval(updateAdminStats, 5000);
    setInterval(updateRecentRequests, 5000);
</script>

</body>
</html>