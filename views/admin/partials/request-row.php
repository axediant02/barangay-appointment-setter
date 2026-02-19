<?php
/** @var array $req */
/** @var int $rowCount */
$reqJson = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
?>
<tr class="group hover:bg-slate-50 transition-colors">
    <td class="px-8 py-6">
        <div class="flex items-center gap-4">
            <div class="h-10 w-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs group-hover:bg-teal-50 group-hover:text-teal-600 transition-colors">
                <?= str_pad($rowCount, 2, '0', STR_PAD_LEFT) ?>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800 transition-colors group-hover:text-teal-700"><?= htmlspecialchars($req['full_name']) ?></p>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"><?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?></p>
            </div>
        </div>
    </td>

    <td class="px-8 py-6">
        <div class="flex flex-col">
            <span class="text-[10px] font-black text-slate-700 bg-slate-100 px-2 py-1 rounded-md border border-slate-200 uppercase w-fit group-hover:bg-white group-hover:border-teal-100 transition-colors">
                <?= htmlspecialchars($req['certificate_name']) ?>
            </span>
            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1.5 ml-0.5"><?= htmlspecialchars($req['reason_name'] ?? '—') ?></p>
        </div>
    </td>

    <td class="px-8 py-6">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 bg-slate-50 rounded-lg flex flex-col items-center justify-center border border-slate-100 group-hover:border-teal-100 transition-colors">
                <span class="text-[8px] font-black text-slate-400 uppercase leading-none"><?= date('M', strtotime($req['appointment_date'])) ?></span>
                <span class="text-xs font-black text-slate-700 leading-none mt-0.5"><?= date('d', strtotime($req['appointment_date'])) ?></span>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase"><?= date('Y', strtotime($req['appointment_date'])) ?></span>
        </div>
    </td>

    <td class="px-8 py-6 text-center">
        <?php 
        if (function_exists('renderStatusBadge')) {
            echo renderStatusBadge($req['status']);
        } else {
            // Fallback if partial wasn't included correctly
            echo '<span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border border-slate-100 bg-slate-50 text-slate-600">' . htmlspecialchars($req['status']) . '</span>';
        }
        ?>
    </td>

    <td class="px-8 py-6 text-center">
        <div class="flex items-center justify-center gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
            <button type="button" onclick='openDetailsModal(<?= $reqJson ?>)' class="h-9 w-9 flex items-center justify-center bg-teal-50 text-teal-600 rounded-xl hover:bg-teal-600 hover:text-white transition-all shadow-sm" title="View Details">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            </button>
        </div>
    </td>
</tr>
    </td>
</tr>
