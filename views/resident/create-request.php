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
    <!-- Card -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-8 text-white">
            <h2 class="text-3xl font-bold">Request Certificate</h2>
            <p class="text-teal-100 mt-2">Choose a certificate type and schedule your appointment</p>
        </div>

        <!-- Form -->
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
                <!-- Certificate Selection -->
                <div>
                    <label for="certificate_id" class="block text-sm font-semibold text-gray-700 mb-3">
                        Select Certificate Type *
                    </label>
                    <select 
                        id="certificate_id"
                        name="certificate_id" 
                        class="form-input"
                        required
                        aria-label="Certificate type"
                    >
                        <option value="">-- Choose a Certificate --</option>
                        <?php foreach ($certificates as $cert): ?>
                            <option value="<?= $cert['id'] ?>">
                                <?= htmlspecialchars($cert['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-gray-600 text-xs mt-2">Select the type of certificate you need</p>
                </div>

                <!-- Appointment Date -->
                <div>
                    <label for="appointment_date" class="block text-sm font-semibold text-gray-700 mb-3">
                        Preferred Appointment Date *
                    </label>
                    <input 
                        type="date" 
                        id="appointment_date"
                        name="appointment_date"
                        class="form-input"
                        required
                        min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                        aria-label="Appointment date"
                    >
                    <p class="text-gray-600 text-xs mt-2">Choose a date starting from tomorrow</p>
                </div>

                <!-- Info Box -->
                <div class="bg-teal-50 border-2 border-teal-200 rounded-lg p-4">
                    <p class="text-sm text-teal-800">
                        <strong>ℹ️ Note:</strong> Your appointment must be within one week from today. Our admin team will confirm your appointment and notify you of any changes.
                    </p>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="btn-primary mt-8"
                >
                    Submit Request
                </button>
            </form>

            <!-- Help Text -->
            <div class="mt-8 pt-8 border-t border-gray-200">
                <p class="text-gray-600 text-sm text-center">
                    Questions? <a href="mailto:info@barangay.gov.ph" class="text-teal-600 font-semibold hover:text-teal-700">Contact us</a>
                </p>
            </div>
        </div>
    </div>
</div>

</body>
</html>