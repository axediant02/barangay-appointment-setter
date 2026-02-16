<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}
require '../views/layout/header.php';
?>

<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; }

    .sticky-nav {
        position: sticky; top: 0; z-index: 50;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        border-bottom: 1px solid #f1f5f9;
    }

    /* Status Accent Logic */
    .status-Pending { border-left: 4px solid #f59e0b; }
    .status-Approved { border-left: 4px solid #10b981; }
    .status-Completed { border-left: 4px solid #06b6d4; }
    .status-Rejected { border-left: 4px solid #ef4444; }
    .status-Cancelled { border-left: 4px solid #94a3b8; }

    .search-focus:focus-within {
        width: 420px !important;
        border-color: #0d9488 !important;
    }
</style>

<div class="sticky-nav">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <button onclick="history.back()" class="flex items-center gap-2 text-slate-500 font-bold hover:text-teal-600 transition-all group px-3 py-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="text-[10px] uppercase tracking-[0.2em]">Back</span>
        </button>
        
        <div class="flex items-center gap-6">
            <div class="relative search-focus transition-all duration-300 w-80 border-2 border-slate-100 rounded-2xl bg-slate-50 overflow-hidden flex items-center px-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="tableSearch" placeholder="Search name, phone, or address..." 
                    class="bg-transparent border-none w-full py-2.5 text-sm focus:ring-0 outline-none placeholder:text-slate-400 font-medium" 
                    onkeyup="filterTable()">
            </div>

            <div class="flex items-center gap-3 bg-white border border-slate-200 px-4 py-2 rounded-2xl shadow-sm">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                </span>
                <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest text-nowrap">Auto-Sync</span>
            </div>
        </div>
    </div>
</div>

<div class="p-8 max-w-7xl mx-auto min-h-screen">
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-2">
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic">Requests Center</h2>
            <span class="bg-slate-900 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-widest">
                <?= count($requests) ?> Requests
            </span>
        </div>
        <p class="text-slate-500 font-medium">Review resident applications and verify their local address.</p>
    </div>

    <div id="requestsContainer">
        <?php if (empty($requests)): ?>
            <div class="bg-white rounded-[3rem] border-4 border-dashed border-slate-100 p-24 text-center">
                <div class="text-6xl mb-6 grayscale">🏢</div>
                <h3 class="text-2xl font-black text-slate-900 mb-2 uppercase tracking-tighter italic">Queue is Empty</h3>
                <p class="text-slate-400 max-w-xs mx-auto text-sm">No pending requests found.</p>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full whitespace-nowrap" id="requestsTable">
                        <thead>
                            <tr class="bg-slate-900 text-white">
                                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Resident Info</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Address</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Certificate</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Schedule</th>
                                <th class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-[0.2em]">Status</th>
                                <th class="px-8 py-5 text-center text-[10px] font-black uppercase tracking-[0.2em]">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="requestsBody" class="divide-y divide-slate-50">
                            <?php foreach ($requests as $req): ?>
                                <tr data-request-id="<?= $req['id'] ?>" class="group hover:bg-slate-50/50 transition-colors status-<?= $req['status'] ?>">
                                    <form method="POST" class="contents">
                                        <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                        
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div class="h-10 w-10 bg-teal-600 rounded-xl flex items-center justify-center font-black text-white text-xs shadow-lg shadow-teal-100">
                                                    <?= strtoupper(substr($req['full_name'], 0, 1)) ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="text-sm font-black text-slate-800 resident-name tracking-tight"><?= htmlspecialchars($req['full_name']) ?></span>
                                                    <span class="text-[11px] font-bold text-slate-400 resident-phone">📞 <?= htmlspecialchars($req['contact_number'] ?? 'No Number') ?></span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <div class="flex items-start gap-2 max-w-xs whitespace-normal">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-tight resident-address">
                                                    <?= htmlspecialchars($req['address'] ?? 'Address not set') ?>
                                                </span>
                                            </div>
                                        </td>

                                        <td class="px-8 py-6">
                                            <span class="text-xs font-black text-teal-700 bg-teal-50 px-3 py-1.5 rounded-lg border border-teal-100">
                                                <?= htmlspecialchars($req['certificate_name']) ?>
                                            </span>
                                        </td>

                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-slate-600 italic">
                                                <?= date('M d, Y', strtotime($req['appointment_date'])) ?>
                                            </span>
                                        </td>

                                        <td class="px-8 py-6">
                                            <select name="status" class="form-select text-[10px] font-black uppercase border-2 border-slate-100 bg-white rounded-xl px-2 py-2 focus:border-teal-500 outline-none transition-all">
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

                                        <td class="px-8 py-6 text-center">
                                            <button type="submit" class="bg-slate-900 hover:bg-teal-600 text-white px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-xl shadow-slate-200">
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
<div class="mt-12 flex flex-col items-center gap-6 pb-20">
    <div class="flex items-center gap-2">
        
        <?php if ($pageNum > 1): ?>
            <a href="?page=manage-requests&page_num=<?= $pageNum - 1 ?>" 
               class="group flex items-center justify-center w-12 h-12 bg-white border-2 border-slate-100 rounded-2xl hover:border-teal-500 hover:text-teal-600 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        <?php else: ?>
            <div class="flex items-center justify-center w-12 h-12 bg-slate-50 border-2 border-slate-50 text-slate-200 rounded-2xl cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </div>
        <?php endif; ?>

        <div class="flex items-center gap-1 bg-white p-1.5 border-2 border-slate-100 rounded-[1.5rem] shadow-sm">
            <?php for ($i = 1; $i <= $totalPages; $i++): 
                $isActive = ($i == $pageNum);
            ?>
                <a href="?page=manage-requests&page_num=<?= $i ?>"
                   class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all
                   <?= $isActive 
                       ? 'bg-teal-600 text-white shadow-lg shadow-teal-100 scale-110' 
                       : 'text-slate-400 hover:bg-slate-50 hover:text-teal-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>

        <?php if ($pageNum < $totalPages): ?>
            <a href="?page=manage-requests&page_num=<?= $pageNum + 1 ?>" 
               class="group flex items-center justify-center w-12 h-12 bg-white border-2 border-slate-100 rounded-2xl hover:border-teal-500 hover:text-teal-600 transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        <?php else: ?>
            <div class="flex items-center justify-center w-12 h-12 bg-slate-50 border-2 border-slate-50 text-slate-200 rounded-2xl cursor-not-allowed">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        <?php endif; ?>

    </div>
    
    <div class="flex items-center gap-3">
        <div class="h-[1px] w-8 bg-slate-100"></div>
        <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">End of results</p>
        <div class="h-[1px] w-8 bg-slate-100"></div>
    </div>
</div>
<?php endif; ?>

<script>
function filterTable() {
    const input = document.getElementById("tableSearch");
    const filter = input.value.toUpperCase();
    const rows = document.querySelectorAll("#requestsBody tr");

    rows.forEach(row => {
        const name = row.querySelector(".resident-name").textContent.toUpperCase();
        const phone = row.querySelector(".resident-phone").textContent.toUpperCase();
        const address = row.querySelector(".resident-address").textContent.toUpperCase();
        
        if (name.includes(filter) || phone.includes(filter) || address.includes(filter)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}
</script>

<?php require '../views/layout/footer.php'; ?>