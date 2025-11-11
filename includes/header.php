<?php
// Pastikan config.php sudah di-include sebelum header ini
if (session_status() === PHP_SESSION_NONE) {
    // Jika config.php lupa di-include, jalankan sesi di sini
    session_start();
}

// Cek status login
$currentUser = null;
if (isset($_SESSION['loggedin_user'])) {
    $currentUser = $_SESSION['loggedin_user'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tab-active {
            border-bottom: 2px solid #3b82f6;
            color: #3b82f6;
            font-weight: 600;
        }
        .console-output {
            background-color: #1f2937; color: #d1d5db;
            font-family: 'Courier New', Courier, monospace;
            padding: 1rem; border-radius: 0.5rem; margin-top: 1rem;
            min-height: 100px; white-space: pre-wrap; word-wrap: break-word;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen p-4 md:p-8">
    <div class="max-w-4xl mx-auto">
        <header class="mb-8">
            <div class="flex justify-between items-center mb-2">
                 <h1 class="text-3xl md:text-4xl font-bold text-blue-400">Web Vulnerability Lab</h1>
                 <div id="auth-status" class="text-sm text-right">
                    <?php if ($currentUser): ?>
                        <span class="text-gray-300">Selamat datang, <strong><?php echo htmlspecialchars($currentUser); ?></strong>!</span>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                             <a href="<?php echo BASE_URL; ?>/secure/profil.php" class="ml-4 px-3 py-1 bg-gray-600 hover:bg-gray-500 rounded-md text-white text-xs font-medium transition-colors">
                                Profil Saya
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>/logout.php" class="ml-4 px-3 py-1 bg-red-600 hover:bg-red-500 rounded-md text-white text-xs font-medium transition-colors">
                            Logout
                        </a>
                    <?php else: ?>
                        <span class="text-gray-500">Anda belum masuk.</span>
                        <a href="<?php echo BASE_URL; ?>/login.php" class="ml-4 px-3 py-1 bg-blue-600 hover:bg-blue-500 rounded-md text-white text-xs font-medium transition-colors">
                            Login/Register
                        </a>
                    <?php endif; ?>
                 </div>
            </div>
            <p class="text-lg text-gray-400">Sebuah platform untuk mempelajari kerentanan web (Demo PHP)</p>
        </header>
        <main id="app-content">