-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2017 at 02:28 AM
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
-- Table structure for table `citaspares`
--

CREATE TABLE `citaspares` (
  `anio` varchar(4) NOT NULL,
  `sems` int(1) NOT NULL,
  `codcita` varchar(4) NOT NULL,
  `codarea` varchar(2) NOT NULL,
  `mes` varchar(2) NOT NULL,
  `dia` varchar(2) NOT NULL,
  `hora` varchar(2) NOT NULL,
  `codalu` varchar(7) NOT NULL,
  `codmon` varchar(7) NOT NULL,
  `observacion` text NOT NULL,
  `estado` int(1) NOT NULL,
  `fechagen` datetime NOT NULL,
  `respuesta` text NOT NULL,
  `fechares` datetime NOT NULL,
  `archivores` text NOT NULL,
  `soporte` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `citaspares`
--
ALTER TABLE `citaspares`
  ADD PRIMARY KEY (`anio`,`sems`,`codcita`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
