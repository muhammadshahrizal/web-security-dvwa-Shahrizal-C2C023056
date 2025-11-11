<?php
require_once '../config.php';
require_once '../includes/header.php';

// --- HAPUS BLOK INI ---
// // "Database" komentar untuk XSS, disimpan di sesi agar persisten
// if (!isset($_SESSION['xss_comments_vuln'])) {
//     $_SESSION['xss_comments_vuln'] = [];
// }
//
// if (isset($_POST['post_vuln']) && !empty($_POST['xss-comment-vuln'])) {
//     $_SESSION['xss_comments_vuln'][] = $_POST['xss-comment-vuln'];
// }
// --- AKHIR BLOK HAPUS ---

// --- TAMBAHKAN BLOK BARU INI ---
// Logika Lab XSS Rentan dengan Database
if (isset($_POST['post_vuln']) && !empty($_POST['xss-comment-vuln'])) {
    $comment = $_POST['xss-comment-vuln'];
    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO comments_vuln (comment_text) VALUES (?)");
    $stmt->execute([$comment]);
}

// Ambil semua komentar dari database
$comments_vuln_stmt = $pdo->query("SELECT comment_text FROM comments_vuln ORDER BY id DESC");
$comments_vuln = $comments_vuln_stmt->fetchAll(PDO::FETCH_ASSOC);
// --- AKHIR BLOK TAMBAHAN ---
?>

<title>Lab XSS (Rentan)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">Cross-Site Scripting (XSS)</h2>
    <p class="text-gray-300 mb-6">Menyuntikkan skrip sisi klien (JavaScript) ke halaman web.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="xss.php" class="py-2 px-6 tab-active">Versi Rentan (/vulnerable/)</a>
        <a href="../secure/xss.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Rentan -->
    <div id="vulnerable-pane">
        <p class="text-yellow-400 bg-yellow-900/30 p-3 rounded-md border border-yellow-500 mb-4">
            <strong>Peringatan:</strong> Ini adalah simulasi dari kode yang rentan.
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <form method="POST" action="xss.php">
                <label for="xss-comment-vuln" class="block mb-2">Tinggalkan komentar:</label>
                <textarea id="xss-comment-vuln" name="xss-comment-vuln" rows="3" class="w-full p-2 bg-gray-700 border border-gray-600 rounded-md"></textarea>
                <button type="submit" name="post_vuln" class="mt-2 p-2 px-6 bg-red-600 hover:bg-red-500 rounded-md text-white">Posting (Rentan)</button>
                <p class="mt-4 text-sm text-gray-400">Coba masukkan: <code>&lt;script&gt;alert('XSS Dijebol!')&lt;/script&gt;</code></p>
            </form>
            
            <h4 class="text-lg font-semibold mt-6 mb-2">Komentar yang Diposting:</h4>
            <div class="p-4 border border-gray-600 rounded-md min-h-[100px] bg-gray-900 space-y-3">
                <?php foreach ($comments_vuln as $comment_row): // Ganti variabel loop ?>
                    <div class="p-3 bg-gray-700 rounded-md border border-red-500">
                        <?php echo $comment_row['comment_text']; // <-- KERENTANAN: Output mentah ke HTML ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Kerentanan</h4>
        <p class="text-gray-300">Kerentanan terjadi karena `echo` menampilkan input pengguna secara langsung ke HTML. Browser akan mengeksekusi tag <code>&lt;script&gt;</code> yang dimasukkan.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>