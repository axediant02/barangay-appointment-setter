<?php
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $errors[] = "Both email and password are required.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            if ($user['role'] === 'admin') {
                header("Location: ?page=admin-dashboard");
            } else {
                header("Location: ?page=resident-dashboard");
            }
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Barangay Certificate System</title>
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
            <div class="text-5xl mb-3">📋</div>
            <h1 class="text-3xl font-bold">Welcome Back</h1>
            <p class="text-teal-100 mt-2">Log in to your account</p>
        </div>

        <div class="px-8 py-8 space-y-6">
            <?php if ($errors): ?>
                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <div class="text-red-600 text-xl flex-shrink-0">⚠️</div>
                        <div class="text-red-800">
                            <?php foreach ($errors as $error) echo "<p class='font-medium'>$error</p>"; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        placeholder="your@email.com"
                        required 
                        class="form-input"
                        autocomplete="email"
                        aria-label="Email address"
                    >
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="••••••••"
                        required 
                        class="form-input"
                        autocomplete="current-password"
                        aria-label="Password"
                    >
                </div>

                <button type="submit" class="btn-primary mt-8">Sign In</button>
            </form>

            <div class="pt-6 border-t border-gray-200 text-center">
                <p class="text-gray-600">
                    Don't have an account? 
                    <a href="?page=register" class="text-teal-600 font-semibold hover:text-teal-700 transition">
                        Create one now
                    </a>
                </p>
            </div>
        </div>
    </div>

    <div class="mt-8 text-center text-gray-600 text-sm">
        <p>Need help? Contact support at <strong>info@barangay.gov.ph</strong></p>
    </div>
</div>

</body>
</html>