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
            $role = strtolower((string)$user['role']);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['role'] = $role;
            $_SESSION['username'] = (string)$user['name'];
            $_SESSION['email'] = (string)$user['email'];
            session_commit();
            
            if ($role === 'admin') {
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
        body { font-family: 'Inter', sans-serif; background-color: #f1f5f9; }
        /* Inputs: underline only, full-width line */
        .form-input {
            @apply pt-2 pb-3 transition-all duration-150 font-semibold text-slate-800 placeholder:text-slate-500;
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding-left: 0;
            padding-right: 0;
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
        .label-bold { font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; }
        .auth-card {
            border: 2px solid #94a3b8;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08), 0 0 0 1px rgba(0,0,0,0.02);
        }
        /* Sticky Header */
        .header-sticky {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 50;
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #e2e8f0;
        }

        /* Fixed Footer */
        .footer-fixed {
            position: fixed;
            bottom: 0;
            width: 100%;
            background: white;
            border-top: 1px solid #e2e8f0;
            padding: 1rem 0;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center p-6 pb-24 pt-20">

<header class="header-sticky">
    <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
        <button onclick="location.href='index.php'" class="flex items-center gap-2 text-slate-700 font-bold hover:text-teal-600 transition group">
            <span class="bg-white w-10 h-10 flex items-center justify-center rounded-xl shadow-sm border border-gray-200 group-hover:border-teal-200 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </span>
            <span class="text-xs uppercase tracking-widest font-black">Return to Home</span>
        </button>
        <div class="hidden md:block">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Secure Auth Node</span>
        </div>
    </div>
</header>

<div class="w-full max-w-[420px] relative z-10">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-teal-600 rounded-[2rem] shadow-2xl shadow-teal-200 mb-6 transform -rotate-3 transition hover:rotate-0 duration-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
        </div>
        <h1 class="text-4xl heading-bold text-slate-900 tracking-tighter italic">Welcome Back</h1>
        <p class="text-slate-500 font-medium mt-2">Access your secure barangay document portal</p>
    </div>

    <div class="auth-card bg-white rounded-[2.5rem] overflow-hidden">
        <div class="p-8 sm:p-10">
            <?php if ($errors): ?>
                <div class="bg-red-50 border-2 border-red-100 rounded-2xl p-4 mb-8 animate-pulse">
                    <div class="flex gap-3 items-center">
                        <div class="bg-red-100 w-6 h-6 rounded-full flex items-center justify-center text-red-600 font-bold">!</div>
                        <div class="text-red-700 text-[10px] font-black uppercase tracking-widest">
                            <?php foreach ($errors as $error) echo $error; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6 w-full">
                <div class="w-full">
                    <label for="email" class="block text-[10px] label-bold text-slate-500 mb-2">Email Address</label>
                    <input 
                        type="email" 
                        id="email"
                        name="email" 
                        placeholder="e.g. juan.delacruz@example.ph"
                        required 
                        class="form-input"
                    >
                </div>

                <div class="w-full">
                    <label for="password" class="block text-[10px] label-bold text-slate-500 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password"
                        name="password" 
                        placeholder="••••••••••••"
                        required 
                        class="form-input"
                    >
                </div>

                <div class="flex items-center gap-3 px-1">
                    <input type="checkbox" id="remember" class="w-5 h-5 accent-teal-600 rounded-lg border-2 border-slate-300">
                    <label for="remember" class="text-xs font-black text-slate-600 uppercase tracking-tight cursor-pointer">Stay signed in on this device</label>
                </div>

                <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-5 rounded-2xl font-black text-xs uppercase tracking-[0.25em] transition-all transform active:scale-[0.97] shadow-xl shadow-slate-300 mt-6">
                    Sign In to Portal
                </button>
            </form> 
        </div>

        <div class="bg-slate-50 p-6 sm:p-8 border-t-2 border-slate-200 text-center">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-tight">
                Not a member yet? 
                <a href="?page=register" class="text-teal-600 hover:text-teal-700 ml-1 underline decoration-2 underline-offset-4">Register Now</a>
            </p>
        </div>
    </div>
</div>

<footer class="footer-fixed">
    <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-slate-400 text-[10px] label-bold">
            &copy; 2026 BRGY. PORTAL • ALL RIGHTS RESERVED
        </p>
        <div class="flex items-center gap-6">
            <a href="#" class="text-[10px] font-black text-slate-400 hover:text-teal-600 uppercase tracking-widest">Privacy Policy</a>
            <a href="#" class="text-[10px] font-black text-slate-400 hover:text-teal-600 uppercase tracking-widest">Terms of Service</a>
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-lg shadow-emerald-200"></div>
        </div>
    </div>
</footer>

</body>
</html>