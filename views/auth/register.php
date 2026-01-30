<?php
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email || !$password || !$confirm) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            $errors[] = "Email is already registered.";
        } else {
            // Insert user
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'resident')");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hash
            ]);
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['role'] = 'resident';
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
    <title>Register - Barangay Certificate System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
    <h1 class="text-2xl font-bold mb-6 text-center">Register</h1>

    <?php if ($errors): ?>
        <div class="mb-4 bg-red-100 text-red-700 p-3 rounded">
            <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block text-gray-700">Full Name</label>
            <input type="text" name="name" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <div>
            <label class="block text-gray-700">Email</label>
            <input type="email" name="email" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <div>
            <label class="block text-gray-700">Password</label>
            <input type="password" name="password" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <div>
            <label class="block text-gray-700">Confirm Password</label>
            <input type="password" name="confirm_password" required class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring focus:ring-blue-300">
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Register</button>
    </form>

    <p class="mt-4 text-center text-gray-600">
        Already have an account? <a href="?page=login" class="text-blue-600 hover:underline">Login</a>
    </p>
</div>

</body>
</html>