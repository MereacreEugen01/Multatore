-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 01, 2022 at 02:46 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_multe`
--

-- --------------------------------------------------------

--
-- Table structure for table `multe`
--

CREATE TABLE `multe` (
  `ID_Multa` int(16) NOT NULL,
  `CF` varchar(16) NOT NULL,
  `ID_effrazione` int(5) NOT NULL,
  `Data` date NOT NULL,
  `Ora` varchar(5) NOT NULL,
  `Luogo` varchar(100) NOT NULL,
  `importo_da_pagare` int(100) NOT NULL,
  `foto` text NOT NULL,
  `longitudine` text NOT NULL,
  `latitudine` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `multe`
--

INSERT INTO `multe` (`ID_Multa`, `CF`, `ID_effrazione`, `Data`, `Ora`, `Luogo`, `importo_da_pagare`, `foto`, `longitudine`, `latitudine`) VALUES
(1, 'BNTLSE03L19G713L', 1, '2022-02-17', '15:45', 'Viale Adua 217, Pistoia, Italia, 51100', 80, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `operatore`
--

CREATE TABLE `operatore` (
  `CF` varchar(16) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(20) NOT NULL,
  `accessoDB?` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `operatore`
--

INSERT INTO `operatore` (`CF`, `username`, `password`, `accessoDB?`) VALUES
('BNTLSE03L19G713L', 'eliasbonti', '1234', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tipoeffrazione`
--

CREATE TABLE `tipoeffrazione` (
  `ID_effrazione` int(3) NOT NULL,
  `Tipologia` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tipoeffrazione`
--

INSERT INTO `tipoeffrazione` (`ID_effrazione`, `Tipologia`) VALUES
(1, 'Divieto di Sosta'),
(2, 'Parcheggio su Strisce Pedonali'),
(3, 'Revisione Scaduta');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `multe`
--
ALTER TABLE `multe`
  ADD PRIMARY KEY (`ID_Multa`),
  ADD UNIQUE KEY `CF` (`CF`),
  ADD UNIQUE KEY `ID_effrazione` (`ID_effrazione`);

--
-- Indexes for table `operatore`
--
ALTER TABLE `operatore`
  ADD PRIMARY KEY (`CF`);

--
-- Indexes for table `tipoeffrazione`
--
ALTER TABLE `tipoeffrazione`
  ADD PRIMARY KEY (`ID_effrazione`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `multe`
--
ALTER TABLE `multe`
  ADD CONSTRAINT `multe_ibfk_1` FOREIGN KEY (`CF`) REFERENCES `operatore` (`CF`),
  ADD CONSTRAINT `multe_ibfk_2` FOREIGN KEY (`ID_effrazione`) REFERENCES `tipoeffrazione` (`ID_effrazione`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
