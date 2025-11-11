<?php
require_once '../config.php';

// PERLINDUNGAN SESI
if (!isset($_SESSION['auth_token']) || !isset($_SESSION['loggedin_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/header.php';

// --- HAPUS BLOK INI ---
// // "Database" komentar untuk XSS
// if (!isset($_SESSION['xss_comments_sec'])) {
//     $_SESSION['xss_comments_sec'] = [];
// }
//
// if (isset($_POST['post_sec']) && !empty($_POST['xss-comment-sec'])) {
//     $_SESSION['xss_comments_sec'][] = $_POST['xss-comment-sec'];
// }
// --- AKHIR BLOK HAPUS ---

// --- TAMBAHKAN BLOK BARU INI ---
// Logika Lab XSS Aman dengan Database
if (isset($_POST['post_sec']) && !empty($_POST['xss-comment-sec'])) {
    $comment = $_POST['xss-comment-sec'];
    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO comments_sec (comment_text) VALUES (?)");
    $stmt->execute([$comment]);
}

// Ambil semua komentar dari database
$comments_sec_stmt = $pdo->query("SELECT comment_text FROM comments_sec ORDER BY id DESC");
$comments_sec = $comments_sec_stmt->fetchAll(PDO::FETCH_ASSOC);
// --- AKHIR BLOK TAMBAHAN ---
?>

<title>Lab XSS (Aman)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">Cross-Site Scripting (XSS)</h2>
    <p class="text-gray-300 mb-6">Menyuntikkan skrip sisi klien (JavaScript) ke halaman web.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="../vulnerable/xss.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Rentan (/vulnerable/)</a>
        <a href="xss.php" class="py-2 px-6 tab-active">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Aman -->
    <div id="secure-pane">
        <p class="text-green-400 bg-green-900/30 p-3 rounded-md border border-green-500 mb-4">
            <strong>Mitigasi:</strong> Anda sedang melihat versi aman (Hanya untuk pengguna terotentikasi).
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <form method="POST" action="xss.php">
                <label for="xss-comment-sec" class="block mb-2">Tinggalkan komentar:</label>
                <textarea id="xss-comment-sec" name="xss-comment-sec" rows="3" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md"></textarea>
                <button type="submit" name="post_sec" class="mt-2 p-2 px-6 bg-green-600 hover:bg-green-500 rounded-md text-white">Posting (Aman)</button>
                <p class="mt-4 text-sm text-gray-400">Coba masukkan: <code>&lt;script&gt;alert('XSS Dijebol!')&lt;/script&gt;</code></p>
            </form>
            
            <h4 class="text-lg font-semibold mt-6 mb-2">Komentar yang Diposting:</h4>
            <div class="p-4 border border-gray-600 rounded-md min-h-[100px] bg-gray-900 space-y-3">
                <?php foreach ($comments_sec as $comment_row): // Ganti variabel loop ?>
                    <div class="p-3 bg-gray-700 rounded-md border border-green-500">
                        <?php echo htmlspecialchars($comment_row['comment_text']); // <-- MITIGASI: Membersihkan output ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Mitigasi</h4>
        <p class="text-gray-300">Mitigasi dilakukan dengan menggunakan <code>htmlspecialchars()</code>. Fungsi ini mengubah karakter khusus HTML (seperti <code>&lt;</code> dan <code>&gt;</code>) menjadi entitas HTML (<code>&amp;lt;</code> dan <code>&amp;gt;</code>). Browser akan menampilkannya sebagai teks, bukan mengeksekusinya sebagai kode.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>