<?php
if (!isset($stats['pending']) && isset($pdo)) {
    try {
        $stats['pending'] = $pdo->query("SELECT COUNT(*) FROM requests WHERE status = 'Pending'")->fetchColumn() ?: 0;
    } catch (PDOException $e) {
        $stats['pending'] = 0;
    }
}
?>
<aside class="w-72 bg-white border-r border-slate-100 hidden lg:flex flex-col h-screen sticky top-0 py-8 px-6 shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
    <div class="flex items-center gap-3 px-3 mb-12 group cursor-pointer">
        <div class="h-11 w-11 bg-teal-600 rounded-2xl flex items-center justify-center text-white text-xl shadow-lg shadow-teal-100 rotate-3 group-hover:rotate-0 transition-transform">
            🏛️
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-slate-800 tracking-tight leading-none">Brgy<span class="text-teal-600">Portal</span></h1>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Management Hub</p>
        </div>
    </div>

    <div class="flex flex-col gap-1.5 flex-1">
        <p class="px-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Main Menu</p>
        
        <a href="?page=admin-dashboard" class="sidebar-item group <?= $currentPage == 'admin-dashboard' ? 'active' : '' ?>">
            <div class="icon-box">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
            </div>
            <span>Dashboard</span>
            <?php if($currentPage == 'admin-dashboard'): ?>
                <div class="ml-auto w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_8px_#fff]"></div>
            <?php endif; ?>
        </a>

        <a href="?page=manage-requests" class="sidebar-item group <?= $currentPage == 'manage-requests' ? 'active' : '' ?>">
            <div class="icon-box">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
            </div>
            <span>Requests</span>
            <?php if (isset($stats['pending'])): ?>
            <span class="ml-auto bg-amber-100 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-lg group-[.active]:bg-white/20 group-[.active]:text-white">
                <?= $stats['pending'] ?>
            </span>
            <?php endif; ?>
        </a>

        <a href="?page=manage-certificates" class="sidebar-item group <?= $currentPage == 'manage-certificates' ? 'active' : '' ?>">
            <div class="icon-box">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-1.17-10.957a9 9 0 0113.844-4.241M7.5 13H15m-2.25 3H15m-3 3H15m0-12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            </div>
            <span>Certificates</span>
        </a>
    </div>

    <div class="mt-auto border-t border-slate-100 pt-6 px-2">
        <a href="?page=logout" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-400 font-bold hover:text-red-500 hover:bg-red-50 transition-all duration-300 group">
            <div class="p-2 rounded-lg group-hover:bg-red-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </div>
            <span class="text-sm">Log Out System</span>
        </a>
    </div>
</aside>
