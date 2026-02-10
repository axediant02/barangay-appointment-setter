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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Barangay Certificate System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

<!-- Navbar -->
<nav class="bg-blue-600 text-white py-4">
    <div class="container mx-auto flex justify-between items-center px-4">
        <a href="#" class="text-xl font-bold">Barangay Certificate System</a>
    </div>
</nav>

<!-- Main Content -->
<main class="flex-grow container mx-auto px-4 flex flex-col items-center text-center mt-12">
    <h1 class="text-4xl md:text-5xl font-bold mb-4 text-gray-800">
        Welcome to the Barangay Certificate Request System
    </h1>
    <p class="text-gray-700 text-lg mb-8 max-w-xl">
        Submit certificate requests online, schedule appointments, and track status without long queues.
    </p>

    <!-- Buttons -->
    <div class="flex gap-4 mb-10">
        <a href="?page=login" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Login
        </a>
        <a href="?page=register" class="px-6 py-3 border border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50 transition">
            Register
        </a>
    </div>

    <!-- Certificate List -->
    <div class="text-left w-full max-w-md bg-white shadow-md rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Available Certificates:</h2>
        <ul class="list-disc list-inside space-y-2 text-gray-700">
            <li>Residency (Proof of Address)</li>
            <li>Indigency (Financial Aid / Scholarships)</li>
            <li>Good Moral Character (Jobs / School)</li>
            <li>Business Operation (For Permits)</li>
            <li>No Objection (Consent for Activities)</li>
        </ul>
    </div>
</main>

<!-- Footer -->
<footer class="bg-blue-600 text-white text-center py-4 mt-12">
    &copy; <?= date("Y") ?> Barangay Certificate System. All rights reserved.
</footer>

</body>
</html>