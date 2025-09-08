-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 03:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blood_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) NOT NULL,
  `bag_number` varchar(50) NOT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `donated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `donor_id`, `bag_number`, `blood_group`, `donated_at`) VALUES
(1, 1, 'BAG-68BD59AED52C2', 'A+', '2025-09-07 10:08:46'),
(2, 2, 'BAG-68BD5EB899E31', 'O-', '2025-09-07 10:30:16'),
(3, 4, 'BAG-68BE77E248686', 'A+', '2025-09-08 06:29:54'),
(4, 5, 'BAG-68BE7BDC41B43', 'AB+', '2025-09-08 06:46:52');

-- --------------------------------------------------------

--
-- Table structure for table `donors`
--

CREATE TABLE `donors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donors`
--

INSERT INTO `donors` (`id`, `name`, `email`, `password`, `blood_group`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'ABDUR ROUF', 'roufofficial2020@gmail.com', '$2y$10$kqjR95L2TzJY09YxUFPjx.XkjpSvpG8qKTAjNMtgKYM6V0VT7WQhK', 'A+', '01585902697', 'dhaka uttara', '2025-09-07 09:08:35', '2025-09-07 09:13:42'),
(2, 'ABDUR ROUF', 'roufofficial202@gmail.com', '$2y$10$QGm3e.9TVGNdv.A8bjgjTOnXpQflgebioJEiT.BNoZYpuCALVKXJi', 'O-', '01585902697', 'Bagmara, Rajshahi\r\nRajshahi', '2025-09-07 10:30:00', '2025-09-07 10:30:00'),
(3, 'ROUF', 'roufofficial200@gmail.com', '$2y$10$yBKf4Bxz6uq3RyZtRBnXke.0GLgdUFTlHOKfbxidxIoMGyHLwsYp.', 'B+', '01585902697', 'Rajshahi\r\nRajshahi', '2025-09-08 04:49:59', '2025-09-08 04:49:59'),
(4, 'ar', 'roufofficial@gmail.com', '$2y$10$LxpvmRumn3fk5oqE0fxLmOS.Q/ytcHSOiXi8h7QKyDoXL8o0Gr0SG', 'A+', '01585902697', 'uttara', '2025-09-08 06:26:11', '2025-09-08 06:26:11'),
(5, 'abc', 'rouf@gmail.com', '$2y$10$ZgcG6uW0Zag7GrgpLkCRtOcWkbAT18ZShPSWI1Pmi3uyK.3Z5ZMja', 'AB+', '01585902697', 'Bagmara, Rajshahi\r\nRajshahi', '2025-09-08 06:46:18', '2025-09-08 06:46:18');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `email`, `password`, `blood_group`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(1, 'ABDUR ROUF', 'roufofficial2020@gmail.com', '$2y$10$e34ubF4mW5NmOTG38zUxC.xnmaQKYS1TgQpOK7SmuhqgTOGekkGyG', 'B+', '01585902697', 'Bagmara, \r\nRajshahi', '2025-09-07 09:15:19', '2025-09-07 09:15:44');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `donor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `bags_requested` int(11) NOT NULL,
  `status` enum('pending','success') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `donor_id`, `patient_id`, `blood_group`, `bags_requested`, `status`, `created_at`) VALUES
(2, 1, NULL, 'O-', 2, 'success', '2025-09-07 09:51:57'),
(3, 1, NULL, 'A+', 1, 'success', '2025-09-07 09:53:57'),
(4, NULL, 1, 'A-', 1, 'success', '2025-09-07 10:23:21'),
(5, NULL, 1, 'A-', 1, 'success', '2025-09-07 10:24:47'),
(6, NULL, 1, 'AB+', 20, 'pending', '2025-09-07 10:24:56'),
(7, NULL, 1, 'O-', 4, 'success', '2025-09-07 10:25:40'),
(8, NULL, 1, 'O+', 10, 'success', '2025-09-07 10:26:25'),
(9, NULL, 1, 'A+', 11, 'pending', '2025-09-07 10:26:55'),
(10, NULL, 1, 'A+', 10, 'success', '2025-09-07 10:27:02'),
(11, 1, NULL, 'AB+', 6, 'pending', '2025-09-07 10:28:09'),
(12, 1, NULL, 'AB+', 4, 'success', '2025-09-07 10:28:22'),
(13, 1, NULL, 'B+', 6, 'success', '2025-09-07 10:29:20'),
(14, 2, NULL, 'O+', 2, 'success', '2025-09-07 10:36:42'),
(15, 1, NULL, 'AB+', 1, 'pending', '2025-09-08 03:10:09'),
(16, 1, NULL, 'A+', 1, 'pending', '2025-09-08 03:10:21'),
(17, 1, NULL, 'A-', 1, 'success', '2025-09-08 03:10:27'),
(18, 1, NULL, 'B-', 1, 'success', '2025-09-08 03:10:55'),
(19, NULL, 1, 'AB-', 1, 'success', '2025-09-08 03:12:36'),
(20, 4, NULL, 'A+', 1, 'pending', '2025-09-08 06:29:40'),
(21, 4, NULL, 'A+', 1, 'success', '2025-09-08 06:30:04'),
(22, 4, NULL, 'A+', 1, 'pending', '2025-09-08 06:31:03'),
(23, 4, NULL, 'AB+', 1, 'pending', '2025-09-08 06:45:11'),
(24, 5, NULL, 'AB+', 1, 'success', '2025-09-08 06:47:00'),
(25, 5, NULL, 'AB+', 9, 'pending', '2025-09-08 06:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `storage`
--

CREATE TABLE `storage` (
  `id` int(11) NOT NULL,
  `blood_group` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
  `bags_available` int(11) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `storage`
--

INSERT INTO `storage` (`id`, `blood_group`, `bags_available`, `updated_at`) VALUES
(1, 'A+', 0, '2025-09-08 06:30:04'),
(2, 'A-', 2, '2025-09-08 03:10:27'),
(3, 'B+', 2, '2025-09-07 10:29:20'),
(4, 'B-', 2, '2025-09-08 03:10:55'),
(5, 'AB+', 0, '2025-09-08 06:47:00'),
(6, 'AB-', 1, '2025-09-08 03:12:36'),
(7, 'O+', 0, '2025-09-07 10:36:42'),
(8, 'O-', 1, '2025-09-07 10:30:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`);

--
-- Indexes for table `donors`
--
ALTER TABLE `donors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donor_id` (`donor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `storage`
--
ALTER TABLE `storage`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `blood_group` (`blood_group`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `donors`
--
ALTER TABLE `donors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `storage`
--
ALTER TABLE `storage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_fk_donor` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `requests`
--
ALTER TABLE `requests`
  ADD CONSTRAINT `requests_fk_donor` FOREIGN KEY (`donor_id`) REFERENCES `donors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `requests_fk_patient` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
