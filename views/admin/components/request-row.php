<?php
/** @var array $req */
/** @var int $rowCount */
$reqJson = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
?>
<tr class="group hover:bg-slate-50 transition-colors">
    <td class="px-6 py-5 w-16">
        <div class="h-10 w-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-black text-xs group-hover:bg-teal-50 group-hover:text-teal-600 transition-colors">
            <?= str_pad($rowCount, 2, '0', STR_PAD_LEFT) ?>
        </div>
    </td>

    <td class="px-6 py-5 min-w-[200px]">
        <div>
            <p class="text-sm font-bold text-slate-800 transition-colors group-hover:text-teal-700"><?= htmlspecialchars($req['full_name']) ?></p>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight"><?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?></p>
        </div>
    </td>

    <td class="px-6 py-5">
        <div class="flex flex-col">
            <span class="text-[10px] font-black text-slate-700 bg-slate-100 px-2 py-1 rounded-md border border-slate-200 uppercase w-fit group-hover:bg-white group-hover:border-teal-100 transition-colors">
                <?= htmlspecialchars($req['certificate_name']) ?>
            </span>
            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1.5 ml-0.5"><?= htmlspecialchars($req['reason_name'] ?? '—') ?></p>
        </div>
    </td>

    <td class="px-6 py-5 w-32">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 bg-slate-50 rounded-lg flex flex-col items-center justify-center border border-slate-100 group-hover:border-teal-100 transition-colors">
                <span class="text-[8px] font-black text-slate-400 uppercase lead ing-none"><?= date('M', strtotime($req['appointment_date'])) ?></span>
                <span class="text-xs font-black text-slate-700 leading-none mt-0.5"><?= date('d', strtotime($req['appointment_date'])) ?></span>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase"><?= date('Y', strtotime($req['appointment_date'])) ?></span>
        </div>
    </td>

    <td class="px-6 py-5 text-center w-32">
        <?php 
        if (function_exists('renderStatusBadge')) {
            echo renderStatusBadge($req['status']);
        } else {
            // Fallback if partial wasn't included correctly
            echo '<span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border border-slate-100 bg-slate-50 text-slate-600">' . htmlspecialchars($req['status']) . '</span>';
        }
        ?>
    </td>

    <td class="px-6 py-5 text-center w-32">
        <div class="flex items-center justify-center">
            <?php 
            $isVerified = isset($req['is_verified']) ? $req['is_verified'] : null;
            $idPath = $req['id_image_path'] ?? '';
            if ($idPath && strpos($idPath, 'http') !== 0 && strpos($idPath, 'public/') !== 0) {
                $idPath = 'public/' . $idPath;
            }
            ?>

            <?php if ($isVerified == 1): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 text-[10px] font-black uppercase tracking-wider shadow-sm" title="Resident ID Verified">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                    Verified
                </span>
            <?php else: ?>
                <button type="button" onclick="openIdModal('<?= htmlspecialchars($idPath) ?>', '<?= $req['id'] ?>')" 
                        class="h-9 px-3 flex items-center gap-2 <?= $isVerified === 0 && $isVerified !== null ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-teal-50 text-teal-600 border-teal-100' ?> rounded-xl hover:opacity-80 transition-all shadow-sm border group/verify" 
                        title="<?= $isVerified === 0 && $isVerified !== null ? 'ID Rejected - Click to re-verify' : 'Verify Resident ID' ?>">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                    <span class="text-[9px] font-black uppercase tracking-wider">Verify</span>
                </button>
            <?php endif; ?>
        </div>
    </td>

    <td class="px-6 py-5 text-center w-24">
        <div class="flex items-center justify-center">
            <button type="button" onclick='openDetailsModal(<?= $reqJson ?>)' class="h-9 w-9 flex items-center justify-center bg-slate-50 text-slate-500 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-200" title="View Details">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
            </button>
        </div>
    </td>
</tr>
