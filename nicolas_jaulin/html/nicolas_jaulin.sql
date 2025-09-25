-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el7.remi
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 25, 2025 at 11:54 AM
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
-- Database: `nicolas_jaulin`
--

-- --------------------------------------------------------

--
-- Table structure for table `Arbitres`
--

CREATE TABLE `Arbitres` (
  `id_arbitre` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Arbitres`
--

INSERT INTO `Arbitres` (`id_arbitre`, `nom`, `prenom`, `nationalite`) VALUES
(1, 'Poite', 'Romain', 'Française');

-- --------------------------------------------------------

--
-- Table structure for table `Equipes`
--

CREATE TABLE `Equipes` (
  `id_equipe` int(11) NOT NULL,
  `nom_equipe` varchar(100) NOT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `annee_creation` year(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Equipes`
--

INSERT INTO `Equipes` (`id_equipe`, `nom_equipe`, `ville`, `annee_creation`) VALUES
(1, 'Les Titans Rugby', 'Toulouse', '1985'),
(2, 'Dragons Rouges', 'Bordeaux', '1992');

-- --------------------------------------------------------

--
-- Table structure for table `Joueurs`
--

CREATE TABLE `Joueurs` (
  `id_joueur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `poste` varchar(50) DEFAULT NULL,
  `id_equipe` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Joueurs`
--

INSERT INTO `Joueurs` (`id_joueur`, `nom`, `prenom`, `date_naissance`, `poste`, `id_equipe`) VALUES
(1, 'Dupont', 'Antoine', '1996-11-15', 'Demi de mêlée', 1),
(2, 'Marchand', 'Julien', '1995-05-10', 'Talonneur', 1),
(3, 'Penaud', 'Damian', '1996-09-25', 'Ailier', 2);

-- --------------------------------------------------------

--
-- Table structure for table `Matchs`
--

CREATE TABLE `Matchs` (
  `id_match` int(11) NOT NULL,
  `date_match` date DEFAULT NULL,
  `lieu` varchar(100) DEFAULT NULL,
  `id_equipe_dom` int(11) DEFAULT NULL,
  `id_equipe_ext` int(11) DEFAULT NULL,
  `score_dom` int(11) DEFAULT NULL,
  `score_ext` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Matchs`
--

INSERT INTO `Matchs` (`id_match`, `date_match`, `lieu`, `id_equipe_dom`, `id_equipe_ext`, `score_dom`, `score_ext`) VALUES
(1, '2025-03-10', 'Toulouse', 1, 2, 25, 20);

-- --------------------------------------------------------

--
-- Table structure for table `Matchs_Arbitres`
--

CREATE TABLE `Matchs_Arbitres` (
  `id_match` int(11) NOT NULL,
  `id_arbitre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Matchs_Arbitres`
--

INSERT INTO `Matchs_Arbitres` (`id_match`, `id_arbitre`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `Statistiques_Joueur`
--

CREATE TABLE `Statistiques_Joueur` (
  `id_stat` int(11) NOT NULL,
  `id_match` int(11) DEFAULT NULL,
  `id_joueur` int(11) DEFAULT NULL,
  `essais` int(11) DEFAULT 0,
  `plaquages` int(11) DEFAULT 0,
  `passes_decisives` int(11) DEFAULT 0,
  `cartons_jaunes` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `Statistiques_Joueur`
--

INSERT INTO `Statistiques_Joueur` (`id_stat`, `id_match`, `id_joueur`, `essais`, `plaquages`, `passes_decisives`, `cartons_jaunes`) VALUES
(1, 1, 1, 1, 12, 2, 0),
(2, 1, 3, 2, 5, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Arbitres`
--
ALTER TABLE `Arbitres`
  ADD PRIMARY KEY (`id_arbitre`);

--
-- Indexes for table `Equipes`
--
ALTER TABLE `Equipes`
  ADD PRIMARY KEY (`id_equipe`);

--
-- Indexes for table `Joueurs`
--
ALTER TABLE `Joueurs`
  ADD PRIMARY KEY (`id_joueur`),
  ADD KEY `id_equipe` (`id_equipe`);

--
-- Indexes for table `Matchs`
--
ALTER TABLE `Matchs`
  ADD PRIMARY KEY (`id_match`),
  ADD KEY `id_equipe_dom` (`id_equipe_dom`),
  ADD KEY `id_equipe_ext` (`id_equipe_ext`);

--
-- Indexes for table `Matchs_Arbitres`
--
ALTER TABLE `Matchs_Arbitres`
  ADD PRIMARY KEY (`id_match`,`id_arbitre`),
  ADD KEY `id_arbitre` (`id_arbitre`);

--
-- Indexes for table `Statistiques_Joueur`
--
ALTER TABLE `Statistiques_Joueur`
  ADD PRIMARY KEY (`id_stat`),
  ADD KEY `id_match` (`id_match`),
  ADD KEY `id_joueur` (`id_joueur`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Arbitres`
--
ALTER TABLE `Arbitres`
  MODIFY `id_arbitre` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Equipes`
--
ALTER TABLE `Equipes`
  MODIFY `id_equipe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Joueurs`
--
ALTER TABLE `Joueurs`
  MODIFY `id_joueur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Matchs`
--
ALTER TABLE `Matchs`
  MODIFY `id_match` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Statistiques_Joueur`
--
ALTER TABLE `Statistiques_Joueur`
  MODIFY `id_stat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Joueurs`
--
ALTER TABLE `Joueurs`
  ADD CONSTRAINT `Joueurs_ibfk_1` FOREIGN KEY (`id_equipe`) REFERENCES `Equipes` (`id_equipe`) ON DELETE SET NULL;

--
-- Constraints for table `Matchs`
--
ALTER TABLE `Matchs`
  ADD CONSTRAINT `Matchs_ibfk_1` FOREIGN KEY (`id_equipe_dom`) REFERENCES `Equipes` (`id_equipe`) ON DELETE CASCADE,
  ADD CONSTRAINT `Matchs_ibfk_2` FOREIGN KEY (`id_equipe_ext`) REFERENCES `Equipes` (`id_equipe`) ON DELETE CASCADE;

--
-- Constraints for table `Matchs_Arbitres`
--
ALTER TABLE `Matchs_Arbitres`
  ADD CONSTRAINT `Matchs_Arbitres_ibfk_1` FOREIGN KEY (`id_match`) REFERENCES `Matchs` (`id_match`) ON DELETE CASCADE,
  ADD CONSTRAINT `Matchs_Arbitres_ibfk_2` FOREIGN KEY (`id_arbitre`) REFERENCES `Arbitres` (`id_arbitre`) ON DELETE CASCADE;

--
-- Constraints for table `Statistiques_Joueur`
--
ALTER TABLE `Statistiques_Joueur`
  ADD CONSTRAINT `Statistiques_Joueur_ibfk_1` FOREIGN KEY (`id_match`) REFERENCES `Matchs` (`id_match`) ON DELETE CASCADE,
  ADD CONSTRAINT `Statistiques_Joueur_ibfk_2` FOREIGN KEY (`id_joueur`) REFERENCES `Joueurs` (`id_joueur`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
