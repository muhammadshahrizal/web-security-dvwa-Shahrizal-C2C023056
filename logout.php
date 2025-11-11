<?php
require_once 'config.php';

// Hapus semua variabel sesi
session_unset();

// Hancurkan sesi
session_destroy();

// Redirect kembali ke halaman utama
header("Location: index.php");
exit;
?>