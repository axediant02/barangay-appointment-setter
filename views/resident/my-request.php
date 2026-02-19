<?php 
require '../views/layout/header.php'; 

?>

<style>
    .status-badge {
        min-width: 110px;
        justify-content: center;
        @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-[10px] uppercase tracking-wide border;
    }
    .status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
    .status-approved { @apply bg-teal-50 text-teal-700 border-teal-200; }
    .status-completed { @apply bg-cyan-50 text-cyan-700 border-cyan-200; }
    .status-rejected { @apply bg-red-50 text-red-700 border-red-200; }
    .status-cancelled { @apply bg-gray-100 text-gray-600 border-gray-300; }

    .request-card {
        @apply bg-white rounded-2xl border border-gray-200 shadow-sm transition-all duration-200 hover:border-teal-300 overflow-hidden;
    }

    .sticky-nav {
        position: sticky; top: 0; z-index: 50;
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #e2e8f0;
    }

    .remarks-highlight {
        background-color: #f0fdfa;
        border: 2px dashed #99f6e4;
    }
</style>

<nav class="sticky-nav mb-6">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="?page=resident-dashboard" class="inline-flex items-center gap-2 text-slate-600 font-bold hover:text-teal-600 transition group" aria-label="Back to dashboard">
            <span class="bg-white w-9 h-9 flex items-center justify-center rounded-lg shadow-sm border border-gray-200 group-hover:border-teal-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            <span class="text-sm tracking-tight">Back to Dashboard</span>
        </a>
        <div class="hidden sm:block">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Resident Portal v2.0</span>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-4 pb-20">

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php
        $totalRecords = (int)($totalRecords ?? 0);
        $limit = (int)($limit ?? 5);
        $currentPage = (int)($currentPage ?? 1);
        $from = $totalRecords === 0 ? 0 : (($currentPage - 1) * $limit) + 1;
        $to = min($currentPage * $limit, $totalRecords);
    ?>
    <div class="mb-10">
        <h2 class="text-5xl font-black text-slate-900 tracking-tighter italic uppercase">My Requests</h2>
        <p class="text-slate-500 text-xs font-bold mt-2 uppercase tracking-widest">
            Showing <?php echo $from; ?>–<?php echo $to; ?> of <?php echo $totalRecords; ?>
        </p>
    </div>

    <?php if (empty($requests)): ?>
        <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-20 text-center">
            <h3 class="text-xl font-bold text-slate-800 uppercase">No requests found</h3>
        </div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($requests as $req): 
            $status = $req['status'] ?? 'Pending';
            $statusLower = strtolower($status);
            $certificateId = $req['certificate_id'];
            $cancelCount = $userCancellationCounts[$certificateId] ?? 0;
            $isBanned = $userBanStatus[$certificateId] ?? false;

            $accentClass = 'border-l-slate-300';
            $statusEmoji = '⏳';

            switch($status) {
                case 'Approved':  $accentClass = 'border-l-teal-500'; $statusEmoji = '✅'; break;
                case 'Completed': $accentClass = 'border-l-cyan-500'; $statusEmoji = '💎'; break;
                case 'Rejected':  $accentClass = 'border-l-red-500';  $statusEmoji = '❌'; break;
                case 'Cancelled': $accentClass = 'border-l-gray-400'; $statusEmoji = '🚫'; break;
                default:          $accentClass = 'border-l-amber-500'; $statusEmoji = '⏳'; break;
            }
        ?>

        <div class="request-card border-l-[6px] <?php echo $accentClass; ?>" data-request-id="<?php echo (int)$req['id']; ?>">
            <div class="p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                
                <div class="flex-1 min-w-0">
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-tight truncate certificate-name">
                        <?php echo htmlspecialchars($req['certificate_name']); ?>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1 italic"><?php echo htmlspecialchars($req['reason_name'] ?? '—'); ?></p>
                    <p class="text-xs font-bold text-gray-500 mt-1 uppercase tracking-wider">
                        📅 Appointment Date: <span class="text-slate-700"><?php echo date('M d, Y', strtotime($req['appointment_date'])); ?></span>
                    </p>
                </div>

                <div class="flex items-center justify-end gap-4 w-full md:w-64 shrink-0">
                    <a href="?page=view-request&id=<?php echo (int)$req['id']; ?>" 
                       class="px-6 py-2 bg-teal-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-teal-700 transition shadow-sm">
                        View
                    </a>
                    
                    <div class="w-32 flex justify-end">
                        <span class="status-badge status-<?php echo $statusLower; ?> shadow-sm request-status">
                            <?php echo $statusEmoji . ' ' . $status; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-6 pb-6 border-t border-slate-50 pt-4">
                <div class="flex flex-col md:flex-row justify-between items-end gap-6">

                    <div class="w-full flex-1 remarks-wrapper">
                        <?php if (!empty($req['remarks'])): ?>
                            <div class="remarks-highlight rounded-xl p-4 border-2">
                                <p class="text-[9px] font-black uppercase tracking-widest text-teal-600 mb-1">Official Admin Remarks</p>
                                <p class="text-sm text-slate-700 italic font-medium">"<?php echo htmlspecialchars($req['remarks']); ?>"</p>
                            </div>
                        <?php else: ?>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 opacity-60">
                                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Status: Waiting for review</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-col items-end gap-1 shrink-0">
                        <?php if ($status === 'Pending'): ?>
                            <?php if ($isBanned): ?>
                                <div class="text-red-600 text-[10px] font-black uppercase tracking-widest text-right leading-tight">
                                    Cancellation limit reached<br>(<?php echo $cancelCount; ?>/3)
                                </div>
                            <?php else: ?>
                                <form method="POST" action="?page=cancel-request" onsubmit="return confirm('Cancel this request?');">
                                    <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                    <button type="submit" class="px-6 py-2 bg-white text-red-600 border border-red-200 rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-red-50 transition shadow-sm">
                                        Cancel Request
                                    </button>
                                </form>
                                <p class="text-[9px] text-slate-400 font-bold uppercase">Used: <?php echo $cancelCount; ?>/3</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($totalPages > 1): ?>
    <div class="mt-12 flex justify-center items-center gap-2">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=my-requests&page_num=<?php echo $i; ?>" class="px-4 py-2 rounded-lg font-black text-xs <?php echo $i === $currentPage ? 'bg-teal-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-teal-500 hover:text-white transition'; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

</div>

<script>
function updateRequestCard(updatedReq) {
    const card = document.querySelector(`[data-request-id="${updatedReq.id}"]`);
    if (!card) return;

    const badge = card.querySelector('.request-status');
    let emoji = '⏳';
    let borderClass = 'border-l-amber-500';

    switch (updatedReq.status) {
        case 'Approved': emoji='✅'; borderClass='border-l-teal-500'; break;
        case 'Completed': emoji='💎'; borderClass='border-l-cyan-500'; break;
        case 'Rejected': emoji='❌'; borderClass='border-l-red-500'; break;
        case 'Cancelled': emoji='🚫'; borderClass='border-l-gray-400'; break;
    }

    badge.className = 'status-badge request-status shadow-sm status-' + updatedReq.status.toLowerCase();
    badge.innerText = emoji + ' ' + updatedReq.status;

    card.classList.remove('border-l-amber-500','border-l-teal-500','border-l-cyan-500','border-l-red-500','border-l-gray-400');
    card.classList.add(borderClass);

    const remarksWrapper = card.querySelector('.remarks-wrapper');
    if (updatedReq.remarks) {
        remarksWrapper.innerHTML = `
            <div class="remarks-highlight rounded-xl p-4 border-2">
                <p class="text-[9px] font-black uppercase tracking-widest text-teal-600 mb-1">
                    Official Admin Remarks
                </p>
                <p class="text-sm text-slate-700 italic font-medium">
                    "${updatedReq.remarks}"
                </p>
            </div>
        `;
    } else {
        remarksWrapper.innerHTML = `
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 opacity-60">
                <p class="text-[9px] font-black uppercase tracking-widest text-slate-400">Status: Waiting for review</p>
            </div>
        `;
    }
}

function syncRequests() {
    var apiUrl = (typeof APP_BASE !== 'undefined' ? APP_BASE : '') + 'api.php?action=my-requests';
    fetch(apiUrl)
        .then(function(res) {
            var ct = res.headers.get('Content-Type') || '';
            if (!res.ok) throw new Error('API returned ' + res.status);
            if (!ct.includes('application/json')) throw new Error('API returned non-JSON (check that api.php is reachable at ' + apiUrl + ')');
            return res.json();
        })
        .then(function(data) {
            if (Array.isArray(data)) data.forEach(function(req) { updateRequestCard(req); });
        })
        .catch(function(err) { console.error('Realtime sync error:', err.message || err); });
}

setInterval(syncRequests, 5000);
</script>

<?php require '../views/layout/footer.php'; ?>