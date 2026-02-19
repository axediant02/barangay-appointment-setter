<?php
/** @var array $req */
/** @var int $rowCount */
/** @var string $searchQuery */
/** @var int $pageNum */

$reqJson = htmlspecialchars(json_encode($req), ENT_QUOTES, 'UTF-8');
$lowerStatus = strtolower($req['status']);

if (!function_exists('renderStatusBadge')) {
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
}
?>
<tr class="group hover:bg-slate-50/50 transition-colors status-<?= $lowerStatus ?>">
    <td class="px-6 py-6 text-xs font-black text-slate-300 italic"><?= str_pad($rowCount, 2, '0', STR_PAD_LEFT) ?></td>
    
    <td class="px-6 py-6 font-bold text-slate-800 tracking-tight">
        <?= htmlspecialchars($req['full_name']) ?>
        <p class="text-[10px] text-slate-400 font-bold uppercase"><?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?></p>
    </td>

    <td class="px-6 py-6">
        <span class="text-[10px] font-black text-teal-700 bg-teal-50 px-2 py-1 rounded-md border border-teal-100 uppercase"><?= htmlspecialchars($req['certificate_name']) ?></span>
        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1"><?= htmlspecialchars($req['reason_name'] ?? '—') ?></p>
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
        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase border <?= getStatusBadgeStyle($req['status']) ?>">
            <?= htmlspecialchars($req['status']) ?>
        </span>
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
