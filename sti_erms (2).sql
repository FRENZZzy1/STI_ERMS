-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2026 at 03:35 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sti_erms`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `barcode` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `lab_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Good',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `barcode`, `name`, `description`, `lab_id`, `status`, `registered_at`) VALUES
(9, '11234', 'PC 10000', 'HDD 500', 504, 'Good', '2025-10-10 04:11:34'),
(10, '100002', 'PC2', 'HDD 500', 504, 'not functional', '2025-10-10 04:11:55'),
(11, '100003', 'PC1', 'HDD 500', 506, 'Good', '2025-10-10 04:12:28'),
(12, '100004', 'PC 2', 'HDD 500', 506, 'Good', '2025-10-10 04:13:01'),
(13, '1000012', 'PC 12', 'Wala', 507, 'Good', '2025-10-11 05:01:20'),
(14, '1000020000000000', 'PC 211111112', 'dasdasd', 517, 'Good', '2025-10-16 11:20:11'),
(15, '12', 'PC 100', 'burat', 504, 'Archived', '2025-10-28 12:06:28'),
(16, '131313', 'PC 200', 'dada', 504, 'Good', '2025-10-28 12:06:56'),
(17, '900', 'PC 900', 'dadsad', 504, 'Good', '2025-10-28 12:07:56'),
(18, '111111111111', 'PC111111111111111111111', 'dadas', 504, 'Good', '2025-10-28 12:10:20'),
(19, '11111111', '1212121', 'dasdas', 504, 'Good', '2025-10-28 13:45:21'),
(21, 'asdasdasda', 'sdasdadas', 'adsasda', 504, 'Good', '2025-10-28 13:45:46'),
(22, '1000013', 'PC 14', 'sadasda', 507, 'Good', '2025-10-28 14:04:18'),
(23, '111111111111111', 'asdasdasda', 'asdasdasdasd', 504, 'Good', '2025-11-10 06:31:55'),
(24, '98999999999999999999', 'asdasda', 'dasdasda', 504, 'Good', '2025-11-10 06:45:36'),
(29, 'ASDasdasdasd', 'asdasdasdasd', 'dasdasdasd', 504, 'Good', '2025-11-10 06:58:11'),
(30, '10000111111', 'pC10101', 'dadadadad', 506, 'Good', '2025-11-10 09:48:01'),
(31, '10000211', 'PC 1', 'dadadada', 508, 'Good', '2025-11-10 10:15:21'),
(32, 'dsfsdgdgh', 'PC4555', 'HDD10', 504, 'Good', '2025-11-15 03:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_history`
--

CREATE TABLE `equipment_history` (
  `history_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_history`
--

INSERT INTO `equipment_history` (`history_id`, `equipment_id`, `action`, `old_status`, `new_status`, `changed_by`, `changed_at`) VALUES
(120, 9, 'Removed', 'Good', 'Archived', 10, '2025-11-10 09:48:27'),
(121, 9, 'Restored', 'Archived', 'Good', 10, '2025-11-10 09:48:34'),
(122, 31, 'Removed', 'Good', 'Archived', 10, '2025-11-10 10:15:29'),
(123, 31, 'Restored', 'Archived', 'Good', 10, '2025-11-10 10:15:33'),
(124, 11, 'Reported', 'Good', 'missing', 66, '2025-11-11 21:15:50'),
(125, 12, 'Reported', 'Good', 'replacement', 66, '2025-11-11 21:16:16'),
(126, 11, 'Fixed', 'missing', 'Good', 65, '2025-11-11 21:18:18'),
(127, 12, 'Fixed', 'replacement', 'Good', 65, '2025-11-11 21:18:32'),
(128, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-12 00:28:31'),
(129, 10, 'Fixed', 'not functional', 'Good', 64, '2025-11-12 00:29:22'),
(130, 10, 'Reported', 'Good', 'not functional', 64, '2025-11-14 01:52:24'),
(131, 10, 'Reported', 'not functional', 'not functional', 64, '2025-11-14 01:52:44'),
(132, 10, 'Reported', 'not functional', 'not functional', 64, '2025-11-14 01:52:49'),
(133, 15, 'Removed', 'Good', 'Archived', 10, '2025-11-15 04:28:05'),
(134, 10, 'Reported', 'not functional', 'missing', 66, '2025-11-15 04:45:49'),
(135, 10, 'Reported', 'missing', 'replacement', 66, '2025-11-15 04:45:58'),
(136, 10, 'Fixed', 'replacement', 'Good', 69, '2025-11-15 04:48:36'),
(137, 11, 'Reported', 'Good', 'missing', 66, '2025-11-15 04:49:12'),
(138, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-15 04:49:23'),
(139, 10, 'Reported', 'not functional', 'replacement', 66, '2025-11-15 04:49:43'),
(140, 10, 'Reported', 'replacement', 'missing', 66, '2025-11-15 04:49:50'),
(141, 10, 'Reported', 'missing', 'replacement', 66, '2025-11-15 04:50:01'),
(142, 10, 'Reported', 'replacement', 'missing', 66, '2025-11-15 04:50:10'),
(143, 10, 'Fixed', 'missing', 'Good', 69, '2025-11-15 04:53:15'),
(144, 11, 'Fixed', 'missing', 'Good', 69, '2025-11-15 04:53:26'),
(145, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-15 04:57:02'),
(146, 11, 'Reported', 'Good', 'missing', 66, '2025-11-15 04:57:14'),
(147, 11, 'Reported', 'missing', 'missing', 66, '2025-11-15 04:57:27'),
(148, 10, 'Fixed', 'not functional', 'Good', 69, '2025-11-15 04:58:04'),
(149, 11, 'Fixed', 'missing', 'Good', 69, '2025-11-15 04:58:12'),
(150, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:03:38'),
(151, 11, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:03:54'),
(152, 12, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:04:13'),
(153, 10, 'Reported', 'not functional', 'missing', 66, '2025-11-15 05:04:37'),
(154, 10, 'Reported', 'missing', 'replacement', 66, '2025-11-15 05:05:22'),
(155, 10, 'Reported', 'replacement', 'not functional', 66, '2025-11-15 05:05:42'),
(156, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:08:35'),
(157, 12, 'Reported', 'not functional', 'replacement', 66, '2025-11-15 05:08:46'),
(158, 10, 'Fixed', 'not functional', 'Good', 64, '2025-11-15 05:09:11'),
(159, 12, 'Fixed', 'replacement', 'Good', 64, '2025-11-15 05:09:21'),
(160, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:11:59'),
(161, 12, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:12:12'),
(162, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:13:36'),
(163, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:18:37'),
(164, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:23:21'),
(165, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:23:36'),
(166, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:24:30'),
(167, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:24:49'),
(168, 10, 'Fixed', 'not functional', 'Good', 69, '2025-11-15 05:26:31'),
(169, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:27:04'),
(170, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:27:24'),
(171, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:27:39'),
(172, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:28:01'),
(173, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:28:10'),
(174, 12, 'Fixed', 'not functional', 'Good', 69, '2025-11-15 05:28:56'),
(175, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:29:28'),
(176, 10, 'Reported', 'not functional', 'replacement', 66, '2025-11-15 05:29:49'),
(177, 10, 'Fixed', 'replacement', 'Good', 69, '2025-11-15 05:31:30'),
(178, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:34:50'),
(179, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:34:58'),
(180, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:35:05'),
(181, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:35:12'),
(182, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:35:38'),
(183, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:35:50'),
(184, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:35:59'),
(185, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:36:05'),
(186, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:36:12'),
(187, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:36:24'),
(188, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:36:31'),
(189, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:36:37'),
(190, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:38:37'),
(191, 12, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:38:51'),
(192, 17, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:39:16'),
(193, 17, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:39:27'),
(194, 17, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:41:26'),
(195, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:41:37'),
(196, 12, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 05:42:01'),
(197, 13, 'Reported', 'Good', 'not functional', 66, '2025-11-15 05:42:18'),
(198, 9, 'Reported', 'Good', 'missing', 66, '2025-11-15 05:43:29'),
(199, 31, 'Reported', 'Good', 'missing', 66, '2025-11-15 05:43:59'),
(200, 22, 'Reported', 'Good', 'replacement', 66, '2025-11-15 05:44:21'),
(201, 10, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 06:04:44'),
(202, 11, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 06:05:12'),
(203, 17, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 06:05:22'),
(204, 13, 'Reported', 'not functional', 'not functional', 66, '2025-11-15 06:05:35'),
(205, 17, 'Fixed', 'not functional', 'Good', 64, '2025-11-22 03:13:15'),
(206, 13, 'Fixed', 'not functional', 'Good', 64, '2025-11-22 03:13:57'),
(207, 11, 'Fixed', 'not functional', 'Good', 64, '2025-11-22 03:14:15'),
(208, 11, 'Reported', 'Good', 'not functional', 64, '2025-11-22 03:14:55'),
(209, 10, 'Reported', 'not functional', 'replacement', 64, '2025-11-22 03:24:21'),
(210, 9, 'Reported', 'missing', 'missing', 64, '2025-11-22 04:19:21'),
(211, 9, 'Reported', 'missing', 'missing', 64, '2025-11-22 04:19:37'),
(212, 10, 'Fixed', 'replacement', 'Good', 64, '2025-11-22 04:28:37'),
(213, 10, 'Reported', 'Good', 'not functional', 66, '2025-11-23 05:48:49'),
(214, 9, 'Reported', 'Good', 'not functional', 66, '2025-11-23 05:53:04'),
(215, 10, 'Fixed', 'not functional', 'Good', 64, '2025-11-23 06:22:43'),
(216, 11, 'Fixed', 'not functional', 'Good', 64, '2025-11-23 06:25:45'),
(217, 10, 'Reported', 'Good', 'not functional', 64, '2025-11-23 06:25:54'),
(218, 10, 'Reported', 'not functional', 'replacement', 64, '2025-11-23 06:26:04'),
(219, 9, 'Fixed', 'Good', 'Good', 64, '2025-11-23 06:35:12'),
(220, 10, 'Fixed', 'replacement', 'Good', 64, '2025-11-23 06:35:24'),
(221, 10, 'Reported', 'Good', 'replacement', 64, '2025-11-23 06:56:06'),
(222, 10, 'Fixed', 'replacement', 'Good', 64, '2025-11-23 07:25:24'),
(223, 10, 'Reported', 'Good', 'not functional', 69, '2025-11-23 07:34:55'),
(224, 10, 'Fixed', 'not functional', 'Good', 64, '2025-11-23 07:36:18'),
(225, 9, 'Reported', 'Good', 'replacement', 64, '2025-11-23 07:43:54'),
(226, 10, 'Reported', 'Good', 'not functional', 69, '2025-11-23 07:44:53'),
(227, 10, 'Reported', 'not functional', 'missing', 66, '2025-11-24 00:12:48'),
(228, 9, 'Removed', 'replacement', 'Archived', 10, '2025-11-24 03:50:10'),
(229, 10, 'Reported', 'missing', 'not functional', 66, '2025-11-24 03:54:32'),
(230, 9, 'Fixed', 'Archived', 'Good', 64, '2025-11-24 03:56:07'),
(231, 10, 'Reported', 'not functional', 'not functional', 66, '2026-02-16 05:20:27');

-- --------------------------------------------------------

--
-- Table structure for table `laboratories`
--

CREATE TABLE `laboratories` (
  `lab_id` int(11) NOT NULL,
  `lab_name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laboratories`
--

INSERT INTO `laboratories` (`lab_id`, `lab_name`, `location`) VALUES
(504, 'LAB-504', 'fifth floor'),
(506, 'LAB-506', 'fifth floor'),
(507, 'LAB-507', 'Fifth Floor'),
(508, 'LAB-508', 'fifth floor'),
(509, 'LAB-509', 'fifth floor'),
(510, 'LAB-510', 'fifth floor'),
(511, 'LAB-511', 'fifth floor'),
(512, 'LAB-512', 'fifth floor'),
(513, 'LAB-513', 'fifth floor'),
(514, 'LAB-514', 'fifth floor'),
(515, 'LAB-515', 'fifth floor'),
(516, 'LAB-516', 'fifth floor'),
(517, 'LAB-517', 'fifth floor'),
(518, 'LAB-518', 'Fifth Floor');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_tasks`
--

CREATE TABLE `maintenance_tasks` (
  `task_id` int(11) NOT NULL,
  `maintenance_id` int(11) NOT NULL,
  `equipment_id` int(11) DEFAULT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `equipment_condition` varchar(50) NOT NULL,
  `remarks` text DEFAULT NULL,
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `location` varchar(50) NOT NULL,
  `State` varchar(50) NOT NULL,
  `assigned_to` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `equipment_id`, `reported_by`, `equipment_condition`, `remarks`, `reported_at`, `location`, `State`, `assigned_to`) VALUES
(266, 10, 69, 'not functional', '', '2025-11-23 07:44:53', '504', '', '64'),
(267, 10, 66, 'not functional', 'nawawala yung mouse', '2025-11-24 00:12:48', '504', '', '64'),
(268, 10, 66, 'not functional', 'No pwer', '2025-11-24 03:54:32', '504', '', '65'),
(270, 10, 66, 'not functional', 'Ayaw gana', '2026-02-16 05:20:27', '504', '', '64');

-- --------------------------------------------------------

--
-- Table structure for table `task_assign_state`
--

CREATE TABLE `task_assign_state` (
  `id` int(11) NOT NULL,
  `last_index` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task_assign_state`
--

INSERT INTO `task_assign_state` (`id`, `last_index`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','maintenance','Staff') NOT NULL,
  `status` varchar(50) DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `status`, `created_at`) VALUES
(10, 'frenz', '$2y$10$CIafnxRxOSQjsQRiX5mv.uj4SBJ/6p.v2NGMVLMtXGXLWUtaJtwbm', 'admin', 'Online', '2025-10-10 04:10:05'),
(64, 'rose', '$2y$10$LlJM6lN8oc1qLomm/T0xU.UhBy/u16RXSzjnlrlJsGefhDl9AxwAi', 'maintenance', 'offline', '2025-11-10 10:16:12'),
(65, 'Exequile', '$2y$10$OPe31CxNSKxac/7czWk6P.sjsrXl9MbmU/S6bZC4KDHwQCrDR0YPC', 'maintenance', 'offline', '2025-11-10 10:16:26'),
(66, 'dan', '$2y$10$nmvQjNx7qSXzH/a0a2wz6Otuj.W/NHqtUJ1uIBIaVcPOe5OaDsI86', 'Staff', 'Online', '2025-11-11 21:15:06'),
(69, 'ocay', '$2y$10$gfC/RIofaH4XGu48Do/0lO6CgR/RtI7X/ADdKL6S3pskV7lNrhWGm', 'Staff', 'Online', '2025-11-15 04:47:42'),
(76, 'Allen', '$2y$10$fnlD00fRZ9nhQNcviA/WA.ynKZNUMCYnN4fRmvlBBMqsCriCyjfa2', 'admin', 'offline', '2026-02-16 05:16:36'),
(77, 'Paller', '$2y$10$nID/0kkF9VPkNan9KxhIJeTf23sIsY19mzTgWJtobLqj0usXO.8My', 'admin', 'offline', '2026-05-19 03:14:45'),
(79, 'User', '$2y$10$l/HWqAwGW.xDzNwhS3ynFOqlvweT9QqUX3kIyNZd.t2GXYgjp5nLG', 'maintenance', 'Online', '2026-05-19 03:16:11'),
(80, 'admin', '$2y$10$GsVaeReHsqgJrqAOn5mSFujUfHIQs/RRZPgjEV0Ot9TZ7KAo6xrQ.', 'admin', 'Online', '2026-05-27 13:33:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `lab_id` (`lab_id`);

--
-- Indexes for table `equipment_history`
--
ALTER TABLE `equipment_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `laboratories`
--
ALTER TABLE `laboratories`
  ADD PRIMARY KEY (`lab_id`);

--
-- Indexes for table `maintenance_tasks`
--
ALTER TABLE `maintenance_tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `maintenance_id` (`maintenance_id`),
  ADD KEY `equipment_id` (`equipment_id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `task_assign_state`
--
ALTER TABLE `task_assign_state`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `equipment_history`
--
ALTER TABLE `equipment_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `maintenance_tasks`
--
ALTER TABLE `maintenance_tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `equipment_ibfk_1` FOREIGN KEY (`lab_id`) REFERENCES `laboratories` (`lab_id`);

--
-- Constraints for table `equipment_history`
--
ALTER TABLE `equipment_history`
  ADD CONSTRAINT `equipment_history_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`),
  ADD CONSTRAINT `equipment_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `maintenance_tasks`
--
ALTER TABLE `maintenance_tasks`
  ADD CONSTRAINT `maintenance_tasks_ibfk_1` FOREIGN KEY (`maintenance_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `maintenance_tasks_ibfk_2` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE SET NULL;

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
