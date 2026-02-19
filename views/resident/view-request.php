<?php require __DIR__ . '/../layout/header.php'; ?>

<style>
.status-badge { @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-xs uppercase tracking-wide border; }
.status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
.status-approved { @apply bg-teal-50 text-teal-700 border-teal-200; }
.status-completed { @apply bg-cyan-50 text-cyan-700 border-cyan-200; }
.status-rejected { @apply bg-red-50 text-red-700 border-red-200; }
.status-cancelled { @apply bg-gray-100 text-gray-600 border-gray-300; }
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
            <span class="status-badge status-<?= strtolower($request['status'] ?? 'pending') ?>">
                <?= htmlspecialchars($request['status'] ?? 'Pending') ?>
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
                <div class="w-full bg-slate-100 rounded-lg p-2 border border-slate-200">
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
