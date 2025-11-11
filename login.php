<?php
require_once 'config.php'; // $pdo sekarang tersedia dari sini

$loginError = '';
$registerError = '';
$registerSuccess = '';

// Logika Pendaftaran (Register)
if (isset($_POST['register'])) {
    $user = $_POST['register-user'];
    $pass = $_POST['register-pass'];

    if (empty($user) || empty($pass)) {
        $registerError = "Nama pengguna dan kata sandi tidak boleh kosong.";
    } else {
        // Cek dulu apakah username sudah ada di DB
        $stmt_check = $pdo->prepare("SELECT 1 FROM users_app WHERE username = ?");
        $stmt_check->execute([$user]);
        if ($stmt_check->fetch()) {
            $registerError = "Nama pengguna sudah terdaftar. Silakan pilih yang lain.";
        } else {
            // Username belum ada, lanjutkan pendaftaran
            $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
            $stmt_insert = $pdo->prepare("INSERT INTO users_app (username, password) VALUES (?, ?)");
            if ($stmt_insert->execute([$user, $hashed_pass])) {
                $registerSuccess = "Pendaftaran berhasil! Silakan masuk.";
            } else {
                $registerError = "Terjadi kesalahan saat pendaftaran.";
            }
        }
    }
}

// Logika Login
if (isset($_POST['login'])) {
    $user = $_POST['login-user'];
    $pass = $_POST['login-pass'];

    if (empty($user) || empty($pass)) {
        $loginError = "Nama pengguna dan kata sandi tidak boleh kosong.";
    } else {
        // Ambil data pengguna dari database
        $stmt = $pdo->prepare("SELECT id, password FROM users_app WHERE username = ?"); // Ambil 'id' juga
        $stmt->execute([$user]);
        $db_user = $stmt->fetch();

        if ($db_user && password_verify($pass, $db_user['password'])) {
            // SUKSES LOGIN
            $_SESSION['loggedin_user'] = $user;
            $_SESSION['user_id'] = $db_user['id']; // <-- TAMBAHKAN INI
            $_SESSION['auth_token'] = bin2hex(random_bytes(32)); // Token sesi aman (mirip UUID)
            
            // Redirect ke halaman utama setelah login
            header("Location: index.php");
            exit;
        } else {
            $loginError = "Nama pengguna atau kata sandi salah.";
        }
    }
}

// Include header SETELAH semua logika PHP
require_once 'includes/header.php';
?>

<title>Login/Register - Web Vuln Lab</title>

<div class="bg-gray-800 p-8 rounded-lg shadow-2xl max-w-sm w-full mx-auto">
            
    <!-- Formulir Login -->
    <div id="login-form">
        <h3 class="text-xl font-semibold mb-4 text-center">Login</h3>
        <p class="text-gray-300 mb-6 text-center">Masuk untuk melihat versi aman.</p>
        
        <form method="POST" action="login.php">
            <div class="mb-4">
                <label for="login-user" class="block text-sm font-medium text-gray-300 mb-2">Nama Pengguna</label>
                <input type="text" id="login-user" name="login-user" class="w-full p-3 bg-gray-900 border border-gray-700 rounded-md text-white" required>
            </div>
            <div class="mb-4">
                <label for="login-pass" class="block text-sm font-medium text-gray-300 mb-2">Kata Sandi</label>
                <input type="password" id="login-pass" name="login-pass" class="w-full p-3 bg-gray-900 border border-gray-700 rounded-md text-white" required>
            </div>
            
            <?php if ($loginError): ?>
                <p class="text-red-400 text-sm mb-4"><?php echo $loginError; ?></p>
            <?php endif; ?>
            
            <div class="flex flex-col gap-4">
                <button type="submit" name="login" class="w-full p-3 bg-blue-600 hover:bg-blue-500 rounded-md text-white">Masuk</button>
                <button type="button" onclick="toggleAuthMode('register')" class="text-blue-400 hover:text-blue-300 text-sm text-center">Belum punya akun? Daftar.</button>
            </div>
        </form>
    </div>

    <!-- Formulir Registrasi -->
    <div id="register-form" class="hidden">
        <h3 class="text-xl font-semibold mb-4 text-center">Daftar Akun Baru</h3>
        
        <form method="POST" action="login.php">
            <div class="mb-4">
                <label for="register-user" class="block text-sm font-medium text-gray-300 mb-2">Nama Pengguna Baru</label>
                <input type="text" id="register-user" name="register-user" class="w-full p-3 bg-gray-900 border border-gray-700 rounded-md text-white" required>
            </div>
            <div class="mb-4">
                <label for="register-pass" class="block text-sm font-medium text-gray-300 mb-2">Kata Sandi Baru</label>
                <input type="password" id="register-pass" name="register-pass" class="w-full p-3 bg-gray-900 border border-gray-700 rounded-md text-white" required>
            </div>
            
            <?php if ($registerError): ?>
                <p class="text-red-400 text-sm mb-4"><?php echo $registerError; ?></p>
            <?php endif; ?>
            <?php if ($registerSuccess): ?>
                <p class="text-green-400 text-sm mb-4"><?php echo $registerSuccess; ?></p>
            <?php endif; ?>
            
            <div class="flex flex-col gap-4">
                <button type="submit" name="register" class="w-full p-3 bg-green-600 hover:bg-green-500 rounded-md text-white">Daftar</button>
                <button type="button" onclick="toggleAuthMode('login')" class="text-blue-400 hover:text-blue-300 text-sm text-center">Sudah punya akun? Masuk.</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAuthMode(mode) {
        if (mode === 'login') {
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('register-form').classList.add('hidden');
        } else {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
        }
    }
    // Set mode awal berdasarkan pesan error (jika ada)
    <?php if ($registerError || $registerSuccess): ?>
        toggleAuthMode('register');
    <?php endif; ?>
</script>

<?php
require_once 'includes/footer.php';
?>