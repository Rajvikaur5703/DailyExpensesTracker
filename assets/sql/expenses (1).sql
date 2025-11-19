-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 06, 2025 at 09:48 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `expense_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
CREATE TABLE IF NOT EXISTS `expenses` (
  `expense_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `item` varchar(255) NOT NULL,
  `description` text,
  `expense_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`expense_id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `user_id`, `category_id`, `amount`, `item`, `description`, `expense_date`, `created_at`) VALUES
(27, 30, 15, 2000.00, '', 'debit', '2025-09-02', '2025-09-03 12:29:00'),
(28, 34, 15, 1500.00, '', 'Debit the cash', '2025-09-06', '2025-09-06 05:33:56'),
(29, 34, 9, 1000.00, '', 'Grocery Shopping', '2025-09-06', '2025-09-06 05:34:45'),
(26, 30, 15, 2000.00, '', 'debit', '2025-09-02', '2025-09-01 12:31:44'),
(25, 30, 14, 1000.00, '', 'Books', '2025-09-01', '2025-09-01 05:56:19'),
(23, 30, 15, 3000.00, '', 'Credit', '2025-08-31', '2025-08-31 17:28:45'),
(24, 30, 13, 20000.00, '', 'Bags', '2025-09-01', '2025-09-01 05:46:06'),
(30, 34, 13, 2000.00, '', 'Clothes', '2025-09-05', '2025-09-06 05:35:17'),
(31, 35, 14, 1000.00, '', 'Buy a book for bca', '2025-09-06', '2025-09-06 09:23:26');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
