-- =============================================================
-- CREATE TABLES SCRIPT – NFL Stats Analyzer
-- Auteur : Briac Deschaux
-- Description : Structure complète de la base de données
-- =============================================================

DROP TABLE IF EXISTS stats;
DROP TABLE IF EXISTS player;
DROP TABLE IF EXISTS team;

-- Table team
CREATE TABLE team (
    id_team INT AUTO_INCREMENT PRIMARY KEY,
    nom_team VARCHAR(100) NOT NULL UNIQUE,
    ville VARCHAR(100) NOT NULL,
    conference ENUM('AFC','NFC') NOT NULL,
    division VARCHAR(50) NOT NULL,
    logo_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table player
CREATE TABLE player (
    id_player INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    poste VARCHAR(20) NOT NULL,
    age INT DEFAULT NULL,
    taille_cm INT DEFAULT NULL,
    poids_kg INT DEFAULT NULL,
    annee_debut INT DEFAULT NULL,
    id_team INT DEFAULT NULL,
    FOREIGN KEY (id_team) REFERENCES team(id_team) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table stats
CREATE TABLE stats (
    id_stat INT AUTO_INCREMENT PRIMARY KEY,
    id_player INT NOT NULL,
    saison INT NOT NULL,
    yards_passe INT DEFAULT 0,
    td_passe INT DEFAULT 0,
    interceptions INT DEFAULT 0,
    yards_course INT DEFAULT 0,
    td_course INT DEFAULT 0,
    receptions INT DEFAULT 0,
    yards_reception INT DEFAULT 0,
    td_reception INT DEFAULT 0,
    plaquages INT DEFAULT 0,
    sacks DECIMAL(4,1) DEFAULT 0.0,
    interceptions_def INT DEFAULT 0,
    fg_reussis INT DEFAULT 0,
    punts INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_player) REFERENCES player(id_player) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Données d'exemple
INSERT INTO team (nom_team, ville, conference, division) VALUES
('Kansas City Chiefs', 'Kansas City', 'AFC', 'West'),
('Dallas Cowboys', 'Dallas', 'NFC', 'East'),
('Buffalo Bills', 'Buffalo', 'AFC', 'East');

INSERT INTO player (prenom, nom, poste, age, id_team) VALUES
('Patrick', 'Mahomes', 'QB', 28, 1),
('Travis', 'Kelce', 'TE', 34, 1),
('Josh', 'Allen', 'QB', 27, 3);

INSERT INTO stats (id_player, saison, yards_passe, td_passe) VALUES
(1, YEAR(CURDATE()), 356, 3),
(3, YEAR(CURDATE()), 280, 2);
