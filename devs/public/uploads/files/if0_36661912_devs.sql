-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: sql104.infinityfree.com
-- Üretim Zamanı: 19 Eki 2024, 04:10:39
-- Sunucu sürümü: 10.6.19-MariaDB
-- PHP Sürümü: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `if0_36661912_devs`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `admin_logs`
--

CREATE TABLE `admin_logs` (
  `id` int(11) UNSIGNED NOT NULL,
  `admin_id` int(11) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `token` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `created_at`) VALUES
(1, 2, '69b6c20f02d6531a2bf2b6e0dc9022b8d4e3438b64b87c6e990c53ecb168f601cbe95dad2144e5aa6f740de0731e2af71eee', '2024-09-22 10:26:12'),
(2, 2, '45055513ec5f9d98f0669f9a8fb086f730ada3ce4914f86aefc05f523465cb622a278cfa79dc2395d034277db07a9f85d598', '2024-09-22 10:40:00'),
(3, 2, '28893e08414cd0dc1cf88378d7827d1264e2a9e51bed4858941cf6d3c7a4668b945255d0cb7517c92b6ad00074cb0e6ad12b', '2024-09-22 10:56:54'),
(4, 2, '61c7fc6c267d5dfe4f587255ab2e574588515b919eec474fcbac9f9febf3aed885f4724181cd3dcf221ada0e816d2409413e', '2024-09-22 10:58:10'),
(5, 2, 'ec2bb363b7e81f2403bb389326b2a7bc1155f5dbd61b892e93f39bb8ca4f4782ac55ca548a7f8e5a5cb9e89863e616f96df6', '2024-09-22 10:58:14'),
(6, 2, '5d944e8be905724e90865aa7094e1b6676573b59bdd6eb808e0c5d310803a2dac3b51176e033387c3dd0ec26713be4a0bc95', '2024-09-22 10:58:26');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `wid` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `auid` int(11) UNSIGNED NOT NULL,
  `vid` varchar(255) NOT NULL,
  `images` text DEFAULT NULL,
  `type` enum('workshop','game','app') NOT NULL,
  `genre` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `projects`
--

CREATE TABLE `projects` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('workshop','game','app') NOT NULL,
  `genre` varchar(255) NOT NULL,
  `images` text DEFAULT NULL,
  `download_link` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `projects`
--

INSERT INTO `projects` (`id`, `user_id`, `name`, `description`, `type`, `genre`, `images`, `download_link`, `category`, `created_at`) VALUES
(7, 2, 'Celer', 'celer', '', 'browser', 'https://artado.xyz/assest/img/artado-project-img/celer.webp', 'https://localhost', NULL, '2024-09-27 15:40:01'),
(10, 2, 'ArusOS', 'Artado Software tarafından geliştirilen açık kaynaklı ve özgür yazılım GNU işletim sistemi.', 'workshop', 'İşletim Sistemi', 'https://artado.xyz/assest/img/artado-project-img/arus.webp', 'https://artado.xyz', NULL, '2024-09-27 16:00:26'),
(12, 11, 'Artado', 'ArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtadoArtado', '', 'Artado', 'https://artado.xyz/assest/img/artado-project-img/celer.webp', 'https://github.com/Artado-Project/celerbrowser', NULL, '2024-09-27 17:12:58'),
(15, 2, 'merhaba', 'merhaba merhaba merhaba merhaba merhaba merhaba merhaba', '', 'merhaba', 'https://artado.xyz/assest/img/artado-project-img/celer.webp', 'https://localhost', NULL, '2024-09-27 17:19:57'),
(16, 2, 'merhaba', 'merhaba merhaba merhaba merhaba merhaba merhaba merhaba', '', 'merhaba', 'https://artado.xyz/assest/img/artado-project-img/celer.webp', 'https://localhost', NULL, '2024-09-27 17:20:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `publisher_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `avatar` text DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `publisher_name`, `description`, `website`, `avatar`, `role`, `created_at`, `is_verified`, `verification_token`) VALUES
(2, 'basaransemih@tuta.io', '$2y$10$DjTndBXRnh5vP1hceiEx.Ob2Bo4j.5a1RionEy.7xIYhnhA06HSYq', 'Sxinar', 'Semih', 'https://sxi.is-a.dev', 'uploads/2.png', 'admin', '2024-09-21 16:33:56', 0, NULL),
(10, 'basaransemih@tuta.io', '$2y$10$nvshHOKh4LOlsJ5wQoiC1ebMUn3AFmZITWDqSytlbITCWqqF6IsIO', 'semih', NULL, 'semih', NULL, 'user', '2024-09-22 11:04:07', 0, NULL),
(11, 'artado@artado.xyz', '$2y$10$AC0I7vBkC8lKAl0R1zgOQO2KkGLtkB8NHKKpls29NTGi0AE2bRMq2', 'admin', NULL, '', NULL, 'admin', '2024-09-22 16:44:38', 0, NULL);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Tablo için indeksler `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`wid`),
  ADD KEY `auid` (`auid`);

--
-- Tablo için indeksler `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `admin_logs`
--
ALTER TABLE `admin_logs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `wid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `admin_logs`
--
ALTER TABLE `admin_logs`
  ADD CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`auid`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
