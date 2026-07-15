-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 26, 2026 at 09:30 AM
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
-- Database: `food_waste`
--

-- --------------------------------------------------------

--
-- Table structure for table `food_preparation`
--

CREATE TABLE `food_preparation` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `day_type` varchar(50) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `quantity_prepared` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_preparation`
--

INSERT INTO `food_preparation` (`id`, `date`, `day_type`, `food_name`, `quantity_prepared`) VALUES
(7, '2026-02-22', 'Weekend', 'biryani', 500.00),
(10, '2026-02-23', 'Normal', 'vadapav', 30.00),
(11, '2026-02-24', 'Normal', 'veg rice', 500.00),
(12, '2025-05-16', 'Normal', 'lemon rice', 100.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `food_preparation`
--
ALTER TABLE `food_preparation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `food_preparation`
--
ALTER TABLE `food_preparation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
