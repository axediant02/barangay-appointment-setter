<?php
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ?page=login");
    exit;
}

include 'components/status-badge.php';

$itemsPerPage = 25;
$pageNum = isset($currentPage) ? (int)$currentPage : 1;
$search = isset($search) ? $search : '';
$startIndex = ($pageNum - 1) * $itemsPerPage;
$count = $startIndex + 1;
$searchQuery = $search !== '' ? '&search=' . rawurlencode($search) : '';

// AJAX Fragment update
if (!empty($ajaxFragment)) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<div id="manage-requests-fragment"><table><tbody id="manage-requests-tbody">';
    foreach ($requests as $req):
        $rowCount = $count++;
        include 'components/request-row.php';
    endforeach;
    echo '</tbody></table><div id="manage-requests-pagination">';
    if (isset($totalPages) && $totalPages > 1) {
        echo '<nav class="flex items-center gap-2 bg-white p-2 border-2 border-slate-100 rounded-2xl shadow-sm">';
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($i == $pageNum) ? 'bg-slate-900 text-white shadow-lg' : 'text-slate-400 hover:bg-slate-50 hover:text-teal-600';
            echo '<a href="?page=manage-requests&page_num=' . $i . $searchQuery . '" class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-black transition-all ' . $activeClass . '">' . $i . '</a>';
        }
        echo '</nav>';
    }
    echo '</div></div>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Requests | BrgyPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        @layer base {
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        }
        @layer components {
            .sidebar-item {
                @apply flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-300 font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-700 hover:translate-x-1;
            }
            .sidebar-item.active {
                @apply bg-teal-600 text-white shadow-xl shadow-teal-200/50 hover:translate-x-0 hover:bg-teal-600;
            }
            .icon-box {
                @apply transition-transform group-hover:scale-110 flex-shrink-0;
            }
            .status-badge {
                @apply inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border transition-all duration-200;
            }
            .status-badge svg { @apply w-3.5 h-3.5; }
            .status-pending { @apply bg-amber-50 text-amber-600 border-amber-100; }
            .status-approved { @apply bg-emerald-50 text-emerald-600 border-emerald-100; }
            .status-completed { @apply bg-cyan-50 text-cyan-600 border-cyan-100; }
            .status-rejected { @apply bg-red-50 text-red-600 border-red-100; }
            .status-cancelled { @apply bg-slate-50 text-slate-500 border-slate-200; }
        }
    </style>
</head>
<body class="h-full overflow-hidden">

<div class="flex h-full">
    <?php 
    $currentPage = 'manage-requests';
    include 'components/sidebar.php'; 
    ?>

    <main class="flex-1 h-full overflow-y-auto bg-slate-50/50">
        <header class="bg-white border-b border-slate-200 px-10 py-4 flex justify-between items-center sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <h2 class="text-xl font-bold text-slate-800">Requests Management</h2>
                <span class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-700 text-[10px] font-black uppercase tracking-wider">Live View</span>
            </div>
            
            <form id="searchForm" method="get" action="" class="relative group w-96">
                <input type="hidden" name="page" value="manage-requests">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-slate-400 group-focus-within:text-teal-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" id="searchInput" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search residents, certificates..." class="block w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none">
            </form>

            <div class="flex items-center gap-3">
                <div class="text-right mr-2">
                    <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
                    <p class="text-[10px] text-slate-400 font-medium italic">Official Desk</p>
                </div>
                <div class="h-10 w-10 bg-teal-600 rounded-full border-2 border-white shadow-sm overflow-hidden flex items-center justify-center font-bold text-white uppercase text-sm">
                    <?= substr($_SESSION['username'] ?? 'A', 0, 2) ?>
                </div>
            </div>
        </header>

        <div class="p-10 max-w-[1600px] mx-auto">
            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Requests Hub</h1>
                <p class="text-slate-500 font-medium mt-1">Manage and verify resident applications.</p>
            </div>
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center bg-white">
                    <div>
                        <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full bg-teal-500 animate-pulse"></span>
                            Incoming Requests
                        </h3>
                        <p class="text-slate-400 text-xs font-medium mt-1">Review the latest document submissions.</p>
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full table-auto" id="requestsTable">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-500">
                                <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-16">#</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest min-w-[200px]">Resident</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest min-w-[180px]">Email</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest">Certificate</th>
                                <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-32">Appointment</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-32">Status</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-32">Identification</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-24">Action</th>
                            </tr>
                        </thead>
                        <tbody id="requestsBody" class="divide-y divide-slate-50">
                            <?php foreach ($requests as $req): 
                                $rowCount = $count++;
                                include 'components/request-row.php';
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
    </main>
</div>

<?php 
include '../views/admin/components/modal-id-preview.php';
include '../views/admin/components/modal-request-details.php';
include '../views/admin/components/modal-quick-update.php';
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
    if (!modal) return;
    
    // Set text contents
    const title = document.getElementById('modal-details-title');
    if (title) title.textContent = data.certificate_name;

    const reqId = document.getElementById('detailsRequestId');
    if (reqId) reqId.textContent = 'Request #' + String(data.id).padStart(2, '0');

    const fullName = document.getElementById('detailsFullName');
    if (fullName) fullName.textContent = data.full_name;

    const civilStatus = document.getElementById('detailsCivilStatus');
    if (civilStatus) civilStatus.textContent = data.civil_status || '—';

    const contact = document.getElementById('detailsContact');
    if (contact) contact.textContent = data.contact_number || '—';

    const email = document.getElementById('detailsEmail');
    if (email) email.textContent = data.email || '—';

    const address = document.getElementById('detailsAddress');
    if (address) address.textContent = data.address;

    const certName = document.getElementById('detailsCertName');
    if (certName) certName.textContent = data.certificate_name;

    const reason = document.getElementById('detailsReason');
    if (reason) reason.textContent = data.reason_name || '—';

    const remarks = document.getElementById('detailsRemarks');
    if (remarks) remarks.value = data.remarks || '';

    const inputId = document.getElementById('detailsInputId');
    if (inputId) inputId.value = data.id;

    const headId = document.getElementById('detailsRequestIdHead');
    if (headId) headId.textContent = '#' + String(data.id).padStart(5, '0');

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
    if (statusSelect) statusSelect.innerHTML = ''; // Clear previous
    
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

function openQuickUpdateModal(data) {
    const modal = document.getElementById('quickUpdateModal');
    if (!modal) return;

    // Populate fields
    document.getElementById('quickUpdateInputId').value = data.id;
    document.getElementById('quickUpdateName').textContent = data.full_name || '—';
    document.getElementById('quickUpdateCert').textContent = data.certificate_name || '—';
    document.getElementById('quickUpdateRemarks').value = data.remarks || '';

    // Set status select to current
    const select = document.getElementById('quickUpdateStatus');
    if (select) select.value = data.status || 'Pending';

    // Render current status badge
    const badgeColors = {
        'Pending':   'bg-amber-50 text-amber-700 border-amber-200',
        'Approved':  'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Completed': 'bg-blue-50 text-blue-700 border-blue-200',
        'Rejected':  'bg-red-50 text-red-700 border-red-200',
        'Cancelled': 'bg-slate-100 text-slate-500 border-slate-200'
    };
    const color = badgeColors[data.status] || badgeColors['Pending'];
    const badgeContainer = document.getElementById('quickUpdateCurrentBadge');
    if (badgeContainer) {
        badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl border text-[10px] font-black uppercase tracking-wider ${color}">${data.status}</span>`;
    }

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeQuickUpdateModal() {
    const modal = document.getElementById('quickUpdateModal');
    if (modal) modal.classList.add('hidden');
    document.body.style.overflow = '';
}

// Close modals when clicking outside
window.onclick = function(event) {
    const idModal = document.getElementById('idModal');
    const detailsModal = document.getElementById('detailsModal');
    const quickUpdateModal = document.getElementById('quickUpdateModal');
    if (event.target == idModal) closeIdModal();
    if (event.target == detailsModal) closeDetailsModal();
    if (event.target == quickUpdateModal) closeQuickUpdateModal();
}

document.addEventListener('keydown', function(event) {
    if (event.key === "Escape") { 
        closeIdModal();
        closeDetailsModal();
        closeQuickUpdateModal();
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
    function syncAdminRequests() {
        // Don't sync if user is typing or a modal is open
        if (document.activeElement === searchInput || !document.getElementById('idModal').classList.contains('hidden') || !document.getElementById('detailsModal').classList.contains('hidden')) {
            return;
        }

        var q = searchInput.value.trim();
        // Get current page from pagination if possible, otherwise use pageNum from PHP
        var currentPage = <?= $pageNum ?>;
        var url = '?page=manage-requests&search=' + encodeURIComponent(q) + '&page_num=' + currentPage + '&ajax=1';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newTbody = doc.getElementById('manage-requests-tbody');
                var newPagination = doc.getElementById('manage-requests-pagination');
                
                if (newTbody && tbody.innerHTML !== newTbody.innerHTML) {
                    tbody.innerHTML = newTbody.innerHTML;
                }
                if (paginationContainer && newPagination && paginationContainer.innerHTML !== newPagination.innerHTML) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }
            })
            .catch(function() {});
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initSearchAsYouType();
            setInterval(syncAdminRequests, 15000);
        });
    } else {
        initSearchAsYouType();
        setInterval(syncAdminRequests, 15000);
    }
})();
</script>

</body>
</html>