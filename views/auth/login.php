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
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role'] = (string)$user['role'];
            $_SESSION['username'] = (string)$user['name'];
            $_SESSION['email'] = (string)$user['email'];
            
            // Ensure session is saved before redirect
            session_commit();

            if ($user['role'] === 'admin') {
                header("Location: ?page=admin-dashboard", true, 302);
            } else {
                header("Location: ?page=resident-dashboard", true, 302);
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
    <title>Login - Barangay Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .form-input {
            @apply w-full border-2 border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-600 transition-all font-semibold text-slate-700 placeholder:text-slate-300;
        }
        .heading-bold { font-weight: 900; letter-spacing: -0.025em; }
        .label-bold { font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-6">

<div class="w-full max-w-[400px]">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-teal-600 rounded-2xl shadow-xl shadow-teal-200 mb-4 transform -rotate-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h1 class="text-3xl heading-bold text-slate-900">Welcome Back</h1>
        <p class="text-slate-400 font-semibold text-sm mt-1">Access your barangay documents</p>
    </div>

    <div class="bg-white rounded-[2rem] shadow-2xl shadow-slate-200/60 border border-slate-100 overflow-hidden">
        <div class="p-8">
            <?php if ($errors): ?>
                <div class="bg-red-50 border-2 border-red-100 rounded-xl p-4 mb-6 animate-pulse">
                    <div class="flex gap-3 items-center">
                        <span class="text-red-600 text-lg">✕</span>
                        <div class="text-red-700 text-xs font-bold uppercase tracking-wider">
                            <?php foreach ($errors as $error) echo $error; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label for="email" class="block text-[10px] label-bold text-slate-400 mb-2 ml-1">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        placeholder="e.g. juan@example.ph"
                        required 
                        class="form-input"
                    >
                </div>

                <div class="relative">
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label for="password" class="block text-[10px] label-bold text-slate-400">Password</label>
                        <a href="#" class="text-[10px] label-bold text-teal-600 hover:text-teal-700">Forgot?</a>
                    </div>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="••••••••"
                        required 
                        class="form-input"
                    >
                </div>

                <div class="flex items-center gap-2 px-1">
                    <input type="checkbox" id="remember" class="w-4 h-4 accent-teal-600 rounded">
                    <label for="remember" class="text-xs font-bold text-slate-500 uppercase tracking-tighter">Remember this device</label>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-4 rounded-xl font-black text-xs uppercase tracking-[0.2em] transition-all transform active:scale-[0.98] shadow-xl shadow-slate-200 mt-4">
                    Sign In
                </button>
            </form>
        </div>

        <div class="bg-slate-50 p-6 border-t border-slate-100 text-center">
            <p class="text-slate-400 text-xs font-bold uppercase tracking-tight">
                New resident? 
                <a href="?page=register" class="text-teal-600 hover:text-teal-700 ml-1">Create Account</a>
            </p>
        </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-slate-400 text-[10px] label-bold">Official Digital Portal &copy; 2026</p>
    </div>
</div>

</body>
</html>