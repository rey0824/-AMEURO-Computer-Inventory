-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 02:42 AM
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
(1, 'ACCTG', 'JEZarandona', 'laptop', 'LENOVO_LAPTOP11', '192.168.1.23', 'Intel Core i7-8265 CPU @ 1.80GHz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 500GB', 'Windows 10 Pro 64-bit', '', '2025-05-13', '', '2025-05-15 00:40:44', '2025-04-27 06:07:16', 'N', 'Sir.Javee', '2025-05-15 08:40:44'),
(2, 'ACCTG', 'Mfinsigne', 'desktop', 'LENOVO_WKS01', '192.168.1.21', 'Intel Core i5-6500 CPU @ 3.20GHz', 'Lenovo IB250MH xxxxxxxxx', 'Huntkey 180W', 'Samsung DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', '2025-05-15 00:11:37', '2025-04-27 06:07:16', 'Y', 'Sir.troy', '2025-05-13 22:22:33'),
(3, 'ACCTG', 'JFCapistrano', 'desktop', 'LENOVO_WKS02', '192.168.1.22', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Ramaxel DDR3 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', '2025-05-13 13:14:15', '2025-05-06 03:37:39', 'Y', NULL, NULL),
(4, 'BCAM', 'MBPornea', '', 'DELL_LAPTOP01', '192.168.1.25', 'Intel Core i5-8365U CPU', 'x', 'x', 'Fury DDR4 16GB', '250GB SSD M.2', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 03:37:39', 'Y', NULL, NULL),
(5, 'BCAM', 'BTGamboa', '', 'LENOVO_WKS03', '192.168.1.26', 'Intel Core i5-6500 CPU @ 3.20Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Kingston DDR4 8GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 03:40:24', 'Y', NULL, NULL),
(6, 'BCAM', 'CAHolgado', '', 'LENOVO_WKS16', '192.168.1.27', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Kingston DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 03:40:24', 'Y', NULL, NULL),
(7, 'BD', 'JPFrance', 'laptop', 'LENOVO_LAPTOP11', '192.168.1.32', 'Intel Core i5-8265 CPU @ 1.80Ghz', 'x', 'x', 'DDR4 8GB', 'SSD 233GB', 'Windows 10 Pro 64-bit', '', NULL, '', '2025-05-13 10:58:49', '2025-05-06 03:54:49', 'Y', NULL, NULL),
(8, 'BD', 'LLBelan', '', 'LENOVO_LAPTOP03', '192.168.1.33', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'Samsung SSD 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 03:54:49', 'Y', NULL, NULL),
(9, 'BD', 'JAVerian', '', 'LENOVO_LAPTOP08', '192.168.1.30', '192.168.1.30', 'x', 'x', 'DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 03:57:20', 'Y', NULL, NULL),
(10, 'BD', 'KVRico', '', 'LENOVO_LAPTOP06', '192.168.1.31', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'SSD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', '2025-05-07 01:51:08', '2025-05-06 03:57:20', 'Y', NULL, NULL),
(11, 'BD', 'JBLibrojo', '', 'PROD_LAPTOP02', '192.168.1.34', 'Intel Core i3-1115G4 CPU @ 3000GHz', 'x', 'x', 'DDR4 16GB', 'SSD M.2 250GB', 'Windows 10 Pro 64-bit', '', NULL, '', '2025-05-06 08:03:03', '2025-05-06 04:16:56', 'Y', NULL, NULL),
(12, 'BMS', 'RMSantiago', '', 'LENOVO_LAPTOP05', '192.168.1.36', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR4 8GB', 'HIKSEMI 500GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 04:16:56', 'Y', NULL, NULL),
(13, 'BMS', 'ACVanguardia', '', 'LENOVO_WKS07', '192.168.1.37', 'Intel Core i5-7500 CPU @ 3.40Ghz', 'Lenovo IB250MH', 'Huntkey 180W', 'Samsung DDR3 8GB', 'HIKSEMI 512GB SSD', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 04:22:22', 'Y', NULL, NULL),
(14, 'EMD', 'GBCarpena', '', 'LENOVO_LAPTOP04', '192.168.1.127', '', '', '', '', '', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 04:22:22', 'Y', NULL, NULL),
(15, 'EMD', 'RCBautista', '', 'ACER_LAPTOP04', '192.168.1.45', 'Intel Core i3-7100 CPU @ 2.40GHz', 'x', 'x', 'DDR4 4GB', 'Samsung SSD 500GB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 07:13:26', 'Y', NULL, NULL),
(16, 'EMD', 'ALYanogacio', '', 'ACER_LAPTOP03', '192.168.1.42', 'Intel Core i5-8250U CPU @ 1.60GHz', 'x', 'x', 'DDR3L SDRAM 4GB', 'HDD 1TB', 'Windows 10 Pro 64-bit', '', NULL, '', NULL, '2025-05-06 07:31:48', 'Y', NULL, NULL),
(20, 'BD', 'ralph', 'desktop', 'asd', '192.168.1.99', 'MOBO', 'aduyiy', 'adayiyi', 'dassa', 's', 'dsad', '', '2025-05-13', '', '2025-05-13 19:06:50', '2025-05-13 18:02:41', 'N', 'Sir.troy', '2025-05-14 02:03:45'),
(21, 'ICT', 'Interns', 'laptop', 'ASUS', '192.168.1.60', 'AMD Ryzen 7 4800H with Radeon Graphics', 'ASUS', 'x', '24 GB', '256', 'Windows 10 Pro 64-bit', '', NULL, '123123123', '2025-05-15 06:08:00', '2025-05-15 06:01:21', 'Y', NULL, NULL);

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
  MODIFY `computer_No` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

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
