<?php
require_once '../config.php';
require_once '../includes/header.php';

$upload_message = '';
$uploaded_file_url = '';

if (isset($_POST['upload_vuln'])) {
    $target_dir = "../uploads/"; // Gunakan path relatif dari file ini
    // KERENTANAN: Nama file diambil langsung dari input pengguna
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $upload_message = "File '" . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . "' berhasil diunggah.";
        // Memberi link ke file yang diunggah
        $uploaded_file_url = "uploads/" . basename($_FILES["fileToUpload"]["name"]);
    } else {
        $upload_message = "Error: Terjadi kesalahan saat mengunggah file.";
    }
}
?>

<title>Lab Upload (Rentan)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">File Upload Injection</h2>
    <p class="text-gray-300 mb-6">Mengunggah file berbahaya (misal: PHP shell) ke server.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="upload.php" class="py-2 px-6 tab-active">Versi Rentan (/vulnerable/)</a>
        <a href="../secure/upload.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Rentan -->
    <div id="vulnerable-pane">
        <p class="text-yellow-400 bg-yellow-900/30 p-3 rounded-md border border-yellow-500 mb-4">
            <strong>Peringatan:</strong> Formulir ini tidak memiliki validasi tipe file.
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <form method="POST" action="upload.php" enctype="multipart/form-data">
                <label for="fileToUpload" class="block mb-2">Pilih file untuk diunggah:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                
                <button type="submit" name="upload_vuln" class="mt-4 p-2 px-6 bg-red-600 hover:bg-red-500 rounded-md text-white">Upload (Rentan)</button>
                <p class="mt-4 text-sm text-gray-400">Coba unggah file <code>shell.php</code> sederhana, lalu akses file tersebut.</p>
            </form>
            
            <?php if ($upload_message): ?>
            <div class="console-output mt-4">
                <p><?php echo $upload_message; ?></p>
                <?php if ($uploaded_file_url): ?>
                    <p>Link file: <a href="../<?php echo htmlspecialchars($uploaded_file_url); ?>" target="_blank" class="text-blue-400 underline"><?php echo htmlspecialchars($uploaded_file_url); ?></a></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Kerentanan</h4>
        <p class="text-gray-300">Server menerima file tanpa memvalidasi ekstensinya (misal: <code>.php</code>, <code>.phtml</code>) atau tipe MIME-nya. Penyerang dapat mengunggah web shell dan mengeksekusi kode arbitrer di server.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>