<?php
// Mulai sesi PHP di setiap halaman.
// Ini HARUS menjadi baris paling pertama sebelum output HTML apapun.
session_start();

// --- TAMBAHKAN BLOK BARU INI ---
// Tentukan BASE_URL secara dinamis
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
// Dapatkan path skrip, ganti backslash dengan slash
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
// Hapus /includes, /vulnerable, or /secure dari akhir path
$base_dir = preg_replace('~/(includes|vulnerable|secure)$~i', '', $script_path);
// Jika kita di root, $base_dir bisa jadi '/' atau kosong, kita handle itu
if ($base_dir === '/' || $base_dir === DIRECTORY_SEPARATOR) {
    $base_dir = '';
}
define('BASE_URL', $protocol . '://' . $host . $base_dir);
// --- AKHIR BLOK TAMBAHAN ---


// --- HAPUS BLOK INI ---
// // Inisialisasi "database" pengguna di dalam sesi jika belum ada.
// // Ini adalah pengganti database MySQL kita untuk demo.
// if (!isset($_SESSION['userDB'])) {
//     $_SESSION['userDB'] = [];
// }
// --- AKHIR BLOK HAPUS ---

// --- TAMBAHKAN BLOK BARU INI ---
// Konfigurasi Koneksi Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'web_vuln_lab_db');
define('DB_USER', 'root'); // Pengguna default XAMPP
define('DB_PASS', '');     // Password default XAMPP (kosong)

// Buat koneksi PDO (PHP Data Objects)
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set error mode ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Tampilkan pesan error jika koneksi gagal
    die("ERROR: Tidak dapat terhubung ke database. " . $e->getMessage());
}
// --- AKHIR BLOK TAMBAHAN ---
?>