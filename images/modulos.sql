-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2017 at 05:05 AM
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

--
-- Dumping data for table `modulos`
--

INSERT INTO `modulos` (`nivel`, `seccion`, `codigo`, `subcodigo`, `descripcion`) VALUES
(3, 1, '01', '00', 'MONITORIAS PERSONALIZADAS/-/Programa de Monitorias Privadas/-/En este modulo usted prodrá agendar, verificar estados y ver disponibilidad de citas personalizadas con los tutuores/-/../images/personalizada.jpg/-/PARES/-/'),
(3, 1, '01', '01', 'Agendar cita/-/../images/agendar.png?v=2/-/'),
(3, 1, '01', '02', 'Mis citas agendadas/-/../images/historial.png?v=2/-/'),
(3, 1, '02', '00', 'MONITORIAS COMUNES/-/Programa de Ambientes de estudio/-/En este modulo usted podra consultar e inscribirse a los distintos ambientes de estudios dispuestos por la direccion Academica/-/../images/ambiente.jpg/-/AMBIENTES/-/'),
(3, 1, '02', '01', 'Inscribirse en un horario/-/../images/calendario_buscar.png/-/'),
(3, 1, '02', '02', 'Mis asistencias a tutorias/-/../images/historial.png?v=2/-/'),
(3, 1, '03', '00', 'FOROS/-/Publicacion y Visiualizacion de informacion/-/En este modulo usted podra publicar y visualizar publicaciones utiles para las distintas areas academicas. /-/../images/foro.jpg/-/FOROS/-/');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
