<?php
require_once '../config.php';
require_once '../includes/header.php';

$profil_user_id = null;
$profil_username = null;
$error_message = '';

// KERENTANAN: Mengambil ID langsung dari parameter GET tanpa otorisasi
if (isset($_GET['id'])) {
    $id_to_view = $_GET['id'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, username FROM users_app WHERE id = ?");
        $stmt->execute([$id_to_view]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            $profil_user_id = $user_data['id'];
            $profil_username = $user_data['username'];
        } else {
            $error_message = "Error: Pengguna dengan ID tersebut tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
} else {
    $error_message = "Silakan berikan 'id' di URL (misal: ?id=1)";
}
?>

<title>Lab BAC (Rentan)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">Broken Access Control (BAC)</h2>
    <p class="text-gray-300 mb-6">Mengakses data pengguna lain dengan mengubah parameter ID.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="profil.php?id=1" class="py-2 px-6 tab-active">Versi Rentan (/vulnerable/)</a>
        <a href="../secure/profil.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Rentan -->
    <div id="vulnerable-pane">
        <p class="text-yellow-400 bg-yellow-900/30 p-3 rounded-md border border-yellow-500 mb-4">
            <strong>Peringatan:</strong> Halaman ini tidak mengecek siapa yang sedang login.
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <p class="mb-4">Coba ubah parameter <code>?id=...</code> di URL bar browser Anda ke ID pengguna lain (misal: 1, 2, 3, dst.)</p>
            
            <div class="console-output">
                <?php if ($error_message): ?>
                    <p class="text-red-400"><?php echo $error_message; ?></p>
                <?php elseif ($profil_user_id): ?>
                    <p class="text-xl font-bold">Menampilkan Profil untuk Pengguna:</p>
                    <p><strong>ID Pengguna:</strong> <?php echo htmlspecialchars($profil_user_id); ?></p>
                    <p><strong>Nama Pengguna:</strong> <?php echo htmlspecialchars($profil_username); ?></p>
                <?php endif; ?>
            </div>

        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Kerentanan</h4>
        <p class="text-gray-300">Aplikasi menampilkan data profil hanya berdasarkan parameter <code>id</code> dari URL. Aplikasi gagal memverifikasi apakah pengguna yang sedang login (dari <code>$_SESSION</code>) berhak melihat profil tersebut. Penyerang dapat melihat data pengguna lain hanya dengan menebak ID mereka.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>