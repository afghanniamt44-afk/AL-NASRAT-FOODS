<?php
// login.php - Secure login for Shop Owner / Admin
session_start();
require_once __DIR__ . '/db.php';

$error = '';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'مهرباني وکړئ د کاروونکي نوم او پټنوم داخل کړئ (Please enter username and password)';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_user'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['fullname'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'د کاروونکي نوم یا پټنوم غلط دی (Invalid username or password)';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ps" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AL NASRAT FOODS | د اډمین ننوتل (Admin Login)</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background: #121212;
            color: #f2efe9;
        }
        .glass-card {
            background: linear-gradient(180deg, rgba(31, 31, 30, 0.95), rgba(22, 22, 22, 0.92));
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.45);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md glass-card rounded-2xl p-8 border border-white/10">
        <div class="text-center mb-8">
            <div class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-400 text-2xl font-bold mb-4">
                👑
            </div>
            <h1 class="text-xl font-bold text-stone-100">د النصرت فوډز اډمین پینل</h1>
            <p class="text-xs text-stone-400 mt-1">AL NASRAT FOODS · Admin Access</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs text-center font-medium">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="space-y-4">
            <div>
                <label class="block text-[11px] font-medium text-stone-300 mb-1.5">کارونکی نوم (Username)</label>
                <input type="text" name="username" required autofocus placeholder="admin"
                    class="w-full px-4 py-3 rounded-xl bg-stone-900/80 border border-stone-800 text-stone-100 text-sm focus:outline-none focus:border-amber-500/60 transition" />
            </div>

            <div>
                <label class="block text-[11px] font-medium text-stone-300 mb-1.5">پټنوم (Password)</label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-4 py-3 rounded-xl bg-stone-900/80 border border-stone-800 text-stone-100 text-sm focus:outline-none focus:border-amber-500/60 transition" />
            </div>

            <button type="submit"
                class="w-full mt-2 py-3.5 px-4 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 text-stone-950 font-bold text-sm shadow-lg shadow-amber-500/20 hover:brightness-110 active:scale-[0.99] transition">
                ننوتل (Login)
            </button>
        </form>

        <div class="mt-6 pt-5 border-t border-white/5 text-center text-[10px] text-stone-400">
            <span class="text-stone-300 font-medium">ښکاره لاګ ان (Default):</span> Username: <code class="text-amber-400 font-bold">admin</code> | Password: <code class="text-amber-400 font-bold">admin123</code>
        </div>
    </div>
</body>
</html>

