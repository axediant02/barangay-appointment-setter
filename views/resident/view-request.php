<?php require __DIR__ . '/../layout/header.php'; ?>

<style>
.status-badge { @apply inline-flex items-center gap-2 px-4 py-1.5 rounded-full font-black text-[10px] uppercase tracking-widest border transition-all; }
.status-badge svg { width: 14px; height: 14px; }
.status-pending { background-color: #fef3c7; color: #92400e; border-color: #fde68a; }
.status-approved { background-color: #dcfce7; color: #166534; border-color: #bbf7d0; }
.status-completed { background-color: #ecfeff; color: #0891b2; border-color: #cffafe; }
.status-rejected { background-color: #fee2e2; color: #991b1b; border-color: #fecaca; }
.status-cancelled { background-color: #f1f5f9; color: #475569; border-color: #e2e8f0; }
.sticky-nav {
    position: sticky; top: 0; z-index: 50;
    background: rgba(248, 250, 252, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
}
.remarks-highlight { background-color: #f0fdfa; border: 2px dashed #99f6e4; box-shadow: 0 0 15px rgba(20, 184, 166, 0.05); }
.detail-row { @apply flex justify-between items-start py-3 border-b border-slate-100 last:border-0; }
.detail-label { @apply text-[10px] font-black uppercase tracking-widest text-slate-400; }
.detail-value { @apply text-sm font-semibold text-slate-800 text-right; }
</style>

<nav class="sticky-nav mb-6">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="?page=my-requests" class="inline-flex items-center gap-2 text-slate-600 font-bold hover:text-teal-600 transition group" aria-label="Back to my requests">
            <span class="bg-white w-9 h-9 flex items-center justify-center rounded-lg shadow-sm border border-gray-200 group-hover:border-teal-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            <span class="text-sm tracking-tight">Back to My Requests</span>
        </a>
        <div class="hidden sm:block">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Request #<?= (int)($request['id'] ?? 0) ?></span>
        </div>
    </div>
</nav>

<div class="max-w-4xl mx-auto px-4">

<?php if (!empty($_SESSION['error'])): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['success'])): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
        <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-2xl shadow-xl border-2 border-slate-200 overflow-hidden">
    <div class="bg-slate-900 px-6 py-8 text-white">
        <h1 class="text-2xl font-black tracking-tight"><?= htmlspecialchars($request['certificate_name'] ?? 'Request') ?></h1>
        <p class="text-slate-400 mt-1 font-bold uppercase text-xs tracking-widest">Request details</p>
    </div>

    <div class="p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <?php
            $status = $request['status'] ?? 'Pending';
            $lowerStatus = strtolower($status);
            $icons = [
                'pending'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" /></svg>',
                'approved'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>',
                'completed' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" /></svg>',
                'rejected'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>',
                'cancelled' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" /></svg>'
            ];
            $icon = $icons[$lowerStatus] ?? '';
            ?>
            <span class="status-badge status-<?= $lowerStatus ?>">
                <?= $icon ?>
                <?= htmlspecialchars($status) ?>
            </span>
            <span class="text-slate-500 text-xs font-semibold">
                Submitted <?= date('M d, Y \a\t g:i A', strtotime($request['created_at'] ?? 'now')) ?>
            </span>
        </div>

        <div class="space-y-0 divide-y divide-slate-100 rounded-xl border border-slate-100 overflow-hidden bg-slate-50/50">
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Certificate</span>
                <span class="detail-value"><?= htmlspecialchars($request['certificate_name']) ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Reason for Request</span>
                <span class="detail-value"><?= htmlspecialchars($request['reason_for_request'] ?? '—') ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Status</span>
                <span class="detail-value"><?= htmlspecialchars($request['status']) ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Appointment date</span>
                <span class="detail-value"><?= date('M d, Y', strtotime($request['appointment_date'])) ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Full name</span>
                <span class="detail-value"><?= htmlspecialchars($request['full_name']) ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Civil status</span>
                <span class="detail-value"><?= htmlspecialchars($request['civil_status'] ?? '—') ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Birthday</span>
                <span class="detail-value"><?= $request['birthday'] ? date('M d, Y', strtotime($request['birthday'])) : '—' ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Address</span>
                <span class="detail-value"><?= htmlspecialchars($request['address']) ?></span>
            </div>
            <div class="detail-row px-4 bg-white">
                <span class="detail-label">Contact number</span>
                <span class="detail-value"><?= htmlspecialchars($request['contact_number']) ?></span>
            </div>
            <?php if (!empty($request['id_image_path'])): 
                $displayPath = $request['id_image_path'];
                if (strpos($displayPath, 'public/') !== 0 && strpos($displayPath, 'http') !== 0) {
                    $displayPath = 'public/' . $displayPath;
                }
            ?>
            <div class="detail-row px-4 bg-white flex-col gap-3 items-start">
                <span class="detail-label">Uploaded ID</span>
                <div class="w-full bg-slate-100 rounded-lg p-2 border border-slate-200 relative">
                    <?php if ($request['is_verified']): ?>
                        <div class="absolute top-4 left-4 bg-teal-500 text-white text-[10px] font-black px-2 py-1 rounded shadow-lg uppercase tracking-widest flex items-center gap-1.5 z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Verified ID
                        </div>
                    <?php endif; ?>
                    <img src="<?= htmlspecialchars($displayPath) ?>" alt="ID Preview" class="w-full h-auto rounded-md shadow-sm">
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($request['remarks'])): ?>
            <div class="remarks-highlight rounded-xl p-4 mt-6">
                <p class="text-[10px] font-black uppercase tracking-widest text-teal-600 mb-1">Admin remarks</p>
                <p class="text-sm text-slate-700 leading-relaxed italic font-medium">"<?= htmlspecialchars($request['remarks']) ?>"</p>
            </div>
        <?php endif; ?>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <a href="?page=my-requests" class="px-5 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-bold hover:bg-slate-200 transition">
                Back to My Requests
            </a>
            <?php if (($request['status'] ?? '') === 'Pending'): ?>
                <a href="?page=edit-request&id=<?= (int)$request['id'] ?>" class="px-5 py-2.5 bg-teal-600 text-white rounded-xl text-sm font-bold hover:bg-teal-700 transition">
                    Edit Request
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
