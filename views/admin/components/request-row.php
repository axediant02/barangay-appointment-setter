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
            <p class="text-sm font-bold text-slate-800 group-hover:text-teal-700 transition-colors">
                <?= htmlspecialchars($req['full_name']) ?>
            </p>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                <?= htmlspecialchars($req['contact_number'] ?? 'No Contact') ?>
            </p>
        </div>
    </td>

    <td class="px-6 py-5 min-w-[180px]">
        <a href="mailto:<?= htmlspecialchars($req['email'] ?? '') ?>"
           class="text-[11px] font-bold text-teal-600 hover:text-teal-800 hover:underline transition-colors truncate block max-w-[180px]"
           title="<?= htmlspecialchars($req['email'] ?? '') ?>">
            <?= htmlspecialchars($req['email'] ?? '—') ?>
        </a>
    </td>

    <td class="px-6 py-5">
        <div class="flex flex-col">
            <span class="text-[10px] font-black text-slate-700 bg-slate-100 px-2 py-1 rounded-md border border-slate-200 uppercase w-fit group-hover:bg-white group-hover:border-teal-100 transition-colors">
                <?= htmlspecialchars($req['certificate_name']) ?>
            </span>
            <p class="text-[10px] text-slate-400 font-bold uppercase mt-1.5 ml-0.5">
                <?= htmlspecialchars($req['reason_name'] ?? '—') ?>
            </p>
            <?php if (!empty($req['remarks'])): ?>
                <p class="text-[9px] text-teal-600 font-medium italic mt-1 ml-0.5 truncate max-w-[150px]" title="<?= htmlspecialchars($req['remarks']) ?>">
                    <span class="font-black text-[8px] uppercase tracking-tighter not-italic text-teal-500 mr-1">Note:</span>
                    "<?= htmlspecialchars($req['remarks']) ?>"
                </p>
            <?php endif; ?>
        </div>
    </td>

    <td class="px-6 py-5 w-32">
        <div class="flex items-center gap-3">
            <div class="h-8 w-8 bg-slate-50 rounded-lg flex flex-col items-center justify-center border border-slate-100 group-hover:border-teal-100 transition-colors">
                <span class="text-[8px] font-black text-slate-400 uppercase leading-none">
                    <?= date('M', strtotime($req['appointment_date'])) ?>
                </span>
                <span class="text-xs font-black text-slate-700 leading-none mt-0.5">
                    <?= date('d', strtotime($req['appointment_date'])) ?>
                </span>
            </div>
            <span class="text-xs font-bold text-slate-500 uppercase">
                <?= date('Y', strtotime($req['appointment_date'])) ?>
            </span>
        </div>
    </td>


<td class="px-6 py-5 text-center w-40">
    <?php
    $currentStatus = $req['status'];
    $isVerified    = $req['is_verified'] ?? null;

    $statusTransitions = [
        'Pending'   => ['Pending','Approved','Rejected'],
        'Approved'  => ['Approved','Completed'],
        'Rejected'  => ['Rejected'],
        'Completed' => ['Completed'],
        'Cancelled' => ['Cancelled']
    ];

    $allowedStatuses = $statusTransitions[$currentStatus] ?? [$currentStatus];

    // 🔒 Disable if not verified
    $isDisabled = $isVerified != 1;
    ?>

    <form method="POST">
        <input type="hidden" name="request_id" value="<?= $req['id'] ?>">

        <select name="status"
            onchange="this.form.submit()"
            <?= $isDisabled ? 'disabled' : '' ?>
            class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider border
            <?= match($currentStatus) {
                'Pending'   => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                'Approved'  => 'bg-blue-50 text-blue-600 border-blue-100',
                'Completed' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                'Rejected'  => 'bg-rose-50 text-rose-600 border-rose-100',
                'Cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                default     => 'bg-slate-50 text-slate-600 border-slate-200',
            } ?>
            <?= $isDisabled ? 'opacity-50 cursor-not-allowed bg-slate-100 text-slate-400 border-slate-200' : 'cursor-pointer outline-none' ?>">

            <?php foreach ($allowedStatuses as $statusOption): ?>
                <option value="<?= $statusOption ?>"
                    <?= $statusOption === $currentStatus ? 'selected' : '' ?>>
                    <?= $statusOption ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
</td>

    <td class="px-6 py-5 text-center w-32">
    <div class="flex items-center justify-center">
        <?php 
        $isVerified = $req['is_verified'] ?? null;
        $idPath = $req['id_image_path'] ?? '';

        if ($idPath && strpos($idPath, 'http') !== 0 && strpos($idPath, 'public/') !== 0) {
            $idPath = 'public/' . $idPath;
        }
        ?>

        <?php if ($isVerified === null): ?>
            <!-- 🔴 UNVERIFIED (Clickable) -->
            <button type="button"
                onclick="openIdModal('<?= htmlspecialchars($idPath) ?>', '<?= $req['id'] ?>')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl 
                bg-rose-50 text-rose-600 border border-rose-100 
                text-[10px] font-black uppercase tracking-wider shadow-sm 
                hover:opacity-80 hover:scale-105 transition-all">
                Unverified
            </button>

        <?php elseif ($isVerified == 1): ?>
            <!-- 🟢 VERIFIED (Static) -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl 
                bg-emerald-50 text-emerald-600 border border-emerald-100 
                text-[10px] font-black uppercase tracking-wider shadow-sm">
                Verified
            </span>

        <?php else: ?>
            <!-- 🟡 REJECTED (Static) -->
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl 
                bg-amber-50 text-amber-600 border border-amber-100 
                text-[10px] font-black uppercase tracking-wider shadow-sm">
                Rejected
            </span>
        <?php endif; ?>
    </div>
</td>

    <td class="px-6 py-5 text-center w-24">
        <button type="button"
                onclick='openDetailsModal(<?= $reqJson ?>)'
                class="h-9 w-9 flex items-center justify-center bg-slate-50 text-slate-500 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm border border-slate-200"
                title="View Details">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
        </button>
    </td>
</tr>