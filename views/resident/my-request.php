<?php require '../views/layout/header.php'; ?>

<style type="text/tailwindcss">
    /* Improved Badge System */
    .status-badge {
        @apply inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg font-bold text-[10px] uppercase tracking-wider border shadow-sm transition-all duration-300;
    }
    .status-pending   { @apply bg-amber-50 text-amber-700 border-amber-200; }
    .status-approved  { @apply bg-emerald-50 text-emerald-700 border-emerald-200; }
    .status-completed { @apply bg-blue-50 text-blue-700 border-blue-200; }
    .status-rejected  { @apply bg-red-50 text-red-700 border-red-200; }
    .status-cancelled { @apply bg-slate-100 text-slate-500 border-slate-200; }

    .request-card {
        @apply bg-white rounded-2xl border border-slate-200 transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/40 overflow-hidden;
    }

    .sticky-nav {
        @apply sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200;
    }
    
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>

<nav class="sticky-nav mb-8">
    <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="?page=resident-dashboard" class="flex items-center gap-2 text-slate-500 hover:text-teal-600 transition font-bold text-sm group">
            <div class="p-1.5 rounded-lg bg-slate-50 border border-slate-200 group-hover:border-teal-200 group-hover:bg-teal-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                </svg>
            </div>
            Dashboard
        </a>
        <div class="flex items-center gap-2 bg-slate-50 px-3 py-1 rounded-full border border-slate-100">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">System Live</span>
        </div>
    </div>
</nav>

<div class="max-w-5xl mx-auto px-4 pb-24">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Request History</h1>
            <p class="text-slate-500 font-medium mt-1">Track the progress of your official documents.</p>
        </div>
        <div class="flex items-center gap-4 bg-white p-1.5 rounded-2xl border border-slate-200 shadow-sm">
            <div class="px-4 py-2 border-r border-slate-100">
                <span class="text-[9px] font-bold text-slate-400 uppercase block tracking-tighter">Applications</span>
                <span class="text-lg font-black text-slate-800 leading-none"><?= $totalRecords ?? 0 ?></span>
            </div>
            <a href="?page=create-request" class="bg-teal-600 hover:bg-teal-700 text-white p-2.5 rounded-xl transition shadow-md shadow-teal-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" /></svg>
            </a>
        </div>
    </div>

    <?php if (empty($requests)): ?>
        <div class="text-center py-20 bg-slate-50 rounded-3xl border-2 border-dashed border-slate-200">
             <p class="text-slate-400 font-bold">No requests found.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
        <?php foreach ($requests as $req): 
            $status = $req['status'] ?? 'Pending';
            $statusLower = strtolower($status);
            
            $statusIcon = match($status) {
                'Approved'  => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>',
                'Completed' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                'Rejected'  => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>',
                'Cancelled' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>',
                default     => '<svg class="w-3.5 h-3.5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            $accentClass = match($status) {
                'Approved'  => 'bg-emerald-500',
                'Completed' => 'bg-blue-500',
                'Rejected'  => 'bg-red-500',
                'Cancelled' => 'bg-slate-300',
                default     => 'bg-amber-400'
            };
        ?>

        <div class="request-card" data-request-id="<?= (int)$req['id']; ?>">
            <div class="p-5">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    
                    <div class="flex items-center gap-5 flex-1 min-w-0">
                        <div class="w-1.5 h-10 rounded-full <?= $accentClass ?> flex-shrink-0 shadow-sm shadow-<?= $statusLower ?>-200"></div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-slate-800 tracking-tight truncate mb-1">
                                <?= htmlspecialchars($req['certificate_name']); ?>
                            </h3>
                            <div class="flex items-center gap-4 text-[11px] font-bold text-slate-400 uppercase tracking-wide">
                                <span class="flex items-center gap-1.5 text-slate-600">
                                    <svg class="w-3.5 h-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <?= date('M d, Y', strtotime($req['appointment_date'])); ?>
                                </span>
                                <span class="opacity-30">|</span>
                                <span class="truncate italic font-medium lowercase text-slate-400">#<?= str_pad($req['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 lg:justify-end flex-shrink-0">
                        
                        <div class="hidden sm:flex justify-end w-28">
                            <span class="status-badge status-<?= $statusLower ?> request-status whitespace-nowrap">
                                <?= $statusIcon ?>
                                <span><?= $status ?></span>
                            </span>
                        </div>

                        <div class="flex items-center justify-end gap-2 sm:border-l sm:border-slate-100 sm:pl-6 min-w-[110px]">
                            <a href="?page=view-request&id=<?= (int)$req['id']; ?>" 
                               class="text-center px-5 py-2 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition shadow-sm h-[40px] flex items-center justify-center">
                                View
                            </a>
                            
                            <?php if ($status === 'Pending' && !($userBanStatus[$req['certificate_id']] ?? false)): ?>
                                <form method="POST" action="?page=cancel-request" onsubmit="return confirm('Cancel request?');" class="flex-shrink-0">
                                    <input type="hidden" name="request_id" value="<?= $req['id']; ?>">
                                    <button type="submit" class="w-[40px] h-[40px] flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl border border-slate-200 hover:border-red-100 transition shadow-sm" title="Cancel">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="w-[40px] hidden sm:block"></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="sm:hidden -mt-4">
                         <span class="status-badge status-<?= $statusLower ?> request-status w-full justify-center">
                            <?= $statusIcon ?>
                            <span><?= $status ?></span>
                        </span>
                    </div>

                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function updateRequestCard(updatedReq) {
    const card = document.querySelector(`[data-request-id="${updatedReq.id}"]`);
    if (!card) return;

    // Detect current status from the first badge
    const badgeSpan = card.querySelector('.request-status span');
    if (!badgeSpan) return;

    const currentStatus = badgeSpan.innerText.trim();
    if (currentStatus === updatedReq.status) return;

    const statusLower = updatedReq.status.toLowerCase();
    
    // Status Icon Mapping
    const icons = {
        'Approved': `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>`,
        'Completed': `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>`,
        'Rejected': `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>`,
        'Cancelled': `<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>`,
        'Pending': `<svg class="w-3.5 h-3.5 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`
    };

    // Accent Color Mapping
    const accentClasses = {
        'Approved': 'bg-emerald-500',
        'Completed': 'bg-blue-500',
        'Rejected': 'bg-red-500',
        'Cancelled': 'bg-slate-300',
        'Pending': 'bg-amber-400'
    };

    const iconHtml = icons[updatedReq.status] || icons['Pending'];
    const accentClass = accentClasses[updatedReq.status] || accentClasses['Pending'];

    // Update All Badges (Desktop & Mobile)
    const allBadges = card.querySelectorAll('.request-status');
    allBadges.forEach(badge => {
        // Keep special classes like justify-center or w-full if present
        const extraClasses = Array.from(badge.classList).filter(c => c !== 'status-badge' && !c.startsWith('status-') && c !== 'request-status' && c !== 'whitespace-nowrap').join(' ');
        badge.className = `status-badge status-${statusLower} request-status whitespace-nowrap ${extraClasses}`;
        badge.innerHTML = `${iconHtml}<span>${updatedReq.status}</span>`;
    });

    // Update Accent Bar & Shadow
    const accentBar = card.querySelector('.rounded-full[class*="h-10"]');
    if (accentBar) {
        accentBar.className = `w-1.5 h-10 rounded-full ${accentClass} flex-shrink-0 shadow-sm shadow-${statusLower}-200`;
    }

    // Handle Cancel Button Logic
    if (updatedReq.status !== 'Pending') {
        const cancelForm = card.querySelector('form[action*="cancel-request"]');
        if (cancelForm) {
            const spacer = document.createElement('div');
            spacer.className = "w-[40px] hidden sm:block";
            cancelForm.replaceWith(spacer);
        }
    }
}

async function syncRequests() {
    try {
        const baseUrl = window.APP_BASE || '';
        const response = await fetch(baseUrl + 'api.php?action=my-requests');
        if (response.ok) {
            const data = await response.json();
            if (Array.isArray(data)) data.forEach(updateRequestCard);
        }
    } catch (err) {
        console.error('Sync failed:', err);
    }
}
// Run sync every 10 seconds
setInterval(syncRequests, 10000);
// Also run on load
document.addEventListener('DOMContentLoaded', syncRequests);
</script>

<?php require '../views/layout/footer.php'; ?>