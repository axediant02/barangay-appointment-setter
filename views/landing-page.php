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

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .hero-gradient { background: radial-gradient(circle at 10% 20%, rgba(20, 184, 166, 0.05) 0%, rgba(255, 255, 255, 0) 100%); }
    
    .process-swiper { padding: 40px 0 60px 0 !important; }
    .swiper-pagination-bullet-active { background: #0d9488 !important; }
    .step-card { 
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); 
        opacity: 0.5;
        transform: scale(0.9);
    }
    .swiper-slide-active .step-card { 
        opacity: 1; 
        transform: scale(1);
        border-color: #0d9488;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.05);
    }
</style>

<div class="hero-gradient min-h-screen">
    <div class="max-w-6xl mx-auto px-4 pt-16 pb-20">
        
        <div class="flex flex-col lg:flex-row items-center gap-12 mb-32">
            <div class="lg:w-1/2 text-left">
                <div class="inline-block px-4 py-1.5 mb-6 text-teal-700 bg-teal-50 border border-teal-100 rounded-full text-[10px] font-black uppercase tracking-widest">
                    🚀 Government Services Made Modern
                </div>
                <h1 class="text-5xl md:text-7xl font-black text-slate-900 leading-[1.05] mb-6 tracking-tighter italic">
                    Digital Docs, <br>
                    <span class="text-teal-600">Zero Queues.</span>
                </h1>
                <p class="text-slate-600 text-lg md:text-xl mb-10 leading-relaxed max-w-xl">
                    The official digital gateway for your Barangay. Request, track, and receive your legal documents from the comfort of your home.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="?page=register" class="px-8 py-4 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl font-bold text-sm uppercase tracking-wider transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
                        Get Started
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                    </a>
                    <a href="?page=login" class="px-8 py-4 bg-white border-2 border-slate-200 text-slate-600 hover:border-teal-600 hover:text-teal-600 rounded-2xl font-bold text-sm uppercase tracking-wider transition-all">
                        Sign In
                    </a>
                </div>
                
                <div class="mt-12 flex gap-8 items-center border-t border-slate-100 pt-8">
                    <div>
                        <p class="text-2xl font-black text-slate-900">5k+</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Issued</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div>
                        <p class="text-2xl font-black text-slate-900">15min</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Avg. Request</p>
                    </div>
                    <div class="w-px h-8 bg-slate-200"></div>
                    <div>
                        <p class="text-2xl font-black text-slate-900">24/7</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Online Access</p>
                    </div>
                </div>
            </div>

            <div class="lg:w-1/2 relative">
                <div class="absolute -inset-4 bg-teal-500/10 rounded-full blur-3xl"></div>
                <div class="relative bg-white border-2 border-slate-200 p-8 rounded-[3rem] shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex gap-2">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-teal-400"></div>
                        </div>
                        <div class="h-2 w-20 bg-slate-100 rounded-full"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-12 w-full bg-slate-50 rounded-2xl border border-dashed border-slate-200 flex items-center px-4">
                            <div class="w-1/2 h-2 bg-slate-200 rounded-full"></div>
                        </div>
                        <div class="h-40 w-full bg-teal-50/50 rounded-2xl border-2 border-teal-100 flex flex-col items-center justify-center gap-3">
                             <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-xl">📄</div>
                             <div class="w-24 h-2 bg-teal-200 rounded-full"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="h-12 bg-slate-50 rounded-xl"></div>
                            <div class="h-12 bg-slate-900 rounded-xl"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mb-32">
            <h3 class="text-xs font-black text-teal-600 uppercase tracking-[0.4em] mb-4">The Process</h3>
            <h2 class="text-4xl font-black text-slate-900 mb-8 tracking-tight">How it works in 4 easy steps</h2>
            
            

            <div class="swiper process-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="step-card bg-white border-2 border-slate-100 rounded-[2.5rem] p-10 flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-900 text-white rounded-2xl flex items-center justify-center text-xl font-black mb-6">01</div>
                            <h4 class="font-black text-xl text-slate-900 mb-3 italic">Register</h4>
                            <p class="text-slate-500 text-sm leading-relaxed">Create your secure account to verify your residency within the barangay system.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="step-card bg-white border-2 border-slate-100 rounded-[2.5rem] p-10 flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-100 text-slate-900 rounded-2xl flex items-center justify-center text-xl font-black mb-6">02</div>
                            <h4 class="font-black text-xl text-slate-900 mb-3 italic">Request</h4>
                            <p class="text-slate-500 text-sm leading-relaxed">Browse the catalog and fill out the digital form for the document you need.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="step-card bg-white border-2 border-slate-100 rounded-[2.5rem] p-10 flex flex-col items-center">
                            <div class="w-16 h-16 bg-slate-100 text-slate-900 rounded-2xl flex items-center justify-center text-xl font-black mb-6">03</div>
                            <h4 class="font-black text-xl text-slate-900 mb-3 italic">Tracking</h4>
                            <p class="text-slate-500 text-sm leading-relaxed">Monitor the real-time status of your application from your personal dashboard.</p>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="step-card bg-white border-2 border-slate-100 rounded-[2.5rem] p-10 flex flex-col items-center">
                            <div class="w-16 h-16 bg-teal-600 text-white rounded-2xl flex items-center justify-center text-xl font-black mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h4 class="font-black text-xl text-slate-900 mb-3 italic">Collection</h4>
                            <p class="text-slate-500 text-sm leading-relaxed">Get notified when it's ready. Visit the hall for a quick, hassle-free pickup.</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[4rem] p-12 lg:p-24 text-white overflow-hidden relative">
            <div class="absolute top-0 right-0 w-96 h-96 bg-teal-500/10 blur-[120px]"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-end mb-16 gap-6">
                <div class="text-left">
                    <h2 class="text-4xl font-black mb-4 tracking-tight italic">Available Documents</h2>
                    <p class="text-slate-400 max-w-md">All certificates are processed following official standards.</p>
                </div>
                <a href="?page=register" class="px-6 py-3 bg-white/10 hover:bg-white/20 rounded-xl text-white font-bold uppercase tracking-widest text-[10px] transition">Apply Now &rarr;</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
                <div class="bg-white/5 border border-white/10 p-8 rounded-3xl hover:bg-white/10 transition-all group cursor-default">
                    <div class="text-4xl mb-6 group-hover:scale-110 transition-transform inline-block"><?= $c['icon'] ?></div>
                    <h4 class="font-black text-xl text-white mb-2 tracking-tight italic"><?= $c['title'] ?></h4>
                    <p class="text-slate-500 text-sm font-medium leading-relaxed"><?= $c['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const swiper = new Swiper('.process-swiper', {
        slidesPerView: 1,
        spaceBetween: 0,
        centeredSlides: true,
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1024: {
                slidesPerView: 3,
            },
        },
    });
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>