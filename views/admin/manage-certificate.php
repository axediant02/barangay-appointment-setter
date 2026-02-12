<?php
require_once '../config/database.php';

$certificates = []; 
try {
    $stmt = $pdo->query("SELECT * FROM certificates ORDER BY name ASC");
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $certificates = $fetched ? $fetched : [];
} catch (PDOException $e) {
    $certificates = []; 
}

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $deleteStmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
    $deleteStmt->execute([$id]);
    header("Location: ?page=manage-certificates");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    
    if (!empty($name)) {
        $insertStmt = $pdo->prepare("INSERT INTO certificates (name, description) VALUES (?, ?)");
        $insertStmt->execute([$name, $description]);
        header("Location: ?page=manage-certificates");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Certificates - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; }
        
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #0d9488; border-radius: 10px; }

        .form-input {
            width: 100%;
            background-color: white;
            border: 2px solid #E5E7EB;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            outline: none;
            transition: all 0.2s ease-in-out;
        }
        .form-input:focus {
            border-color: #0D9488;
            box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.1);
        }
        .btn-primary {
            @apply px-8 py-3 bg-teal-600 hover:bg-teal-700 text-white rounded-xl font-bold text-sm transition transform active:scale-95 shadow-lg shadow-teal-900/10 flex items-center gap-2;
        }
        .card-hover {
            @apply transition-all duration-300 hover:shadow-xl hover:-translate-y-1;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 40;
            background: rgba(249, 250, 251, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>
<body>

<div class="sticky-header border-b border-gray-200 shadow-sm mb-6">
    <div class="max-w-7xl mx-auto px-8 py-4 flex items-center justify-between">
        <button onclick="history.back()" class="flex items-center gap-2 text-teal-600 font-bold hover:text-teal-800 transition group">
            <span class="bg-white w-10 h-10 flex items-center justify-center rounded-xl shadow-sm border border-gray-100 group-hover:bg-teal-50 group-hover:scale-110 transition">←</span> 
            <span class="hidden sm:inline font-black text-xs uppercase tracking-widest">Return to Dashboard</span>
        </button>

        <div class="flex items-center gap-3">
             <div class="bg-teal-50 border border-teal-100 px-4 py-2 rounded-xl flex items-center gap-3">
                <span class="text-teal-800 font-black text-[10px] uppercase tracking-wider">Active Catalog:</span>
                <span class="text-teal-600 font-black text-lg"><?= count($certificates) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="p-8 max-w-7xl mx-auto min-h-screen">
    
    <div class="mb-10">
        <h2 class="text-5xl font-black text-gray-900 tracking-tighter mb-2 italic">CERTIFICATE REGISTRY</h2>
        <p class="text-gray-500 text-lg">Define and organize the documents available for resident requests.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-200 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-5 text-7xl select-none rotate-12">📝</div>
        <h3 class="text-xl font-black text-gray-900 mb-6 uppercase tracking-tight flex items-center gap-2">
            <span class="w-2 h-6 bg-teal-500 rounded-full"></span>
            Add New Document Type
        </h3>
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block text-[10px] font-black text-slate-400 mb-2 uppercase tracking-widest ml-1">Official Name</label>
                    <input type="text" name="name" placeholder="e.g. Indigency Certificate" class="form-input" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[10px] font-black text-slate-400 mb-2 uppercase tracking-widest ml-1">Purpose/Description</label>
                    <input type="text" name="description" placeholder="What is this document used for?" class="form-input" required>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn-primary">
                    <span>➕</span> Register Document
                </button>
            </div>
        </form>
    </div>

    <div class="flex flex-col sm:flex-row items-center gap-4 mb-10">
        <div class="relative w-full sm:w-64">
            <input type="text" id="certSearch" placeholder="Search catalog..." class="form-input text-sm py-2" onkeyup="filterCerts()">
        </div>
        <hr class="flex-grow border-gray-200">
        <span class="text-gray-400 font-black text-[10px] uppercase tracking-[0.3em]">Document Portfolio</span>
        <hr class="flex-grow border-gray-200">
    </div>

    <?php if (empty($certificates)): ?>
        <div class="bg-white rounded-3xl border-4 border-dashed border-gray-100 p-20 text-center">
            <div class="text-7xl mb-6">🏜️</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2 uppercase tracking-tight">Empty Registry</h3>
            <p class="text-gray-400">Add your first certificate type above to start operations.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="certGrid">
            <?php foreach ($certificates as $cert): ?>
                <div class="cert-card bg-white rounded-3xl border border-gray-100 p-8 card-hover flex flex-col justify-between group">
                    <div>
                        <div class="w-14 h-14 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center text-3xl mb-6 group-hover:bg-teal-600 group-hover:text-white transition-all duration-300 transform group-hover:rotate-3 shadow-sm group-hover:shadow-teal-200">
                            📄
                        </div>
                        <h4 class="text-2xl font-black text-gray-900 mb-3 tracking-tight group-hover:text-teal-600 transition-colors">
                            <?= htmlspecialchars($cert['name']) ?>
                        </h4>
                        <p class="text-gray-500 text-sm leading-relaxed mb-8">
                            <?= htmlspecialchars($cert['description']) ?>
                        </p>
                    </div>

                    <div class="pt-6 border-t border-gray-50 flex items-center justify-between">
                        <span class="bg-teal-50 text-teal-700 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-widest">Active</span>
                        <a 
                            href="?page=manage-certificates&delete=1&id=<?= $cert['id'] ?>"
                            class="p-3 text-gray-300 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all duration-300"
                            onclick="return confirm('WARNING: Deleting this document type will affect existing resident options. Continue?');"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="mt-20 py-10 border-t border-gray-100 flex flex-col items-center gap-4">
        <p class="text-slate-400 text-[10px] font-black uppercase tracking-[0.4em]">Proprietary Management System</p>
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="text-teal-600 text-xs font-bold hover:underline">Scroll to Top ↑</button>
    </div>
</div>

<script>
    function filterCerts() {
        const input = document.getElementById('certSearch');
        const filter = input.value.toLowerCase();
        const cards = document.getElementsByClassName('cert-card');

        for (let i = 0; i < cards.length; i++) {
            const title = cards[i].getElementsByTagName('h4')[0].innerText;
            cards[i].style.display = title.toLowerCase().includes(filter) ? "" : "none";
        }
    }
</script>

</body>
</html>