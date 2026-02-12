<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ?page=admin-dashboard");
    } else {
        header("Location: ?page=resident-dashboard");
    }
    exit;
}

$pageTitle = 'Home';
include __DIR__ . '/layout/header.php';
?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
    body { font-family: 'Inter', sans-serif; }
    .hero-gradient { background: radial-gradient(circle at 10% 20%, rgba(20, 184, 166, 0.05) 0%, rgba(255, 255, 255, 0) 100%); }
    .glass-card { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
</style>

<div class="hero-gradient min-h-screen">
    <div class="max-w-6xl mx-auto px-4 pt-16 pb-20">
        
        <div class="flex flex-col lg:flex-row items-center gap-12 mb-24">
            <div class="lg:w-1/2 text-left">
                <div class="inline-block px-4 py-1.5 mb-6 text-teal-700 bg-teal-50 border border-teal-100 rounded-full text-xs font-bold uppercase tracking-widest">
                    🚀 Government Services Made Modern
                </div>
                <h1 class="text-5xl md:text-6xl font-black text-slate-900 leading-[1.1] mb-6 tracking-tight">
                    Digital Certificates, <br>
                    <span class="text-teal-600">Zero Queues.</span>
                </h1>
                <p class="text-slate-600 text-lg md:text-xl mb-10 leading-relaxed max-w-xl">
                    The official digital gateway for your Barangay. Request, track, and receive your legal documents from the comfort of your home.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="?page=register" class="px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm uppercase tracking-wider transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
                        Get Started
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                    <a href="?page=login" class="px-8 py-4 bg-white border-2 border-slate-200 text-slate-600 hover:border-teal-600 hover:text-teal-600 rounded-xl font-bold text-sm uppercase tracking-wider transition-all">
                        Sign In
                    </a>
                </div>
                
                <div class="mt-12 flex gap-8 items-center border-t border-slate-100 pt-8">
                    <div>
                        <p class="text-2xl font-black text-slate-900">5k+</p>
                        <p class="text-xs font-bold text-slate-400 uppercase">Issued</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div>
                        <p class="text-2xl font-black text-slate-900">15min</p>
                        <p class="text-xs font-bold text-slate-400 uppercase">Avg. Request</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div>
                        <p class="text-2xl font-black text-slate-900">24/7</p>
                        <p class="text-xs font-bold text-slate-400 uppercase">Online Access</p>
                    </div>
                </div>
            </div>

            <div class="lg:w-1/2 relative">
                <div class="absolute -inset-4 bg-teal-500/10 rounded-full blur-3xl"></div>
                <div class="relative bg-white border-2 border-slate-200 p-6 rounded-[2rem] shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-teal-400"></div>
                        </div>
                        <div class="h-2 w-20 bg-slate-100 rounded-full"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-12 w-full bg-slate-50 rounded-xl border border-dashed border-slate-200"></div>
                        <div class="h-32 w-full bg-teal-50/50 rounded-xl border-2 border-teal-100 flex items-center justify-center">
                            <span class="text-teal-600 font-black text-xs uppercase tracking-widest italic"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-10 bg-slate-50 rounded-lg"></div>
                            <div class="h-10 bg-slate-900 rounded-lg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-24">
            <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.3em] mb-4">The Process</h3>
            <h2 class="text-4xl font-black text-slate-900 mb-16 tracking-tight">How it works in 3 steps</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative">
                <div class="hidden md:block absolute top-12 left-1/4 right-1/4 h-0.5 bg-slate-100"></div>
                
                <div class="relative z-10 group">
                    <div class="w-20 h-20 bg-white border-2 border-slate-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:border-teal-500 transition-colors shadow-sm">
                        <span class="text-2xl font-black text-slate-900">01</span>
                    </div>
                    <h4 class="font-bold text-lg text-slate-900 mb-2">Create Account</h4>
                    <p class="text-slate-500 text-sm">Sign up with your details to verify your residency.</p>
                </div>

                <div class="relative z-10 group">
                    <div class="w-20 h-20 bg-white border-2 border-slate-200 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:border-teal-500 transition-colors shadow-sm">
                        <span class="text-2xl font-black text-slate-900">02</span>
                    </div>
                    <h4 class="font-bold text-lg text-slate-900 mb-2">Submit Request</h4>
                    <p class="text-slate-500 text-sm">Choose your certificate and fill out the simple form.</p>
                </div>

                <div class="relative z-10 group">
                    <div class="w-20 h-20 bg-teal-600 border-2 border-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-teal-200">
                        <span class="text-2xl font-black text-white text-center">✅</span>
                    </div>
                    <h4 class="font-bold text-lg text-slate-900 mb-2">Pick Up</h4>
                    <p class="text-slate-500 text-sm">Receive a notification and collect your document.</p>
                </div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-12 lg:p-20 text-white overflow-hidden relative">
            <div class="absolute top-0 right-0 w-64 h-64 bg-teal-500/20 blur-[100px]"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-end mb-16 gap-6">
                <div class="text-left">
                    <h2 class="text-4xl font-black mb-4 tracking-tight">Available Documents</h2>
                    <p class="text-slate-400 max-w-md">All certificates are processed following official barangay standards and legal requirements.</p>
                </div>
                <a href="?page=register" class="text-teal-400 font-bold uppercase tracking-widest text-xs hover:text-white transition">Apply Now &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php
                $certs = [
                    ['icon' => '🏠', 'title' => 'Residency', 'desc' => 'Proof of your home address.'],
                    ['icon' => '🤝', 'title' => 'Indigency', 'desc' => 'For financial aid & assistance.'],
                    ['icon' => '🛡️', 'title' => 'Good Moral', 'desc' => 'For job & school applications.'],
                    ['icon' => '💼', 'title' => 'Business', 'desc' => 'Permits for local operations.'],
                    ['icon' => '🕊️', 'title' => 'No Objection', 'desc' => 'Clearance for activities.'],
                    ['icon' => '➕', 'title' => 'And More', 'desc' => 'Other legal barangay forms.']
                ];
                foreach ($certs as $c): ?>
                <div class="bg-white/5 border border-white/10 p-6 rounded-2xl hover:bg-white/10 transition group cursor-default">
                    <div class="text-3xl mb-4 group-hover:scale-125 transition-transform inline-block"><?= $c['icon'] ?></div>
                    <h4 class="font-extrabold text-white mb-1 tracking-tight"><?= $c['title'] ?></h4>
                    <p class="text-slate-500 text-sm font-medium"><?= $c['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>