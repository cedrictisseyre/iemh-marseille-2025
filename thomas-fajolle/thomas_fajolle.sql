-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el7.remi
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 06, 2025 at 09:01 AM
-- Server version: 11.1.3-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thomas_fajolle`
--

-- --------------------------------------------------------

--
-- Table structure for table `joueurs`
--

CREATE TABLE `joueurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `numero` int(11) DEFAULT NULL,
  `poste` varchar(30) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `pied_fort` enum('Gauche','Droit','Ambidextre') DEFAULT 'Droit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `joueurs`
--

INSERT INTO `joueurs` (`id`, `nom`, `prenom`, `numero`, `poste`, `nationalite`, `date_naissance`, `pied_fort`) VALUES
(1, 'López', 'Pau', 16, 'Gardien', 'Espagnole', '1994-12-13', 'Droit'),
(2, 'Blanco', 'Rubén', 36, 'Gardien', 'Espagnole', '1995-07-25', 'Droit'),
(3, 'Balerdi', 'Leonardo', 5, 'Défenseur central', 'Argentine', '1999-01-26', 'Droit'),
(4, 'Gigot', 'Samuel', 4, 'Défenseur central', 'Française', '1993-10-12', 'Droit'),
(5, 'Merlin', 'Quentin', 3, 'Latéral gauche', 'Française', '2002-05-16', 'Gauche'),
(6, 'Clauss', 'Jonathan', 7, 'Latéral droit', 'Française', '1992-09-25', 'Droit'),
(7, 'Veretout', 'Jordan', 27, 'Milieu central', 'Française', '1993-03-01', 'Droit'),
(8, 'Kondogbia', 'Geoffrey', 19, 'Milieu défensif', 'Centrafricaine', '1993-02-15', 'Gauche'),
(9, 'Harit', 'Amine', 11, 'Milieu offensif', 'Marocaine', '1997-06-18', 'Droit'),
(10, 'Ndiaye', 'Iliman', 10, 'Ailier gauche', 'Sénégalaise', '2000-03-06', 'Droit'),
(11, 'Correa', 'Joaquín', 20, 'Ailier droit', 'Argentine', '1994-08-13', 'Gauche'),
(12, 'Aubameyang', 'Pierre-Emerick', 9, 'Attaquant', 'Gabonaise', '1989-06-18', 'Droit'),
(13, 'Luis Henrique', 'Luis', 44, 'Ailier gauche', 'Brésilienne', '2001-12-14', 'Gauche'),
(14, 'Meïté', 'Bamo', 99, 'Défenseur central', 'Ivoirienne', '2001-06-20', 'Droit'),
(15, 'Mughe', 'Franck', 24, 'Ailier droit', 'Camerounaise', '2004-06-18', 'Droit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `joueurs`
--
ALTER TABLE `joueurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `joueurs`
--
ALTER TABLE `joueurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
