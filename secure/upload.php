<?php
require_once '../config.php';

// PERLINDUNGAN SESI
if (!isset($_SESSION['auth_token']) || !isset($_SESSION['loggedin_user'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/header.php';

$upload_message = '';
$uploaded_file_url = '';

if (isset($_POST['upload_sec'])) {
    $target_dir = "../uploads/";
    
    // MITIGASI 1: Cek ekstensi file (Whitelist)
    $file_extension = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));
    $allowed_extensions = ["jpg", "jpeg", "png", "gif"];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $upload_message = "Error: Tipe file tidak diizinkan. Hanya JPG, JPEG, PNG, & GIF.";
    } else {
        // MITIGASI 2: Cek tipe MIME asli
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check === false) {
             $upload_message = "Error: File bukan gambar.";
        } else {
            // MITIGASI 3: Buat nama file baru yang unik
            $new_filename = uniqid('img_sec_') . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;

            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $upload_message = "File berhasil diunggah dengan aman.";
                $uploaded_file_url = "uploads/" . $new_filename;
            } else {
                $upload_message = "Error: Terjadi kesalahan saat mengunggah file.";
            }
        }
    }
}
?>

<title>Lab Upload (Aman)</title>

<section id="lab-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <a href="../index.php" class="mb-6 text-blue-400 hover:text-blue-300 inline-block">&larr; Kembali ke Menu</a>
    
    <h2 class="text-3xl font-bold mb-2">File Upload Injection</h2>
    <p class="text-gray-300 mb-6">Mengunggah file berbahaya (misal: PHP shell) ke server.</p>

    <!-- Navigasi Tab -->
    <div class="flex border-b border-gray-700 mb-6">
        <a href="../vulnerable/upload.php" class="py-2 px-6 text-gray-400 hover:text-gray-200">Versi Rentan (/vulnerable/)</a>
        <a href="upload.php" class="py-2 px-6 tab-active">Versi Aman (/secure/)</a>
    </div>

    <!-- Konten Tab Aman -->
    <div id="secure-pane">
        <p class="text-green-400 bg-green-900/30 p-3 rounded-md border border-green-500 mb-4">
            <strong>Mitigasi:</strong> Validasi ketat diterapkan (ekstensi, tipe MIME, nama file unik).
        </p>
        <div class="mb-4 p-4 border border-gray-700 rounded-lg">
            <form method="POST" action="upload.php" enctype="multipart/form-data">
                <label for="fileToUpload" class="block mb-2">Pilih file untuk diunggah:</label>
                <input type="file" name="fileToUpload" id="fileToUpload" class="block w-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500">
                
                <button type="submit" name="upload_sec" class="mt-4 p-2 px-6 bg-green-600 hover:bg-green-500 rounded-md text-white">Upload (Aman)</button>
                 <p class="mt-4 text-sm text-gray-400">Coba unggah file <code>shell.php</code>. Unggahan akan ditolak.</p>
            </form>
            
            <?php if ($upload_message): ?>
            <div class="console-output mt-4 <?php echo ($uploaded_file_url) ? 'text-green-400' : 'text-red-400'; ?>">
                <p><?php echo $upload_message; ?></p>
                <?php if ($uploaded_file_url): ?>
                    <p>Link file: <a href="../<?php echo htmlspecialchars($uploaded_file_url); ?>" target="_blank" class="text-blue-400 underline"><?php echo htmlspecialchars($uploaded_file_url); ?></a></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <h4 class="text-lg font-semibold mt-6 mb-2">Penjelasan Mitigasi</h4>
        <p class="text-gray-300">1. <strong>Whitelist Ekstensi:</strong> Hanya mengizinkan ekstensi gambar yang aman (jpg, png, dll).<br>2. <strong>Validasi Tipe MIME:</strong> Mengecek isi file (menggunakan `getimagesize`) untuk memastikan itu benar-benar gambar.<br>3. <strong>Nama File Unik:</strong> Menyimpan file dengan nama yang dibuat server (<code>uniqid()</code>) untuk mencegah konflik dan serangan terkait nama file.</p>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>