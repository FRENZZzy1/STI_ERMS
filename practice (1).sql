-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 09, 2025 at 05:01 PM
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
-- Database: `practice`
--

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` bigint(20) NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `specs` varchar(255) DEFAULT NULL,
  `item_status` varchar(50) DEFAULT NULL,
  `maintenance_report` varchar(500) DEFAULT 'None',
  `equipment_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `location`, `specs`, `item_status`, `maintenance_report`, `equipment_name`) VALUES
(2323, 'sadad', 'asdasd', 'asd', 'Noneasdasda', 'TESTING'),
(100001, 'Main Office - Floor 1', 'HP LaserJet Pro M404dn, Printer', 'Available', 'replacement', 'PRINTER1'),
(100002, 'Warehouse - Section A', 'Dell PowerEdge R740, Server', 'In Use', 'None', 'PC1'),
(100003, 'Lab 2', 'Epson Projector EB-S41, 3300 Lumens', 'Under Maintenance', 'None', 'PRINTER1'),
(100004, 'IT Department', 'Cisco Catalyst 2960-X Switch', 'Available', 'None', 'SWITCH1'),
(100005, 'Main Entrance', 'CCTV Camera Hikvision DS-2CD2143G0-I', 'Decommisioned', 'None', 'CCTV12');

-- --------------------------------------------------------

--
-- Table structure for table `equipments_tables`
--

CREATE TABLE `equipments_tables` (
  `barcode` bigint(20) NOT NULL,
  `Equipment_Name` varchar(100) NOT NULL,
  `Description` varchar(100) NOT NULL,
  `Location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipments_tables`
--

INSERT INTO `equipments_tables` (`barcode`, `Equipment_Name`, `Description`, `Location`) VALUES
(4800301903309, 'PC_20', '500GB\r\n8GB RAM\r\nINTEL I5', 'LAB 607'),
(58866611239, 'PC_21', 'EXAMPLE', 'LAB_605'),
(58866685888, 'PC_22', 'EXAMPLE', 'LAB_604'),
(588666112444, 'PC_23', 'EXAMPLES', 'LAB_606');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `ReportID` int(11) NOT NULL,
  `EquipmentID` varchar(50) NOT NULL,
  `Location` varchar(100) NOT NULL,
  `Reason` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`ReportID`, `EquipmentID`, `Location`, `Reason`, `Username`) VALUES
(1, '100001', 'Main Office - Floor 1', 'replacement', 'paopao');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `Nam` varchar(50) NOT NULL,
  `pass` varchar(50) NOT NULL,
  `Role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`Nam`, `pass`, `Role`) VALUES
('PAO', 'PALLER', 'Admin'),
('ANIASCO', 'DANIEL', 'STAFF');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`ReportID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
