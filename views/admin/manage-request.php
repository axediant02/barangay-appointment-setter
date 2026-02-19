<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}

$itemsPerPage = 25;
$pageNum = isset($currentPage) ? (int)$currentPage : 1;
$search = isset($search) ? $search : '';
$startIndex = ($pageNum - 1) * $itemsPerPage;
$count = $startIndex + 1;
$searchQuery = $search !== '' ? '&search=' . rawurlencode($search) : '';

// Partial Row Logic (moved to views/admin/partials/request-row.php)

if (!empty($ajaxFragment)) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<div id="manage-requests-fragment"><table><tbody id="manage-requests-tbody">';
    foreach ($requests as $req):
        $rowCount = $count++;
        include '../views/admin/partials/request-row.php';
    endforeach;
    echo '</tbody></table><div id="manage-requests-pagination">';
    if (isset($totalPages) && $totalPages > 1) {
        echo '<nav class="flex items-center gap-2 bg-white p-2 border-2 border-slate-100 rounded-2xl shadow-sm">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i == $pageNum) ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 hover:text-teal-600';
            echo '<a href="?page=manage-requests&page_num=' . $i . $searchQuery . '" class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all ' . $active . '">' . $i . '</a>';
        }
        echo '</nav>';
    }
    echo '</div></div>';
    exit;
}

require '../views/layout/header.php';
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

    .custom-scrollbar::-webkit-scrollbar { height: 8px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.85rem;
        border-radius: 9999px;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .status-badge svg { width: 12px; height: 12px; }

    .status-pending { 
        background-color: #fef3c7; color: #92400e; border-color: #fde68a;
    }
    .status-approved { 
        background-color: #dcfce7; color: #166534; border-color: #bbf7d0;
    }
    .status-completed { 
        background-color: #ecfeff; color: #0891b2; border-color: #cffafe;
    }
    .status-rejected { 
        background-color: #fee2e2; color: #991b1b; border-color: #fecaca;
    }
    .status-cancelled { 
        background-color: #f1f5f9; color: #475569; border-color: #e2e8f0;
    }
    
    .remark-input { transition: all 0.2s ease-in-out; }
</style>


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
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-32">Appointment</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-32">Proof of ID</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-32">Status</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-24">Action</th>
                    </tr>
                </thead>
                <tbody id="requestsBody" class="divide-y divide-slate-50">
                    <?php foreach ($requests as $req): 
                        $rowCount = $count++;
                        include '../views/admin/partials/request-row.php';
                    endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="paginationContainer" class="mt-10 flex justify-center pb-12">
    <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav class="flex items-center gap-2 bg-white p-2 border-2 border-slate-100 rounded-2xl shadow-sm">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=manage-requests&page_num=<?= $i ?><?= $searchQuery ?>" 
                   class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all <?= ($i == $pageNum) ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 hover:text-teal-600' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </nav>
    <?php endif; ?>
    </div>
</div>

<?php 
include '../views/admin/partials/modal-id-preview.php';
include '../views/admin/partials/modal-request-details.php';
?>

<script>
function openIdModal(imagePath, requestId) {
    const modal = document.getElementById('idModal');
    const modalImg = document.getElementById('modalIdImage');
    const requestIdInput = document.getElementById('modalRequestId');
    const requestIdDisplay = document.getElementById('modal-title');

    modalImg.src = imagePath;
    requestIdInput.value = requestId;
    requestIdDisplay.textContent = 'Request ID: #' + requestId;
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeIdModal() {
    const modal = document.getElementById('idModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function openDetailsModal(data) {
    const modal = document.getElementById('detailsModal');
    
    // Set text contents
    document.getElementById('modal-title').textContent = data.certificate_name;
    document.getElementById('detailsRequestId').textContent = 'Request #' + String(data.id).padStart(2, '0');
    document.getElementById('detailsFullName').textContent = data.full_name;
    document.getElementById('detailsCivilStatus').textContent = data.civil_status || '—';
    document.getElementById('detailsContact').textContent = data.contact_number || '—';
    document.getElementById('detailsEmail').textContent = data.email || '—';
    document.getElementById('detailsAddress').textContent = data.address;
    document.getElementById('detailsCertName').textContent = data.certificate_name;
    document.getElementById('detailsReason').textContent = data.reason_name || '—';
    document.getElementById('detailsRemarks').value = data.remarks || '';
    document.getElementById('detailsInputId').value = data.id;

    // Birthday formatting
    if (data.birthday) {
        const bday = new Date(data.birthday);
        document.getElementById('detailsBirthday').textContent = bday.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    } else {
        document.getElementById('detailsBirthday').textContent = '—';
    }

    // Appointment date formatting
    const appt = new Date(data.appointment_date);
    document.getElementById('detailsApptMonth').textContent = appt.toLocaleDateString('en-US', { month: 'short' });
    document.getElementById('detailsApptDay').textContent = appt.getDate();
    document.getElementById('detailsApptYear').textContent = appt.getFullYear();

    // ID Image
    const idImg = document.getElementById('detailsIdImage');
    const idContainer = document.getElementById('detailsIdContainer');
    const verifiedBadge = document.getElementById('detailsVerifiedBadge');
    
    if (data.id_image_path) {
        let path = data.id_image_path;
        if (!path.startsWith('public/') && !path.startsWith('http')) {
            path = 'public/' + path;
        }
        idImg.src = path;
        idContainer.classList.remove('hidden');
        if (data.is_verified == 1) {
            verifiedBadge.classList.remove('hidden');
        } else {
            verifiedBadge.classList.add('hidden');
        }
    } else {
        idContainer.classList.add('hidden');
    }

    // Status mapping and options
    const statusSelect = document.getElementById('detailsStatusSelect');
    const badgeContainer = document.getElementById('detailsStatusBadgeContainer');
    statusSelect.innerHTML = ''; // Clear previous
    
    const curr = data.status;
    const lowerStatus = curr.toLowerCase();
    
    // Update Badge in Modal
    const icons = {
        'pending': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" /></svg>',
        'approved': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>',
        'completed': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" /></svg>',
        'rejected': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>',
        'cancelled': '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" /></svg>'
    };
    
    badgeContainer.innerHTML = `<span class="status-badge status-${lowerStatus}">${icons[lowerStatus] || ''}${curr}</span>`;

    const statusMap = {
        'Pending': { 'Pending': '⏳ Pending', 'Approved': '✅ Approved', 'Rejected': '❌ Rejected' },
        'Approved': { 'Approved': '✅ Approved', 'Completed': '💎 Completed' },
        'Rejected': { 'Rejected': '❌ Rejected' },
        'Completed': { 'Completed': '💎 Completed' },
        'Cancelled': { 'Cancelled': '🚫 Cancelled' }
    };

    const options = statusMap[curr] || { [curr]: curr };
    for (const [val, label] of Object.entries(options)) {
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = label;
        if (val === curr) opt.selected = true;
        statusSelect.appendChild(opt);
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeDetailsModal() {
    document.getElementById('detailsModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function expandIdImage() {
    const src = document.getElementById('detailsIdImage').src;
    const id = document.getElementById('detailsInputId').value;
    openIdModal(src, id);
}

// Close modals when clicking outside
window.onclick = function(event) {
    const idModal = document.getElementById('idModal');
    const detailsModal = document.getElementById('detailsModal');
    if (event.target == idModal) closeIdModal();
    if (event.target == detailsModal) closeDetailsModal();
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") {
        closeIdModal();
    }
});

(function() {
    function initSearchAsYouType() {
        var searchForm = document.getElementById('searchForm');
        var searchInput = document.getElementById('searchInput');
        var tbody = document.getElementById('requestsBody');
        var paginationContainer = document.getElementById('paginationContainer');
        if (!searchForm || !searchInput || !tbody) return;
        var debounceTimer;
        function doSearch() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(function() {
                var q = searchInput.value.trim();
                var url = '?page=manage-requests&search=' + encodeURIComponent(q) + '&page_num=1&ajax=1';
                fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) { return r.text(); })
                    .then(function(html) {
                        var parser = new DOMParser();
                        var doc = parser.parseFromString(html, 'text/html');
                        var newTbody = doc.getElementById('manage-requests-tbody');
                        var newPagination = doc.getElementById('manage-requests-pagination');
                        if (newTbody) tbody.innerHTML = newTbody.innerHTML;
                        if (paginationContainer && newPagination) paginationContainer.innerHTML = newPagination.innerHTML;
                        var newUrl = '?page=manage-requests' + (q ? '&search=' + encodeURIComponent(q) : '');
                        if (window.history && window.history.replaceState) window.history.replaceState(null, '', newUrl);
                    })
                    .catch(function() {});
            }, 200);
        }
        searchForm.addEventListener('submit', function(e) { e.preventDefault(); doSearch(); });
        searchInput.addEventListener('input', doSearch);
        searchInput.addEventListener('keyup', doSearch);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSearchAsYouType);
    } else {
        initSearchAsYouType();
    }
})();
</script>

<?php require '../views/layout/footer.php'; ?>