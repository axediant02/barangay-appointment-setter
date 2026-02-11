<!DOCTYPE html>
<html>
<head>
    <title>My Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .status-badge {
            @apply inline-block px-4 py-2 rounded-full font-semibold text-sm;
        }
        .status-pending {
            @apply bg-amber-100 text-amber-800 border border-amber-300;
        }
        .status-approved {
            @apply bg-teal-100 text-teal-800 border border-teal-300;
        }
        .status-completed {
            @apply bg-cyan-100 text-cyan-800 border border-cyan-300;
        }
        .status-rejected {
            @apply bg-red-100 text-red-800 border border-red-300;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen p-4">

<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-4xl font-bold text-gray-900 mb-2">My Requests</h2>
        <p class="text-gray-600 text-lg">Track all your certificate requests and their current status</p>
    </div>

    <?php if (empty($requests)): ?>
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow border border-gray-100 p-12 text-center">
            <div class="text-6xl mb-4">📋</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">No Requests Yet</h3>
            <p class="text-gray-600 mb-6">You haven't submitted any certificate requests yet.</p>
            <a href="?page=create-request" class="inline-block px-6 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition">
                Create Your First Request →
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 gap-4" id="requests-container">
            <?php foreach ($requests as $req): ?>
                <div class="bg-white rounded-xl shadow border border-gray-100 hover:shadow-lg transition" data-request-id="<?= $req['id'] ?>">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-grow">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    📄 <span class="cert-name"><?= htmlspecialchars($req['certificate_name']) ?></span>
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-600 font-medium">Appointment Date</p>
                                        <p class="text-gray-900 font-semibold">
                                            📅 <span class="appt-date"><?= date('F d, Y', strtotime($req['appointment_date'])) ?></span>
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-600 font-medium">Status</p>
                                        <div class="mt-1">
                                            <span class="status-badge status-<?= strtolower($req['status']) ?>" data-status="<?= $req['status'] ?>">
                                                <span class="status-emoji">
                                                    <?php 
                                                    $statusEmoji = '❓';
                                                    switch($req['status']) {
                                                        case 'Pending': $statusEmoji = '⏳'; break;
                                                        case 'Approved': $statusEmoji = '✅'; break;
                                                        case 'Completed': $statusEmoji = '🎉'; break;
                                                        case 'Rejected': $statusEmoji = '❌'; break;
                                                    }
                                                    echo $statusEmoji;
                                                    ?>
                                                </span>
                                                <span class="status-text"><?= htmlspecialchars($req['status']) ?></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="remarks-container">
                                    <?php if (!empty($req['remarks'])): ?>
                                        <div class="mt-4 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                            <p class="text-sm text-blue-800">
                                                <strong>📝 Remarks:</strong> <span class="remarks-text"><?= htmlspecialchars($req['remarks']) ?></span>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="flex flex-col gap-2 action-buttons">
                                <?php if ($req['status'] === 'Completed'): ?>
                                    <button class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">
                                        ✅ Ready
                                    </button>
                                <?php elseif ($req['status'] === 'Approved'): ?>
                                    <button class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition">
                                        📅 Upcoming
                                    </button>
                                <?php else: ?>
                                    <button class="px-6 py-2 bg-gray-400 text-white rounded-lg font-semibold" disabled>
                                        <span class="btn-status"><?= htmlspecialchars($req['status']) ?></span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-600 flex items-center gap-2">
                        <span>📌 Request ID: #<?= $req['id'] ?></span>
                        <span>•</span>
                        <span>🕐 Submitted: <?= date('M d, Y', strtotime($req['created_at'] ?? 'now')) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-8">
            <a href="?page=resident-dashboard" class="text-teal-600 font-semibold hover:text-teal-700">
                ← Back to Dashboard
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Real-time Update Script -->
<script>
    function updateRequests() {
        fetch('api.php?action=my-requests')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('requests-container');
                if (!container) return;

                // Update existing requests
                data.forEach(req => {
                    const card = document.querySelector(`[data-request-id="${req.id}"]`);
                    if (card) {
                        // Update status badge
                        const statusEmojis = {
                            'Pending': '⏳',
                            'Approved': '✅',
                            'Completed': '🎉',
                            'Rejected': '❌'
                        };
                        
                        const statusBadge = card.querySelector('.status-badge');
                        if (statusBadge) {
                            statusBadge.className = 'status-badge status-' + req.status.toLowerCase();
                            statusBadge.setAttribute('data-status', req.status);
                            statusBadge.querySelector('.status-emoji').textContent = statusEmojis[req.status] || '❓';
                            statusBadge.querySelector('.status-text').textContent = req.status;
                        }

                        // Update action button based on status
                        const actionButtons = card.querySelector('.action-buttons');
                        if (actionButtons) {
                            let buttonHtml = '';
                            if (req.status === 'Completed') {
                                buttonHtml = '<button class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition">✅ Ready</button>';
                            } else if (req.status === 'Approved') {
                                buttonHtml = '<button class="px-6 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold transition">📅 Upcoming</button>';
                            } else {
                                buttonHtml = `<button class="px-6 py-2 bg-gray-400 text-white rounded-lg font-semibold" disabled><span class="btn-status">${req.status}</span></button>`;
                            }
                            actionButtons.innerHTML = buttonHtml;
                        }

                        // Update remarks
                        const remarksContainer = card.querySelector('.remarks-container');
                        if (req.remarks) {
                            if (!remarksContainer.querySelector('.remarks-text')) {
                                remarksContainer.innerHTML = `
                                    <div class="mt-4 p-4 bg-blue-50 border-2 border-blue-200 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <strong>📝 Remarks:</strong> <span class="remarks-text">${req.remarks}</span>
                                        </p>
                                    </div>
                                `;
                            } else {
                                remarksContainer.querySelector('.remarks-text').textContent = req.remarks;
                            }
                        }
                    }
                });
            })
            .catch(error => console.log('Update error:', error));
    }
    setInterval(updateRequests, 5000);
</script>