<?php require '../views/layout/header.php'; ?>

<style type="text/tailwindcss">
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    
    body { font-family: 'Plus Jakarta Sans', sans-serif; @apply bg-slate-50; }

    .form-section {
        @apply bg-white rounded-2xl p-6 md:p-8 border border-slate-200 shadow-sm mb-6 transition-all;
    }

    .input-label {
        @apply block text-sm font-bold text-slate-700 mb-2;
    }

    .form-input {
        @apply w-full border border-slate-300 rounded-xl px-4 py-3 bg-white text-slate-900 transition-all;
        @apply placeholder:text-slate-400;
        @apply focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500;
    }

    option:disabled { @apply text-slate-400 bg-slate-50; }

    .btn-primary {
        @apply w-full bg-teal-600 hover:bg-teal-700 text-white py-4 rounded-xl shadow-lg shadow-teal-900/20 font-bold text-lg transition-all transform active:scale-[0.98];
    }
</style>

<div class="max-w-4xl mx-auto pt-10 pb-20 px-4">

    <?php if (!empty($_SESSION['error']) || !empty($_SESSION['success'])): ?>
        <div class="mb-8 animate-in fade-in slide-in-from-top-4">
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    <span class="font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    <span class="font-medium"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <div class="flex items-center justify-between mb-8">
        <a href="?page=resident-dashboard" class="flex items-center gap-2 text-slate-500 hover:text-slate-800 transition font-bold text-sm uppercase tracking-wider group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:-translate-x-1 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Dashboard
        </a>
        <span class="text-[10px] font-black bg-slate-200 text-slate-600 px-3 py-1 rounded-full uppercase tracking-tighter">Official Request Form</span>
    </div>

    <div class="mb-10 text-center md:text-left">
        <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Request a Certificate</h1>
        <p class="text-slate-500 mt-2 font-medium">Please provide accurate information to avoid processing delays.</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="space-y-6">
        
        <div class="form-section">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center font-bold shadow-lg shadow-slate-200">1</div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg leading-none">Personal Information</h3>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Your Official Records</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="input-label">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['username'] ?? '') ?>" class="form-input font-semibold" required>
                </div>

                <div>
                    <label class="input-label">Civil Status <span class="text-red-500">*</span></label>
                    <select name="civil_status" class="form-input cursor-pointer" required>
                        <option value="" hidden>Select Status</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                    </select>
                </div>

                <div>
                    <label class="input-label">Date of Birth <span class="text-red-500">*</span></label>
                    <input type="date" name="birthday" class="form-input" required max="<?= date('Y-m-d', strtotime('-18 years')) ?>">
                </div>

                <div class="md:col-span-2">
                    <label class="input-label">Current Address <span class="text-red-500">*</span></label>
                    <input type="text" name="address" placeholder="House No., Street, Barangay" class="form-input" required>
                </div>

                <div class="md:col-span-2">
                    <label class="input-label">Mobile Number <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold border-r pr-3">+63</span>
                        <input type="tel" name="contact_number" placeholder="9XX XXX XXXX" class="form-input !pl-16" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-indigo-600 text-white rounded-xl flex items-center justify-center font-bold shadow-lg shadow-indigo-100">2</div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg leading-none">Identity Verification</h3>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Upload a Valid Document</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch">
                <div class="relative">
                    <label class="input-label mb-4">Valid Government ID</label>
                    <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-slate-300 rounded-2xl cursor-pointer hover:bg-slate-50 hover:border-teal-500 transition-all group">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-slate-400 group-hover:text-teal-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            <p class="mb-2 text-sm text-slate-700 font-bold">Click to upload image</p>
                            <p class="text-xs text-slate-400">PNG, JPG or WebP (Max. 5MB)</p>
                        </div>
                        <input type="file" name="id_image" id="id_image" class="hidden" accept="image/*" required onchange="previewImage(this)">
                    </label>
                </div>

                <div class="bg-slate-100 rounded-2xl flex flex-col items-center justify-center overflow-hidden border border-slate-200 relative min-h-[12rem]">
                    <img id="imagePreview" src="#" alt="Preview" class="w-full h-full object-cover hidden">
                    <div id="placeholderPreview" class="text-center p-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Document Preview</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-10 h-10 bg-teal-600 text-white rounded-xl flex items-center justify-center font-bold shadow-lg shadow-teal-100">3</div>
                <div>
                    <h3 class="font-bold text-slate-900 text-lg leading-none">Request Details</h3>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-bold">Purpose & Schedule</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="input-label">Type of Certificate <span class="text-red-500">*</span></label>
                    <select name="certificate_id" class="form-input cursor-pointer" required>
                        <option value="" hidden>Select Certificate Type</option>
                        <?php foreach ($certificates as $cert): 
                            $alreadyRequested = in_array($cert['id'], $userRequestsToday ?? []);
                            $isBanned = $userBanStatus[$cert['id']] ?? false;
                            $cancelCount = $userCancellationCounts[$cert['id']] ?? 0;
                            $isDisabled = $alreadyRequested || $isBanned;
                        ?>
                            <option value="<?= $cert['id'] ?>" <?= $isDisabled ? 'disabled' : '' ?>>
                                <?= htmlspecialchars($cert['name']) ?>
                                <?php if ($alreadyRequested) echo " (Requested Today)"; ?>
                                <?php if ($isBanned) echo " (Limit Reached)"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="input-label">Appointment Date</label>
                        <input type="date" name="appointment_date" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                    </div>
                    <div>
                        <label class="input-label">Reason</label>
                        <select name="reason_id" class="form-input cursor-pointer" required>
                            <option value="" hidden>Select Reason</option>
                            <?php foreach ($reasons as $reason): ?>
                                <option value="<?= $reason['id'] ?>"><?= htmlspecialchars($reason['reason']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                        <div class="text-xs text-amber-800 space-y-1">
                            <p class="font-bold">Important Policies:</p>
                            <p>• Only 1 request per certificate type is allowed daily.</p>
                            <p>• Cancelling 3 times for the same document will temporarily disable that option.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn-primary">
            Submit Application
        </button>

    </form>
</div>

<?php require '../views/layout/footer.php'; ?>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const placeholder = document.getElementById('placeholderPreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>