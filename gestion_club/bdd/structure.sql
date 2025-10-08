-- ===================================================================
--  Base de données "Gestionnaire_club" (version finale)
--  Un seul club omnisport, avec plusieurs sections (clubs = sports)
-- ===================================================================

-- (Optionnel)
CREATE DATABASE IF NOT EXISTS Gestionnaire_club
  CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE Gestionnaire_club;

-- -----------------------
-- Reset pour rejouer
-- -----------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS adherent_club;
DROP TABLE IF EXISTS adherent;
DROP TABLE IF EXISTS club;
DROP TABLE IF EXISTS niveau;
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------
-- 1) NIVEAU (référence)
-- -----------------------
CREATE TABLE niveau (
  id_niveau INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom_niveau VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO niveau (nom_niveau) VALUES
  ('Amateur'), ('Intermédiaire'), ('Avancé'), ('Expert');

-- -----------------------
-- 2) CLUB
--     - chaque club = une section sportive (ex : foot, rugby)
-- -----------------------
CREATE TABLE club (
  id_club INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(150) NOT NULL,        -- ex : "Section Football"
  sport VARCHAR(120) NOT NULL,      -- ex : "football"
  lieu VARCHAR(150) NULL,
  date_creation DATE NULL,
  INDEX idx_club_nom (nom),
  INDEX idx_club_sport (sport)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------
-- 3) ADHERENT
-- -----------------------
CREATE TABLE adherent (
  id_adherent INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sexe   ENUM('M','F','Autre') NULL,
  nom    VARCHAR(120) NOT NULL,
  prenom VARCHAR(120) NOT NULL,
  age    TINYINT UNSIGNED NULL,
  id_niveau INT UNSIGNED NULL,
  CONSTRAINT fk_adherent_niveau
    FOREIGN KEY (id_niveau) REFERENCES niveau(id_niveau)
    ON UPDATE CASCADE ON DELETE SET NULL,
  INDEX idx_adherent_nom (nom, prenom),
  INDEX idx_adherent_niveau (id_niveau)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- -----------------------
-- 4) ADHERENT_CLUB
--     - relie un adhérent à une section sportive (club)
-- -----------------------
CREATE TABLE adherent_club (
  id_adherent_club INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_adherent INT UNSIGNED NOT NULL,
  id_club     INT UNSIGNED NOT NULL,
  saison VARCHAR(9) NOT NULL,  -- ex: '2024/2025'
  statut ENUM('inscrit','en_attente','resilie') DEFAULT 'inscrit',
  date_inscription DATE NOT NULL DEFAULT (CURRENT_DATE),
  CONSTRAINT fk_ac_adherent
    FOREIGN KEY (id_adherent) REFERENCES adherent(id_adherent)
    ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_ac_club
    FOREIGN KEY (id_club) REFERENCES club(id_club)
    ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE KEY uq_ac_unique (id_adherent, id_club, saison),
  INDEX idx_ac_saison (saison),
  INDEX idx_ac_statut (statut)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
