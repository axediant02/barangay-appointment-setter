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

function renderStatusBadge($status) {
    $status = htmlspecialchars($status);
    $lowerStatus = strtolower($status);
    
    $icons = [
        'pending'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-13a.75.75 0 00-1.5 0v5c0 .414.336.75.75.75h4a.75.75 0 000-1.5h-3.25V5z" clip-rule="evenodd" /></svg>',
        'approved'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>',
        'completed' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" /></svg>',
        'rejected'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>',
        'cancelled' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM6.75 9.25a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" /></svg>'
    ];

    $icon = $icons[$lowerStatus] ?? '';
    return "<span class='status-badge status-{$lowerStatus}'>{$icon}{$status}</span>";
}

if (!empty($ajaxFragment)) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<div id="manage-requests-fragment"><table><tbody id="manage-requests-tbody">';
    foreach ($requests as $req):
        $rowCount = $count++;
        $reqJson = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
?>
<tr class="group hover:bg-slate-50/50 transition-colors status-<?= strtolower($req['status']) ?>">
    <td class="px-6 py-6 text-xs font-black text-slate-300 italic"><?= str_pad($rowCount, 2, '0', STR_PAD_LEFT) ?></td>
    
    <td class="px-6 py-6 font-bold text-slate-800 tracking-tight">
        <?= htmlspecialchars($req['full_name']) ?>
        <p class="text-[10px] text-slate-400 font-bold uppercase"><?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?></p>
    </td>

    <td class="px-6 py-6">
        <span class="text-[10px] font-black text-teal-700 bg-teal-50 px-2 py-1 rounded-md border border-teal-100 uppercase"><?= htmlspecialchars($req['certificate_name']) ?></span>
    </td>

    <td class="px-6 py-6">
        <span class="text-xs font-bold text-slate-600"><?= date('M d, Y', strtotime($req['appointment_date'])) ?></span>
    </td>

    <td class="px-6 py-6">
        <?php if (!empty($req['id_image_path'])): 
            $displayPath = $req['id_image_path'];
            if (strpos($displayPath, 'public/') !== 0 && strpos($displayPath, 'http') !== 0) {
                $displayPath = 'public/' . $displayPath;
            }
        ?>
            <?php if ($req['is_verified']): ?>
                <button type="button" onclick="openIdModal('<?= htmlspecialchars($displayPath) ?>', <?= $req['id'] ?>)" class="inline-flex items-center gap-1.5 bg-teal-500 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg uppercase tracking-wider shadow-sm hover:bg-teal-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    Verified
                </button>
            <?php else: ?>
                <button type="button" onclick="openIdModal('<?= htmlspecialchars($displayPath) ?>', <?= $req['id'] ?>)" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors border border-slate-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View ID
                </button>
            <?php endif; ?>
        <?php else: ?>
            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">No ID</span>
        <?php endif; ?>
    </td>

    <td class="px-6 py-6 text-center">
        <?= renderStatusBadge($req['status']) ?>
    </td>

    <td class="px-6 py-6 text-center">
        <button type="button" onclick='openDetailsModal(<?= $reqJson ?>)' class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-teal-600 text-slate-600 hover:text-white rounded-xl transition-all shadow-sm hover:shadow-teal-100 group" title="View Full Details">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </td>
</tr>
<?php
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

<div class="sticky-nav">
    <div class="max-w-[1600px] mx-auto px-6 py-4 flex items-center justify-between">
        <a href="?page=admin-dashboard" class="inline-flex items-center gap-2 text-slate-500 font-bold hover:text-teal-600 transition-all group" aria-label="Back to admin dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="text-[10px] uppercase tracking-widest">Back to Dashboard</span>
        </a>
        
        <form id="searchForm" method="get" action="" class="relative w-64 md:w-80 border border-slate-200 rounded-xl bg-slate-50 flex items-center px-3 focus-within:ring-2 focus-within:ring-teal-500/20 transition-all">
            <input type="hidden" name="page" value="manage-requests">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input type="text" id="searchInput" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by name, contact, certificate..." class="bg-transparent border-none w-full py-2 text-xs focus:ring-0 outline-none font-medium" aria-label="Search all requests" autocomplete="off">
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
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-32">Appointment</th>
                        <th class="px-6 py-5 text-left text-[10px] font-black uppercase tracking-widest w-32">Proof of ID</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-32">Status</th>
                        <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest w-24">Action</th>
                    </tr>
                </thead>
                <tbody id="requestsBody" class="divide-y divide-slate-50">
                    <?php foreach ($requests as $req): 
                        $rowCount = $count++;
                        $reqJson = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
                    ?>
                        <tr class="group hover:bg-slate-50/50 transition-colors status-<?= strtolower($req['status']) ?>">
                            <td class="px-6 py-6 text-xs font-black text-slate-300 italic"><?= str_pad($rowCount, 2, '0', STR_PAD_LEFT) ?></td>
                            
                            <td class="px-6 py-6 font-bold text-slate-800 tracking-tight">
                                <?= htmlspecialchars($req['full_name']) ?>
                                <p class="text-[10px] text-slate-400 font-bold uppercase"><?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?></p>
                            </td>

                            <td class="px-6 py-6">
                                <span class="text-[10px] font-black text-teal-700 bg-teal-50 px-2 py-1 rounded-md border border-teal-100 uppercase"><?= htmlspecialchars($req['certificate_name']) ?></span>
                                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1"><?= htmlspecialchars($req['reason_for_request'] ?? '—') ?></p>
                            </td>

                            <td class="px-6 py-6">
                                <span class="text-xs font-bold text-slate-600"><?= date('M d, Y', strtotime($req['appointment_date'])) ?></span>
                            </td>

                            <td class="px-6 py-6">
                                <?php if (!empty($req['id_image_path'])): 
                                    $displayPath = $req['id_image_path'];
                                    if (strpos($displayPath, 'public/') !== 0 && strpos($displayPath, 'http') !== 0) {
                                        $displayPath = 'public/' . $displayPath;
                                    }
                                ?>
                                    <?php if ($req['is_verified']): ?>
                                        <button type="button" onclick="openIdModal('<?= htmlspecialchars($displayPath) ?>', <?= $req['id'] ?>)" class="inline-flex items-center gap-1.5 bg-teal-500 text-white text-[10px] font-black px-2.5 py-1.5 rounded-lg uppercase tracking-wider shadow-sm hover:bg-teal-600 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Verified
                                        </button>
                                    <?php else: ?>
                                        <button type="button" onclick="openIdModal('<?= htmlspecialchars($displayPath) ?>', <?= $req['id'] ?>)" class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 hover:bg-teal-50 text-slate-600 hover:text-teal-700 rounded-lg text-xs font-bold uppercase tracking-wider transition-colors border border-slate-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View ID
                                        </button>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">No ID</span>
                                <?php endif; ?>
                            </td>

                            <td class="px-6 py-6 text-center">
                                <?= renderStatusBadge($req['status']) ?>
                            </td>

                            <td class="px-6 py-6 text-center">
                                <button type="button" onclick='openDetailsModal(<?= $reqJson ?>)' class="inline-flex items-center justify-center p-2.5 bg-slate-100 hover:bg-teal-600 text-slate-600 hover:text-white rounded-xl transition-all shadow-sm hover:shadow-teal-100 group" title="View Full Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
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

<!-- ID Preview Modal -->
<div id="idModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/90 backdrop-blur-sm transition-opacity" onclick="closeIdModal()"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200">
                
                <div class="bg-slate-900 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-black leading-6 text-white uppercase tracking-widest" id="modal-title">Proof of Identification</h3>
                    <button type="button" onclick="closeIdModal()" class="text-slate-400 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-slate-100 p-2 flex items-center justify-center min-h-[300px]">
                    <img id="modalIdImage" src="" alt="ID Preview" class="max-w-full max-h-[60vh] rounded-lg shadow-md object-contain">
                </div>
                
                <form method="POST" action="?page=manage-requests<?= $pageNum > 1 ? '&page_num=' . (int)$pageNum : '' ?><?= $search !== '' ? '&search=' . rawurlencode($search) : '' ?>" class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2 border-t border-slate-200">
                    <input type="hidden" name="request_id" id="modalRequestId">
                    <input type="hidden" name="page_num" value="<?= (int)$pageNum ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    
                    <button type="submit" name="is_verified" value="1" class="inline-flex w-full justify-center rounded-md bg-teal-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-teal-500 sm:w-auto uppercase tracking-wider transition-colors">Verify ID</button>
                    <button type="submit" name="is_verified" value="0" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-bold text-white shadow-sm hover:bg-red-500 sm:w-auto uppercase tracking-wider transition-colors">Reject ID</button>
                    <button type="button" onclick="closeIdModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto uppercase tracking-wider transition-colors">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Request Details Modal -->
<div id="detailsModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100">
                
                <!-- Modal Header -->
                <div class="bg-slate-900 px-6 py-6 text-white flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-black tracking-tight" id="modal-title">Request Details</h3>
                        <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-0.5" id="detailsRequestId">Request #00</p>
                    </div>
                    <button type="button" onclick="closeDetailsModal()" class="text-slate-400 hover:text-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="?page=manage-requests<?= $pageNum > 1 ? '&page_num=' . (int)$pageNum : '' ?><?= $search !== '' ? '&search=' . rawurlencode($search) : '' ?>">
                    <input type="hidden" name="request_id" id="detailsInputId">
                    <input type="hidden" name="page_num" value="<?= (int)$pageNum ?>">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">

                    <div class="bg-white p-6 sm:p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            
                            <!-- Left Column: Resident Info -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Resident Information</h4>
                                    <div class="space-y-3 bg-slate-50 rounded-2xl p-4 border border-slate-100">
                                        <div class="flex justify-between items-start border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                            <span class="text-[9px] font-black uppercase text-slate-400">Full Name</span>
                                            <span class="text-xs font-bold text-slate-800" id="detailsFullName">—</span>
                                        </div>
                                        <div class="flex justify-between items-start border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                            <span class="text-[9px] font-black uppercase text-slate-400">Civil Status</span>
                                            <span class="text-xs font-bold text-slate-800" id="detailsCivilStatus">—</span>
                                        </div>
                                        <div class="flex justify-between items-start border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                            <span class="text-[9px] font-black uppercase text-slate-400">Birthday</span>
                                            <span class="text-xs font-bold text-slate-800" id="detailsBirthday">—</span>
                                        </div>
                                        <div class="flex justify-between items-start border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                            <span class="text-[9px] font-black uppercase text-slate-400">Contact</span>
                                            <span class="text-xs font-bold text-slate-800" id="detailsContact">—</span>
                                        </div>
                                        <div class="flex justify-between items-start border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                            <span class="text-[9px] font-black uppercase text-slate-400">Email</span>
                                            <span class="text-xs font-bold text-slate-800" id="detailsEmail">—</span>
                                        </div>
                                        <div class="pt-1">
                                            <span class="text-[9px] font-black uppercase text-slate-400 block mb-1">Address</span>
                                            <p class="text-xs font-medium text-slate-600 leading-relaxed" id="detailsAddress">—</p>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Proof of Identity</h4>
                                    <div id="detailsIdContainer" class="relative group cursor-pointer overflow-hidden rounded-2xl border-2 border-slate-100" onclick="expandIdImage()">
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                            </svg>
                                        </div>
                                        <img id="detailsIdImage" src="" alt="ID Preview" class="w-full h-32 object-cover">
                                        <div id="detailsVerifiedBadge" class="absolute top-3 right-3 bg-teal-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded shadow-sm uppercase tracking-tighter flex items-center gap-1 hidden">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            Verified
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Request Management -->
                            <div class="space-y-6">
                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Request Details</h4>
                                    <div class="bg-teal-50 rounded-2xl p-5 border border-teal-100">
                                        <div class="flex justify-between items-start mb-1">
                                            <p class="text-[9px] font-black text-teal-600 uppercase tracking-widest" id="detailsCertType">Certificate Name</p>
                                            <div id="detailsStatusBadgeContainer"></div>
                                        </div>
                                        <p class="text-base font-black text-slate-900 leading-tight mb-4" id="detailsCertName">Barangay Clearance</p>
                                        
                                        <div class="mb-4">
                                            <p class="text-[9px] font-black text-teal-600 uppercase tracking-widest mb-1">Reason for Request</p>
                                            <p class="text-[11px] font-bold text-slate-700 italic" id="detailsReason">—</p>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm border border-teal-200 flex flex-col items-center justify-center">
                                                <span class="text-[8px] font-black text-teal-600 uppercase" id="detailsApptMonth">Oct</span>
                                                <span class="text-sm font-black text-slate-900 leading-none" id="detailsApptDay">12</span>
                                            </div>
                                            <div>
                                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Appointment Date</p>
                                                <p class="text-xs font-bold text-slate-700" id="detailsApptYear">2023</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-3">Update Status</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Request Status</label>
                                            <select name="status" id="detailsStatusSelect" class="w-full text-xs font-bold uppercase border-2 border-slate-100 bg-white rounded-xl px-4 py-3 focus:border-teal-500 outline-none cursor-pointer transition-all">
                                                <!-- Options populated via JS -->
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-2">Internal Remark</label>
                                            <textarea name="remarks" id="detailsRemarks" rows="3" placeholder="Add a note (e.g., missing documents, reason for rejection...)" class="w-full text-xs font-medium border-2 border-slate-100 bg-slate-50 rounded-xl px-4 py-3 focus:bg-white focus:border-teal-500 outline-none transition-all resize-none"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3 rounded-b-3xl border-t border-slate-100">
                        <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-slate-900 px-8 py-3 text-xs font-black text-white shadow-xl hover:bg-teal-600 sm:w-auto uppercase tracking-widest transition-all">
                            Save Changes
                        </button>
                        <button type="button" onclick="closeDetailsModal()" class="inline-flex w-full justify-center rounded-xl bg-white px-8 py-3 text-xs font-black text-slate-600 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:w-auto uppercase tracking-widest transition-all">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openIdModal(imagePath, requestId) {
    const modal = document.getElementById('idModal');
    const modalImg = document.getElementById('modalIdImage');
    const requestIdInput = document.getElementById('modalRequestId');
    const requestIdDisplay = document.getElementById('modalIdTitle');

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
    document.getElementById('detailsReason').textContent = data.reason_for_request || '—';
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