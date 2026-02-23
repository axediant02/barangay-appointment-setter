<?php
/** @var int $pageNum */
/** @var string $search */
?>
<!-- Details Modal -->
<div id="detailsModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="closeDetailsModal()"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
        <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200">
            
            <div class="bg-slate-900 px-8 py-6 flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-black text-white uppercase tracking-widest" id="modal-details-title">Request Details</h3>
                    <p class="text-slate-400 text-[10px] font-bold uppercase mt-1">Transaction ID: <span id="detailsRequestIdHead" class="text-teal-400">#00000</span></p>
                </div>
                <button type="button" onclick="closeDetailsModal()" class="text-slate-400 hover:text-white transition-all bg-slate-800 p-2 rounded-xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="POST" action="?page=manage-requests<?= $pageNum > 1 ? '&page_num=' . (int)$pageNum : '' ?><?= $search !== '' ? '&search=' . rawurlencode($search) : '' ?>">
                <input type="hidden" name="request_id" id="detailsInputId">
                <input type="hidden" name="page_num" value="<?= (int)$pageNum ?>">
                <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8 bg-white">
                    <!-- Left: Resident Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="h-1.5 w-6 bg-teal-500 rounded-full"></div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Resident Information</h4>
                        </div>
                        
                        <div class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Full Name</label>
                                <p id="detailsFullName" class="text-sm font-bold text-slate-800"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Civil Status</label>
                                    <p id="detailsCivilStatus" class="text-sm font-bold text-slate-800"></p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Birthday</label>
                                    <p id="detailsBirthday" class="text-sm font-bold text-slate-800"></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Contact Number</label>
                                    <p id="detailsContact" class="text-sm font-bold text-slate-800"></p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Email Address</label>
                                    <p id="detailsEmail" class="text-sm font-bold text-teal-600"></p>
                                </div>
                            </div>
                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Current Address</label>
                                <p id="detailsAddress" class="text-xs font-bold text-slate-800 leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- ID Preview Section inside Details -->
                        <div id="detailsIdContainer" class="hidden">
                             <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2 ml-1">Proof of Identity</label>
                             <div class="relative group cursor-pointer overflow-hidden rounded-2xl border-2 border-slate-100" onclick="expandIdImage()">
                                <img id="detailsIdImage" src="" alt="ID Image" class="w-full h-32 object-cover transition-transform group-hover:scale-110">
                                <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <span class="text-white text-[10px] font-black uppercase tracking-widest bg-slate-900/60 px-3 py-1.5 rounded-lg backdrop-blur-sm">Click to Expand</span>
                                </div>
                                <div id="detailsVerifiedBadge" class="absolute top-2 right-2 hidden">
                                    <span class="bg-emerald-500 text-white p-1 rounded-full shadow-lg">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    </span>
                                </div>
                             </div>
                             <div id="detailsIdStatus" class="mt-2 flex justify-center">
                                  <!-- Status indicator injected by JS -->
                              </div>
                        </div>
                    </div>

                    <!-- Right: Request Info -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="h-1.5 w-6 bg-teal-500 rounded-full"></div>
                            <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Document & Status</h4>
                        </div>

                        <div class="space-y-5">
                            <div class="bg-slate-50 p-6 rounded-3xl border border-slate-100 space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Certificate Type</label>
                                    <p id="detailsCertName" class="text-sm font-bold text-slate-800"></p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-1">Reason for Request</label>
                                    <p id="detailsReason" class="text-sm font-bold text-slate-800"></p>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2 ml-1">Appointment Schedule</label>
                                <div class="flex items-center gap-4 bg-white border border-slate-200 p-4 rounded-2xl shadow-sm">
                                    <div class="flex flex-col items-center justify-center bg-teal-50 text-teal-600 rounded-xl px-3 py-2 border border-teal-100 min-w-[60px]">
                                        <span id="detailsApptMonth" class="text-[10px] font-black uppercase leading-none">---</span>
                                        <span id="detailsApptDay" class="text-xl font-black leading-none mt-1">--</span>
                                        <span id="detailsApptYear" class="text-[8px] font-bold opacity-60 leading-none mt-1">----</span>
                                    </div>
                                    <div>
                                        <p id="detailsAppointmentDate" class="font-black text-slate-800 text-sm">Appointment Day</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">Office Hours: 8:00 AM - 5:00 PM</p>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2 ml-1">Current Status</label>
                                    <div id="detailsStatusBadgeContainer" class="flex items-center">
                                        <!-- Badge injected by JS -->
                                    </div>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2 ml-1">Actions</label>
                                    <select name="status" id="detailsStatusSelect" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none">
                                        <!-- Options injected by JS -->
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] block mb-2 ml-1">Internal Remarks</label>
                                <textarea name="remarks" id="detailsRemarks" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all outline-none resize-none" placeholder="Add internal notes about this request..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-50 px-8 py-6 flex items-center justify-between border-t border-slate-100">
                    <div>
                        <span id="detailsRequestId" class="text-[10px] font-black text-slate-300 uppercase tracking-widest pl-2">Request #00</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <button type="button" onclick="closeDetailsModal()" class="text-sm font-bold text-slate-500 hover:text-slate-800 transition-colors uppercase tracking-widest px-4">Cancel</button>
                        <button type="submit" name="update_request" value="1" id="detailsSubmitBtn" class="bg-teal-600 hover:bg-teal-700 text-white px-8 py-3.5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-teal-200 transition-all active:scale-95 flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
