<?php
// Route protection
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}

// Data Fetching - Using fetchColumn() for simple counts
$totalResidents = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'resident'")->fetchColumn();
$totalResidents = $totalResidents ?: 0;

$stmtStats = $pdo->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
    FROM requests
");
$stats = $stmtStats->fetch(PDO::FETCH_ASSOC);

$stmtRecent = $pdo->query("
    SELECT r.*, u.username as resident_name, c.name as certificate_name
    FROM requests r
    JOIN users u ON r.user_id = u.id
    JOIN certificates c ON r.certificate_id = c.id
    ORDER BY r.created_at DESC
    LIMIT 8
");
$recentRequests = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | BrgyPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #F3F4F6; 
        }

        .stat-card { 
            background-color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            border: 1px solid #E5E7EB;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transform: translateY(-4px);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-thumb { background: #0D9488; border-radius: 10px; }
    </style>
</head>
<body class="min-h-screen">

<nav class="sticky top-0 z-50 bg-gradient-to-r from-teal-600 to-teal-700 p-4 text-white shadow-lg">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-2">
            <span class="text-2xl">🏘️</span>
            <h1 class="font-bold text-xl tracking-tight">BrgyPortal <span class="text-teal-200 font-light text-sm ml-1 italic">Admin</span></h1>
        </div>
        <div class="hidden md:flex items-center gap-8 font-semibold text-sm uppercase tracking-wide">
            <a href="?page=manage-requests" class="hover:text-teal-200 transition">Requests</a>
            <a href="?page=manage-certificates" class="hover:text-teal-200 transition">Certificates</a>
            <a href="?page=logout" class="bg-orange-500 hover:bg-orange-600 px-5 py-2 rounded-lg transition shadow-md normal-case">Logout</a>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto py-10 px-6">
    
    <header class="mb-12">
        <h2 class="text-6xl font-extrabold text-gray-900 tracking-tighter mb-2">System Overview</h2>
        <p class="text-gray-500 text-lg">Manage residents and certificate requests with ease.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-12" id="stats-container">
        
        <div class="stat-card">
            <div class="text-3xl mb-2">👥</div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Residents</p>
            <h3 class="text-3xl font-extrabold text-gray-900" id="stat-residents"><?= $totalResidents ?></h3>
        </div>

        <div class="stat-card border-l-4 border-teal-500">
            <div class="text-3xl mb-2">📊</div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Requests</p>
            <h3 class="text-3xl font-extrabold text-gray-900" id="stat-total"><?= $stats['total'] ?: 0 ?></h3>
        </div>

        <div class="stat-card border-l-4 border-amber-500 bg-amber-50/50">
            <div class="text-3xl mb-2">⏳</div>
            <p class="text-amber-700 text-xs font-bold uppercase tracking-wider">Pending</p>
            <h3 class="text-3xl font-extrabold text-amber-600" id="stat-pending"><?= $stats['pending'] ?: 0 ?></h3>
        </div>

        <div class="stat-card border-l-4 border-emerald-500 bg-emerald-50/50">
            <div class="text-3xl mb-2">✅</div>
            <p class="text-emerald-700 text-xs font-bold uppercase tracking-wider">Approved</p>
            <h3 class="text-3xl font-extrabold text-emerald-600" id="stat-approved"><?= $stats['approved'] ?: 0 ?></h3>
        </div>

        <div class="stat-card border-l-4 border-cyan-500 bg-cyan-50/50">
            <div class="text-3xl mb-2">💎</div>
            <p class="text-cyan-700 text-xs font-bold uppercase tracking-wider">Completed</p>
            <h3 class="text-3xl font-extrabold text-cyan-600" id="stat-completed"><?= $stats['completed'] ?: 0 ?></h3>
        </div>

        <div class="stat-card border-l-4 border-red-500 bg-red-50/50">
            <div class="text-3xl mb-2">❌</div>
            <p class="text-red-700 text-xs font-bold uppercase tracking-wider">Rejected</p>
            <h3 class="text-3xl font-extrabold text-red-600" id="stat-rejected"><?= $stats['rejected'] ?: 0 ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center bg-white">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2 mb-4 sm:mb-0">
                <span>⚡</span> Recent Activity
            </h3>
            <a href="?page=manage-requests" class="bg-teal-50 text-teal-700 px-4 py-2 rounded-lg font-bold text-sm hover:bg-teal-100 transition">
                Manage All Requests →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="recent-requests-table">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold tracking-widest">
                    <tr>
                        <th class="px-8 py-4">Resident</th>
                        <th class="px-8 py-4">Certificate</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4">Date Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="recent-requests-body">
                    <?php 
                    // PHP 7 Compatible status colors array
                    $statusColors = [
                        'Pending'   => 'text-amber-600 bg-amber-50 border-amber-200',
                        'Approved'  => 'text-emerald-600 bg-emerald-50 border-emerald-200',
                        'Completed' => 'text-cyan-600 bg-cyan-50 border-cyan-200',
                        'Rejected'  => 'text-red-600 bg-red-50 border-red-200'
                    ];

                    foreach ($recentRequests as $row): 
                        $currentStatus = $row['status'];
                        $badgeColor = isset($statusColors[$currentStatus]) ? $statusColors[$currentStatus] : 'text-gray-600 bg-gray-50 border-gray-200';
                    ?>
                    <tr class="hover:bg-teal-50/30 transition-colors" data-request-id="<?= $row['id'] ?>">
                        <td class="px-8 py-5 font-bold text-gray-900"><?= htmlspecialchars($row['resident_name']) ?></td>
                        <td class="px-8 py-5">
                            <span class="text-sm text-gray-600 font-medium">📄 <?= htmlspecialchars($row['certificate_name']) ?></span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase border <?= $badgeColor ?> status-cell">
                                <?= $currentStatus ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-gray-500 text-sm">
                            <?= date('M d, Y', strtotime($row['created_at'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    /**
     * Status color mapping
     */
    const statusColors = {
        'Pending': 'text-amber-600 bg-amber-50 border-amber-200',
        'Approved': 'text-emerald-600 bg-emerald-50 border-emerald-200',
        'Completed': 'text-cyan-600 bg-cyan-50 border-cyan-200',
        'Rejected': 'text-red-600 bg-red-50 border-red-200'
    };
</script>

</body>
</html>