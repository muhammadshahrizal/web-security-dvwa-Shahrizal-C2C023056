<?php
require_once '../config.php';
require_once '../includes/header.php';

$sqli_output = '';
$simulated_query = '';
$nama_lengkap = '';

// Logika Lab Rentan
if (isset($_POST['login_vuln'])) {
    $user = $_POST['sqli-user-vuln'];
    $pass = $_POST['sqli-pass-vuln']; // Sekarang kita gunakan ini

    // --- HAPUS BLOK INI ---
    // // SIMULASI QUERY RENTAN
    // $simulated_query = "SELECT * FROM users WHERE username = '" . $user . "' AND ...";
    //
    // // Cek serangan sederhana
    // if (strpos($user, "'") !== false || strpos($user, "=") !== false) {
    //     $sqli_output = "Status: SUKSES LOGIN! (DIJEBOL)\nKerentanan terdeteksi. Query dievaluasi sebagai 'true'.";
    // } else {
    //     $sqli_output = "Status: Gagal Login. Nama pengguna tidak ditemukan.";
    // }
    // --- AKHIR BLOK HAPUS ---

    // --- TAMBAHKAN BLOK BARU INI ---
    // QUERY RENTAN YANG SEBENARNYA
    $simulated_query = "SELECT * FROM users_vuln WHERE username = '" . $user . "' AND password = '" . $pass . "'";

    try {
        // Gunakan $pdo->query() yang rentan terhadap injeksi
        $stmt = $pdo->query($simulated_query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $sqli_output = "Status: SUKSES LOGIN!";
            $nama_lengkap = $result['nama_lengkap'];
        } else {
            $sqli_output = "Status: Gagal Login. Nama pengguna atau kata sandi salah.";
        }
    } catch (PDOException $e) {
        // Jika query-nya error (misal karena injeksi ' ), itu akan ditangkap di sini
        $sqli_output = "Status: ERROR SQL!\n" . $e->getMessage();
    }
    // --- AKHIR BLOK TAMBAHAN ---
}
?>

<title>Lab SQLi (Rentan)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">SQL Injection (SQLi)</h2>
    <p class="text-gray-300 mb-6">Menyuntikkan query SQL berbahaya melalui input pengguna.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="sqli.php" class="py-2 px-6 tab-active">Versi Rentan (/vulnerable/)</a>
        <a href="../secure/sqli.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Rentan -->
    <div id="vulnerable-pane">
        <p class="text-yellow-400 bg-yellow-900/30 p-3 rounded-md border border-yellow-500 mb-4">
            <strong>Peringatan:</strong> Ini adalah simulasi dari kode yang rentan.
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <form method="POST" action="sqli.php">
                <p class="mb-2">Masukkan nama pengguna dan kata sandi untuk login.</p>
                <div class="flex flex-col md:flex-row gap-4 mb-4">
                    <input name="sqli-user-vuln" type="text" placeholder="Nama Pengguna" class="flex-1 p-2 bg-gray-700 border border-gray-600 rounded-md">
                    <input name="sqli-pass-vuln" type="password" placeholder="Kata Sandi" class="flex-1 p-2 bg-gray-700 border border-gray-600 rounded-md">
                </div>
                <button type="submit" name="login_vuln" class="w-full md:w-auto p-2 px-6 bg-red-600 hover:bg-red-500 rounded-md text-white">Login (Rentan)</button>
                <p class="mt-4 text-sm text-gray-400">Coba masukkan: <code>admin'--</code> atau <code>admin'#</code> di kolom nama pengguna.</p>
                
                <?php if ($simulated_query): ?>
                <div class="console-output <?php echo (strpos($sqli_output, 'DIJEBOL') !== false) ? 'text-red-400 border border-red-500' : ''; ?>">
                    <strong>Query yang Disimulasikan:</strong>
                    <br>
                    <?php echo htmlspecialchars($simulated_query); ?>
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
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Kerentanan</h4>
        <p class="text-gray-300">Kode sisi server (PHP) mungkin terlihat seperti ini: <br><code>$sql = "SELECT * FROM users_vuln WHERE username = '" . $_POST['user'] . "' AND password = '" . $_POST['pass'] . "';</code><br><br>Menyambung string secara langsung memungkinkan penyerang untuk memanipulasi query. Memasukkan <code>admin'--</code> mengubah query menjadi <code>... WHERE username = 'admin'--' AND ...</code> yang mengomentari sisa query dan berhasil login sebagai admin.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>