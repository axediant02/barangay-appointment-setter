<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}
require '../views/layout/header.php';

$itemsPerPage = 25;
$pageNum = isset($currentPage) ? (int)$currentPage : 1;
$search = isset($search) ? $search : '';
$startIndex = ($pageNum - 1) * $itemsPerPage;
$count = $startIndex + 1;
$searchQuery = $search !== '' ? '&search=' . rawurlencode($search) : '';
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

    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .remark-input { transition: all 0.2s ease-in-out; }
</style>

<div class="sticky-nav">
    <div class="max-w-[1600px] mx-auto px-6 py-4 flex items-center justify-between">
        <a href="?page=admin-dashboard" class="inline-flex items-center gap-2 text-slate-500 font-bold hover:text-teal-600 transition-all group" aria-label="Back to admin dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="text-[10px] uppercase tracking-widest">Back to Dashboard</span>
        </a>
        
        <form method="get" action="" class="relative w-64 md:w-80 border border-slate-200 rounded-xl bg-slate-50 flex items-center px-3 focus-within:ring-2 focus-within:ring-teal-500/20 transition-all">
            <input type="hidden" name="page" value="manage-requests">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, contact, certificate..." class="bg-transparent border-none w-full py-2 text-xs focus:ring-0 outline-none font-medium" aria-label="Search all requests">
            <button type="submit" class="shrink-0 p-1.5 text-slate-400 hover:text-teal-600 transition font-semibold text-xs" aria-label="Search">Search</button>
        </form>
    </div>
</div>

<div class="p-6 md:p-10 max-w-[1600px] mx-auto min-h-screen">
    <div class="mb-8 flex items-end justify-between">
        <div>
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase italic leading-none">Requests Center</h2>
            <p class="text-slate-500 text-sm font-medium mt-2">Update status and manage internal documentation.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full table-auto" id="requestsTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-slate-500">
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-16">#</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest min-w-[200px]">Resident</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest">Certificate</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest min-w-[150px]">Status Update</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-48">Internal Remark</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-44">Action</th>
                    </tr>
                </thead>
                <tbody id="requestsBody" class="divide-y divide-slate-50">
                    <?php foreach ($requests as $req): ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors status-<?= $req['status'] ?>">
                            <td class="px-6 py-6 text-xs font-black text-slate-300 italic"><?= str_pad($count++, 2, '0', STR_PAD_LEFT) ?></td>
                            
                            <td class="px-6 py-6">
                                <p class="text-sm font-black text-slate-800 tracking-tight"><?= htmlspecialchars($req['full_name']) ?></p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase"><?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?></p>
                            </td>

                            <td class="px-6 py-6">
                                <span class="text-[10px] font-black text-teal-700 bg-teal-50 px-2 py-1 rounded-md border border-teal-100 uppercase"><?= htmlspecialchars($req['certificate_name']) ?></span>
                            </td>

                            <form method="POST" action="?page=manage-requests<?= $pageNum > 1 ? '&page_num=' . (int)$pageNum : '' ?><?= $search !== '' ? '&search=' . rawurlencode($search) : '' ?>" class="contents">
                                <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                <input type="hidden" name="page_num" value="<?= (int)$pageNum ?>">
                                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                
                                <td class="px-6 py-6">
                                    <select name="status" class="w-full text-[10px] font-black uppercase border-2 border-slate-100 bg-white rounded-xl px-2 py-2 focus:border-teal-500 outline-none cursor-pointer">
                                        <?php
                                        $curr = $req['status'];
                                        $statusMap = [
                                            'Pending' => ['Pending' => '⏳ Pending', 'Approved' => '✅ Approved', 'Rejected' => '❌ Rejected'],
                                            'Approved' => ['Approved' => '✅ Approved', 'Completed' => '💎 Completed'],
                                            'Rejected' => ['Rejected' => '❌ Rejected'],
                                            'Completed' => ['Completed' => '💎 Completed'],
                                            'Cancelled' => ['Cancelled' => '🚫 Cancelled']
                                        ];
                                        foreach (($statusMap[$curr] ?? [$curr => $curr]) as $val => $label): ?>
                                            <option value="<?= $val ?>" <?= $curr == $val ? 'selected' : '' ?>><?= $label ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>

                                <td class="px-6 py-6">
                                    <div class="flex items-center">
                                        <input type="text" name="remarks" id="input-<?= $req['id'] ?>" 
                                            value="<?= htmlspecialchars($req['remarks'] ?? '') ?>" 
                                            placeholder="Write note..."
                                            class="<?= empty($req['remarks']) ? 'hidden' : '' ?> w-full text-[11px] font-medium border-2 border-slate-100 bg-slate-50 rounded-lg px-3 py-1.5 focus:bg-white focus:border-teal-500 outline-none">
                                        
                                        <button type="button" 
                                            onclick="showRemarkInput(<?= $req['id'] ?>, this)"
                                            class="<?= !empty($req['remarks']) ? 'hidden' : '' ?> flex items-center gap-2 text-[10px] font-bold text-slate-400 hover:text-teal-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Remark
                                        </button>
                                    </div>
                                </td>

                                <td class="px-6 py-6 text-center">
                                    <button type="submit" class="bg-slate-900 hover:bg-teal-600 text-white px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-md hover:shadow-teal-100 whitespace-nowrap">
                                        Save Changes
                                    </button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($totalPages) && $totalPages > 1): ?>
    <div class="mt-10 flex justify-center pb-12">
        <nav class="flex items-center gap-2 bg-white p-2 border-2 border-slate-100 rounded-2xl shadow-sm">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=manage-requests&page_num=<?= $i ?><?= $searchQuery ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all <?= ($i == $pageNum) ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 hover:text-teal-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
function showRemarkInput(id, btnElement) {
    const input = document.getElementById('input-' + id);
    input.classList.remove('hidden');
    btnElement.classList.add('hidden');
    input.focus();
}
</script>

<?php require '../views/layout/footer.php'; ?>