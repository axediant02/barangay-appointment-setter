<?php
require_once '../config/database.php'; // Make sure your $pdo is available

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        } else {
            // Insert new user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, role)
                VALUES (?, ?, ?, 'resident')
            ");
            $stmt->execute([$username, $email, $hash]);

            // Log in user
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['role'] = 'resident';
            $_SESSION['username'] = $username;

            header("Location: ?page=resident-dashboard");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Barangay Certificate System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .form-input {
            @apply w-full border-2 border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition font-medium;
        }
        .btn-primary {
            @apply w-full bg-teal-600 hover:bg-teal-700 text-white py-3 rounded-lg transition font-semibold text-lg;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-teal-50 to-cyan-50 min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 px-8 py-8 text-white text-center">
            <div class="text-5xl mb-3">📝</div>
            <h1 class="text-3xl font-bold">Create Account</h1>
            <p class="text-teal-100 mt-2">Join our certificate system</p>
        </div>

        <div class="px-8 py-8 space-y-6">
            <?php if ($errors): ?>
                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="text-red-600 text-xl flex-shrink-0">⚠️</div>
                        <div class="text-red-800">
                            <?php foreach ($errors as $error) echo "<p class='font-medium text-sm mb-1'>$error</p>"; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                    <input type="text" id="username" name="username" placeholder="JuanDelaCruz" required class="form-input" aria-label="Username">
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="your@email.com" required class="form-input" autocomplete="email" aria-label="Email address">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" placeholder="At least 6 characters" required class="form-input" autocomplete="new-password" aria-label="Password">
                    <p class="text-xs text-gray-500 mt-1">At least 6 characters</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required class="form-input" autocomplete="new-password" aria-label="Confirm password">
                </div>

                <button type="submit" class="btn-primary mt-8">Create Account</button>
            </form>

            <div class="pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600">
                    Already have an account? 
                    <a href="?page=login" class="text-teal-600 font-semibold hover:text-teal-700 transition">Sign in</a>
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center text-gray-600 text-sm">
        <p>Get access to request certificates and track your status</p>
    </div>
</div>

</body>
</html>