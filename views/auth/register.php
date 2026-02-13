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
            background: #ffffff;
            backdrop-filter: blur(16px);
            border: 2px solid #94a3b8;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02);
        }
        /* Inputs: underline only, no full border */
        .form-input {
            @apply w-full px-0 pt-2 pb-3 transition-all duration-150 font-semibold text-slate-800 text-sm;
            border: none;
            border-bottom: 2px solid #94a3b8;
            background: transparent;
        }
        .form-input::placeholder { color: #64748b; opacity: 0.8; }
        .form-input:hover { border-bottom-color: #64748b; }
        .form-input:focus {
            outline: none;
            border-bottom-color: #0d9488;
            border-bottom-width: 3px;
        }
        .heading-bold { font-weight: 900; letter-spacing: -0.025em; }
        .label-bold { font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: #64748b; font-size: 10px; }
        .glow-icon {
            box-shadow: 0 0 25px rgba(20, 184, 166, 0.4);
        }

        /* Fixed Navigation Header */
        .sticky-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }

        /* Fixed Footer */
        .fixed-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 0;
            z-index: 40;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 pt-24 pb-24">

<header class="sticky-header">
    <div class="max-w-4xl mx-auto px-6 py-4 flex items-center justify-between">
        <button onclick="location.href='index.php'" class="flex items-center gap-2 text-slate-600 font-bold hover:text-teal-600 transition group">
            <span class="bg-white w-9 h-9 flex items-center justify-center rounded-lg shadow-sm border border-gray-200 group-hover:border-teal-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </span>
            <span class="text-xs tracking-tight uppercase font-black">Back to Home</span>
        </button>
        <div class="hidden sm:block">
            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-slate-400">Secure Registration Node</span>
        </div>
    </div>
</header>

<div class="w-full max-w-[520px]">
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-[#14b8a6] rounded-2xl glow-icon mb-6 transform rotate-3 transition-transform hover:rotate-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
        </div>
        <h1 class="text-4xl heading-bold text-slate-900 mb-1 italic">Create Account</h1>
        <p class="text-[#14b8a6] font-black text-xs uppercase tracking-[0.2em]">Join the Barangay Network</p>
    </div>

    <div class="glass-card rounded-[2.5rem] shadow-xl overflow-hidden ring-1 ring-slate-200">
        <div class="p-8 sm:p-10">
            <?php if ($errors): ?>
                <div class="bg-red-50 border-2 border-red-100 rounded-2xl p-4 mb-8">
                    <div class="flex gap-3 items-center">
                        <div class="bg-red-500 text-white w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold">!</div>
                        <div class="text-red-700 text-[10px] font-black uppercase tracking-tight">
                            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block label-bold mb-2 ml-1">Full Name</label>
                        <input type="text" name="username" placeholder="e.g. Juan Dela Cruz" required class="form-input">
                    </div>
                    <div>
                        <label class="block label-bold mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" placeholder="juan@email.com" required class="form-input">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block label-bold mb-2 ml-1">Password</label>
                        <input type="password" name="password" placeholder="Min. 6 chars" required class="form-input">
                    </div>
                    <div>
                        <label class="block label-bold mb-2 ml-1">Confirm</label>
                        <input type="password" name="confirm_password" placeholder="••••••••" required class="form-input">
                    </div>
                </div>

                <div class="pt-3">
                    <label class="flex items-start gap-4 cursor-pointer group p-4 rounded-xl transition-colors border-2 border-slate-200 bg-slate-50 hover:border-slate-300 hover:bg-slate-100/80">
                        <input type="checkbox" required class="mt-1 w-5 h-5 accent-teal-600 rounded border-2 border-slate-400 focus:ring-2 focus:ring-teal-500/30">
                        <span class="text-[10px] font-black text-slate-500 leading-normal uppercase tracking-tight">
                            I understand and agree to the <a href="#" class="text-teal-600 hover:text-teal-700 underline decoration-2 underline-offset-2">Terms of Service</a> and <a href="#" class="text-teal-600 hover:text-teal-700 underline decoration-2 underline-offset-2">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-[#14b8a6] hover:bg-teal-600 text-white py-5 rounded-[1.25rem] font-black text-xs uppercase tracking-[0.25em] transition-all transform active:scale-[0.97] shadow-xl shadow-teal-500/20 flex items-center justify-center gap-3 mt-6">
                    Begin Registration
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </button>
            </form>
        </div>

        <div class="bg-slate-50 p-6 sm:p-8 border-t-2 border-slate-200 text-center">
            <p class="text-slate-500 text-[10px] font-black uppercase tracking-widest">
                Already part of the network? 
                <a href="?page=login" class="text-teal-600 hover:text-teal-700 ml-2 underline decoration-2 underline-offset-4">Sign In Here</a>
            </p>
        </div>
    </div>
</div>

<footer class="fixed-footer">
    <div class="max-w-4xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.4em]">Official Barangay Digital Network &copy; 2026</p>
        <div class="flex items-center gap-2">
            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-widest">System Online</span>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-200"></div>
        </div>
    </div>
</footer>

</body>
</html>