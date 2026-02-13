<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}
require '../views/layout/header.php';

// Ensure $requests, $totalPages, $pageNum are defined from backend
?>

<script src="https://cdn.tailwindcss.com"></script>
<style>
/* Sticky nav */
.sticky-nav {
    position: sticky; top: 0; z-index: 50;
    background: rgba(248, 250, 252, 0.8);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid #e2e8f0;
}

/* Table row status colors */
.status-Pending { background-color: #fef3c7; }       /* Amber */
.status-Approved { background-color: #d1fae5; }      /* Teal */
.status-Completed { background-color: #cffafe; }     /* Cyan */
.status-Rejected { background-color: #fee2e2; }      /* Red */
.status-Cancelled { background-color: #e5e7eb; }     /* Gray */
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

    <div id="requestsContainer">
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
                        <tbody id="requestsBody" class="divide-y divide-gray-100">
                            <?php foreach ($requests as $req): ?>
                                <tr data-request-id="<?= $req['id'] ?>" class="hover:bg-slate-50 transition-all duration-200 status-<?= $req['status'] ?>">
                                    <form method="POST" class="contents">
                                        <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="h-9 w-9 bg-teal-600 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg shadow-teal-200">
                                                    <?= strtoupper(substr($req['full_name'], 0, 1)) ?>
                                                </div>
                                                <span class="text-sm font-bold text-slate-800 resident-name"><?= htmlspecialchars($req['full_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-xs font-black text-slate-400 uppercase tracking-tighter">Type</span>
                                                <span class="text-sm font-bold text-teal-700 certificate-name"><?= htmlspecialchars($req['certificate_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="text-sm font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-md italic appointment-date"><?= date('M d, Y', strtotime($req['appointment_date'])) ?></span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <select name="status" class="form-input text-[10px] font-black uppercase tracking-widest border-none bg-slate-50 status-select">
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
                                                foreach ($available as $val => $label):
                                                    $selected = $currentStatus == $val ? 'selected' : '';
                                                ?>
                                                    <option value="<?= $val ?>" <?= $selected ?>><?= $label ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td class="px-6 py-5">
                                            <input type="text" name="remarks" value="<?= htmlspecialchars($req['remarks'] ?? '') ?>" placeholder="Add remark..." class="form-input text-xs w-full bg-transparent border-dashed border-slate-200 remarks-input">
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <button type="submit" class="bg-slate-900 hover:bg-teal-600 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] transition-all transform active:scale-90 shadow-md">Update</button>
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

// Update or insert a request row
function updateRequestRow(req) {
    let tbody = document.getElementById('requestsBody');
    let row = document.querySelector(`tr[data-request-id='${req.id}']`);
    let statusClass = 'status-' + req.status;

    if (!row) {
        // New request, insert at top
        let tr = document.createElement('tr');
        tr.dataset.requestId = req.id;
        tr.className = `hover:bg-slate-50 transition-all duration-200 ${statusClass}`;
        tr.innerHTML = `
        <form method="POST" class="contents">
            <input type="hidden" name="request_id" value="${req.id}">
            <td class="px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 bg-teal-600 text-white rounded-lg flex items-center justify-center font-black text-xs shadow-lg shadow-teal-200">
                        ${req.full_name[0].toUpperCase()}
                    </div>
                    <span class="text-sm font-bold text-slate-800 resident-name">${req.full_name}</span>
                </div>
            </td>
            <td class="px-6 py-5">
                <div class="flex flex-col">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-tighter">Type</span>
                    <span class="text-sm font-bold text-teal-700 certificate-name">${req.certificate_name}</span>
                </div>
            </td>
            <td class="px-6 py-5">
                <span class="text-sm font-bold text-slate-600 bg-slate-100 px-3 py-1 rounded-md italic appointment-date">${req.appointment_date}</span>
            </td>
            <td class="px-6 py-5">
                <select name="status" class="form-input text-[10px] font-black uppercase tracking-widest border-none bg-slate-50 status-select">
                    <option value="${req.status}" selected>${req.status}</option>
                </select>
            </td>
            <td class="px-6 py-5">
                <input type="text" name="remarks" value="${req.remarks ?? ''}" placeholder="Add remark..." class="form-input text-xs w-full bg-transparent border-dashed border-slate-200 remarks-input">
            </td>
            <td class="px-6 py-5 text-center">
                <button type="submit" class="bg-slate-900 hover:bg-teal-600 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-[0.1em] transition-all transform active:scale-90 shadow-md">Update</button>
            </td>
        </form>`;
        tbody.prepend(tr);
    } else {
        // Existing row, update content
        row.querySelector('.resident-name').innerText = req.full_name;
        row.querySelector('.certificate-name').innerText = req.certificate_name;
        row.querySelector('.appointment-date').innerText = req.appointment_date;
        row.querySelector('.status-select').value = req.status;
        row.querySelector('.remarks-input').value = req.remarks ?? '';

        // Update status class
        row.className = `hover:bg-slate-50 transition-all duration-200 ${statusClass}`;
    }
}

// Poll every 5 seconds
function syncAdminRequests() {
    fetch('api.php?action=admin-requests&page_num=<?= (int)($pageNum ?? 1) ?>')
        .then(res => res.json())
        .then(data => {
            const requests = Array.isArray(data) ? data : data.requests ?? [];
            requests.forEach(req => updateRequestRow(req));
        })
        .catch(err => console.error('Realtime admin sync error:', err));
}

setInterval(syncAdminRequests, 5000);
</script>

<?php require '../views/layout/footer.php'; ?>