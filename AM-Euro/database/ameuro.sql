-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 03:28 AM
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
  `Role` varchar(50) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbemployee`
--

INSERT INTO `tbemployee` (`Name`, `Password`, `emp_ID`, `Role`, `status`) VALUES
('Sir.Javee', 'default', 1, 'Manager', 'active'),
('Sir.troy', 'default', 2, 'Supervisor', 'active'),
('Sir.Joms', 'default', 3, 'staff', 'active'),
('Sir.Arjay', 'default', 4, 'staff', 'active');

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
  `Machine_type` varchar(50) NOT NULL,
  `computer_name` varchar(50) NOT NULL,
  `ip` varchar(50) NOT NULL,
  `processor` varchar(50) NOT NULL,
  `MOBO` varchar(50) NOT NULL,
  `power_supply` varchar(50) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `SSD` varchar(50) NOT NULL,
  `OS` varchar(50) NOT NULL,
  `MAC_Address` varchar(255) NOT NULL,
  `deployment_date` date DEFAULT NULL,
  `Asset_no` varchar(255) NOT NULL,
  `last_updated` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `status_changed_by` varchar(255) DEFAULT NULL,
  `status_changed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcomputer`
--

INSERT INTO `tblcomputer` (`computer_No`, `department`, `user`, `Machine_type`, `computer_name`, `ip`, `processor`, `MOBO`, `power_supply`, `ram`, `SSD`, `OS`, `MAC_Address`, `deployment_date`, `Asset_no`, `last_updated`, `date_added`, `is_active`, `status_changed_by`, `status_changed_date`) VALUES
(1, 'ACCTG', 'JEZarandona', '', 'LENOVO_LAPTOP11', '192.168.1.23', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(2, 'ACCTG', 'Mfinsigne', '', 'ACT_WKS01', '192.168.1.21', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(3, 'ACCTG', 'JFCapistrano', '', 'ACT_WKS02', '192.168.1.22', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Ramaxel DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(4, 'BCAM', 'MBPornea', '', 'DELL_LAPTOP01', '192.168.1.25', 'Intel Core i5-8365U CPU', 'x', 'x', 'Fury DDR4 16GB', '250GB SSD M.2', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(5, 'BCAM', 'BTGamboa', '', 'BCAM_WKS01', '192.168.1.26', '12th Gen Intel(R) Core(TM) - i7-12700 CPU @ 2.10GH', 'Asus PRIME H610M-K D4', 'Silverstone 700W', 'KINGSTON FURY DDR4 8GB 2X', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(6, 'BCAM', 'CAHolgado', '', 'BCAM_WKS02', '192.168.1.27', '12th Gen Intel(R) Core(TM) - i7-12700 CPU @ 2.10GH', 'Asus PRIME H610M-K D4', 'Silverstone 700W', 'KINGSTON FURY DDR4 8GB 2X', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(7, 'BD', 'JPFrance', '', 'LENOVO_LAPTOP11', '192.168.1.32', 'Intel Core i5-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 8GB', 'SSD 233GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(8, 'BD', 'LLBelan', '', 'LENOVO_LAPTOP03', '192.168.1.33', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(9, 'BD', 'JAVerian', '', 'LENOVO_LAPTOP08', '192.168.1.30', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(10, 'BD', 'KVRico', '', 'LENOVO_LAPTOP06', '192.168.1.31', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'SSD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(11, 'BD', 'JBLibrojo', '', 'BD_LAPTOP01', '192.168.1.34', 'Intel Core i3-1115G4 CPU @ 3000GHz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(12, 'BMS', 'RMSantiago', '', 'LENOVO_LAPTOP05', '192.168.1.36', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 16GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(13, 'BMS', 'ACVanguardia', '', 'LENOVO_WKS07', '192.168.1.37', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(14, 'EMD', 'GBCarpena', '', 'LENOVO_LAPTOP04', '192.168.1.127', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 16GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(15, 'EMD', 'RCBautista', '', 'LENOVO_WKS13', '192.168.1.137', 'Intel Core i7-9700U CPU @ 3.00GHz', 'Lenovo I3XOMS', 'Acbcl 60Hz', 'DDR4 8GB', 'Hikvision SSD 512GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(16, 'EMD', 'REbale', '', 'ACER_LAPTOP04', '192.168.1.42', 'Intel Core i3-7100 CPU @ 2.40GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(17, 'EMD', 'EMDGuest', '', 'EMD_WKS01', '192.168.1.40', 'Intel Core i3-4170 CPU @ 3.70GHz', 'Asus H81M-D', 'Thermaltake 550W', '8GB', 'HDD 500 GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(18, 'EMD', 'MBBagares', '', 'EMD_WKS02', '192.168.1.41', 'Intel Core i3-4170 CPU @ 3.70GHz', 'Asus H81M-C', 'Generic 600W', 'Kingston DDR3 4GB', 'Western Digital HDD 500GB', 'Windows 10 Professional 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(19, 'EMD', 'ASIlagan', '', 'LENOVO_WKS12', '192.168.1.43', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(20, 'EMD', 'FSDeVilla', '', 'LENOVO_WKS14', '192.168.1.44', 'Intel Core i7-9700U CPU @ 3.00GHz', 'Lenovo I3XOMS', 'Acbcl 60Hz', 'DDR4 8GB', 'Hikvision SSD 512GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(21, 'EMD', 'RTCatipon', '', 'EMD_WKS03', '192.168.1.45', 'Intel Core i3-4170 CPU @ 3.70GHz', 'Asus H81M-C', 'Supercool 750W', 'Kingston DDR3 8GB', 'Western Digital HDD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(22, 'HRD', 'TCDy', '', 'ACER_LAPTOP06', '192.168.1.128', 'Intel® Core™ i5-1135G7', 'x', 'x', '8GB Ram DDR4', 'SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(23, 'HRD', 'CDToledo', '', 'ACER_LAPTOP01', '192.168.1.51', 'Intel Core i3-7100 CPU @ 3.90GHz', 'x', 'x', 'Kingston DDR4 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(24, 'HRD', 'MMPega', '', 'LENOVO_WKS03', '192.168.1.48', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'DDR4 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(25, 'HRD', 'MVBanaticla', '', 'HR_WKS02', '192.168.1.49', 'Intel Core i3-4170 CPU @ 3.70GHz', 'Asus H81M-C', 'Switching 600W', 'Hyper X DDR3 4GB / kingston 2GB DDR3', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(26, 'HRD', 'ERDelRosario', '', 'HR_WKS03', '192.168.1.50', 'Intel Core i3-4170 CPU @ 3.70GHz', 'Asus H81M-D', 'Generic 600W', '8 GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(27, 'HRD', 'JPSavedra', '', 'LENOVO_WKS19', '192.168.1.47', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Lenovo I47OMS', 'Huntkey 180W', 'Samsung DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(28, 'HRD', 'HR.Security', '', 'HR_WKS04', '192.168.1.78', 'Intel Core i3-7100 CPU @ 3.90GHz', 'Asus H110M-D', 'Powerlogic ATX-700W', 'Crucial DDR4 4GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(29, 'ICT', 'JRBenavidez', '', 'LENOVO_LAPTOP01', '192.168.1.129', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'HDD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(30, 'ICT', 'TQLabbino', '', 'ICT_WKS01', '192.168.1.53', 'Intel(R) Core(TM) i7-12700 CPU @ 2.1 GHz', 'Asus PRIME H610M-E D4', 'SILVERSTONE 700W', 'KINGSTON FURY DDR4 16GB', 'Seagate HDD 1TB/\r\nSamsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(31, 'ICT', 'JTApellido', '', 'ICT_WKS02', '192.168.1.54', '12th Gen Intel(R) Core(TM) - i7-12700 CPU @ 2.10GH', 'Asus PRIME H610M-K D4', 'SILVERSTONE ST70F 700W', 'Hyper X DDR3 8GB 2X', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(32, 'ICT', 'AMJavier', '', 'ICT_WKS03', '192.168.1.55', 'Intel(R) Core(TM) i5-3470 CPU @ 3.20GHz', 'Asus H81M-D', 'Thermaltake 530W', 'Fury 8GB RAM 2X', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(33, 'ICT', 'Conference Room', '', 'ACER_LAPTOP05', '192.168.1.143', 'Intel Core i3-7100 CPU @ 3.90GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(34, 'ICT', 'Training Room', '', 'ICT_LAPTOP05S', '192.168.1.144', 'Intel Core i3-1115G4 CPU @ 3000GHz', 'x', 'x', 'DDR4 8GB', 'SSD M.2 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(35, 'ICT', 'Guard', '', 'ICT_WKS03S', '192.168.1.145', 'Intel(R) Core(TM) i5-3470 CPU @ 3.20GHz', 'Asus H61M-C', 'Intelligent 700W', 'SAMSUNG DDR4 8GB / SAMSUNG DDR4 4GB', 'Samsung SSD 500GB', 'Windows 10 Pro', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(36, 'ICT', '-', '', 'ICT_LAPTOP01S', '192.168.1.146', 'Intel(R) Core(TM) i3-7100U @ 2.40 GHz', 'x', 'x', '8 GB', '250 GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(37, 'ICT', '-', '', 'ICT_LAPTOP02S', '192.168.1.147', 'AMD RYZEN 5 7520U 2.80 GHz', 'x', 'x', '8 GB', '250 GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(38, 'ICT', '-', '', 'ICT_LAPTOP03S', '192.168.1.148', 'Intel(R) Core(TM) i5-1155G7 @ 2.50 GHz', 'x', 'x', '8 GB', '250 GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(39, 'ICT', '-', '', 'ICT_LAPTOP04S', '192.168.1.149', 'Intel(R) Core(TM) i3-7100 2-40 GHz', 'x', 'x', '8 GB', '250 GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(40, 'LOG', 'ELMacasadia', '', 'LENOVO_LAPTOP13', '192.168.1.130', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(41, 'LOG', 'GFAdorna', '', 'LENOVO_WKS20', '192.168.1.61', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Lenovo 1470MS', 'Huntkey 180W', 'DDR4 8GB', 'SSD 512GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(42, 'LOG', 'RDMailom', '', 'LOG_WKS01', '192.168.1.62', 'Intel Core i7-11700 CPU @ 2.50GHz', 'Gigabyte H510M', 'HV PRO 85+', 'Team Group DDR4 8GB', 'Crucial SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(43, 'LOG', 'ABorja', '', 'LENOVO_WKS25', '192.168.1.63', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(44, 'LOG', 'GMRoa', '', 'LENOVO_WKS02', '192.168.1.70', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(45, 'LOG', 'CEBeren', '', 'LOG_WKS04', '192.168.1.65', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Asus H81M-C', 'ANTEC Meta', 'T-Force DDR4 8GB', 'SSD 516GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(46, 'LOG', 'JPSiojo', '', 'LOG_WKS05', '192.168.1.66', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 210W', 'DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(47, 'LOG', 'DQOriel', '', 'LOG_WKS06', '192.168.1.67', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Asus H110M-D', 'ANTEC Meta', 'T-Force DDR4 8GB', 'SSD 515GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(48, 'LOG', 'AILopez', '', 'LENOVO_WKS16', '192.168.1.68', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(49, 'LOG', 'NJSiman', '', 'LOG_WKS08', '192.168.1.69', 'Intel(R) Core(TM) i7-12700 CPU @ 2.1 GHz', 'Asus PRIME H610M-E D4', 'SILVERSTONE 700W', 'KINGSTON FURY DDR4 16GB', 'Seagate HDD 1TB/\r\nSamsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(50, 'PID', 'IMRodenas', '', 'LENOVO_LAPTOP14', '192.168.1.131', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(51, 'PID', 'INTuliao', '', 'PID_WKS01', '192.168.1.71', 'Intel Core i3-2100U CPU @ 3.10GHz', 'H61H2-MV', 'Neutron ATX-700W', 'KINGSTON DDR3 8GB', 'HIKSEMI SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(52, 'PID', 'MCCondat', '', 'PID_WKS02', '192.168.1.72', 'Intel Core i5-10400 CPU @ 2.90GHz', 'ASUS P8H77-MLE', 'ANTEC V550', 'FURY DDR4 8 GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(53, 'PID', 'JBVelasquez', '', 'PID_WKS03', '192.168.1.73', 'Intel Core i5-10400 CPU @ 2.90GHz', 'ASUS P8H77-MLE', 'ANTEC V550', 'FURY DDR4 8 GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(54, 'PID', 'JVPasuengos', '', 'PID_WKS04', '192.168.1.74', 'Intel Core i3-7100 CPU @ 3.90GHz', 'INTEL DH55HC', 'POWER LOGIC 600', 'ZEPPELIN 4GB / KINGSTON 4GB', 'TOSHIBA HDD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(55, 'PLAN', 'JSVillarino', '', 'LENOVO_LAPTOP15', '192.168.1.126', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(56, 'PLAN', 'AGCalderon', '', 'LENOVO_WKS18', '192.168.1.77', 'Intel Core i5-10400 CPU @ 2.90GHz', 'LENOVO I470MS', 'Huntkey 180W', 'DDR4 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(57, 'PROD', 'CVSamonte', '', 'LENOVO_LAPTOP16', '192.168.1.132', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(58, 'PROD', 'MDFillado', '', 'PROD_LAPTOP01', '192.168.1.133', 'Intel Core i5-7200U CPU @ 2.50GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(59, 'PROD', 'RCBordeos', '', 'PROD_LAPTOP02', '192.168.1.134', 'Intel Core I5 -7210U', 'x', 'x', '8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(60, 'PROD', 'RDBaldedara', '', 'PROD_WKS02', '192.168.1.81', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'FSP 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(61, 'PROD', 'MOParcia', '', 'PROD_WKS03', '192.168.1.82', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(62, 'PROD', 'MBDeGuia', '', 'PROD_WKS06', '192.168.1.83', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(63, 'PROD', 'RMCabelin', '', 'PROD_WKS04', '192.168.1.84', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(64, 'PROD', 'RLAldovino', '', 'PROD_WKS05', '192.168.1.85', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(65, 'PROD', 'AJRocello/Palce', '', 'LENOVO_WKS26', '192.168.1.80', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(66, 'PUR', 'CHOrense', '', 'LENOVO_LAPTOP17', '192.168.1.135', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(67, 'PUR', 'EOTorres', '', 'LENOVO_WKS04', '192.168.1.88', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 4GB / KINGSTON DDR3 4GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(68, 'PUR', 'GRSarmiento', '', 'PUR_WKS03', '192.168.1.89', '12th Gen Intel(R) Core(TM) - i7-12700 CPU @ 2.10GH', 'Asus PRIME H610M-K D4', 'Silverstone 700W', 'KINGSTON FURY DDR4 8GB 2X', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(69, 'PUR', 'HSDiche', '', 'PUR_WKS01', '192.168.1.90', 'Intel Core i5-11400 CPU @ 2.60GHz', 'Asus Prime H510M-E', 'Silverstone 550W', 'FURY 8GB DDR4', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(70, 'PUR', 'MCMedel', '', 'PUR_WKS02', '192.168.1.91', 'Intel Core i7-10700 CPU @ 2.90GHz', 'Gigabyte Can TCES-003', 'Corsair CV550W', 'Team Group DDR4 8GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(71, 'QA', 'CRVillanueva', '', 'LENOVO_LAPTOP18', '192.168.1.136', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(72, 'QA', 'ASCabugao', '', 'LENOVO_LAPTOP09', '192.168.1.94', 'Intel Core i5-7200 CPU @ 2.50Ghz', 'x', 'x', 'DDR4 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(73, 'QA', 'MBTatad', '', 'LENOVO_LAPTOP07', '192.168.1.95', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(74, 'QA', 'VTSeñadan', '', 'LENOVO_WKS23', '192.168.1.96', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Lenovo 1470MS', 'Huntkey 180W', 'Kingston DDR4 8GB', 'Western Digital HDD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(75, 'QA', 'DDDelaRaga', '', 'QA_WKS01', '192.168.1.97', 'Intel Core i3-7100 CPU@3.90GHz', 'ASUS Hi10M-D', 'INTEX 600W', 'Kingston Fury 8GB DDR4', 'Western Digital HDD 500TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(76, 'QA', 'QA Prod', '', 'QA_WKS02', '192.168.1.98', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Asus Prime H510M-K', 'Neutron ATX-700W', 'Hyper X DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(77, 'QA', 'MSFlores', '', 'LENOVO_WKS05', '192.168.1.99', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Ram Axel DDR3 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(78, 'QA', 'MPSalvador', '', 'QA_WKS04', '192.168.1.100', 'Intel Core i3-2100U CPU @ 3.10GHz', 'Intel Desktop Board', 'INPLAY G5250BK', '8 GB', 'SEAGATE HDD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(79, 'QA', 'JMQuimora', '', 'LENOVO_WKS06', '192.168.1.101', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'FSP 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(80, 'QA', 'DFOrgen', '', 'LENOVO_WKS22', '192.168.1.102', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Lenovo 1470MS', 'Huntkey 180W', 'Kingston DDR4 8GB', 'Western Digital HDD 1TB', 'Windows 11 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(81, 'QA', 'HMObdianela', '', 'QA_WKS07', '192.168.1.103', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(82, 'QA', 'CSZarsuelo', '', 'LENOVO_WKS08', '192.168.1.104', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(83, 'QC', 'KSDavis', '', 'LENOVO_LAPTOP19', '192.168.1.138', 'Intel Core i7-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(84, 'QC', 'LPAlcido', '', 'QC_WKS02', '192.168.1.108', 'Intel Core i5-10400 CPU @ 2.90GHz', 'ASUS H610M-K', 'NEUTRON ELECTRON 550W', 'FURY DDR4 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(85, 'QC', 'AMNavarez', '', 'QC_WKS03', '192.168.1.109', 'Intel Core i3-3240 CPU @ 3.40GHz', 'Asus H81M-D', 'ELECTRON 530W', '8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(86, 'QC', 'AMNavarez', '', 'QC_WKS04', '192.168.1.110', 'Intel Core i5-10400 CPU @ 2.90GHz', 'HP MS-7525', 'NEUTRON 218W', 'FURY DDR3 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(87, 'QC', 'EADeGuzman', '', 'QC_WKS05', '192.168.1.111', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Asus Prime H510M-K', 'Neutron ATX-700W', 'FURY DDR3 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(88, 'QC', 'JSMatienzo', '', 'QC_WKS06', '192.168.1.112', 'Intel Core i5-10400 CPU @ 2.90GHz', 'INTEL DESKTOP BOARD', 'FRONTIER', 'Kingston DDR3 4GB / Kingston DDR3 4GB', 'SEAGATE 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(89, 'QC', 'AMBeatriz', '', 'QC_WKS07', '192.168.1.113', 'Intel(R) Core(TM) i5-3470 CPU @ 3.20GHz', 'Asus H81M-D', 'Thermaltake 530W', 'Kingston DDR3 4GB / Kingston DDR3 4GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(90, 'QC', 'MEGarcia', '', 'QC_WKS08', '192.168.1.107', 'Intel Core i3-3240 CPU @ 3.40GHz', 'Asus H61M-C', 'Generic 600W', 'Kinston DDR3 4GB / Samsung DDR3 2GB', 'Western Digital HDD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(91, 'RAD', 'OYLicuanan', '', 'RAD_WKS01', '192.168.1.139', 'Intel Core i3-4170 CPU @ 3.70GHz', 'Asus H81M-D', 'THERMALTAKE 750W', 'Hyper X DDR3 8GB RAM', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(92, 'RAD', 'MOVillamayor', '', 'ACER_LAPTOP07', '192.168.1.117', 'Intel® Core™ i5-1135G7', '', '', '8GB Ram DDR4', 'SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(93, 'RAD', 'JCModelo', '', 'RAD_WKS02', '192.168.1.119', 'Intel(R) Core(TM) i5-3470 CPU @ 3.20GHz', 'Asus P8H77-M LE', 'Intelligent 700W', 'Hyper X DDR3 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(94, 'RAD', 'JJNecio', '', 'RAD_WKS03', '192.168.1.118', 'Intel Core i7-10700 CPU @ 2.90GHz', 'ASUS H510M-K R2.0', 'Generic 600W', 'FURY DDR4 8gb RAM', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(95, 'VALIDATION', 'PMSunglao', '', 'ACER_LAPTOP02', '192.168.1.140', 'Intel® Core™ i5-1135G7', 'x', 'x', 'DDR4 8GB', 'SSD 500GB', 'Windows 11 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(96, 'VALIDATION', 'MBMaca', '', 'LENOVO_WKS17', '192.168.1.121', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 210W', 'kingston DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(97, 'VALIDATION', 'JVMariano', '', 'LENOVO_WKS10', '192.168.1.122', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL),
(98, 'VALIDATION', 'JLJavier', '', 'LENOVO_WKS21', '192.168.1.123', 'Intel Core i5-10400 CPU @ 2.90GHz', 'Lenovo L470MS', 'Huntkey 180W', 'Hynix DDR4 8GB', 'Western Digital HDD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-16 01:27:28', 'Y', NULL, NULL);

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
-- AUTO_INCREMENT for table `tbemployee`
--
ALTER TABLE `tbemployee`
  MODIFY `emp_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblchange_comments`
--
ALTER TABLE `tblchange_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcomputer`
--
ALTER TABLE `tblcomputer`
  MODIFY `computer_No` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `tblcomputer_history`
--
ALTER TABLE `tblcomputer_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

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
