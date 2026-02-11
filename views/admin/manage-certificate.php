<?php

// fetch certificates from the database
try {
    $stmt = $pdo->query("SELECT * FROM certificates ORDER BY name ASC");
    $certificates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // error handling
    $certificates = [];

}
if (!$certificates) {
    $certificates = [];
}

// delete logic
if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $deleteStmt = $pdo->prepare("DELETE FROM certificates WHERE id = ?");
    $deleteStmt->execute([$id]);
    header("Location: ?page=manage-certificates");
    exit;
}

// certificate form submision handling
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

<script src="https://cdn.tailwindcss.com"></script>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
    body { font-family: 'Inter', sans-serif; background-color: #F9FAFB; }
    
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
</style>

<div class="p-8 max-w-7xl mx-auto min-h-screen">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10 gap-4">
        <div>
            <h2 class="text-5xl font-extrabold text-gray-900 tracking-tight mb-2">Manage Certificates 📜</h2>
            <p class="text-gray-500 text-lg">Configure the types of documents residents can request.</p>
        </div>
        <div class="bg-teal-50 border border-teal-100 px-6 py-3 rounded-2xl">
            <p class="text-teal-800 font-bold text-sm uppercase tracking-wider">Active Services</p>
            <p class="text-3xl font-black text-teal-600"><?= count($certificates) ?></p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-12 relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl select-none">🏗️</div>
        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            Create New Document Type
        </h3>
        <form method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-1">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Certificate Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="e.g., Barangay Clearance"
                        class="form-input"
                        required
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Public Description</label>
                    <input 
                        type="text" 
                        name="description" 
                        placeholder="Explain what this is for (e.g., For employment, ID, etc.)"
                        class="form-input"
                        required
                    >
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary">
                    <span>➕</span> Add to Official List
                </button>
            </div>
        </form>
    </div>

    <div class="flex items-center gap-4 mb-6">
        <hr class="flex-grow border-gray-200">
        <span class="text-gray-400 font-bold text-xs uppercase tracking-widest text-center">Current Offerings</span>
        <hr class="flex-grow border-gray-200">
    </div>

    <?php if (empty($certificates)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-dashed border-gray-300 p-20 text-center">
            <div class="text-7xl mb-6">🏜️</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">The catalog is empty</h3>
            <p class="text-gray-500">Your residents won't be able to request anything until you add a certificate type.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($certificates as $cert): ?>
                <div class="bg-white rounded-2xl border border-gray-100 p-8 card-hover flex flex-col justify-between group">
                    <div>
                        <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center text-2xl mb-6 group-hover:bg-teal-600 group-hover:text-white transition-colors duration-300">
                            📄
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-teal-700 transition-colors">
                            <?= htmlspecialchars($cert['name']) ?>
                        </h4>
                        <p class="text-gray-500 leading-relaxed mb-8">
                            <?= htmlspecialchars($cert['description']) ?>
                        </p>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Active Document</span>
                        <a 
                            href="?page=manage-certificates&delete=1&id=<?= $cert['id'] ?>"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                            onclick="return confirm('Residents will no longer be able to request this. Continue?');"
                            title="Delete Certificate"
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

    <div class="mt-16 flex justify-center">
        <a href="?page=admin-dashboard" class="flex items-center gap-2 text-gray-500 font-bold hover:text-teal-600 transition group">
            <span class="bg-white w-8 h-8 flex items-center justify-center rounded-full shadow-sm group-hover:shadow-md transition">←</span> 
            Back to Control Center
        </a>
    </div>
</div>