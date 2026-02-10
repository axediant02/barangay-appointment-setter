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
<div class="flex flex-col items-center text-center mb-16">
    <h1 class="text-5xl md:text-6xl font-bold mb-6 text-gray-900">
        Welcome to Digital Certificates
    </h1>
    <p class="text-gray-600 text-xl mb-8 max-w-2xl leading-relaxed">
        Request barangay certificates online, schedule appointments, and track your status in real-time. No more long queues.
    </p>

    <!-- Features Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full max-w-4xl mb-12">
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl mb-3">⚡</div>
            <h3 class="font-semibold text-lg mb-2">Fast Processing</h3>
            <p class="text-gray-600 text-sm">Submit requests online and get faster processing times.</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl mb-3">📅</div>
            <h3 class="font-semibold text-lg mb-2">Easy Scheduling</h3>
            <p class="text-gray-600 text-sm">Choose your appointment date and time at your convenience.</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl mb-3">📱</div>
            <h3 class="font-semibold text-lg mb-2">Track Anytime</h3>
            <p class="text-gray-600 text-sm">Monitor your request status from anywhere, anytime.</p>
        </div>
    </div>

    <!-- Certificate List -->
    <div class="text-left w-full max-w-2xl bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-8 border border-blue-200">
        <h2 class="text-2xl font-semibold mb-6 text-gray-800">Available Certificates</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-start gap-3">
                <span class="text-blue-600 font-bold">✓</span>
                <div>
                    <h4 class="font-semibold text-gray-800">Residency</h4>
                    <p class="text-sm text-gray-600">Proof of Address</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-blue-600 font-bold">✓</span>
                <div>
                    <h4 class="font-semibold text-gray-800">Indigency</h4>
                    <p class="text-sm text-gray-600">Financial Aid / Scholarships</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-blue-600 font-bold">✓</span>
                <div>
                    <h4 class="font-semibold text-gray-800">Good Moral Character</h4>
                    <p class="text-sm text-gray-600">Jobs / School Requirements</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-blue-600 font-bold">✓</span>
                <div>
                    <h4 class="font-semibold text-gray-800">Business Operation</h4>
                    <p class="text-sm text-gray-600">For Permits & Licenses</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <span class="text-blue-600 font-bold">✓</span>
                <div>
                    <h4 class="font-semibold text-gray-800">No Objection</h4>
                    <p class="text-sm text-gray-600">Consent for Activities</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>