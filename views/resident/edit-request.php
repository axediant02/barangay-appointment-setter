<?php require __DIR__ . '/../layout/header.php'; ?>

<style>
.form-section { @apply bg-slate-50 rounded-xl p-8 border-2 border-slate-200 mb-6; }
.input-label { @apply block text-base font-black text-slate-800 mb-2 uppercase tracking-wide; }
.form-input {
    @apply w-full border-2 border-slate-400 rounded-lg px-4 py-3.5 bg-slate-100 text-lg font-semibold transition-all;
    @apply placeholder:text-slate-500 text-slate-900;
    @apply focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-600 focus:bg-white;
}
.sticky-nav {
    position: sticky; top: 0; z-index: 50;
    background: rgba(248, 250, 252, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
}
</style>

<nav class="sticky-nav mb-6">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="?page=view-request&id=<?= (int)$request['id'] ?>" class="inline-flex items-center gap-2 text-slate-600 font-bold hover:text-teal-600 transition group" aria-label="Back to request details">
            <span class="bg-white w-9 h-9 flex items-center justify-center rounded-lg shadow-sm border border-gray-200 group-hover:border-teal-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            <span class="text-sm tracking-tight">Back to Request</span>
        </a>
        <div class="hidden sm:block">
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Edit #<?= (int)$request['id'] ?></span>
        </div>
    </div>
</nav>

<div class="max-w-3xl mx-auto px-4">

<?php if ($request['status'] !== 'Pending'): ?>
    <div class="bg-red-50 border-2 border-red-200 text-red-700 px-4 py-4 rounded-xl mb-6">
        <p class="font-bold">You cannot edit this request.</p>
        <p class="text-sm mt-1">Only pending requests can be edited.</p>
        <a href="?page=view-request&id=<?= (int)$request['id'] ?>" class="inline-block mt-3 text-teal-600 font-bold text-sm hover:underline">View request</a>
    </div>
<?php else: ?>

<?php if (!empty($_SESSION['error'])): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
        <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-[2rem] shadow-2xl border-2 border-slate-300 overflow-hidden">
    <div class="bg-slate-950 px-8 py-10 text-white">
        <h2 class="text-3xl font-black tracking-tight">Edit Request</h2>
        <p class="text-slate-400 mt-2 font-bold uppercase text-xs tracking-[0.2em]"><?= htmlspecialchars($request['certificate_name']) ?></p>
    </div>

    <div class="px-8 py-10">
        <form method="POST" class="space-y-8">
            <input type="hidden" name="request_id" value="<?= (int)$request['id'] ?>">

            <div class="form-section">
                <div class="flex items-center gap-3 mb-6 border-b-4 border-slate-200 pb-4">
                    <span class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center font-black">1</span>
                    <h3 class="font-black text-slate-900 uppercase text-lg tracking-widest">Personal Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="input-label">Full Name <span class="text-red-600">*</span></label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($request['full_name']) ?>" class="form-input" required>
                    </div>

                    <div>
                        <label class="input-label">Civil Status <span class="text-red-600">*</span></label>
                        <select name="civil_status" class="form-input w-full border-2 border-slate-400 rounded-lg px-4 py-3.5 bg-slate-100 text-lg font-semibold focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-600" required>
                            <option value="">Select Status</option>
                            <option value="Single" <?= ($request['civil_status'] ?? '') === 'Single' ? 'selected' : '' ?>>Single</option>
                            <option value="Married" <?= ($request['civil_status'] ?? '') === 'Married' ? 'selected' : '' ?>>Married</option>
                            <option value="Widowed" <?= ($request['civil_status'] ?? '') === 'Widowed' ? 'selected' : '' ?>>Widowed</option>
                        </select>
                    </div>

                    <div>
                        <label class="input-label">Birthday</label>
                        <input type="date" name="birthday" value="<?= htmlspecialchars($request['birthday'] ?? '') ?>" class="form-input" max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                    </div>

                    <div class="md:col-span-2">
                        <label class="input-label">Address <span class="text-red-600">*</span></label>
                        <input type="text" name="address" value="<?= htmlspecialchars($request['address']) ?>" class="form-input" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="input-label">Contact Number <span class="text-red-600">*</span></label>
                        <input type="tel" name="contact_number" value="<?= htmlspecialchars($request['contact_number']) ?>" class="form-input" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="flex items-center gap-3 mb-6 border-b-4 border-slate-200 pb-4">
                    <span class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center font-black">2</span>
                    <h3 class="font-black text-slate-900 uppercase text-lg tracking-widest">Appointment</h3>
                </div>
                <div>
                    <label class="input-label">Preferred Appointment Date <span class="text-red-600">*</span></label>
                    <input type="date" name="appointment_date" value="<?= date('Y-m-d', strtotime($request['appointment_date'])) ?>" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-black uppercase tracking-widest transition">
                    Save Changes
                </button>
                <a href="?page=view-request&id=<?= (int)$request['id'] ?>" class="px-8 py-4 bg-slate-100 text-slate-700 rounded-xl font-bold hover:bg-slate-200 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
