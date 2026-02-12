<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
    
    body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; }

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
    
    .status-Pending { border-left: 4px solid #CA8A04; }
    .status-Approved { border-left: 4px solid #059669; }
    .status-Completed { border-left: 4px solid #0891B2; }
    .status-Rejected { border-left: 4px solid #DC2626; }

    /* Sticky Navigation Styles */
    .sticky-nav {
        position: sticky;
        top: 0;
        z-index: 50;
        background: rgba(249, 250, 251, 0.9);
        backdrop-filter: blur(8px);
    }
</style>

<div class="sticky-nav border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-8 py-4 flex items-center justify-between">
        <button onclick="history.back()" class="inline-flex items-center gap-2 text-teal-600 font-bold hover:text-teal-800 transition group bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-100">
            <span class="transform group-hover:-translate-x-1 transition">←</span> 
            <span class="text-sm">Back</span>
        </button>
        
        <div class="flex items-center gap-4">
            <input type="text" id="tableSearch" placeholder="Search residents..." class="form-input text-xs w-64 shadow-sm" onkeyup="filterTable()">
            <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-full shadow-sm border border-gray-100">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                </span>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Live</span>
            </div>
        </div>
    </div>
</div>

<div class="p-8 max-w-7xl mx-auto min-h-screen">
    <div class="mb-8">
        <h2 class="text-5xl font-extrabold text-gray-900 tracking-tight mb-2 uppercase italic">Requests Center</h2>
        <p class="text-gray-500 font-medium">Reviewing and updating certificate statuses for residents.</p>
    </div>

    <?php if (empty($requests)): ?>
        <div class="bg-white rounded-[2rem] border-4 border-dashed border-gray-100 p-24 text-center">
            <div class="text-7xl mb-6">🏝️</div>
            <h3 class="text-3xl font-black text-gray-900 mb-2 uppercase">Nothing to show</h3>
            <p class="text-gray-400">All resident requests have been processed.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-3xl shadow-2xl shadow-slate-200/50 border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap" id="requestsTable">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em]">Resident</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em]">Document</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em]">Appointment</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em]">Status</th>
                            <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-[0.2em]">Admin Notes</th>
                            <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-[0.2em]">Control</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($requests as $req): 
                            $statusClass = "status-" . $req['status'];
                        ?>
                            <tr class="hover:bg-slate-50 transition-all duration-200 <?= $statusClass ?>">
                                <form method="POST" class="contents">
                                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>">

                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 bg-teal-600 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg shadow-teal-200">
                                                <?= strtoupper(substr($req['full_name'], 0, 1)) ?>
                                            </div>
                                            <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($req['full_name']) ?></span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-slate-400 uppercase tracking-tighter">Type</span>
                                            <span class="text-sm font-bold text-teal-700"><?= htmlspecialchars($req['certificate_name']) ?></span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-5">
                                        <span class="text-sm font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-md italic">
                                            <?= date('M d, Y', strtotime($req['appointment_date'])) ?>
                                        </span>
                                    </td>

                                    <td class="px-6 py-5">
                                        <select name="status" class="form-input text-[10px] font-black uppercase tracking-widest border-none bg-slate-50">
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
                                        <input type="text" name="remarks" value="<?= htmlspecialchars($req['remarks'] ?? '') ?>" placeholder="Add remark..." class="form-input text-xs w-full bg-transparent border-dashed border-slate-200">
                                    </td>

                                    <td class="px-6 py-5 text-center">
                                        <button type="submit" class="bg-slate-900 hover:bg-teal-600 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] transition-all transform active:scale-90 shadow-md">
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

    <div class="mt-12 flex justify-between items-center px-4">
        <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.3em]">Official Personnel Interface</p>
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-teal-600 text-xs font-bold hover:underline">Scroll to top ↑</button>
    </div>
</div>

<script>
    function filterTable() {
        let input = document.getElementById("tableSearch");
        let filter = input.value.toUpperCase();
        let table = document.getElementById("requestsTable");
        let tr = table.getElementsByTagName("tr");

        for (let i = 1; i < tr.length; i++) {
            let td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                let txtValue = td.textContent || td.innerText;
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? "" : "none";
            }
        }
    }
</script>