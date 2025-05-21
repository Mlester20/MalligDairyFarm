-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 12:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `malligdairyfarm`
--

-- --------------------------------------------------------

--
-- Table structure for table `live_stocks`
--

CREATE TABLE `live_stocks` (
  `id` int(11) NOT NULL,
  `live_stock_name` varchar(100) NOT NULL,
  `live_stock_code` varchar(100) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `live_stocks`
--

INSERT INTO `live_stocks` (`id`, `live_stock_name`, `live_stock_code`, `created_at`) VALUES
(1, 'Heifer', '3617-7', '2025-04-03');

-- --------------------------------------------------------

--
-- Table structure for table `milk_records`
--

CREATE TABLE `milk_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `live_stock_id` int(11) NOT NULL,
  `record_date` date NOT NULL,
  `quantity` float NOT NULL,
  `recorded_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `milk_records`
--

INSERT INTO `milk_records` (`id`, `user_id`, `live_stock_id`, `record_date`, `quantity`, `recorded_at`) VALUES
(6, 2, 1, '2025-04-04', 25, '2025-04-03'),
(7, 2, 1, '2025-05-21', 25, '2025-05-21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `role` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `username`, `password`, `created_at`, `role`) VALUES
(1, 'Mark Lester Raguindin', 'admin', '0192023a7bbd73250516f069df18b500', '2025-04-02 08:16:47', 'admin'),
(2, 'Jean Dominque Bulusan', 'test', '202cb962ac59075b964b07152d234b70', '2025-04-02 00:38:17', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `live_stocks`
--
ALTER TABLE `live_stocks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milk_records`
--
ALTER TABLE `milk_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `live_stock_id` (`live_stock_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `live_stocks`
--
ALTER TABLE `live_stocks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `milk_records`
--
ALTER TABLE `milk_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `milk_records`
--
ALTER TABLE `milk_records`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `milk_records_ibfk_1` FOREIGN KEY (`live_stock_id`) REFERENCES `live_stocks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
