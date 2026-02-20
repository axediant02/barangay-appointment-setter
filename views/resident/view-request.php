<?php require __DIR__ . '/../layout/header.php'; ?>

<style type="text/tailwindcss">
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap');
    
    body { font-family: 'Plus Jakarta Sans', sans-serif; @apply bg-slate-50 text-slate-900; }

    .status-badge { 
        @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide border transition-all; 
    }
    
    .status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
    .status-approved { @apply bg-emerald-50 text-emerald-700 border-emerald-200; }
    .status-completed { @apply bg-blue-50 text-blue-700 border-blue-200; }
    .status-rejected { @apply bg-red-50 text-red-700 border-red-200; }
    .status-cancelled { @apply bg-slate-100 text-slate-600 border-slate-200; }

    .info-card {
        @apply bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden;
    }

    .detail-label {
        @apply text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1;
    }
    .detail-value {
        @apply text-base font-medium text-slate-800;
    }

    .timeline-node { @apply relative flex-1 flex flex-col items-center; }
    .timeline-node::after {
        content: '';
        @apply absolute top-5 left-1/2 w-full h-[2px] bg-slate-100 -z-0;
    }
    .timeline-node:last-child::after { display: none; }
    
    .node-circle {
        @apply w-10 h-10 rounded-full flex items-center justify-center border-4 border-white shadow-sm z-10 transition-all duration-300;
    }
    .node-active .node-circle { @apply bg-teal-600 ring-4 ring-teal-50; }
    .node-inactive .node-circle { @apply bg-slate-200; }
    .node-completed .node-circle { @apply bg-teal-500; }
</style>

<nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-4 h-16 flex items-center justify-between">
        <a href="?page=my-requests" class="flex items-center gap-2 group text-slate-600 hover:text-teal-600 transition-colors">
            <div class="p-2 rounded-lg bg-slate-100 group-hover:bg-teal-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </div>
            <span class="text-sm font-bold uppercase tracking-tight">Back</span>
        </a>
        <div class="flex items-center gap-3">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Request ID</span>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-md border border-slate-200">
                #<?= str_pad($request['id'], 4, '0', STR_PAD_LEFT) ?>
            </span>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto px-4 py-8 lg:py-12">
    
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                <?= htmlspecialchars($request['certificate_name'] ?? 'Request Details') ?>
            </h1>
            <div class="mt-2 flex items-center gap-4 text-slate-500 text-sm">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2" stroke-linecap="round"/></svg>
                    Submitted <?= date('M d, Y', strtotime($request['created_at'])) ?>
                </span>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <?php if (($request['status'] ?? '') === 'Pending'): ?>
                <a href="?page=edit-request&id=<?= (int)$request['id'] ?>" class="px-5 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-xs font-bold uppercase hover:bg-slate-50 transition shadow-sm">
                    Edit
                </a>
            <?php endif; ?>
            
            <?php
            $status = $request['status'] ?? 'Pending';
            $lowerStatus = strtolower($status);
            ?>
            <span class="status-badge status-<?= $lowerStatus ?> px-5 py-2 text-sm">
                <?= htmlspecialchars($status) ?>
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 space-y-6">
            
            <div class="info-card p-6 md:p-8">
                <h2 class="detail-label mb-8">Live Tracking</h2>
                <div class="flex items-start">
                    <?php
                    $stages = ['Pending', 'Approved', 'Completed'];
                    $currentIndex = array_search($status, $stages);
                    if ($status === 'Rejected' || $status === 'Cancelled') $currentIndex = -1;
                    
                    foreach ($stages as $index => $stage):
                        $state = ($index === $currentIndex) ? 'node-active' : (($index < $currentIndex) ? 'node-completed' : 'node-inactive');
                    ?>
                    <div class="timeline-node <?= $state ?>">
                        <div class="node-circle">
                            <?php if ($index < $currentIndex): ?>
                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                            <?php elseif ($index === $currentIndex): ?>
                                <div class="w-2.5 h-2.5 bg-white rounded-full animate-pulse"></div>
                            <?php endif; ?>
                        </div>
                        <span class="mt-3 text-[10px] font-bold uppercase tracking-wider <?= $index <= $currentIndex ? 'text-teal-700' : 'text-slate-400' ?>"><?= $stage ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="info-card">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-sm font-bold text-slate-700">Application Information</h3>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <div>
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value"><?= htmlspecialchars($request['full_name']) ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Appointment Date</span>
                        <span class="detail-value text-teal-600 font-bold"><?= date('F d, Y', strtotime($request['appointment_date'])) ?></span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="detail-label">Purpose of Request</span>
                        <span class="detail-value">"<?= htmlspecialchars($request['reason_name'] ?? '—') ?>"</span>
                    </div>
                    <div>
                        <span class="detail-label">Contact</span>
                        <span class="detail-value"><?= htmlspecialchars($request['contact_number']) ?></span>
                    </div>
                    <div>
                        <span class="detail-label">Birthday</span>
                        <span class="detail-value"><?= $request['birthday'] ? date('M d, Y', strtotime($request['birthday'])) : '—' ?></span>
                    </div>
                    <div class="md:col-span-2">
                        <span class="detail-label">Address</span>
                        <p class="text-slate-700 leading-relaxed"><?= htmlspecialchars($request['address']) ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($request['remarks'])): ?>
                <div class="rounded-2xl p-6 bg-teal-50 border border-teal-100 flex gap-4">
                    <div class="shrink-0 w-10 h-10 bg-teal-500 rounded-xl flex items-center justify-center text-white shadow-sm">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" stroke-width="2"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-teal-800 uppercase tracking-wider mb-1">Admin Remarks</h4>
                        <p class="text-teal-900 leading-relaxed italic text-sm font-medium">"<?= htmlspecialchars($request['remarks']) ?>"</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-4 space-y-6">
            <div class="info-card">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-slate-700">Identity Proof</h3>
                        <?php if ($request['is_verified']): ?>
                            <span class="text-emerald-500 flex items-center gap-1 text-[10px] font-bold uppercase">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/></svg>
                                Verified
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($request['id_image_path'])): 
                        $displayPath = (strpos($request['id_image_path'], 'public/') === 0 || strpos($request['id_image_path'], 'http') === 0) 
                            ? $request['id_image_path'] 
                            : 'public/' . $request['id_image_path'];
                    ?>
                        <div class="relative group rounded-xl overflow-hidden bg-slate-100 aspect-video border border-slate-200">
                            <img src="<?= htmlspecialchars($displayPath) ?>" alt="ID Proof" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                <span class="bg-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-xl">View Large</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="aspect-video bg-slate-50 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center p-6 text-center">
                            <span class="text-xs font-medium text-slate-400">No document uploaded</span>
                        </div>
                    <?php endif; ?>

                    <div class="mt-4 p-4 bg-slate-50 rounded-xl">
                        <p class="text-[11px] leading-relaxed text-slate-500 font-medium">
                            <strong>Note:</strong> Please bring your original physical ID on your scheduled date for final verification.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layout/footer.php'; ?>