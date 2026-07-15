-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Feb 26, 2026 at 09:26 AM
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
-- Table structure for table `consumption`
--

CREATE TABLE `consumption` (
  `id` int(11) NOT NULL,
  `preparation_id` int(11) NOT NULL,
  `day_type_id` int(11) NOT NULL,
  `morning` decimal(10,2) DEFAULT 0.00,
  `afternoon` decimal(10,2) DEFAULT 0.00,
  `evening` decimal(10,2) DEFAULT 0.00,
  `night` decimal(10,2) DEFAULT 0.00,
  `total_consumed` decimal(10,2) NOT NULL,
  `leftover_quantity` decimal(10,2) NOT NULL,
  `waste_type` varchar(50) DEFAULT NULL,
  `recommendation` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumption`
--

INSERT INTO `consumption` (`id`, `preparation_id`, `day_type_id`, `morning`, `afternoon`, `evening`, `night`, `total_consumed`, `leftover_quantity`, `waste_type`, `recommendation`) VALUES
(13, 7, 1, 100.00, 200.00, 50.00, 20.00, 370.00, 130.00, 'Edible', 'URGENT: Bulk donation required!'),
(16, 10, 1, 5.00, 3.00, 7.00, 10.00, 25.00, 5.00, 'Edible', 'Reuse in next meal (Low Quantity)'),
(17, 11, 1, 200.00, 35.00, 20.00, 10.00, 265.00, 235.00, 'Edible', 'URGENT: Bulk donation required!'),
(18, 12, 1, 30.00, 50.00, 10.00, 10.00, 100.00, 0.00, 'No Waste', 'No action required - All food consumed.');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consumption`
--
ALTER TABLE `consumption`
  ADD PRIMARY KEY (`id`),
  ADD KEY `preparation_id` (`preparation_id`),
  ADD KEY `day_type_id` (`day_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consumption`
--
ALTER TABLE `consumption`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consumption`
--
ALTER TABLE `consumption`
  ADD CONSTRAINT `consumption_ibfk_1` FOREIGN KEY (`preparation_id`) REFERENCES `food_preparation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumption_ibfk_2` FOREIGN KEY (`day_type_id`) REFERENCES `day_type` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
