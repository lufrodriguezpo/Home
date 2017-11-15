-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2017 at 02:42 AM
-- Server version: 10.1.25-MariaDB
-- PHP Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tutos_un`
--

-- --------------------------------------------------------

--
-- Table structure for table `horariosambientes`
--

CREATE TABLE `horariosambientes` (
  `anio` varchar(4) NOT NULL,
  `sems` int(1) NOT NULL,
  `mes` int(2) NOT NULL,
  `dia` int(2) NOT NULL,
  `hora` int(2) NOT NULL,
  `codarea` varchar(2) NOT NULL,
  `monitores` text NOT NULL,
  `lugar` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `horariosambientes`
--

INSERT INTO `horariosambientes` (`anio`, `sems`, `mes`, `dia`, `hora`, `codarea`, `monitores`, `lugar`) VALUES
('2017', 1, 10, 4, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 4, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 5, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 6, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 7, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 8, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 9, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 10, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 11, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 12, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 13, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 14, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 15, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 16, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 10, 17, '01', '0000005,0000003', 'Salon 15 edif 7272'),
('2017', 1, 10, 21, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 21, 18, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 22, 18, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 23, 18, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 24, 18, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 25, 18, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 26, 18, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 10, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 11, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 12, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 13, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 14, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 15, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 16, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 17, '01', '0000005,0000003', 'klxnclkd'),
('2017', 1, 10, 27, 18, '01', '0000005,0000003', 'klxnclkd');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `horariosambientes`
--
ALTER TABLE `horariosambientes`
  ADD UNIQUE KEY `anio` (`anio`,`sems`,`mes`,`dia`,`hora`,`codarea`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
