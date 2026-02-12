<?php require '../views/layout/header.php'; ?>

<style>
.status-badge {
    @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-xs uppercase tracking-wide border;
}
.status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
.status-approved { @apply bg-teal-50 text-teal-700 border-teal-200; }
.status-completed { @apply bg-cyan-50 text-cyan-700 border-cyan-200; }
.status-rejected { @apply bg-red-50 text-red-700 border-red-200; }
.status-cancelled { @apply bg-gray-100 text-gray-600 border-gray-300; }

.request-card {
    @apply bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-200 hover:border-teal-300 overflow-hidden;
}

.sticky-nav {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(248, 250, 252, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
}

.remarks-highlight {
    background-color: #f0fdfa;
    border: 2px dashed #99f6e4;
    box-shadow: 0 0 15px rgba(20, 184, 166, 0.05);
}
</style>

<nav class="sticky-nav mb-6">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <button onclick="history.back()" class="inline-flex items-center gap-2 text-slate-600 font-bold hover:text-teal-600 transition group">
            <span class="bg-white w-9 h-9 flex items-center justify-center rounded-lg shadow-sm border border-gray-200 group-hover:border-teal-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            <span class="text-sm tracking-tight">Return</span>
        </button>
        <div class="hidden sm:block">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Resident Portal v2.0</span>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-4">

<?php if (!empty($_SESSION['success'])): ?>
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg animate-pulse">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="mb-10">
    <h2 class="text-5xl font-black text-slate-900 tracking-tighter italic uppercase">My Requests</h2>
    <div class="flex items-center gap-2 mt-2">
        <span class="h-1 w-8 bg-teal-500 rounded-full"></span>
        <p class="text-slate-500 text-sm font-medium">Showing <?= count($requests ?? []) ?> record(s)</p>
    </div>
</div>

<?php if (empty($requests)): ?>
<div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-20 text-center">
    <div class="text-5xl mb-4">📂</div>
    <h3 class="text-xl font-bold text-slate-800 uppercase tracking-tight">No requests found</h3>
    <p class="text-slate-400 text-sm mt-1">You haven't applied for any certificates yet.</p>
</div>
<?php else: ?>

<div class="space-y-4">
<?php foreach ($requests as $req): 
    $status = $req['status'] ?? 'Pending';
    $statusLower = strtolower($status);

    $accentClass = 'border-l-slate-300';
    $statusEmoji = '⏳';

    switch($status) {
        case 'Approved':  $accentClass = 'border-l-teal-500'; $statusEmoji = '✅'; break;
        case 'Completed': $accentClass = 'border-l-cyan-500'; $statusEmoji = '💎'; break;
        case 'Rejected':  $accentClass = 'border-l-red-500';  $statusEmoji = '❌'; break;
        case 'Cancelled': $accentClass = 'border-l-gray-400'; $statusEmoji = '🚫'; break;
        case 'Pending':   $accentClass = 'border-l-amber-500'; $statusEmoji = '⏳'; break;
    }
?>

<div class="request-card border-l-[6px] <?= $accentClass ?> hover:shadow-md transition-shadow">
    <div class="p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight"><?= htmlspecialchars($req['certificate_name']) ?></h3>
            <p class="text-sm font-medium text-gray-500 mt-1">
                📅 Appointment: <span class="text-slate-700 font-bold"><?= date('M d, Y', strtotime($req['appointment_date'])) ?></span>
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 w-full sm:w-auto sm:justify-end">

            <span class="status-badge status-<?= $statusLower ?> shadow-sm">
                <?= $statusEmoji ?> <?= $status ?>
            </span>

            <?php if ($status === 'Pending'): ?>
                <form method="POST" action="?page=cancel-request" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                    <button type="submit" class="px-5 py-2.5 bg-white text-red-600 border border-red-200 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-red-50 transition active:scale-95 shadow-sm">
                        Cancel
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <div class="px-6 pb-5">
        <?php if (!empty($req['remarks'])): ?>
            <div class="remarks-highlight rounded-xl p-4 border-2">
                <p class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 bg-teal-500 rounded-full animate-pulse"></span>
                    Official Admin Remarks
                </p>
                <p class="text-sm text-slate-700 leading-relaxed italic font-medium">"<?= htmlspecialchars($req['remarks']) ?>"</p>
            </div>
        <?php else: ?>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 opacity-60">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Admin Remarks</p>
                <p class="text-xs text-slate-400 italic font-medium leading-relaxed">No specific instructions or remarks provided for this request yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
<div class="mt-8 flex justify-center items-center gap-2">
    <?php if ($currentPage > 1): ?>
        <a href="?page=my-requests&page_num=<?= $currentPage - 1 ?>" class="px-3 py-1 bg-teal-500 text-white rounded-lg font-bold text-xs hover:bg-teal-600 transition">Prev</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?page=my-requests&page_num=<?= $i ?>" class="px-3 py-1 rounded-lg font-bold text-xs <?= $i === $currentPage ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-teal-500 hover:text-white transition' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($currentPage < $totalPages): ?>
        <a href="?page=my-requests&page_num=<?= $currentPage + 1 ?>" class="px-3 py-1 bg-teal-500 text-white rounded-lg font-bold text-xs hover:bg-teal-600 transition">Next</a>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="mt-16 text-center">
    <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">End of Records</p>
</div>

</div>

<?php require '../views/layout/footer.php'; ?>