<?php
require_once '../config/database.php';

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
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, role)
                VALUES (?, ?, ?, 'resident')
            ");
            $stmt->execute([$username, $email, $hash]);

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
    <title>Register - Barangay Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at bottom left, #e2f1f0, #ffffff, #dcfce7);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .form-input {
            @apply w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 focus:outline-none focus:ring-4 focus:ring-teal-500/10 focus:border-teal-500 transition-all font-medium text-slate-700 placeholder:text-slate-300 text-sm;
        }
        .heading-bold { font-weight: 900; letter-spacing: -0.025em; }
        .label-bold { font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: #94a3b8; font-size: 10px; }
        .glow-icon {
            box-shadow: 0 0 25px rgba(20, 184, 166, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6">

<div class="w-full max-w-[480px]">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-14 h-14 bg-[#14b8a6] rounded-2xl glow-icon mb-4 transform rotate-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </div>
        <h1 class="text-3xl heading-bold text-slate-900 mb-1">Create Account</h1>
        <p class="text-[#14b8a6] font-bold text-xs uppercase tracking-widest">Resident Registration</p>
    </div>

    <div class="glass-card rounded-[2.5rem] shadow-2xl shadow-teal-900/5 overflow-hidden">
        <div class="p-8 sm:p-10">
            <?php if ($errors): ?>
                <div class="bg-red-50 border border-red-100 rounded-2xl p-4 mb-6">
                    <div class="flex gap-3">
                        <span class="text-red-500 font-black">✕</span>
                        <div class="text-red-700 text-[10px] font-black uppercase tracking-tight">
                            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block label-bold mb-2 ml-1">Full Name</label>
                        <input type="text" name="username" placeholder="Juan Dela Cruz" required class="form-input">
                    </div>
                    <div>
                        <label class="block label-bold mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" placeholder="juan@email.com" required class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block label-bold mb-2 ml-1">Password</label>
                        <input type="password" name="password" placeholder="Min. 6 chars" required class="form-input">
                    </div>
                    <div>
                        <label class="block label-bold mb-2 ml-1">Confirm</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required class="form-input">
                    </div>
                </div>

                <div class="pt-2">
                    <label class="flex items-start gap-3 cursor-pointer group">
                        <input type="checkbox" required class="mt-1 w-4 h-4 accent-teal-600 rounded">
                        <span class="text-[10px] font-bold text-slate-500 leading-relaxed uppercase">
                            I agree to the <a href="#" class="text-teal-600 group-hover:underline">Terms of Service</a> and <a href="#" class="text-teal-600 group-hover:underline">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#14b8a6] hover:bg-teal-600 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all shadow-lg shadow-teal-500/20 flex items-center justify-center gap-2 mt-4">
                    Register Now
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </button>
            </form>
        </div>

        <div class="bg-slate-50/50 p-6 border-t border-white/50 text-center">
            <p class="text-slate-400 text-[10px] font-black uppercase tracking-tight">
                Already registered? 
                <a href="?page=login" class="text-teal-600 hover:text-teal-700 ml-1">Sign In Here</a>
            </p>
        </div>
    </div>

    <div class="mt-8 text-center">
        <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.3em]">Official Barangay Digital Network</p>
    </div>
</div>

</body>
</html>