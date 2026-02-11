<!DOCTYPE html>
<html>
<head>
    <title>Request Certificate</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .form-input {
            @apply w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition font-medium;
        }
        .btn-primary {
            @apply w-full bg-teal-600 hover:bg-teal-700 text-white py-3 rounded-lg transition font-semibold text-lg;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-teal-50 to-cyan-50 min-h-screen p-4">

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-8 text-white">
            <h2 class="text-3xl font-bold">Request Certificate</h2>
            <p class="text-teal-100 mt-2">Choose a certificate type and schedule your appointment</p>
        </div>

        <div class="px-8 py-8">
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="text-red-600 text-xl flex-shrink-0">⚠️</div>
                        <div class="text-red-800">
                            <p class="font-medium"><?= htmlspecialchars($_SESSION['error']) ?></p>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form method="POST" class="space-y-6">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Full Name *</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($user['username'] ?? '') ?>" class="form-input" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Civil Status *</label>
                    <select name="civil_status" class="form-input" required>
                        <option value="">-- Select --</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Widowed">Widowed</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Separated">Separated</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Birthday</label>
                    <input type="date" name="birthday" class="form-input">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Address *</label>
                    <input type="text" name="address" class="form-input" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Contact Number *</label>
                    <input type="text" name="contact_number" class="form-input" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Certificate Type *</label>
                    <select name="certificate_id" class="form-input" required>
                        <option value="">-- Choose Certificate --</option>
                        <?php foreach ($certificates as $cert): ?>
                            <option value="<?= $cert['id'] ?>"><?= htmlspecialchars($cert['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Preferred Appointment Date *</label>
                    <input type="date" name="appointment_date" class="form-input" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>

                <button type="submit" class="btn-primary mt-8">Submit Request</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>