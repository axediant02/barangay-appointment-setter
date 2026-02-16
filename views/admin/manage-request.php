<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}
require '../views/layout/header.php';

// Logic for numbering
$itemsPerPage = 10; 
$startIndex = (($pageNum ?? 1) - 1) * $itemsPerPage;
$count = $startIndex + 1;
?>

<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

    .sticky-nav {
        position: sticky; top: 0; z-index: 50;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #e2e8f0;
    }

    /* Status Accent Borders */
    .status-Pending { border-left: 4px solid #f59e0b; }
    .status-Approved { border-left: 4px solid #10b981; }
    .status-Completed { border-left: 4px solid #06b6d4; }
    .status-Rejected { border-left: 4px solid #ef4444; }
    .status-Cancelled { border-left: 4px solid #94a3b8; }

    /* Custom Scrollbar for the table */
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<div class="sticky-nav">
    <div class="max-w-[1600px] mx-auto px-4 py-3 flex items-center justify-between">
        <button onclick="history.back()" class="flex items-center gap-2 text-slate-500 font-bold hover:text-teal-600 transition-all group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="text-[10px] uppercase tracking-widest">Back</span>
        </button>
        
        <div class="flex items-center gap-4">
            <div class="relative transition-all duration-300 w-64 md:w-96 border border-slate-200 rounded-xl bg-slate-50 overflow-hidden flex items-center px-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="tableSearch" placeholder="Filter requests..." 
                    class="bg-transparent border-none w-full py-2 text-xs focus:ring-0 outline-none font-medium" 
                    onkeyup="filterTable()">
            </div>
        </div>
    </div>
</div>

<div class="p-4 md:p-8 max-w-[1600px] mx-auto min-h-screen">
    <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">Requests Center</h2>
            <p class="text-slate-500 text-sm font-medium mt-1">Manage resident applications and verifications.</p>
        </div>
        <div class="bg-slate-900 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg uppercase tracking-[0.2em]">
            Total: <?= count($requests) ?> Requests
        </div>
    </div>

    <div id="requestsContainer">
        <?php if (empty($requests)): ?>
            <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-20 text-center">
                <span class="text-4xl mb-4 block">📁</span>
                <h3 class="text-lg font-bold text-slate-400 uppercase tracking-widest">No requests found</h3>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full table-fixed border-collapse" id="requestsTable">
                        <thead>
                            <tr class="bg-slate-50 border-bottom border-slate-200 text-slate-600">
                                <th class="w-12 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">#</th>
                                <th class="w-56 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">Resident Details</th>
                                <th class="w-64 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">Address</th>
                                <th class="w-40 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">Certificate</th>
                                <th class="w-32 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">Schedule</th>
                                <th class="w-40 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">Status</th>
                                <th class="w-56 px-4 py-4 text-left text-[9px] font-black uppercase tracking-widest">Admin Remarks</th>
                                <th class="w-24 px-4 py-4 text-center text-[9px] font-black uppercase tracking-widest">Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestsBody" class="divide-y divide-slate-100">
                            <?php foreach ($requests as $req): ?>
                                <tr data-request-id="<?= $req['id'] ?>" class="group hover:bg-slate-50/50 transition-colors status-<?= $req['status'] ?>">
                                    <td class="px-4 py-4 text-xs font-black text-slate-300 italic">
                                        <?= str_pad($count++, 2, '0', STR_PAD_LEFT) ?>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 shrink-0 bg-teal-600 rounded-lg flex items-center justify-center font-black text-white text-[10px] shadow-sm">
                                                <?= strtoupper(substr($req['full_name'] ?? 'U', 0, 1)) ?>
                                            </div>
                                            <div class="flex flex-col min-w-0">
                                                <span class="text-xs font-bold text-slate-800 resident-name truncate"><?= htmlspecialchars($req['full_name']) ?></span>
                                                <span class="text-[10px] text-slate-400 font-semibold resident-phone">📞 <?= htmlspecialchars($req['contact_number'] ?? 'N/A') ?></span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="text-[10px] font-medium text-slate-500 leading-relaxed resident-address line-clamp-2 uppercase">
                                            <?= htmlspecialchars($req['address'] ?? 'No address listed') ?>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="text-[9px] font-black text-teal-700 bg-teal-50 px-2 py-1 rounded-md border border-teal-100 uppercase truncate block text-center">
                                            <?= htmlspecialchars($req['certificate_name']) ?>
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="text-[10px] font-bold text-slate-600 whitespace-nowrap">
                                            <?= date('M d, Y', strtotime($req['appointment_date'])) ?>
                                        </span>
                                    </td>

                                    <form method="POST" action="?page=manage-requests" class="contents">
                                        <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                        
                                        <td class="px-4 py-4">
                                            <select name="status" class="w-full text-[10px] font-bold uppercase border border-slate-200 bg-white rounded-lg px-2 py-1.5 focus:border-teal-500 outline-none transition-all">
                                                <?php
                                                $currentStatus = $req['status'];
                                                $statusMap = [
                                                    'Pending' => ['Pending' => '⏳ Pending', 'Approved' => '✅ Approved', 'Rejected' => '❌ Rejected'],
                                                    'Approved' => ['Approved' => '✅ Approved', 'Completed' => '💎 Completed'],
                                                    'Rejected' => ['Rejected' => '❌ Rejected'],
                                                    'Completed' => ['Completed' => '💎 Completed'],
                                                    'Cancelled' => ['Cancelled' => '🚫 Cancelled']
                                                ];
                                                $available = $statusMap[$currentStatus] ?? [$currentStatus => $currentStatus];
                                                foreach ($available as $val => $label): ?>
                                                    <option value="<?= $val ?>" <?= ($currentStatus == $val ? 'selected' : '') ?>><?= $label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>

                                        <td class="px-4 py-4">
                                            <input type="text" name="remarks" value="<?= htmlspecialchars($req['remarks'] ?? '') ?>" 
                                                placeholder="Add internal notes..."
                                                class="w-full text-[10px] font-medium border border-slate-200 bg-slate-50/50 rounded-lg px-3 py-1.5 focus:border-teal-500 focus:bg-white outline-none transition-all">
                                        </td>

                                        <td class="px-4 py-4 text-center">
                                            <button type="submit" class="bg-slate-900 hover:bg-teal-600 text-white w-full py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all shadow-sm">
                                                Save
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
    </div>
</div>

<?php if ($totalPages > 1): ?>
<div class="mt-8 flex flex-col items-center gap-4 pb-12">
    <div class="flex items-center gap-1 bg-white p-1 border border-slate-200 rounded-xl shadow-sm">
        <?php if ($pageNum > 1): ?>
            <a href="?page=manage-requests&page_num=<?= $pageNum - 1 ?>" class="p-2 hover:text-teal-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=manage-requests&page_num=<?= $i ?>" class="w-8 h-8 flex items-center justify-center rounded-lg text-[10px] font-black transition-all <?= ($i == $pageNum) ? 'bg-teal-600 text-white' : 'text-slate-400 hover:bg-slate-50' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($pageNum < $totalPages): ?>
            <a href="?page=manage-requests&page_num=<?= $pageNum + 1 ?>" class="p-2 hover:text-teal-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
/**
 * Optimized search function to handle multiple columns
 */
function filterTable() {
    const input = document.getElementById("tableSearch");
    const filter = input.value.toUpperCase();
    const rows = document.querySelectorAll("#requestsBody tr");

    rows.forEach(row => {
        const text = row.textContent || row.innerText;
        row.style.display = text.toUpperCase().includes(filter) ? "" : "none";
    });
}
</script>

<?php require '../views/layout/footer.php'; ?>