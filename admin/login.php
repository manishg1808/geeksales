<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $stmt = db()->prepare('SELECT * FROM admin_users WHERE username = ? AND active = 1 LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        db()->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$admin['id']]);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['admin_email'] = $admin['email'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="../IMAGE/geeksupport_unique_simple_icon.svg">
    <title>Admin Login - Geek Support LLc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#EFF6FF',100:'#DBEAFE',200:'#BFDBFE',300:'#93C5FD',400:'#60A5FA',500:'#2563EB',600:'#2563EB',700:'#1D4ED8',800:'#0F172A',900:'#0F172A' },
                        navy:  { 50:'#F8FAFC',100:'#F1F5F9',200:'#E5E7EB',300:'#CBD5E1',400:'#6B7280',500:'#2563EB',600:'#2563EB',700:'#1D4ED8',800:'#0F172A',900:'#0F172A' },
                        amber2:{ 50:'#FFF7ED',100:'#FFEDD5',200:'#FED7AA',300:'#FDBA74',400:'#FB923C',500:'#F97316',600:'#EA580C',700:'#C2410C',800:'#9A3412',900:'#7C2D12' },
                    },
                    fontFamily:{ sans:['Inter','system-ui','sans-serif'] },
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet"/>
    <style>
        .bg-gradient-to-br{background-image:linear-gradient(135deg,#2563EB 0%,#0F172A 55%,#60A5FA 100%)!important}
        .text-amber2-500{color:#F97316!important}
        .text-navy-200{color:#DBEAFE!important}
    </style>
</head>
<body class="bg-gradient-to-br from-navy-900 via-navy-800 to-navy-700 min-h-screen flex items-center justify-center p-4 font-sans antialiased">
    
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <img src="../IMAGE/geeksupport_unique_simple_icon.svg" alt="Geek Support LLc" class="w-12 h-12 object-contain">
            </div>
            <h1 class="text-2xl font-black text-white">Geek Support LLc</h1>
            <p class="text-navy-200 text-[11px] font-bold uppercase tracking-widest mt-1">fast secure remote help</p>
            <p class="text-navy-200 text-sm mt-2">Admin Management</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <h2 class="text-xl font-bold text-slate-800 mb-2">Welcome Back!</h2>
            <p class="text-slate-500 text-sm mb-6">Please sign in to continue</p>

            <?php if(isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-xl text-sm mb-4 flex items-center gap-2">
                <i class="ri-error-warning-line text-lg"></i>
                <span><?php echo $error; ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Username</label>
                    <div class="relative">
                        <i class="ri-user-3-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" name="username" required 
                            class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-slate-700 outline-none focus:border-navy-600 focus:ring-2 focus:ring-navy-100 transition"
                            placeholder="Enter username">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <i class="ri-lock-password-line absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="password" name="password" required 
                            class="w-full pl-11 pr-4 py-3 border border-slate-200 rounded-xl text-slate-700 outline-none focus:border-navy-600 focus:ring-2 focus:ring-navy-100 transition"
                            placeholder="Enter password">
                    </div>
                </div>

                <div class="flex items-center justify-between text-sm">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" class="rounded accent-navy-600">
                        <span class="text-slate-600">Remember me</span>
                    </label>
                    <a href="#" class="text-navy-600 hover:underline font-semibold">Forgot Password?</a>
                </div>

                <button type="submit" class="w-full bg-navy-600 hover:bg-navy-700 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="ri-login-box-line"></i>
                    Sign In
                </button>

            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <a href="../index.php" class="text-navy-200 hover:text-white text-sm font-semibold inline-flex items-center gap-1 transition">
                <i class="ri-arrow-left-line"></i> Back to Website
            </a>
        </div>
    </div>

</body>
</html>
