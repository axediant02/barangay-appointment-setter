<?php
/** @var int $pageNum */
/** @var string $search */
?>
<div id="quickUpdateModal" class="fixed inset-0 z-[110] hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" onclick="closeQuickUpdateModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full sm:max-w-md border border-slate-200">

            <div class="bg-slate-900 px-7 py-5 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Update Request</h3>
                    <p class="text-slate-400 text-[10px] font-bold uppercase mt-0.5">Quick Status Change</p>
                </div>
                <button type="button" onclick="closeQuickUpdateModal()" class="text-slate-400 hover:text-white transition-all bg-slate-800 p-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-7 py-4 bg-slate-50 border-b border-slate-100 flex items-center gap-4">
                <div class="flex-1 min-w-0">
                    <p id="quickUpdateName" class="text-sm font-black text-slate-800 truncate">—</p>
                    <p id="quickUpdateCert" class="text-[10px] font-bold text-slate-400 uppercase tracking-wide truncate mt-0.5">—</p>
                </div>
                <div id="quickUpdateCurrentBadge"></div>
            </div>

            <form method="POST" action="?page=manage-requests<?= $pageNum > 1 ? '&page_num=' . (int)$pageNum : '' ?><?= $search !== '' ? '&search=' . rawurlencode($search) : '' ?>">
                <input type="hidden" name="request_id" id="quickUpdateInputId">
                <input type="hidden" name="page_num" value="<?= (int)$pageNum ?>">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">

                <div class="px-7 py-6 space-y-5">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">New Status</label>
                        <select name="status" id="quickUpdateStatus" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none">
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Completed">Completed</option>
                            <option value="Rejected">Rejected</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2">Remarks <span class="text-slate-300 normal-case font-medium tracking-normal">(optional)</span></label>
                        <textarea name="remarks" id="quickUpdateRemarks" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none resize-none" placeholder="Add a note about this status change..."></textarea>
                    </div>
                </div>

                <div class="bg-slate-50 px-7 py-5 flex items-center justify-between border-t border-slate-100">
                    <button type="button" onclick="closeQuickUpdateModal()" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest">Cancel</button>
                    <button type="submit" name="update_request" value="1" class="bg-teal-600 hover:bg-teal-700 text-white px-7 py-3 rounded-2xl font-black text-xs uppercase tracking-[0.15em] shadow-lg shadow-teal-200 transition-all active:scale-95 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
