<?php
require_once 'config.php';
require_once 'includes/header.php';
?>

<title>Menu Utama - Web Vuln Lab</title>

<!-- Tampilan Menu Utama -->
<section id="menu-view" class="bg-gray-800 p-6 rounded-lg shadow-xl">
    <h2 class="text-2xl font-semibold mb-6 border-b border-gray-700 pb-3">Daftar Modul Lab</h2>
    <p class="mb-6 text-gray-300">Silakan pilih modul di bawah ini. Anda akan mulai dari versi rentan. Untuk melihat versi aman, Anda harus <a href="login.php" class="text-blue-400 hover:underline">login</a> terlebih dahulu.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        
        <a href="vulnerable/sqli.php" class="p-6 bg-gray-700 rounded-lg hover:bg-blue-600 hover:shadow-lg transition duration-300 text-left">
            <h3 class="text-xl font-semibold mb-2">SQL Injection (SQLi)</h3>
            <p class="text-gray-300">Menyuntikkan query SQL berbahaya melalui input pengguna.</p>
        </a>
        
        <a href="vulnerable/xss.php" class="p-6 bg-gray-700 rounded-lg hover:bg-blue-600 hover:shadow-lg transition duration-300 text-left">
            <h3 class="text-xl font-semibold mb-2">Cross-Site Scripting (XSS)</h3>
            <p class="text-gray-300">Menyuntikkan skrip berbahaya ke halaman web yang dilihat pengguna lain.</p>
        </a>

        <!-- MODUL BARU DITAMBAHKAN -->
        <a href="vulnerable/upload.php" class="p-6 bg-gray-700 rounded-lg hover:bg-blue-600 hover:shadow-lg transition duration-300 text-left">
            <h3 class="text-xl font-semibold mb-2">File Upload Injection</h3>
            <p class="text-gray-300">Mengunggah file berbahaya (misal: PHP shell) ke server.</p>
        </a>
        
        <a href="vulnerable/profil.php?id=1" class="p-6 bg-gray-700 rounded-lg hover:bg-blue-600 hover:shadow-lg transition duration-300 text-left">
            <h3 class="text-xl font-semibold mb-2">Broken Access Control (BAC)</h3>
            <p class="text-gray-300">Mengakses data pengguna lain dengan mengubah parameter ID.</p>
        </a>
        <!-- AKHIR MODUL BARU -->

    </div>
</section>

<?php
require_once 'includes/footer.php';
?>