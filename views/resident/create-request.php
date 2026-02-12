<?php require '../views/layout/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
    
    .form-section {
        @apply bg-slate-50 rounded-xl p-8 border-2 border-slate-200 mb-8;
    }

    .input-label {
        @apply block text-base font-black text-slate-800 mb-2 uppercase tracking-wide;
    }

    .form-input {
        @apply w-full border-2 border-slate-400 rounded-lg px-4 py-3.5 bg-slate-100 text-lg font-semibold transition-all;
        @apply placeholder:text-slate-500 text-slate-900;
        @apply focus:outline-none focus:ring-4 focus:ring-teal-500/20 focus:border-teal-600 focus:bg-white;
    }

    select.form-input {
        @apply appearance-none cursor-pointer pr-12;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%230f172a' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 1rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
    }
</style>

<div class="max-w-3xl mx-auto pt-8 px-4">

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <div class="flex justify-between items-center mb-6">
        <button onclick="history.back()" class="flex items-center gap-2 text-slate-600 hover:text-teal-600 transition font-black text-sm uppercase tracking-widest">
            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white border-2 border-slate-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                </svg>
            </span>
            Go Back
        </button>
    </div>

    <div class="bg-white rounded-[2rem] shadow-2xl border-2 border-slate-300 overflow-hidden">
        <div class="bg-slate-950 px-8 py-12 text-white">
            <h2 class="text-4xl font-black tracking-tight">Request a Certificate</h2>
            <p class="text-slate-400 mt-3 font-bold uppercase text-xs tracking-[0.2em]">
                Fields with <span class="text-red-500 text-lg">*</span> are required
            </p>
        </div>

        <div class="px-8 py-10">
            <form method="POST" class="space-y-10">
                
                <div class="form-section">
                    <div class="flex items-center gap-3 mb-8 border-b-4 border-slate-200 pb-4">
                        <span class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center font-black">1</span>
                        <h3 class="font-black text-slate-900 uppercase text-lg tracking-widest">Personal Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="input-label">Full Name <span class="text-red-600">*</span></label>
                            <input type="text" name="full_name" placeholder="Juan Dela Cruz" value="<?= htmlspecialchars($user['username'] ?? '') ?>" class="form-input" required>
                        </div>

                        <div>
                            <label class="input-label">Civil Status <span class="text-red-600">*</span></label>
                            <select name="civil_status" class="form-input" required>
                                <option value="">Select Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                            </select>
                        </div>

                        <div>
                            <label class="input-label">Birthday</label>
                            <input type="date" name="birthday" class="form-input">
                        </div>

                        <div class="md:col-span-2">
                            <label class="input-label">Address <span class="text-red-600">*</span></label>
                            <input type="text" name="address" placeholder="House No., Street, Barangay" class="form-input" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="input-label">Contact Number <span class="text-red-600">*</span></label>
                            <input type="tel" name="contact_number" placeholder="09XX XXX XXXX" class="form-input" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="flex items-center gap-3 mb-8 border-b-4 border-slate-200 pb-4">
                        <span class="bg-slate-900 text-white w-8 h-8 rounded-lg flex items-center justify-center font-black">2</span>
                        <h3 class="font-black text-slate-900 uppercase text-lg tracking-widest">Request Details</h3>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="input-label">Type of Certificate <span class="text-red-600">*</span></label>
                            <select name="certificate_id" class="form-input" required>
                                <option value="">Select Certificate Type</option>
                                <?php foreach ($certificates as $cert): 
                                    $alreadyRequested = in_array($cert['id'], $userRequestsToday ?? []);
                                    $isBanned = $userBanStatus[$cert['id']] ?? false;
                                    $cancelCount = $userCancellationCounts[$cert['id']] ?? 0;
                                ?>
                                    <option value="<?= $cert['id'] ?>"
                                        <?= $alreadyRequested ? 'disabled' : '' ?>
                                        <?= $isBanned ? 'disabled' : '' ?>
                                    >
                                        <?= htmlspecialchars($cert['name']) ?>
                                        <?php if ($cancelCount > 0): ?>
                                            (<?= $cancelCount ?>/3 cancellations used)
                                        <?php endif; ?>
                                        <?= $isBanned ? ' - Request Disabled' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Helper Message -->
                            <div class="mt-3 text-xs font-semibold space-y-1">
                                <p class="text-slate-500">
                                    • You can request only once per certificate per day.
                                </p>
                                <p class="text-slate-500">
                                    • If you cancel 3 times for the same certificate, requesting will be disabled.
                                </p>
                                <p class="text-red-600">
                                    • Disabled certificates mean you reached cancellation limit.
                                </p>
                            </div>

                        </div>

                        <div>
                            <label class="input-label">Preferred Appointment Date <span class="text-red-600">*</span></label>
                            <input type="date" name="appointment_date" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white py-5 rounded-2xl shadow-xl font-black text-xl uppercase tracking-[0.2em] transition-all transform active:scale-95">
                        Submit My Request
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php require '../views/layout/footer.php'; ?>