<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Requests | BrgyPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }

        .status-badge {
            /* Increased text size to 12px (text-xs) and added more padding */
            @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-xs uppercase tracking-wide border;
        }
        .status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
        .status-approved { @apply bg-teal-50 text-teal-700 border-teal-200; }
        .status-completed { @apply bg-cyan-50 text-cyan-700 border-cyan-200; }
        .status-rejected { @apply bg-red-50 text-red-700 border-red-200; }

        .request-card {
            @apply bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-200 hover:border-teal-300 overflow-hidden;
        }
    </style>
</head>
<body class="min-h-screen pb-12">

<div class="max-w-4xl mx-auto py-8 px-4">
    
    <div class="flex justify-between items-center mb-8">
        <a href="?page=resident-dashboard" class="group flex items-center gap-3 text-gray-500 hover:text-teal-600 transition-colors">
            <span class="flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 shadow-sm group-hover:border-teal-200 transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </span>
            <span class="text-xs font-bold uppercase tracking-widest">Dashboard</span>
        </a>
        
        <?php if (!empty($requests)): ?>
            <a href="?page=create-request" class="bg-teal-600 hover:bg-teal-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm shadow-md transition-all flex items-center gap-2 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Request
            </a>
        <?php endif; ?>
    </div>

    <div class="mb-10">
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">My Requests</h2>
        <p class="text-slate-500 text-sm font-medium mt-1 uppercase tracking-wide">Showing <?= count($requests ?? []) ?> record(s)</p>
    </div>

    <?php if (empty($requests)): ?>
        <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-16 text-center">
            <div class="text-6xl mb-4 opacity-30">📋</div>
            <h3 class="text-xl font-bold text-slate-800">No requests found</h3>
            <p class="text-slate-500 text-sm mb-8">Ready to get started? Request your document below.</p>
            <a href="?page=create-request" class="inline-block px-8 py-3 bg-teal-600 text-white rounded-xl font-black text-xs uppercase tracking-widest">Request Now</a>
        </div>
    <?php else: ?>
        
        <div class="space-y-4" id="requests-container">
            <?php foreach ($requests as $req): 
                $status = $req['status'] ?? 'Pending';
                $statusLower = strtolower($status);
                
                $accentClass = 'border-l-slate-300';
                $statusEmoji = '⏳';
                switch($status) {
                    case 'Approved':  $accentClass = 'border-l-teal-500'; $statusEmoji = '✅'; break;
                    case 'Completed': $accentClass = 'border-l-cyan-500'; $statusEmoji = '💎'; break;
                    case 'Rejected':  $accentClass = 'border-l-red-500';  $statusEmoji = '❌'; break;
                    case 'Pending':   $accentClass = 'border-l-amber-500'; $statusEmoji = '⏳'; break;
                }
            ?>
                <div class="request-card border-l-[6px] <?= $accentClass ?>" data-request-id="<?= $req['id'] ?>">
                    <div class="p-5 md:p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center text-2xl border border-slate-100 shadow-inner">📄</div>
                                <div>
                                    <h3 class="text-lg font-extrabold text-slate-900 leading-tight mb-1">
                                        <?= htmlspecialchars($req['certificate_name']) ?>
                                    </h3>
                                    <div class="flex items-center gap-3 text-slate-500">
                                        <span class="text-xs font-black bg-slate-100 px-2 py-0.5 rounded text-slate-600">#<?= str_pad($req['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                        <span class="text-sm font-semibold flex items-center gap-1">
                                            <span class="opacity-60">📅</span> <?= date('M d, Y', strtotime($req['appointment_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 pt-4 md:pt-0">
                                <span class="status-badge status-<?= $statusLower ?>" data-status="<?= $status ?>">
                                    <span class="text-sm"><?= $statusEmoji ?></span>
                                    <span class="status-text"><?= $status ?></span>
                                </span>
                                
                                <div class="action-buttons">
                                    <?php if ($status === 'Completed'): ?>
                                        <button class="px-5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-bold text-xs uppercase tracking-wider shadow-sm transition-all active:scale-95">Get File</button>
                                    <?php elseif ($status === 'Approved'): ?>
                                        <button class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold text-xs uppercase tracking-wider shadow-sm transition-all active:scale-95">Set Appointment</button>
                                    <?php else: ?>
                                        <button class="px-5 py-2 bg-slate-50 text-slate-400 border border-slate-200 rounded-lg font-bold text-xs uppercase tracking-wider" disabled>In Review</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($req['remarks'])): ?>
                            <div class="mt-5 py-3 px-4 bg-slate-50 border-l-4 border-teal-200 text-sm text-slate-700 rounded-r-lg">
                                <p class="leading-relaxed">
                                    <span class="font-black text-[10px] uppercase text-teal-600 mr-2 tracking-widest block mb-1">Admin Remarks:</span>
                                    <span class="remarks-text italic italic font-medium">"<?= htmlspecialchars($req['remarks']) ?>"</span>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-12 py-8 border-t border-slate-200 text-center">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-[0.4em]">End of your request list</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>