<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Requests | BrgyPortal</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; }

.status-badge {
    @apply inline-flex items-center gap-1.5 px-3 py-1 rounded-full font-bold text-xs uppercase tracking-wide border;
}
.status-pending { @apply bg-amber-50 text-amber-700 border-amber-200; }
.status-approved { @apply bg-teal-50 text-teal-700 border-teal-200; }
.status-completed { @apply bg-cyan-50 text-cyan-700 border-cyan-200; }
.status-rejected { @apply bg-red-50 text-red-700 border-red-200; }
.status-cancelled { @apply bg-gray-100 text-gray-600 border-gray-300; }

.request-card {
    @apply bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-200 hover:border-teal-300 overflow-hidden;
}
</style>
</head>
<body class="min-h-screen pb-12">

<div class="max-w-4xl mx-auto py-8 px-4">

<?php if (!empty($_SESSION['success'])): ?>
<div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
</div>
<?php endif; ?>

<?php if (!empty($_SESSION['error'])): ?>
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
</div>
<?php endif; ?>

<div class="mb-10">
    <h2 class="text-4xl font-black text-slate-900">My Requests</h2>
    <p class="text-slate-500 text-sm mt-1">Showing <?= count($requests ?? []) ?> record(s)</p>
</div>

<?php if (empty($requests)): ?>
<div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 p-16 text-center">
    <h3 class="text-xl font-bold text-slate-800">No requests found</h3>
</div>
<?php else: ?>

<div class="space-y-4">

<?php foreach ($requests as $req): 
    $status = $req['status'] ?? 'Pending';
    $statusLower = strtolower($status);

    $accentClass = 'border-l-slate-300';
    $statusEmoji = '⏳';

    switch($status) {
        case 'Approved':  $accentClass = 'border-l-teal-500'; $statusEmoji = '✅'; break;
        case 'Completed': $accentClass = 'border-l-cyan-500'; $statusEmoji = '💎'; break;
        case 'Rejected':  $accentClass = 'border-l-red-500';  $statusEmoji = '❌'; break;
        case 'Cancelled': $accentClass = 'border-l-gray-400'; $statusEmoji = '🚫'; break;
        case 'Pending':   $accentClass = 'border-l-amber-500'; $statusEmoji = '⏳'; break;
    }
?>

<div class="request-card border-l-[6px] <?= $accentClass ?>">
<div class="p-6 flex justify-between items-center">

<div>
<h3 class="text-lg font-bold"><?= htmlspecialchars($req['certificate_name']) ?></h3>
<p class="text-sm text-gray-500">
Appointment: <?= date('M d, Y', strtotime($req['appointment_date'])) ?>
</p>
</div>

<div class="flex items-center gap-4">

<span class="status-badge status-<?= $statusLower ?>">
<?= $statusEmoji ?> <?= $status ?>
</span>

<?php if ($status === 'Pending'): ?>
<form method="POST" action="?page=cancel-request"
onsubmit="return confirm('Are you sure you want to cancel this request?');">
<input type="hidden" name="request_id" value="<?= $req['id'] ?>">
<button type="submit"
class="px-4 py-2 bg-red-100 text-red-600 border border-red-300 rounded-lg text-xs font-bold uppercase hover:bg-red-200">
Cancel
</button>
</form>
<?php endif; ?>

</div>
</div>

<?php if (!empty($req['remarks'])): ?>
<div class="px-6 pb-4 text-sm text-gray-600">
<strong>Admin Remarks:</strong>
<?= htmlspecialchars($req['remarks']) ?>
</div>
<?php endif; ?>

</div>

<?php endforeach; ?>

</div>
<?php endif; ?>

</div>
</body>
</html>