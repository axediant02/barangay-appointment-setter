<?php
/** @var int $pageNum */
/** @var string $search */
?>
<div id="idModal" class="fixed inset-0 z-[110] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
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
