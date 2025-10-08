-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Host: 195.15.235.20
-- Generation Time: Sep 30, 2025 at 07:29 PM
-- Server version: 11.1.3-MariaDB
-- PHP Version: 7.0.33-0ubuntu0.16.04.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `jade_mouillot`
--

-- --------------------------------------------------------

--
-- Table structure for table `club`
--

CREATE TABLE `club` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `club`
--

INSERT INTO `club` (`id`, `nom`) VALUES
(1, 'athle_aix'),
(3, 'toulon_athe'),
(2, 'triatlon_aix');

-- --------------------------------------------------------

--
-- Table structure for table `club_membership`
--

CREATE TABLE `club_membership` (
  `id` int(11) NOT NULL,
  `sportif_id` int(11) NOT NULL,
  `club_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `club_membership`
--

INSERT INTO `club_membership` (`id`, `sportif_id`, `club_id`, `start_date`, `end_date`) VALUES
(1, 1, 1, '2000-01-01', NULL),
(2, 4, 1, '2000-01-01', NULL),
(3, 5, 1, '2000-01-01', NULL),
(4, 2, 2, '2000-01-01', NULL),
(5, 3, 2, '2000-01-01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `nom`) VALUES
(3, '10km_toulon'),
(2, 'annecy_tourdulac'),
(1, 'meeting_piste');

-- --------------------------------------------------------

--
-- Table structure for table `discipline`
--

CREATE TABLE `discipline` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `discipline`
--

INSERT INTO `discipline` (`id`, `nom`) VALUES
(1, 'athletisme'),
(2, 'natation'),
(6, 'ski'),
(5, 'velo');

-- --------------------------------------------------------

--
-- Table structure for table `participation`
--

CREATE TABLE `participation` (
  `id` int(11) NOT NULL,
  `sportif_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `date_participation` date DEFAULT NULL,
  `resultat` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sportif`
--

CREATE TABLE `sportif` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `id_course` int(11) DEFAULT NULL,
  `id_discipline` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sportif`
--

INSERT INTO `sportif` (`id`, `nom`, `id_course`, `id_discipline`) VALUES
(1, 'mathieu', 1, 1),
(2, 'nathan', 2, 5),
(3, 'alexandre', 2, 2),
(4, 'ethan', 1, 1),
(5, 'sacha', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sportif_discipline`
--

CREATE TABLE `sportif_discipline` (
  `id` int(11) NOT NULL,
  `sportif_id` int(11) NOT NULL,
  `discipline_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_club_nom` (`nom`);

--
-- Indexes for table `club_membership`
--
ALTER TABLE `club_membership`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_active_membership` (`sportif_id`,`end_date`),
  ADD KEY `club_id` (`club_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_course_nom` (`nom`);

--
-- Indexes for table `discipline`
--
ALTER TABLE `discipline`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_discipline_nom` (`nom`);

--
-- Indexes for table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_sportif_course` (`sportif_id`,`course_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `sportif`
--
ALTER TABLE `sportif`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etrangere_id_discipline` (`id_discipline`),
  ADD KEY `etrangere_id_course` (`id_course`) USING BTREE,
  ADD KEY `idx_sportif_nom` (`nom`);

--
-- Indexes for table `sportif_discipline`
--
ALTER TABLE `sportif_discipline`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unq_sportif_discipline` (`sportif_id`,`discipline_id`),
  ADD KEY `discipline_id` (`discipline_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `club`
--
ALTER TABLE `club`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `club_membership`
--
ALTER TABLE `club_membership`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `discipline`
--
ALTER TABLE `discipline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `participation`
--
ALTER TABLE `participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `sportif`
--
ALTER TABLE `sportif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sportif_discipline`
--
ALTER TABLE `sportif_discipline`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `club_membership`
--
ALTER TABLE `club_membership`
  ADD CONSTRAINT `club_membership_ibfk_1` FOREIGN KEY (`sportif_id`) REFERENCES `sportif` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `club_membership_ibfk_2` FOREIGN KEY (`club_id`) REFERENCES `club` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `participation_ibfk_1` FOREIGN KEY (`sportif_id`) REFERENCES `sportif` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `participation_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sportif`
--
ALTER TABLE `sportif`
  ADD CONSTRAINT `Courses/Sportif` FOREIGN KEY (`id_course`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Discipline/Sportif` FOREIGN KEY (`id_discipline`) REFERENCES `discipline` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sportif_discipline`
--
ALTER TABLE `sportif_discipline`
  ADD CONSTRAINT `sportif_discipline_ibfk_1` FOREIGN KEY (`sportif_id`) REFERENCES `sportif` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sportif_discipline_ibfk_2` FOREIGN KEY (`discipline_id`) REFERENCES `discipline` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
