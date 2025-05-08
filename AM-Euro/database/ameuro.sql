-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 02:09 AM
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
-- Database: `ameuro`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbemployee`
--

CREATE TABLE `tbemployee` (
  `Name` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  `emp_ID` int(11) NOT NULL,
  `Role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbemployee`
--

INSERT INTO `tbemployee` (`Name`, `Password`, `emp_ID`, `Role`) VALUES
('Sir.Javee', 'default', 1, 'Manager'),
('Sir.troy', 'default', 2, 'Supervisor'),
('Sir.Joms', 'default', 3, 'staff'),
('Sir.Arjay', 'default', 4, 'staff');

-- --------------------------------------------------------

--
-- Table structure for table `tblchange_comments`
--

CREATE TABLE `tblchange_comments` (
  `comment_id` int(11) NOT NULL,
  `history_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `change_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tblcomputer`
--

CREATE TABLE `tblcomputer` (
  `computer_No` int(11) NOT NULL,
  `department` varchar(50) NOT NULL,
  `user` varchar(50) NOT NULL,
  `computer_name` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `processor` varchar(50) NOT NULL,
  `MOBO` varchar(50) NOT NULL,
  `power_supply` varchar(50) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `SSD` varchar(50) NOT NULL,
  `OS` varchar(50) NOT NULL,
  `deployment_date` date DEFAULT NULL,
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcomputer`
--

INSERT INTO `tblcomputer` (`computer_No`, `department`, `user`, `computer_name`, `ip`, `processor`, `MOBO`, `power_supply`, `ram`, `SSD`, `OS`, `deployment_date`, `last_updated`, `date_added`) VALUES
(1, 'ACCTG', 'JEZarandona', 'LENOVO_LAPTOP11', '192.168.1.23', 'Intel Core i7-8265 CPU @ 1.80GHz', 'xda', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', NULL, '2025-05-08 00:01:55', '2025-04-27 06:07:16'),
(2, 'ACCTG', 'Mfinsigne', 'LENOVO_WKS01', '192.168.1.21', 'Intel Core i5-6500 CPU @ 3.20GHz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', NULL, '2025-05-06 04:10:04', '2025-04-27 06:07:16'),
(3, 'ACCTG', 'JFCapistrano', 'LENOVO_WKS02', '192.168.1.22', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Ramaxel DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 03:37:39'),
(4, 'BCAM', 'MBPornea', 'DELL_LAPTOP01', '192.168.1.25', 'Intel Core i5-8365U CPU', 'x', 'x', 'Fury DDR4 16GB', '250GB SSD M.2', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 03:37:39'),
(5, 'BCAM', 'BTGamboa', 'LENOVO_WKS03', '192.168.1.26', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Kingston DDR4 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 03:40:24'),
(6, 'BCAM', 'CAHolgado', 'LENOVO_WKS16', '192.168.1.27', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Kingston DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 03:40:24'),
(7, 'BD', 'JPFrance', 'LENOVO_LAPTOP11', '192.168.1.32', 'Intel Core i5-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 8GB', 'SSD 233GB', 'Windows 10 Pro 64-bit', NULL, '2025-05-07 02:02:00', '2025-05-06 03:54:49'),
(8, 'BD', 'LLBelan', 'LENOVO_LAPTOP03', '192.168.1.33', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 03:54:49'),
(9, 'BD', 'JAVerian', 'LENOVO_LAPTOP08', '192.168.1.30', '192.168.1.30', 'x', 'x', 'DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 03:57:20'),
(10, 'BD', 'KVRico', 'LENOVO_LAPTOP06', '192.168.1.31', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'SSD 1TB', 'Windows 10 Pro 64-bit', NULL, '2025-05-07 01:51:08', '2025-05-06 03:57:20'),
(11, 'BD', 'JBLibrojo', 'PROD_LAPTOP02', '192.168.1.34', 'Intel Core i3-1115G4 CPU @ 3000GHz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 250GB', 'Windows 10 Pro 64-bit', NULL, '2025-05-06 08:03:03', '2025-05-06 04:16:56'),
(12, 'BMS', 'RMSantiago', 'LENOVO_LAPTOP05', '192.168.1.36', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 04:16:56'),
(13, 'BMS', 'ACVanguardia', 'LENOVO_WKS07', '192.168.1.37', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 04:22:22'),
(14, 'EMD', 'GBCarpena', 'LENOVO_LAPTOP04', '192.168.1.127', '', '', '', '', '', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 04:22:22'),
(15, 'EMD', 'RCBautista', 'ACER_LAPTOP04', '192.168.1.45', 'Intel Core i3-7100 CPU @ 2.40GHz', 'x', 'x', 'DDR4 4GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 07:13:26'),
(16, 'EMD', 'ALYanogacio', 'ACER_LAPTOP03', '192.168.1.42', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR3L SDRAM 4GB', 'HDD 1TB', 'Windows 10 Pro 64-bit', NULL, NULL, '2025-05-06 07:31:48');

-- --------------------------------------------------------

--
-- Table structure for table `tblcomputer_history`
--

CREATE TABLE `tblcomputer_history` (
  `history_id` int(11) NOT NULL,
  `computer_No` int(11) NOT NULL,
  `previous_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`previous_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `updated_by` varchar(50) NOT NULL,
  `comment` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcomputer_history`
--

INSERT INTO `tblcomputer_history` (`history_id`, `computer_No`, `previous_data`, `new_data`, `updated_by`, `comment`, `timestamp`) VALUES
(1, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"me\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-04-30 11:03:56\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"z\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'sdadzad', '2025-05-03 13:39:40'),
(2, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"z\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-03 21:39:40\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong input in MOBO', '2025-05-03 13:45:13'),
(3, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-03 21:45:13\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"xasdada\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'adasd', '2025-05-03 14:46:46'),
(4, 2, '{\"computer_No\":2,\"department\":\"ACCTG\",\"user\":\"Mfinsigne\",\"computer_name\":\"LENOVO_WKS01\",\"ip\":\"192.168.1.21\",\"processor\":\"Intel Core i5-6500 CPU @ 3.20GHz\",\"MOBO\":\"Lenovo IB250MH\",\"power_supply\":\"Huntkey 180W\",\"ram\":\"Samsung DDR3 8GB\",\"SSD\":\"Samsung SSD 250GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":null,\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"Mfinsigne\",\"computer_name\":\"LENOVO_WKS01\",\"ip\":\"192.168.1.21\",\"processor\":\"Intel Core i5-6500 CPU @ 3.20GHz\",\"MOBO\":\"Lenovo IB250MH\",\"power_supply\":\"Huntkey 180W\",\"ram\":\"Samsung DDR3 8GB\",\"SSD\":\"Samsung SSD 250GB\",\"OS\":\"Windows 7 Pro 64-bit\"}', 'Unknown', 'wadsd', '2025-05-03 14:50:01'),
(5, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"xasdada\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-03 22:46:46\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'worng input', '2025-05-06 00:33:37'),
(7, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 08:33:37\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"dasdasdas\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'asd', '2025-05-06 00:47:13'),
(8, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"dasdasdas\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 08:47:13\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong input of data', '2025-05-06 01:14:16'),
(9, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 09:14:16\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"xx\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'addas', '2025-05-06 01:45:09'),
(10, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"xx\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 09:45:09\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong input', '2025-05-06 02:04:05'),
(11, 2, '{\"computer_No\":2,\"department\":\"ACCTG\",\"user\":\"Mfinsigne\",\"computer_name\":\"LENOVO_WKS01\",\"ip\":\"192.168.1.21\",\"processor\":\"Intel Core i5-6500 CPU @ 3.20GHz\",\"MOBO\":\"Lenovo IB250MH\",\"power_supply\":\"Huntkey 180W\",\"ram\":\"Samsung DDR3 8GB\",\"SSD\":\"Samsung SSD 250GB\",\"OS\":\"Windows 7 Pro 64-bit\",\"last_updated\":\"2025-05-03 22:50:01\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"Mfinsigne\",\"computer_name\":\"LENOVO_WKS01\",\"ip\":\"192.168.1.21\",\"processor\":\"Intel Core i5-6500 CPU @ 3.20GHz\",\"MOBO\":\"Lenovo IB250MH\",\"power_supply\":\"Huntkey 180W\",\"ram\":\"Samsung DDR3 8GB\",\"SSD\":\"Samsung SSD 250GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong input of OS', '2025-05-06 04:10:04'),
(12, 11, '{\"computer_No\":11,\"department\":\"BD\",\"user\":\"JBLibrojo\",\"computer_name\":\"PROD_LAPTOP02\",\"ip\":\"192.168.1.34\",\"processor\":\"Intel Core i3-1115G4 CPU @ 3000GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 250GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":null,\"date_added\":\"2025-05-06 12:16:56\"}', '{\"department\":\"BD\",\"user\":\"JBLibrojo\",\"computer_name\":\"PROD_LAPTOP02\",\"ip\":\"192.168.1.34\",\"processor\":\"Intel Core i3-1115G4 CPU @ 3000GHz\",\"MOBO\":\"sda\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 250GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong input', '2025-05-06 07:38:45'),
(13, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 10:04:05\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.231\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', '', '2025-05-06 07:53:12'),
(14, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.231\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 15:53:12\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.231\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x0\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', '', '2025-05-06 07:53:34'),
(15, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.231\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x0\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 15:53:34\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', '', '2025-05-06 07:53:40'),
(16, 11, '{\"computer_No\":11,\"department\":\"BD\",\"user\":\"JBLibrojo\",\"computer_name\":\"PROD_LAPTOP02\",\"ip\":\"192.168.1.34\",\"processor\":\"Intel Core i3-1115G4 CPU @ 3000GHz\",\"MOBO\":\"sda\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 250GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 15:38:45\",\"date_added\":\"2025-05-06 12:16:56\"}', '{\"department\":\"BD\",\"user\":\"JBLibrojo\",\"computer_name\":\"PROD_LAPTOP02\",\"ip\":\"192.168.1.34\",\"processor\":\"Intel Core i3-1115G4 CPU @ 3000GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 250GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong input in the MOBO', '2025-05-06 08:03:03'),
(18, 10, '{\"computer_No\":10,\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":null,\"date_added\":\"2025-05-06 11:57:20\"}', '{\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"dasd\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Unknown', 'wrong power supply', '2025-05-07 01:30:00'),
(19, 10, '{\"computer_No\":10,\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"dasd\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-07 09:30:00\",\"date_added\":\"2025-05-06 11:57:20\"}', '{\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Javee', 'wrong input', '2025-05-07 01:33:58'),
(20, 10, '{\"computer_No\":10,\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-07 09:33:58\",\"date_added\":\"2025-05-06 11:57:20\"}', '{\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"xdaasd\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Arjay', 'asdasas', '2025-05-07 01:35:16'),
(21, 10, '{\"computer_No\":10,\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"xdaasd\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-07 09:35:16\",\"date_added\":\"2025-05-06 11:57:20\"}', '{\"department\":\"BD\",\"user\":\"KVRico\",\"computer_name\":\"LENOVO_LAPTOP06\",\"ip\":\"192.168.1.31\",\"processor\":\"Intel Core i5-8250U CPU @ 1.60GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 1TB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Arjay', 'corrected the inputs', '2025-05-07 01:51:08'),
(22, 7, '{\"computer_No\":7,\"department\":\"BD\",\"user\":\"JPFrance\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.32\",\"processor\":\"Intel Core i5-8265 CPU @ 1.80Ghz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 8GB\",\"SSD\":\" SSD 233GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":null,\"date_added\":\"2025-05-06 11:54:49\"}', '{\"department\":\"BD\",\"user\":\"JPFrance\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.32\",\"processor\":\"Intel Core i5-8265 CPU @ 1.80Ghz\",\"MOBO\":\"as\",\"power_supply\":\"xdad\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 233GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Arjay', '', '2025-05-07 01:55:08'),
(23, 7, '{\"computer_No\":7,\"department\":\"BD\",\"user\":\"JPFrance\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.32\",\"processor\":\"Intel Core i5-8265 CPU @ 1.80Ghz\",\"MOBO\":\"as\",\"power_supply\":\"xdad\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 233GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-07 09:55:08\",\"date_added\":\"2025-05-06 11:54:49\"}', '{\"department\":\"BD\",\"user\":\"JPFrance\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.32\",\"processor\":\"Intel Core i5-8265 CPU @ 1.80Ghz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 8GB\",\"SSD\":\"SSD 233GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Arjay', '', '2025-05-07 02:02:00'),
(24, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-06 15:53:40\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"jiada\",\"power_supply\":\"asdad\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Javee', '', '2025-05-07 02:21:46'),
(25, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"jiada\",\"power_supply\":\"asdad\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"last_updated\":\"2025-05-07 10:21:46\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.Javee', 'wrong inputs', '2025-05-07 02:22:03'),
(28, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"deployment_date\":null,\"last_updated\":\"2025-05-07 10:22:03\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.troy', '', '2025-05-07 23:52:38'),
(29, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"deployment_date\":null,\"last_updated\":\"2025-05-08 07:52:38\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.troy', '', '2025-05-07 23:53:01'),
(30, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"deployment_date\":null,\"last_updated\":\"2025-05-08 07:53:01\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.troy', '', '2025-05-07 23:53:08'),
(69, 1, '{\"computer_No\":1,\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"x\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\",\"deployment_date\":null,\"last_updated\":\"2025-05-08 07:53:08\",\"date_added\":\"2025-04-27 14:07:16\"}', '{\"department\":\"ACCTG\",\"user\":\"JEZarandona\",\"computer_name\":\"LENOVO_LAPTOP11\",\"ip\":\"192.168.1.23\",\"processor\":\"Intel Core i7-8265 CPU @ 1.80GHz\",\"MOBO\":\"xda\",\"power_supply\":\"x\",\"ram\":\"DDR4 16GB\",\"SSD\":\"SSD M.2 500GB\",\"OS\":\"Windows 10 Pro 64-bit\"}', 'Sir.troy', '', '2025-05-08 00:01:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbemployee`
--
ALTER TABLE `tbemployee`
  ADD PRIMARY KEY (`emp_ID`);

--
-- Indexes for table `tblchange_comments`
--
ALTER TABLE `tblchange_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `history_id` (`history_id`);

--
-- Indexes for table `tblcomputer`
--
ALTER TABLE `tblcomputer`
  ADD PRIMARY KEY (`computer_No`);

--
-- Indexes for table `tblcomputer_history`
--
ALTER TABLE `tblcomputer_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `computer_No` (`computer_No`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblchange_comments`
--
ALTER TABLE `tblchange_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcomputer`
--
ALTER TABLE `tblcomputer`
  MODIFY `computer_No` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tblcomputer_history`
--
ALTER TABLE `tblcomputer_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblchange_comments`
--
ALTER TABLE `tblchange_comments`
  ADD CONSTRAINT `tblchange_comments_ibfk_1` FOREIGN KEY (`history_id`) REFERENCES `tblcomputer_history` (`history_id`) ON DELETE CASCADE;

--
-- Constraints for table `tblcomputer_history`
--
ALTER TABLE `tblcomputer_history`
  ADD CONSTRAINT `tblcomputer_history_ibfk_1` FOREIGN KEY (`computer_No`) REFERENCES `tblcomputer` (`computer_No`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
