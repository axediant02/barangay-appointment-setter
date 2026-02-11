<script src="https://cdn.tailwindcss.com"></script>
<style>
    .form-input {
        @apply border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition font-medium;
    }
    .btn-primary {
        @apply px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold text-sm transition;
    }
</style>

<div class="p-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-4xl font-bold text-gray-900 mb-2">Manage Certificates</h2>
        <p class="text-gray-600">Add and manage certificate types available for residents</p>
    </div>

    <!-- Add New Certificate Form -->
    <div class="bg-white rounded-xl shadow border border-gray-100 p-8 mb-12">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Add New Certificate</h3>
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Certificate Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="e.g., Residency Certificate"
                        class="form-input"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <input 
                        type="text" 
                        name="description" 
                        placeholder="Brief description of the certificate"
                        class="form-input"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="btn-primary mt-4">
                ➕ Add Certificate
            </button>
        </form>
    </div>

    <?php if (empty($certificates)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow border border-gray-100 p-12 text-center">
            <div class="text-6xl mb-4">📄</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Certificates</h3>
            <p class="text-gray-600">No certificate types have been added yet. Add one using the form above.</p>
        </div>
    <?php else: ?>
        <!-- Certificates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($certificates as $cert): ?>
                <div class="bg-white rounded-xl shadow border border-gray-100 hover:shadow-lg transition p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-grow">
                            <h4 class="text-xl font-bold text-gray-900 mb-1">
                                📋 <?= htmlspecialchars($cert['name']) ?>
                            </h4>
                            <p class="text-gray-600 text-sm">
                                <?= htmlspecialchars($cert['description']) ?>
                            </p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-200">
                        <a 
                            href="?page=manage-certificates&delete=1&id=<?= $cert['id'] ?>"
                            class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold text-sm transition"
                            onclick="return confirm('Are you sure you want to delete this certificate?');"
                        >
                            🗑️ Delete
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Back Link -->
    <div class="mt-8">
        <a href="?page=admin-dashboard" class="text-teal-600 font-semibold hover:text-teal-700">
            ← Back to Dashboard
        </a>
    </div>
</div>