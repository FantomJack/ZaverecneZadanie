-- phpMyAdmin SQL Dump
-- version 5.2.1deb1+jammy2
-- https://www.phpmyadmin.net/
--
-- Hostiteľ: localhost:3306
-- Čas generovania: Sun 19.Máj 2024, 10:23
-- Verzia serveru: 8.0.36-0ubuntu0.22.04.1
-- Verzia PHP: 8.3.3-1+ubuntu22.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáza: `zaverecne`
--

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `questions`
--

CREATE TABLE `questions` (
  `id` bigint UNSIGNED NOT NULL,
  `owner_id` int UNSIGNED DEFAULT NULL,
  `subject_id` int UNSIGNED DEFAULT NULL,
  `text` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(5) COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('OPEN','CLOSED') COLLATE utf8mb4_general_ci NOT NULL,
  `is_wordmap` enum('Y','N') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `multiple_answers` enum('Y','N') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N',
  `is_active` enum('Y','N') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Y',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `questions`
--

INSERT INTO `questions` (`id`, `owner_id`, `subject_id`, `text`, `code`, `type`, `is_wordmap`, `multiple_answers`, `is_active`, `created_at`, `closed_at`) VALUES
(9, NULL, NULL, 'testujem navratnost qrcodu', 'THm5F', 'CLOSED', 'N', 'N', 'N', '2024-05-15 16:07:05', NULL),
(10, NULL, NULL, 'testujem navratnost qrcodu', 'er9DE', 'CLOSED', 'N', 'N', 'Y', '2024-05-15 16:09:42', NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `responses`
--

CREATE TABLE `responses` (
  `id` bigint UNSIGNED NOT NULL,
  `batch_id` int UNSIGNED NOT NULL,
  `answer` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `votes` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `responses`
--

INSERT INTO `responses` (`id`, `batch_id`, `answer`, `votes`) VALUES
(15, 6, 'test', 0),
(16, 6, 'tesdsa', 0),
(17, 6, 'tesdsadsa', 0);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `response_batches`
--

CREATE TABLE `response_batches` (
  `id` int UNSIGNED NOT NULL,
  `question_id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `backup_date` datetime DEFAULT NULL,
  `number` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `response_batches`
--

INSERT INTO `response_batches` (`id`, `question_id`, `name`, `backup_date`, `number`) VALUES
(6, 10, 'New batch', NULL, NULL);

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `subjects`
--

CREATE TABLE `subjects` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `name`) VALUES
(2, 'B-MAT2', 'Matematika 2');

-- --------------------------------------------------------

--
-- Štruktúra tabuľky pre tabuľku `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `login` varchar(32) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(256) COLLATE utf8mb4_general_ci NOT NULL,
  `admin_priv` enum('Y','N') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Sťahujem dáta pre tabuľku `users`
--

INSERT INTO `users` (`id`, `email`, `login`, `password`, `admin_priv`) VALUES
(18, 'xkaras@stuba.sk', 'xkaras', '$argon2id$v=19$m=65536,t=4,p=1$dUNzTTRXWm4zZ0RPSUYuQQ$ct9X7pcXaNrBpHXACeytj+YgQ2RtiCnZL1DrJClkoR0', 'N'),
(21, 'belanondrej@gmail.com', 'belano', '$argon2id$v=19$m=65536,t=4,p=1$RTIzVS5LS3NSVDBON3A2dQ$RpH98qPZClbgum7vSlCq7wQqTX3yohkxEafNpU4Y/MU', 'N');

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `code_2` (`code`),
  ADD KEY `owner_id` (`owner_id`,`subject_id`),
  ADD KEY `questions_ibfk_2` (`subject_id`);

--
-- Indexy pre tabuľku `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexy pre tabuľku `response_batches`
--
ALTER TABLE `response_batches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexy pre tabuľku `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexy pre tabuľku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pre tabuľku `responses`
--
ALTER TABLE `responses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pre tabuľku `response_batches`
--
ALTER TABLE `response_batches`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pre tabuľku `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pre tabuľku `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Obmedzenie pre exportované tabuľky
--

--
-- Obmedzenie pre tabuľku `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Obmedzenie pre tabuľku `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `response_batches` (`id`) ON DELETE CASCADE;

--
-- Obmedzenie pre tabuľku `response_batches`
--
ALTER TABLE `response_batches`
  ADD CONSTRAINT `response_batches_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
