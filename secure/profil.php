<?php
require_once '../config.php';

// PERLINDUNGAN SESI: Cek apakah pengguna sudah login
if (!isset($_SESSION['auth_token']) || !isset($_SESSION['loggedin_user']) || !isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/header.php';

$profil_user_id = null;
$profil_username = null;
$error_message = '';

// MITIGASI: Mengambil ID HANYA dari Sesi (SESSION), BUKAN dari GET/URL.
$id_to_view = $_SESSION['user_id'];
    
try {
    $stmt = $pdo->prepare("SELECT id, username FROM users_app WHERE id = ?");
    $stmt->execute([$id_to_view]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user_data) {
        $profil_user_id = $user_data['id'];
        $profil_username = $user_data['username'];
    } else {
        $error_message = "Error: Data profil Anda tidak ditemukan.";
    }
} catch (PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<title>Lab BAC (Aman)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">Broken Access Control (BAC)</h2>
    <p class="text-gray-300 mb-6">Mengakses data pengguna lain dengan mengubah parameter ID.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="../vulnerable/profil.php?id=1" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Rentan (/vulnerable/)</a>
        <a href="profil.php" class="py-2 px-6 tab-active">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Aman -->
    <div id="secure-pane">
        <p class="text-green-400 bg-green-900/30 p-3 rounded-md border border-green-500 mb-4">
            <strong>Mitigasi:</strong> Halaman ini *hanya* akan menampilkan profil pengguna yang sedang login.
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <p class="mb-4">Halaman ini mengabaikan parameter ID dari URL dan hanya menggunakan <code>$_SESSION['user_id']</code> untuk mengambil data.</p>
            
            <div class="console-output text-green-400">
                <?php if ($error_message): ?>
                    <p class="text-red-400"><?php echo $error_message; ?></p>
                <?php elseif ($profil_user_id): ?>
                    <p class="text-xl font-bold">Menampilkan Profil Anda:</p>
                    <p><strong>ID Pengguna:</strong> <?php echo htmlspecialchars($profil_user_id); ?></p>
                    <p><strong>Nama Pengguna:</strong> <?php echo htmlspecialchars($profil_username); ?></p>
                <?php endif; ?>
            </div>

        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Mitigasi</h4>
        <p class="text-gray-300">Aplikasi tidak lagi memercayai input dari URL (<code>$_GET['id']</code>) untuk menentukan data siapa yang akan ditampilkan. Sebaliknya, aplikasi secara paksa menggunakan ID pengguna yang tersimpan di sesi (<code>$_SESSION['user_id']</code>) setelah login. Ini memastikan pengguna *hanya* dapat melihat data milik mereka sendiri.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>