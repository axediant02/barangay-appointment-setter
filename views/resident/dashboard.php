<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'resident') {
    header("Location: ?page=login");
    exit;
}

$userId = $_SESSION['user_id'];

$username = $_SESSION['username'] ?? 'Resident';

// Stats Query
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

// Recent Activity
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

<style type="text/tailwindcss">
    @layer components {
        .stat-card {
            @apply bg-white rounded-2xl border border-slate-200 p-5 transition-all duration-300 hover:shadow-lg hover:shadow-slate-200/50 hover:-translate-y-1;
        }
        .status-badge {
            @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border transition-colors;
        }
        /* Status Colors */
        .status-pending   { @apply bg-amber-50 text-amber-700 border-amber-200; }
        .status-approved  { @apply bg-emerald-50 text-emerald-700 border-emerald-200; }
        .status-completed { @apply bg-blue-50 text-blue-700 border-blue-200; }
        .status-rejected  { @apply bg-red-50 text-red-700 border-red-200; }
        
        .btn-primary {
            @apply bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-xl text-sm font-bold transition-all shadow-lg shadow-teal-100 flex items-center gap-2 active:scale-95;
        }
    }
</style>

<div class="max-w-5xl mx-auto px-4 pt-8 pb-16">

    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
                Welcome back, <span class="text-teal-600"><?= htmlspecialchars($username) ?></span> 👋
            </h1>
            <p class="text-slate-500 font-medium mt-1">Monitor your document applications and status updates.</p>
        </div>
        
        <a href="?page=create-request" class="btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
            </svg>
            New Request
        </a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-12">
        <div class="stat-card">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-slate-100 rounded-lg text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                </div>
                <span class="text-2xl font-black text-slate-800" id="stat-total"><?= (int)($stats['total'] ?? 0) ?></span>
            </div>
            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Total Requests</p>
        </div>

        <div class="stat-card border-amber-100 bg-amber-50/20">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-amber-100 rounded-lg text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-2xl font-black text-amber-600" id="stat-pending"><?= (int)($stats['pending'] ?? 0) ?></span>
            </div>
            <p class="text-amber-600/70 text-[10px] font-bold uppercase tracking-widest">Pending</p>
        </div>

        <div class="stat-card border-emerald-100 bg-emerald-50/20">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-emerald-100 rounded-lg text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-2xl font-black text-emerald-600" id="stat-approved"><?= (int)($stats['approved'] ?? 0) ?></span>
            </div>
            <p class="text-emerald-600/70 text-[10px] font-bold uppercase tracking-widest">Approved</p>
        </div>

        <div class="stat-card border-blue-100 bg-blue-50/20">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-blue-100 rounded-lg text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </div>
                <span class="text-2xl font-black text-blue-600" id="stat-completed"><?= (int)($stats['completed'] ?? 0) ?></span>
            </div>
            <p class="text-blue-600/70 text-[10px] font-bold uppercase tracking-widest">Ready</p>
        </div>

        <div class="stat-card border-red-100 bg-red-50/20">
            <div class="flex items-center justify-between mb-3">
                <div class="p-2 bg-red-100 rounded-lg text-red-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-2xl font-black text-red-600" id="stat-rejected"><?= (int)($stats['rejected'] ?? 0) ?></span>
            </div>
            <p class="text-red-600/70 text-[10px] font-bold uppercase tracking-widest">Rejected</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-5 px-1">
        <div class="flex items-center gap-2">
            <div class="w-1 h-4 bg-teal-500 rounded-full"></div>
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-widest">Recent Activity</h3>
        </div>
        <a href="?page=my-requests" class="text-teal-600 text-xs font-bold hover:text-teal-700 transition-colors flex items-center gap-1 group">
            See all requests
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>

    <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm shadow-slate-200/60">
        <?php if (empty($recentRequests)): ?>
            <div class="p-16 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h4 class="text-slate-900 font-bold">No requests yet</h4>
                <p class="text-slate-400 text-sm mt-1">Apply for a certificate to see it tracked here.</p>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($recentRequests as $req): 
                    $status = $req['status'];
                    $statusClass = 'status-' . strtolower($status);
                ?>
                    <div class="p-5 flex items-center justify-between hover:bg-slate-50/80 transition-all group">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-slate-400 border border-slate-200 shadow-sm group-hover:border-teal-200 group-hover:text-teal-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800 leading-tight mb-1 group-hover:text-teal-700 transition-colors">
                                    <?= htmlspecialchars($req['certificate_name']) ?>
                                </h4>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400 font-bold uppercase tracking-wider">
                                    <span>#<?= str_pad($req['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    <span class="text-slate-200">•</span>
                                    <span><?= date('M d, Y', strtotime($req['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <span class="status-badge <?= $statusClass ?>">
                                <span class="w-1.5 h-1.5 rounded-full bg-current opacity-60"></span>
                                <?= htmlspecialchars($status) ?>
                            </span>
                            <a href="?page=view-request&id=<?= $req['id'] ?>" class="p-2 text-slate-300 hover:text-teal-600 transition-colors hidden sm:block">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
/**
 * Polling function to keep stats fresh without full page reloads
 */
function updateStats() {
    var apiUrl = (typeof APP_BASE !== 'undefined' ? APP_BASE : '') + 'api.php?action=resident-stats';
    fetch(apiUrl)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            const up = (id, val) => {
                const el = document.getElementById(id);
                if (el && el.textContent != val) {
                    el.classList.add('scale-110', 'text-teal-500');
                    setTimeout(() => {
                        el.textContent = val || 0;
                        el.classList.remove('scale-110', 'text-teal-500');
                    }, 200);
                }
            };
            up('stat-total', data.total);
            up('stat-pending', data.pending);
            up('stat-approved', data.approved);
            up('stat-completed', data.completed);
            up('stat-rejected', data.rejected);
        })
        .catch(e => console.error('Dashboard Sync Error'));
}
setInterval(updateStats, 20000);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>