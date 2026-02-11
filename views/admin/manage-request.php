<script src="https://cdn.tailwindcss.com"></script>
<style>
    .form-input {
        @apply border-2 border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition font-medium;
    }
</style>

<div class="p-8 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-4xl font-bold text-gray-900 mb-2">Manage Requests</h2>
        <p class="text-gray-600">Review and update certificate requests from residents</p>
    </div>

    <?php if (empty($requests)): ?>
        <!-- No Requests State -->
        <div class="bg-white rounded-xl shadow border border-gray-100 p-12 text-center">
            <div class="text-6xl mb-4">📭</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Requests</h3>
            <p class="text-gray-600">All requests have been processed.</p>
        </div>
    <?php else: ?>
        <!-- Requests Table -->
        <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-teal-600 to-teal-700 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Resident</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Certificate</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Appointment</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Remarks</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($requests as $req): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <form method="POST" class="contents">
                                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">

                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        <?= htmlspecialchars($req['resident_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        <?= htmlspecialchars($req['certificate_name']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        <?= date('M d, Y', strtotime($req['appointment_date'])) ?>
                                    </td>

                                    <td class="px-6 py-4">
                                        <select name="status" class="form-input text-sm">
                                            <?php
                                            $statuses = ['Pending', 'Approved', 'Completed', 'Rejected'];
                                            foreach ($statuses as $status):
                                                $selected = $req['status'] == $status ? 'selected' : '';
                                            ?>
                                                <option value="<?= $status ?>" <?= $selected ?>>
                                                    <?= $status ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <td class="px-6 py-4">
                                        <input 
                                            type="text" 
                                            name="remarks"
                                            value="<?= htmlspecialchars($req['remarks'] ?? '') ?>"
                                            placeholder="Add remarks..."
                                            class="form-input text-sm w-full"
                                        >
                                    </td>

                                    <td class="px-6 py-4">
                                        <button 
                                            type="submit"
                                            class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold text-sm transition"
                                        >
                                            Update
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Back Link -->
    <div class="mt-8">
        <a href="?page=admin-dashboard" class="text-teal-600 font-semibold hover:text-teal-700">
            ← Back to Dashboard
        </a>
    </div>
</div>