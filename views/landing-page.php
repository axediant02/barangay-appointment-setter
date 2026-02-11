<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    // Redirect logged-in users to their dashboard
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

<!-- Hero Section -->
<div class="flex flex-col items-center text-center mb-20">
    <!-- Main Heading -->
    <div class="mb-12">
        <h1 class="text-6xl md:text-7xl font-bold mb-6 text-gray-900 leading-tight">
            Digital Certificates,<br>
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-600 to-cyan-600">
                Made Simple
            </span>
        </h1>
        <p class="text-gray-600 text-xl md:text-2xl mb-8 max-w-3xl leading-relaxed mx-auto">
            Request barangay certificates online, schedule appointments, and track your status in real-time. No more long queues, just convenience.
        </p>
        
        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="?page=register" class="px-8 py-4 bg-teal-600 hover:bg-teal-700 text-white rounded-lg font-semibold text-lg transition shadow-lg hover:shadow-xl">
                Get Started →
            </a>
            <a href="?page=login" class="px-8 py-4 border-2 border-teal-600 text-teal-600 hover:bg-teal-50 rounded-lg font-semibold text-lg transition">
                Sign In
            </a>
        </div>
    </div>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-5xl mb-16">
        <!-- Feature 1 -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition border border-gray-100 p-8">
            <div class="text-5xl mb-4 text-center">⚡</div>
            <h3 class="font-bold text-xl mb-3 text-gray-900">Fast Processing</h3>
            <p class="text-gray-600 leading-relaxed">Submit requests online and get faster processing times. Skip the queues and enjoy instant confirmation.</p>
        </div>
        
        <!-- Feature 2 -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition border border-gray-100 p-8">
            <div class="text-5xl mb-4 text-center">📅</div>
            <h3 class="font-bold text-xl mb-3 text-gray-900">Easy Scheduling</h3>
            <p class="text-gray-600 leading-relaxed">Choose your appointment date and time at your convenience. Full control over your schedule.</p>
        </div>
        
        <!-- Feature 3 -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-2xl transition border border-gray-100 p-8">
            <div class="text-5xl mb-4 text-center">📱</div>
            <h3 class="font-bold text-xl mb-3 text-gray-900">Track Anytime</h3>
            <p class="text-gray-600 leading-relaxed">Monitor your request status from anywhere. Real-time updates on your certificate requests.</p>
        </div>
    </div>

    <!-- Available Certificates Section -->
    <div class="w-full max-w-4xl">
        <h2 class="text-4xl font-bold mb-12 text-gray-900">Available Certificates</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Certificate 1 -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-8 border-2 border-teal-200 hover:shadow-lg transition">
                <div class="text-3xl mb-3">📄</div>
                <h4 class="font-bold text-lg text-gray-900 mb-2">Residency Certificate</h4>
                <p class="text-gray-600">Proof of your address and residency in the barangay.</p>
            </div>
            
            <!-- Certificate 2 -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-8 border-2 border-teal-200 hover:shadow-lg transition">
                <div class="text-3xl mb-3">💰</div>
                <h4 class="font-bold text-lg text-gray-900 mb-2">Indigency Certificate</h4>
                <p class="text-gray-600">For financial aid, scholarships, and assistance programs.</p>
            </div>
            
            <!-- Certificate 3 -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-8 border-2 border-teal-200 hover:shadow-lg transition">
                <div class="text-3xl mb-3">⭐</div>
                <h4 class="font-bold text-lg text-gray-900 mb-2">Good Moral Character</h4>
                <p class="text-gray-600">Required for jobs, school applications, and legal proceedings.</p>
            </div>
            
            <!-- Certificate 4 -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-8 border-2 border-teal-200 hover:shadow-lg transition">
                <div class="text-3xl mb-3">🏢</div>
                <h4 class="font-bold text-lg text-gray-900 mb-2">Business Certificate</h4>
                <p class="text-gray-600">Permits and licenses for business operations.</p>
            </div>
            
            <!-- Certificate 5 -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-8 border-2 border-teal-200 hover:shadow-lg transition">
                <div class="text-3xl mb-3">✋</div>
                <h4 class="font-bold text-lg text-gray-900 mb-2">No Objection Letter</h4>
                <p class="text-gray-600">Consent for activities and community events.</p>
            </div>

            <!-- Certificate 6 -->
            <div class="bg-gradient-to-br from-teal-50 to-cyan-50 rounded-xl p-8 border-2 border-teal-200 hover:shadow-lg transition">
                <div class="text-3xl mb-3">📋</div>
                <h4 class="font-bold text-lg text-gray-900 mb-2">Other Certificates</h4>
                <p class="text-gray-600">Various other official barangay certificates as needed.</p>
            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <div class="mt-20 text-center">
        <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-xl p-12 text-white max-w-2xl">
            <h3 class="text-3xl font-bold mb-4">Ready to Get Started?</h3>
            <p class="text-teal-100 text-lg mb-6">Create your account now and request your first certificate today.</p>
            <a href="?page=register" class="inline-block px-8 py-3 bg-white text-teal-600 rounded-lg font-bold hover:bg-gray-100 transition">
                Create Account
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>