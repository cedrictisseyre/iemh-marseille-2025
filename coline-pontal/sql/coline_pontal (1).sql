-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el7.remi
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 30, 2025 at 09:23 AM
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
-- Database: `coline_pontal`
--

-- --------------------------------------------------------

--
-- Table structure for table `championnat`
--

CREATE TABLE `championnat` (
  `id_championnat` int(11) NOT NULL,
  `nom_championnat` varchar(50) NOT NULL,
  `lieu` varchar(50) NOT NULL,
  `date_championnat` date NOT NULL,
  `type` enum('Régional','National','International') DEFAULT NULL
) ;

--
-- Dumping data for table `championnat`
--

INSERT INTO `championnat` (`id_championnat`, `nom_championnat`, `lieu`, `date_championnat`, `type`) VALUES
(1, 'Jeux Olympiques Tokyo 2020', 'Tokyo', '2021-08-05', 'International'),
(2, 'Championnat du Monde WKF 2018', 'Madrid', '2018-11-06', 'International'),
(3, 'Championnat du Japon 2019', 'Tokyo', '2019-12-01', 'National');

-- --------------------------------------------------------

--
-- Table structure for table `club`
--

CREATE TABLE `club` (
  `id_club` int(11) NOT NULL,
  `nom_club` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL,
  `pays` varchar(50) NOT NULL,
  `date_creation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `club`
--

INSERT INTO `club` (`id_club`, `nom_club`, `ville`, `pays`, `date_creation`) VALUES
(1, 'JKA Honbu Dojo', 'Tokyo', 'Japon', '1949-05-22'),
(2, 'Inoue-ha Shito-ryu Keishin-kai', 'Osaka', 'Japon', '1962-03-15'),
(3, 'Académie de Karaté France', 'Paris', 'France', '1980-09-01'),
(4, 'Tensho dojo', 'Peypin', 'France', '2021-09-25');

-- --------------------------------------------------------

--
-- Table structure for table `karateka`
--

CREATE TABLE `karateka` (
  `id_karateka` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `date_naissance` date NOT NULL,
  `sexe` enum('M','F') NOT NULL,
  `grade` varchar(50) NOT NULL,
  `id_club` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `karateka`
--

INSERT INTO `karateka` (`id_karateka`, `nom`, `prenom`, `date_naissance`, `sexe`, `grade`, `id_club`) VALUES
(1, 'Funakoshi', 'Gichin', '1868-11-10', 'M', 'Fondateur du Shotokan', 1),
(2, 'Nishiyama', 'Hidetaka', '1928-10-10', 'M', 'Ceinture noire 10e dan', 1),
(3, 'Kagawa', 'Masao', '1955-05-08', 'M', 'Ceinture noire 9e dan', 1),
(4, 'Inoue', 'Yoshimi', '1946-09-10', 'M', 'Maître Shito-ryu', 2),
(5, 'Sanchez', 'Sandra', '1981-10-09', 'F', 'Championne olympique kata', 3);

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

CREATE TABLE `participation` (
  `id_participation` int(11) NOT NULL,
  `id_karateka` int(11) NOT NULL,
  `id_championnat` int(11) NOT NULL,
  `epreuve` enum('Kumite','Kata','Fukugo') NOT NULL,
  `sexe` enum('H','F') NOT NULL,
  `equipe` enum('Individuel','Équipe') NOT NULL,
  `categorie` enum('-60kg','-67kg','-75kg','-84kg','+84kg','-50kg','-55kg','-61kg','-68kg','+68kg','Open') NOT NULL,
  `resultat` enum('Or','Argent','Bronze','Quart de finale','Huitième de finale','Éliminé','Abandon','Disqualification','Non classé') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `participation`
--

INSERT INTO `participation` (`id_participation`, `id_karateka`, `id_championnat`, `epreuve`, `sexe`, `equipe`, `categorie`, `resultat`) VALUES
(1, 5, 1, 'Kata', 'F', 'Individuel', 'Open', 'Or'),
(2, 5, 2, 'Kata', 'F', 'Individuel', 'Open', 'Or'),
(3, 3, 3, 'Kumite', 'H', 'Individuel', '-84kg', 'Or');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `championnat`
--
ALTER TABLE `championnat`
  ADD PRIMARY KEY (`id_championnat`),
  ADD KEY `idx_champ_par_date` (`date_championnat`);

--
-- Indexes for table `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`id_club`),
  ADD UNIQUE KEY `uq_club` (`nom_club`,`ville`);

--
-- Indexes for table `karateka`
--
ALTER TABLE `karateka`
  ADD PRIMARY KEY (`id_karateka`),
  ADD KEY `idx_kara_club` (`id_club`,`nom`,`prenom`);

--
-- Indexes for table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id_participation`),
  ADD UNIQUE KEY `uq_participation` (`id_karateka`,`id_championnat`,`epreuve`,`categorie`,`equipe`) USING BTREE,
  ADD KEY `idx_part_par_champ` (`id_championnat`,`categorie`,`resultat`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `championnat`
--
ALTER TABLE `championnat`
  MODIFY `id_championnat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `club`
--
ALTER TABLE `club`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `karateka`
--
ALTER TABLE `karateka`
  MODIFY `id_karateka` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `participation`
--
ALTER TABLE `participation`
  MODIFY `id_participation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `karateka`
--
ALTER TABLE `karateka`
  ADD CONSTRAINT `fk_karateka_club` FOREIGN KEY (`id_club`) REFERENCES `club` (`id_club`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `fk_participation_championnat` FOREIGN KEY (`id_championnat`) REFERENCES `championnat` (`id_championnat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_participation_karateka` FOREIGN KEY (`id_karateka`) REFERENCES `karateka` (`id_karateka`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
