<?php
require_once '../config.php';

// PERLINDUNGAN SESI: Cek token autentikasi
if (!isset($_SESSION['auth_token']) || !isset($_SESSION['loggedin_user'])) {
    // Jika tidak ada token, paksa login
    header("Location: ../login.php");
    exit;
}

require_once '../includes/header.php';

$sqli_output = '';
$simulated_query = '';
$nama_lengkap = '';

// Logika Lab Aman
if (isset($_POST['login_sec'])) {
    $user = $_POST['sqli-user-sec'];
    $pass = $_POST['sqli-pass-sec']; // Gunakan password

    // --- HAPUS BLOK INI ---
    // // SIMULASI PREPARED STATEMENT
    // $simulated_query = "Jalankan query: SELECT * FROM users WHERE username = ?\nParameter [0]: \"" . $user . "\"";
    // 
    // // Dalam simulasi ini, serangan tidak akan pernah berhasil
    // $sqli_output = "Status: Gagal Login. Input diperlakukan sebagai string literal. Tidak ada kerentanan.";
    // --- AKHIR BLOK HAPUS ---

    // --- TAMBAHKAN BLOK BARU INI ---
    // PREPARED STATEMENT YANG SEBENARNYA
    $simulated_query = "Jalankan query: SELECT * FROM users_vuln WHERE username = ? AND password = ?";
    
    try {
        $stmt = $pdo->prepare($simulated_query);
        $stmt->execute([$user, $pass]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $sqli_output = "Status: SUKSES LOGIN!";
            $nama_lengkap = $result['nama_lengkap'];
        } else {
            $sqli_output = "Status: Gagal Login. Nama pengguna atau kata sandi salah. Serangan injeksi tidak berhasil.";
        }
    } catch (PDOException $e) {
        $sqli_output = "Status: ERROR SQL!\n" . $e->getMessage();
    }
    // --- AKHIR BLOK TAMBAHAN ---
}
?>

<title>Lab SQLi (Aman)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">SQL Injection (SQLi)</h2>
    <p class="text-gray-300 mb-6">Menyuntikkan query SQL berbahaya melalui input pengguna.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="../vulnerable/sqli.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Rentan (/vulnerable/)</a>
        <a href="sqli.php" class="py-2 px-6 tab-active">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Aman -->
    <div id="secure-pane">
        <p class="text-green-400 bg-green-900/30 p-3 rounded-md border border-green-500 mb-4">
            <strong>Mitigasi:</strong> Anda sedang melihat versi aman (Hanya untuk pengguna terotentikasi).
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <form method="POST" action="sqli.php">
                <p class="mb-2">Masukkan nama pengguna dan kata sandi untuk login.</p>
                <div class="flex flex-col md:flex-row gap-4 mb-4">
                    <input name="sqli-user-sec" type="text" placeholder="Nama Pengguna" class="flex-1 p-2 bg-gray-700 border border-gray-600 rounded-md">
                    <input name="sqli-pass-sec" type="password" placeholder="Kata Sandi" class="flex-1 p-2 bg-gray-700 border border-gray-600 rounded-md">
                </div>
                <button type="submit" name="login_sec" class="w-full md:w-auto p-2 px-6 bg-green-600 hover:bg-green-500 rounded-md text-white">Login (Aman)</button>
                <p class="mt-4 text-sm text-gray-400">Coba masukkan: <code>admin'--</code> di kolom nama pengguna.</p>
                
                <?php if ($simulated_query): ?>
                <div class="console-output text-green-400 border border-green-500">
                    <strong>Query yang Disimulasikan:</strong>
                    <br>
                    <?php echo htmlspecialchars($simulated_query); ?>
                    <br><br>
                    <strong>Parameter:</strong>
                    <br>
                    [0] = "<?php echo htmlspecialchars($user); ?>"
                    <br>
                    [1] = "<?php echo htmlspecialchars($pass); ?>"
                    <br><br>
                    <strong>Hasil:</strong>
                    <br>
                    <?php echo htmlspecialchars($sqli_output); ?>

                    <?php if ($nama_lengkap): // Tampilkan nama jika login sukses ?>
                    <br><br>
                    <strong>Selamat datang:</strong>
                    <br>
                    <?php echo htmlspecialchars($nama_lengkap); ?>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </form>
        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Mitigasi</h4>
        <p class="text-gray-300">Mitigasi menggunakan <strong>Prepared Statements (Parameterized Queries)</strong>. <br><code>$stmt = $pdo->prepare('SELECT * FROM users_vuln WHERE username = ? AND password = ?');</code><br><code>$stmt->execute([$_POST['user'], $_POST['pass']]);</code><br><br>Database memperlakukan input <code>admin'--</code> murni sebagai data (string), bukan sebagai bagian dari perintah SQL. Query mencari pengguna dengan nama persis <code>admin'--</code>, yang tidak ada.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>