<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: ?page=login");
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN status = 'Rejected' THEN 1 ELSE 0 END) as rejected
    FROM requests
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$stats = $stmt->fetch();

$recentStmt = $pdo->prepare("
    SELECT r.*, c.name as certificate_name 
    FROM requests r
    JOIN certificates c ON r.certificate_id = c.id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
    LIMIT 5
");
$recentStmt->execute([$userId]);
$recentRequests = $recentStmt->fetchAll();

$pageTitle = 'Dashboard - Resident';
include __DIR__ . '/../layout/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    .stat-card { @apply bg-white rounded-xl border border-slate-200 p-4 transition-all; }
    .status-badge { @apply px-3 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border; }
</style>

<div class="max-w-5xl mx-auto px-4 pt-6 pb-10">
    
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Resident') ?></h1>
            <p class="text-slate-500 text-sm">Here's an update on your certificate requests.</p>
        </div>
        
        <a href="?page=create-request" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-all shadow-md flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
            New Request
        </a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-8" id="stats-container">
        <div class="stat-card">
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider mb-1">Total</p>
            <h3 class="text-xl font-bold text-slate-800" id="stat-total"><?= (int)($stats['total'] ?? 0) ?></h3>
        </div>
        <div class="stat-card border-amber-100 bg-amber-50/30">
            <p class="text-amber-600 text-[10px] font-bold uppercase tracking-wider mb-1">Pending</p>
            <h3 class="text-xl font-bold text-amber-600" id="stat-pending"><?= (int)($stats['pending'] ?? 0) ?></h3>
        </div>
        <div class="stat-card border-teal-100 bg-teal-50/30">
            <p class="text-teal-600 text-[10px] font-bold uppercase tracking-wider mb-1">Approved</p>
            <h3 class="text-xl font-bold text-teal-600" id="stat-approved"><?= (int)($stats['approved'] ?? 0) ?></h3>
        </div>
        <div class="stat-card border-blue-100 bg-blue-50/30">
            <p class="text-blue-600 text-[10px] font-bold uppercase tracking-wider mb-1">Ready</p>
            <h3 class="text-xl font-bold text-blue-600" id="stat-completed"><?= (int)($stats['completed'] ?? 0) ?></h3>
        </div>
        <div class="stat-card border-red-100 bg-red-50/30">
            <p class="text-red-600 text-[10px] font-bold uppercase tracking-wider mb-1">Rejected</p>
            <h3 class="text-xl font-bold text-red-600" id="stat-rejected"><?= (int)($stats['rejected'] ?? 0) ?></h3>
        </div>
    </div>

    <div class="mb-4 flex items-center justify-between px-1">
        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Recent Activity</h3>
        <a href="?page=my-requests" class="text-teal-600 text-xs font-bold hover:underline flex items-center gap-1">
            View All
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm">
        <?php if (empty($recentRequests)): ?>
            <div class="p-12 text-center">
                <p class="text-slate-400 text-sm font-medium italic">No recent requests found.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($recentRequests as $req): ?>
                    <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 border border-slate-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-semibold text-slate-800 leading-tight"><?= htmlspecialchars($req['certificate_name']) ?></h4>
                                <p class="text-[11px] text-slate-500 font-medium mt-0.5">
                                    #<?= $req['id'] ?> • <?= date('M d, Y', strtotime($req['created_at'])) ?>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <?php 
                                $status = $req['status'];
                                $statusClass = 'bg-slate-50 text-slate-600 border-slate-200';
                                switch ($status) {
                                    case 'Approved': $statusClass = 'bg-teal-50 text-teal-700 border-teal-100'; break;
                                    case 'Pending': $statusClass = 'bg-amber-50 text-amber-700 border-amber-100'; break;
                                    case 'Completed': $statusClass = 'bg-blue-50 text-blue-700 border-blue-100'; break;
                                    case 'Rejected': $statusClass = 'bg-red-50 text-red-700 border-red-100'; break;
                                }
                            ?>
                            <span class="status-badge <?= $statusClass ?>">
                                <?= htmlspecialchars($status) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateStats() {
        fetch('api.php?action=resident-stats')
            .then(response => response.json())
            .then(data => {
                const up = (id, val) => { if(document.getElementById(id)) document.getElementById(id).textContent = val || 0; };
                up('stat-total', data.total);
                up('stat-pending', data.pending);
                up('stat-approved', data.approved);
                up('stat-completed', data.completed);
                up('stat-rejected', data.rejected);
            })
            .catch(e => console.log('Sync error'));
    }
    setInterval(updateStats, 20000);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>