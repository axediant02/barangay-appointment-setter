<?php
require_once '../config/database.php';

// Success/Error Message Handling
$message = '';
$status = '';

// Delete Logic with better safety
if (isset($_GET['delete']) && isset($_GET['id'])) {
    try {
        $id = (int)$_GET['id'];
        $deleteStmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
        $deleteStmt->execute([$id]);
        header("Location: ?page=manage-certificates&status=deleted");
        exit;
    } catch (Exception $e) { $message = "Error deleting record."; $status = "error"; }
}

// Data Fetching
try {
    $stmt = $pdo->query("SELECT *, DATE_FORMAT(created_at, '%b %d, %Y') as formatted_date FROM certificates ORDER BY name ASC");
    $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) { $certificates = []; }

// Insert Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    if (!empty($name)) {
        $insertStmt = $pdo->prepare("INSERT INTO certificates (name, description, created_at) VALUES (?, ?, NOW())");
        $insertStmt->execute([$name, $description]);
        header("Location: ?page=manage-certificates&status=added");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Certificates | BrgyPortal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        @layer base {
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
        }
        @layer components {
            .sidebar-item {
                @apply flex items-center gap-4 px-4 py-3.5 rounded-2xl transition-all duration-300 font-bold text-slate-500 hover:bg-slate-50 hover:text-slate-700 hover:translate-x-1;
            }
            .sidebar-item.active {
                @apply bg-teal-600 text-white shadow-xl shadow-teal-200/50 hover:translate-x-0 hover:bg-teal-600;
            }
            .icon-box {
                @apply transition-transform group-hover:scale-110 flex-shrink-0;
            }
            .btn-primary {
                @apply px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-sm transition transform active:scale-95 shadow-lg shadow-teal-900/10 flex items-center gap-2;
            }
            .form-input {
                @apply w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 outline-none transition-all focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 font-bold text-sm text-slate-800;
            }
        }
    </style>
</head>
<body class="h-full overflow-hidden">

<div class="flex h-full">
    <?php 
    $currentPage = 'manage-certificates';
    include 'partials/sidebar.php'; 
    ?>

    <main class="flex-1 h-full overflow-y-auto bg-slate-50/50">
        <header class="bg-white border-b border-slate-200 px-10 py-4 flex justify-between items-center sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <h2 class="text-xl font-bold text-slate-800">Certificate Registry</h2>
                <div class="bg-teal-50 border border-teal-100 px-3 py-1 rounded-full flex items-center gap-2" id="portfolioBadge">
                    <span class="text-teal-800 font-black text-[10px] uppercase tracking-wider">Active Portfolio:</span>
                    <span class="text-teal-600 font-black text-sm" id="totalCountTop"><?= count($certificates) ?></span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right mr-2">
                    <p class="text-sm font-bold text-slate-800"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></p>
                    <p class="text-[10px] text-slate-400 font-medium italic">Document Manager</p>
                </div>
                <div class="h-10 w-10 bg-teal-600 rounded-full border-2 border-white shadow-sm overflow-hidden flex items-center justify-center font-bold text-white uppercase text-sm">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'AD', 0, 2)) ?>
                </div>
            </div>
        </header>

        <div class="p-10 max-w-[1600px] mx-auto min-h-screen">
            <?php if(isset($_GET['status'])): ?>
                <div id="toast" class="mb-8 flex items-center p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl animate-bounce">
                    <span class="mr-3">✨</span> Registry updated successfully!
                </div>
            <?php endif; ?>

            <div class="mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Certificate Registry</h1>
                <p class="text-slate-500 font-medium mt-1">Define and organize the documents available for resident requests.</p>
            </div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
    <div class="lg:col-span-4">
        <div class="sticky top-28 bg-white border border-slate-200 rounded-[2.5rem] p-8 shadow-sm">
            <div class="mb-8">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Add New Type</h2>
                <p class="text-slate-500 text-sm mt-1">Populate the document catalog for residents.</p>
            </div>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Document Title</label>
                    <input type="text" name="name" placeholder="e.g., Barangay Clearance" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 font-medium" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1">Official Description</label>
                    <textarea name="description" rows="4" placeholder="Briefly describe the purpose..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 font-medium resize-none" required></textarea>
                </div>
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-3 rounded-2xl font-bold transition-all active:scale-95 flex items-center gap-2 shadow-lg shadow-teal-600/20 w-full justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add to Catalog
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-8">
        <div class="flex items-center justify-between mb-8">
            <div class="relative w-full max-w-sm">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" id="certSearch" onkeyup="filterCerts()" placeholder="Search registry..." class="w-full bg-white border border-slate-200 rounded-2xl pl-12 pr-5 py-3 text-sm font-medium outline-none focus:border-teal-500 transition-all">
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Entries</span>
                <span class="bg-teal-100 text-teal-700 px-3 py-1 rounded-lg font-bold text-sm" id="totalCount"><?= count($certificates) ?></span>
            </div>
        </div>

        <?php if (empty($certificates)): ?>
            <div class="text-center py-20 bg-slate-50 rounded-[3rem] border-2 border-dashed border-slate-200">
                <div class="text-6xl mb-4">📂</div>
                <h3 class="text-xl font-bold text-slate-400 uppercase tracking-widest">No Documents Found</h3>
                <p class="text-slate-400 text-sm mt-2">Start by adding a document type in the left panel.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="certGrid">
                <?php foreach ($certificates as $cert): ?>
                    <div class="bg-white border border-slate-200/60 rounded-[2rem] p-8 transition-all duration-500 hover:shadow-[0_20px_50px_rgba(0,0,0,0.04)] hover:-translate-y-2 group relative cert-card">
                        <div class="flex justify-between items-start mb-6">
                            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center group-hover:bg-teal-600 group-hover:text-white transition-all duration-300">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <span class="text-[9px] font-black text-slate-300 uppercase tracking-tighter">ID: #<?= $cert['id'] ?></span>
                        </div>

                        <h4 class="text-xl font-bold text-slate-800 mb-2 leading-tight cert-name"><?= htmlspecialchars($cert['name']) ?></h4>
                        <p class="text-slate-500 text-sm leading-relaxed mb-8 line-clamp-3"><?= htmlspecialchars($cert['description']) ?></p>

                        <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Established</span>
                                <span class="text-xs font-bold text-slate-600"><?= $cert['formatted_date'] ?></span>
                            </div>
                            
                            <div class="flex items-center gap-1">
                                <button class="p-2 text-slate-300 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-all" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <a href="?page=manage-certificates&delete=1&id=<?= $cert['id'] ?>" 
                                   onclick="return confirm('Archive this document type? This cannot be undone.')"
                                   class="p-2 text-slate-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

</main>
</div>

<script>
    function filterCerts() {
        const input = document.getElementById('certSearch');
        const filter = input.value.toLowerCase();
        const cards = document.getElementsByClassName('cert-card');
        let visibleCount = 0;

        Array.from(cards).forEach(card => {
            const title = card.querySelector('.cert-name').innerText.toLowerCase();
            if (title.includes(filter)) {
                card.style.display = "";
                visibleCount++;
            } else {
                card.style.display = "none";
            }
        });
        
        const countEl = document.getElementById('totalCount');
        if (countEl) countEl.innerText = visibleCount;
    }

    // Auto-hide toast
    setTimeout(() => {
        const toast = document.getElementById('toast');
        if(toast) toast.style.display = 'none';
    }, 4000);
</script>

</body>
</html>