-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Jun 2024 pada 09.40
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookmyspace`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `invoice` varchar(255) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration_type` enum('jam','hari','bulan') DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `status` enum('menunggu','diterima','ditolak','selesai','dibatalkan') DEFAULT NULL,
  `payment_status` enum('belum_dibayar','sudah_dibayar') DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `invoice`, `start_time`, `end_time`, `duration_type`, `total_price`, `status`, `payment_status`, `payment_date`, `created_at`) VALUES
(12, 9, 1, 'INV/20240621/REGULER/8012083432', '2024-06-21 14:38:00', '2024-06-23 14:38:00', 'hari', 300000.00, 'selesai', 'sudah_dibayar', '2024-06-21', '2024-06-21 14:38:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_name` varchar(255) DEFAULT NULL,
  `category` enum('reguler','vip','vvip') DEFAULT NULL,
  `description` text NOT NULL,
  `facilities` text DEFAULT NULL,
  `base_price_hour` decimal(10,2) DEFAULT NULL,
  `base_price_day` decimal(10,2) DEFAULT NULL,
  `base_price_month` decimal(10,2) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_name`, `category`, `description`, `facilities`, `base_price_hour`, `base_price_day`, `base_price_month`, `stock`, `image`, `created_at`) VALUES
(1, 'Reguler', 'reguler', 'Ruangan Reguler dirancang untuk memenuhi kebutuhan kerja sehari-hari Anda dengan suasana yang nyaman dan fungsional. Ruangan ini dilengkapi dengan meja dan kursi ergonomis yang dapat meningkatkan produktivitas Anda. Ruangan ini juga memiliki pencahayaan alami yang cukup serta fasilitas dasar seperti Wi-Fi, stop kontak, dan AC yang dapat memastikan kenyamanan selama Anda bekerja. Ruangan Reguler cocok untuk para pekerja lepas, pelajar, atau profesional yang membutuhkan ruang kerja sederhana namun efektif.', 'Wi-Fi cepat, meja dan kursi ergonomis, pencahayaan alami, dan akses ke pantry bersama.', 25000.00, 150000.00, 2000000.00, 49, 'assets/img/regular.jpg', '2024-06-19 09:42:26'),
(2, 'VIP', 'vip', 'Ruangan VIP menawarkan pengalaman kerja yang lebih mewah dan eksklusif dengan fasilitas yang lebih lengkap. Selain meja dan kursi ergonomis, ruangan ini dilengkapi dengan fasilitas tambahan seperti mesin kopi pribadi, layar monitor besar untuk presentasi atau pekerjaan visual, serta kursi yang lebih nyaman. Pencahayaan dan interior ruangan VIP didesain untuk memberikan suasana yang lebih profesional dan elegan. Ruangan ini ideal untuk eksekutif, tim kecil, atau para profesional yang menginginkan privasi dan kenyamanan lebih saat bekerja.', 'Wi-Fi cepat, meja dan kursi ergonomis, pencahayaan alami, akses ke pantry bersama, ruang meeting pribadi, dan layanan minuman gratis.', 50000.00, 300000.00, 4000000.00, 25, 'assets/img/vip.jpg', '2024-06-18 20:37:32'),
(3, 'VVIP', 'vvip', 'Ruangan VVIP adalah pilihan terbaik bagi mereka yang mencari ruang kerja dengan fasilitas premium dan layanan eksklusif. Ruangan ini tidak hanya dilengkapi dengan perabotan kantor mewah dan ergonomis, tetapi juga menyediakan fasilitas tambahan seperti ruang pertemuan pribadi, sistem audio-visual canggih, dan layanan sekretaris jika diperlukan. Ruangan ini memiliki dekorasi interior yang elegan dan pencahayaan yang dapat diatur sesuai kebutuhan. Ruangan VVIP sangat cocok untuk para CEO, direktur, atau tim besar yang membutuhkan ruang kerja yang nyaman, mewah, dan privat untuk menyelesaikan pekerjaan penting dan rahasia perusahaan.', 'Wi-Fi cepat, meja dan kursi ergonomis, pencahayaan alami, akses ke pantry bersama, ruang meeting pribadi, layanan minuman gratis, layanan sekretaris, dan akses lounge eksklusif.', 75000.00, 500000.00, 6000000.00, 10, 'assets/img/vvip.jpg', '2024-06-18 20:38:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_images`
--

CREATE TABLE `room_images` (
  `image_id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `room_images`
--

INSERT INTO `room_images` (`image_id`, `room_id`, `image_path`, `created_at`) VALUES
(1, 1, 'assets/img/reguler1.jpg', '2024-06-19 09:32:34'),
(2, 1, 'assets/img/regular2.jpg', '2024-06-19 09:32:34'),
(3, 1, 'assets/img/regular3.jpeg', '2024-06-19 09:32:34'),
(4, 1, 'assets/img/regular4.jpg', '2024-06-19 09:32:34'),
(5, 2, 'assets/img/vip1.jpg', '2024-06-19 10:51:15'),
(6, 2, 'assets/img/vip2.jpg', '2024-06-19 10:51:15'),
(7, 2, 'assets/img/vip3.jpg', '2024-06-19 10:51:15'),
(8, 2, 'assets/img/vip4.jpg', '2024-06-19 10:51:15'),
(9, 3, 'assets/img/vvip1.jpg', '2024-06-19 10:58:50'),
(10, 3, 'assets/img/vvip2.jpg', '2024-06-19 10:58:50'),
(11, 3, 'assets/img/vvip3.png', '2024-06-19 10:58:50'),
(12, 3, 'assets/img/vvip4.jpg', '2024-06-19 10:58:50');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL,
  `user_image` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `email`, `role`, `user_image`, `created_at`) VALUES
(8, 'Admin', 'admin', '$2y$10$LGNxgyfRBfisOiobiTF3be6vfZuMOJCziEDXZ3FWuMxcTla8Dtnn.', 'admin@gmail.com', 'admin', 'assets/img/user/admin.jpg', '2024-06-19 15:06:01'),
(9, 'Raihan Ade Purnomo', 'raihan', '$2y$10$Gla2Nd46yHwq8jpATDahlOPzcTIz8wycMPBi3JlI5H9IL/hZS.BxG', 'raihanadepurnomo123@gmail.com', 'user', 'assets/img/user/raihan.jpg', '2024-06-21 14:37:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indeks untuk tabel `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `room_images`
--
ALTER TABLE `room_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Ketidakleluasaan untuk tabel `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
