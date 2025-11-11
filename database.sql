-- Membuat database (Jika Anda belum membuatnya secara manual)
CREATE DATABASE IF NOT EXISTS `web_vuln_lab_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `web_vuln_lab_db`;

-- --------------------------------------------------------

--
-- Tabel untuk login APLIKASI UTAMA (Aman)
-- Menyimpan password yang di-hash
--
CREATE TABLE `users_app` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabel untuk LAB SQL INJECTION (Sengaja Dibuat Rentan)
-- Menyimpan password sebagai plain text untuk demo serangan.
--
CREATE TABLE `users_vuln` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Data contoh untuk lab SQLi
--
INSERT INTO `users_vuln` (`id`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', 'password123', 'Administrator Utama'),
(2, 'budi', 'budiGanteng', 'Budi Hartono'),
(3, 'citra', 'rahasia', 'Citra Lestari');

-- --------------------------------------------------------

--
-- Tabel untuk LAB XSS (Rentan)
--
CREATE TABLE `comments_vuln` (
  `id` int(11) NOT NULL,
  `comment_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabel untuk LAB XSS (Aman)
--
CREATE TABLE `comments_sec` (
  `id` int(11) NOT NULL,
  `comment_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Menambahkan Primary Keys
--
ALTER TABLE `users_app`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

ALTER TABLE `users_vuln`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `comments_vuln`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `comments_sec`
  ADD PRIMARY KEY (`id`);

--
-- Menambahkan AUTO_INCREMENT
--
ALTER TABLE `users_app`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users_vuln`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `comments_vuln`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `comments_sec`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;