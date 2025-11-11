# web-security-dvwa-Shahrizal-C2C023056

Web Vulnerability Lab (Demo PHP)

Sebuah aplikasi web sederhana yang dibuat dalam PHP untuk mendemonstrasikan kerentanan web umum dan teknik mitigasinya, mirip dengan konsep DVWA (Damn Vulnerable Web App).

Aplikasi ini terhubung ke database MySQL dan mencakup beberapa modul lab, masing-masing dengan versi "Rentan" dan "Aman".

Struktur Proyek

/ (root): Berisi file inti seperti index.php (menu), config.php (koneksi DB), login.php (autentikasi), dan database.sql.

/includes/: Berisi file template header.php dan footer.php.

/vulnerable/: Berisi versi rentan dari setiap lab (SQLi, XSS, BAC, File Upload).

/secure/: Berisi versi aman/mitigasi dari setiap lab, yang hanya dapat diakses setelah login.

/uploads/: Folder (kosong) tempat file yang diunggah akan disimpan.

Fitur Utama

Koneksi Database: Menggunakan PDO (PHP Data Objects) untuk koneksi ke database MySQL, memungkinkan penggunaan prepared statements.

Autentikasi Aman:

Registrasi menggunakan password_hash() untuk menyimpan kata sandi dengan aman.

Login menggunakan password_verify() untuk memvalidasi kata sandi.

Perlindungan Halaman: Semua halaman di folder /secure/ dilindungi dan akan mengarahkan pengguna ke halaman login jika mereka belum terautentikasi (menggunakan $_SESSION['auth_token']).

Cara Instalasi (Lokal)

Web Server: Gunakan XAMPP atau server lokal serupa (Apache + MySQL).

Database:

Buka phpMyAdmin (biasanya http://localhost/phpmyadmin).

Buat database baru dengan nama web_vuln_lab_db.

Pilih database tersebut, pergi ke tab Import, dan unggah file database.sql dari proyek ini untuk membuat semua tabel.

File Proyek:

Salin semua file dan folder proyek ini ke dalam folder htdocs XAMPP Anda (misal: C:\xampp\htdocs\web-lab\).

Konfigurasi (Opsional):

Buka config.php. Pastikan DB_USER (default: 'root') dan DB_PASS (default: '') sesuai dengan pengaturan MySQL Anda.

Jalankan:

Buka browser Anda dan akses http://localhost/nama-folder-proyek/ (misal: http://localhost/web-lab/).

Analisis Kerentanan & Mitigasi

Berikut adalah penjelasan untuk setiap modul lab.

1. SQL Injection (SQLi)

Rentan (/vulnerable/sqli.php):

Kerentanan: String Concatenation (Penyambungan String). Query SQL dibuat dengan menggabungkan string mentah dari $_POST.

Kode Rentan:

$simulated_query = "SELECT * FROM users_vuln WHERE username = '" . $user . "' AND password = '" . $pass . "'";
$stmt = $pdo->query($simulated_query);


Serangan: Memasukkan admin'-- di field nama pengguna akan mengubah query menjadi ... WHERE username = 'admin'--' AND .... Tanda -- mengomentari sisa query, melewati pengecekan password dan berhasil login sebagai 'admin'.

Aman (/secure/sqli.php):

Mitigasi: Prepared Statements (Parameterized Queries).

Kode Aman:

$stmt = $pdo->prepare("SELECT * FROM users_vuln WHERE username = ? AND password = ?");
$stmt->execute([$user, $pass]);


Penjelasan: Database diperintahkan untuk menyiapkan template query terlebih dahulu (dengan tanda ? sebagai placeholder). Input pengguna ($user) kemudian dikirim secara terpisah. Database tidak pernah mengeksekusi input pengguna sebagai kode, melainkan hanya menggunakannya sebagai data untuk dicocokkan.

2. Cross-Site Scripting (XSS)

Rentan (/vulnerable/xss.php):

Kerentanan: Reflected XSS (atau Stored XSS dalam kasus ini) di mana input pengguna yang tidak disanitasi ditampilkan langsung ke HTML.

Kode Rentan:

<?php echo $comment_row['comment_text']; // <-- Output mentah ?>


Serangan: Memasukkan <script>alert('XSS')</script> akan disimpan di database. Saat halaman memuat komentar tersebut, browser akan mengeksekusi tag <script> dan menampilkan kotak peringatan.

Aman (/secure/xss.php):

Mitigasi: Output Escaping.

Kode Aman:

<?php echo htmlspecialchars($comment_row['comment_text']); // <-- Output dibersihkan ?>


Penjelasan: Fungsi htmlspecialchars() mengubah karakter khusus HTML menjadi entitas. <script> diubah menjadi &lt;script&gt;. Browser menafsirkan ini sebagai teks biasa untuk ditampilkan, bukan sebagai tag HTML untuk dieksekusi.

3. Broken Access Control (BAC)

Rentan (/vulnerable/profil.php):

Kerentanan: Aplikasi memercayai input pengguna dari URL ($_GET['id']) untuk menentukan data siapa yang harus ditampilkan, tanpa memeriksa apakah pengguna yang login berhak melihat data tersebut.

Kode Rentan:

$id_to_view = $_GET['id']; // <-- Mengambil ID dari URL
$stmt = $pdo->prepare("SELECT id, username FROM users_app WHERE id = ?");
$stmt->execute([$id_to_view]);


Serangan: Seorang pengguna yang login sebagai ID 1 dapat dengan mudah mengubah URL menjadi .../profil.php?id=2 untuk melihat data profil milik pengguna ID 2.

Aman (/secure/profil.php):

Mitigasi: Mengabaikan input yang tidak tepercaya (URL) dan hanya menggunakan data yang tepercaya dari sesi server.

Kode Aman:

$id_to_view = $_SESSION['user_id']; // <-- Mengambil ID HANYA dari Sesi
$stmt = $pdo->prepare("SELECT id, username FROM users_app WHERE id = ?");
$stmt->execute([$id_to_view]);


Penjelasan: Tidak peduli apa yang pengguna ketik di URL, halaman ini selalu mengambil ID pengguna dari $_SESSION['user_id'] yang diatur saat login. Ini memastikan pengguna hanya dapat melihat profil mereka sendiri.

4. Unrestricted File Upload

Rentan (/vulnerable/upload.php):

Kerentanan: Aplikasi menerima file apa adanya dan menggunakan nama file asli dari klien ($_FILES["fileToUpload"]["name"]) tanpa memvalidasi ekstensi atau tipe file.

Serangan: Penyerang dapat mengunggah file shell.php (sebuah web shell berbahaya). Jika mereka dapat menemukan lokasinya di server, mereka dapat mengeksekusinya untuk mengambil alih server.

Aman (/secure/upload.php):

Mitigasi: Validasi berlapis.

Penjelasan:

Whitelist Ekstensi: Hanya mengizinkan ekstensi yang aman (misal: jpg, png) dan menolak semua yang lain (termasuk php).

Validasi Tipe MIME: Menggunakan getimagesize() untuk memeriksa apakah file tersebut benar-benar gambar, bukan hanya file .php yang diubah namanya menjadi .jpg.

Nama File Unik: Membuat nama file baru yang unik di sisi server (menggunakan uniqid()) untuk mencegah serangan directory traversal atau penimpaan file.
