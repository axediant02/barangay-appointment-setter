<script src="https://cdn.tailwindcss.com"></script>
<style>
    /* Custom Scrollbar for better aesthetics */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #f1f1f1; }
    ::-webkit-scrollbar-thumb { background: #0d9488; border-radius: 10px; }

    .form-input {
        background-color: #fff;
        border: 2px solid #E5E7EB;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        outline: none;
        transition: all 0.2s ease-in-out;
    }
    .form-input:focus {
        border-color: #0D9488;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
    }
    
    /* Dynamic Status Colors */
    .status-Pending { border-left: 4px solid #CA8A04; background-color: #FEFCE8; }
    .status-Approved { border-left: 4px solid #059669; background-color: #ECFDF5; }
    .status-Completed { border-left: 4px solid #0891B2; background-color: #ECFEFF; }
    .status-Rejected { border-left: 4px solid #DC2626; background-color: #FEF2F2; }
</style>

<div class="p-8 max-w-7xl mx-auto min-h-screen bg-[#F9FAFB]">
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h2 class="text-5xl font-extrabold text-gray-900 tracking-tight mb-2">Manage Requests 📋</h2>
            <p class="text-gray-600 text-lg">Centralized oversight for resident certificate applications.</p>
        </div>
        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
            <span class="relative flex h-3 w-3">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-teal-500"></span>
            </span>
            <span class="text-sm font-medium text-gray-500" id="sync-status">Live Sync Active</span>
        </div>
    </div>

    <?php if (empty($requests)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-20 text-center">
            <div class="text-7xl mb-6">✨</div>
            <h3 class="text-3xl font-bold text-gray-900 mb-2">Inbox is Clear!</h3>
            <p class="text-gray-500 max-w-md mx-auto">There are no pending requests to attend to right now. Take a well-deserved break!</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap" id="manage-requests-table">
                    <thead>
                        <tr class="bg-gradient-to-r from-teal-600 to-teal-700 text-white">
                            <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider">Resident Details</th>
                            <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider">Document Type</th>
                            <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider">Schedule</th>
                            <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider">Status</th>
                            <th class="px-6 py-5 text-left text-xs font-bold uppercase tracking-wider">Admin Remarks</th>
                            <th class="px-6 py-5 text-center text-xs font-bold uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="manage-requests-body">
                        <?php foreach ($requests as $req): 
                            $statusClass = "status-" . $req['status'];
                        ?>
                            <tr class="hover:bg-gray-50 transition-all duration-200 <?= $statusClass ?>" data-request-id="<?= $req['id'] ?>">
                                <form method="POST" class="contents">
                                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">

                                    <td class="px-6 py-5">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold">
                                                <?= strtoupper(substr($req['full_name'], 0, 1)) ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($req['full_name']) ?></div>
                                                <!-- <div class="text-xs text-gray-500">Resident ID: #<?= str_pad($req['id'], 5, '0', STR_PAD_LEFT) ?></div> -->
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="text-sm font-semibold text-gray-700">📄 <?= htmlspecialchars($req['certificate_name']) ?></span>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="text-sm text-gray-900 font-medium">📅 <?= date('M d, Y', strtotime($req['appointment_date'])) ?></div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <select name="status" class="form-input text-xs font-bold status-select" data-current="<?= $req['status'] ?>">
                                            <?php
                                            $statuses = ['Pending' => '⏳ Pending', 'Approved' => '✅ Approved', 'Completed' => '💎 Completed', 'Rejected' => '❌ Rejected'];
                                            foreach ($statuses as $val => $label):
                                                $selected = $req['status'] == $val ? 'selected' : '';
                                            ?>
                                                <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <td class="px-6 py-5">
                                        <input 
                                            type="text" 
                                            name="remarks"
                                            value="<?= htmlspecialchars($req['remarks'] ?? '') ?>"
                                            placeholder="Write internal note..."
                                            class="form-input text-sm w-full bg-gray-50 border-transparent hover:border-gray-300"
                                        >
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <button 
                                            type="submit"
                                            class="inline-flex items-center px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-bold text-xs uppercase tracking-widest transition transform active:scale-95 shadow-md hover:shadow-lg"
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

    <div class="mt-12 flex justify-between items-center">
        <a href="?page=admin-dashboard" class="inline-flex items-center text-teal-600 font-bold hover:text-teal-800 transition group">
            <span class="mr-2 transform group-hover:-translate-x-1 transition">←</span> Back to Admin Dashboard
        </a>
        <p class="text-gray-400 text-sm italic">Confidential Data — Authorized Personnel Only</p>
    </div>
</div>