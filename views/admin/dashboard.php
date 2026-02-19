<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}

// Data Fetching
$totalResidents = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'resident'")->fetchColumn() ?: 0;

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

$pageTitle = 'Admin Dashboard';
require_once '../views/layout/header.php';
?>

<div class="mb-10">
    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Overview</h1>
    <p class="text-slate-500 font-medium mt-1">Real-time statistics for today's operations.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-12">
    <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300">
        <div class="absolute right-[-10px] top-[-10px] text-slate-50/50 text-7xl font-black group-hover:scale-110 group-hover:-rotate-6 transition-transform">👥</div>
        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mb-1">Residents</p>
        <h4 class="text-3xl font-black text-slate-800"><?= $totalResidents ?></h4>
        <div class="mt-4 flex items-center text-xs text-teal-600 font-bold italic">Registered Users</div>
    </div>

    <?php
    $statusConfig = [
        'pending' => ['label' => 'Pending', 'color' => 'amber', 'icon' => '⏳'],
        'approved' => ['label' => 'Approved', 'color' => 'emerald', 'icon' => '✅'],
        'completed' => ['label' => 'Completed', 'color' => 'cyan', 'icon' => '💎'],
        'rejected' => ['label' => 'Rejected', 'color' => 'red', 'icon' => '❌'],
    ];
    foreach ($statusConfig as $key => $cfg): ?>
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:border-<?= $cfg['color'] ?>-200 hover:shadow-lg transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-2xl"><?= $cfg['icon'] ?></span>
                <span class="text-[10px] font-black text-<?= $cfg['color'] ?>-600 bg-<?= $cfg['color'] ?>-50 px-2.5 py-1 rounded-lg uppercase tracking-wider"><?= $cfg['label'] ?></span>
            </div>
            <h4 class="text-3xl font-black text-slate-800"><?= $stats[$key] ?: 0 ?></h4>
            <p class="text-slate-400 text-[10px] font-bold mt-1 uppercase tracking-tighter">Total Requests</p>
        </div>
    <?php endforeach; ?>
</div>

<div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-8 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center bg-white">
        <div>
            <h2 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                <span class="h-2.5 w-2.5 rounded-full bg-teal-500 animate-pulse"></span>
                Incoming Requests
            </h2>
            <p class="text-slate-400 text-xs font-medium mt-1">Review the latest document submissions.</p>
        </div>
        <a href="?page=manage-requests" class="bg-slate-900 text-white px-6 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-800 transition shadow-lg shadow-slate-200">
            View Detailed List
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50/50 text-slate-400 text-[10px] uppercase font-black tracking-widest">
                <tr>
                    <th class="px-8 py-4">Resident Info</th>
                    <th class="px-8 py-4">Document Type</th>
                    <th class="px-8 py-4">Status</th>
                    <th class="px-8 py-4">Submission Date</th>
                    <th class="px-8 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php 
                $statusStyles = [
                    'Pending'   => 'text-amber-600 bg-amber-50 border-amber-100',
                    'Approved'  => 'text-emerald-600 bg-emerald-50 border-emerald-100',
                    'Completed' => 'text-cyan-600 bg-cyan-50 border-cyan-100',
                    'Rejected'  => 'text-red-600 bg-red-50 border-red-100'
                ];

                foreach ($recentRequests as $row): 
                    $badgeStyle = $statusStyles[$row['status']] ?? 'text-slate-600 bg-slate-50 border-slate-100';
                ?>
                <tr class="hover:bg-slate-50/50 transition-all duration-200">
                    <td class="px-8 py-5">
                        <div class="font-bold text-slate-800"><?= htmlspecialchars($row['resident_name']) ?></div>
                        <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">ID: #<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></div>
                    </td>
                    <td class="px-8 py-5">
                        <span class="inline-flex items-center gap-2 text-sm text-slate-600 font-semibold bg-slate-100 px-3 py-1 rounded-xl">
                            📄 <?= htmlspecialchars($row['certificate_name']) ?>
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border <?= $badgeStyle ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>
                    <td class="px-8 py-5 text-slate-500 text-sm font-medium">
                        <?= date('M d, Y', strtotime($row['created_at'])) ?>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <a href="?page=manage-requests&search=<?= urlencode($row['resident_name']) ?>" class="bg-teal-50 text-teal-600 px-4 py-2 rounded-lg font-bold text-xs hover:bg-teal-600 hover:text-white transition-all">Manage</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../views/layout/footer.php'; ?>