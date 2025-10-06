-- =============================================================
--  CREATE TABLES SCRIPT – NFL Stats Analyzer
--  Auteur : Briac Deschaux
--  Description : Structure complète de la base de données
-- =============================================================

-- Supprimer les tables existantes pour éviter les doublons
DROP TABLE IF EXISTS stats;
DROP TABLE IF EXISTS player;
DROP TABLE IF EXISTS team;

-- =============================================================
--  Table : team
--  Description : Contient les équipes NFL avec nom et localisation
-- =============================================================
CREATE TABLE team (
    id_team INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    city VARCHAR(100) NOT NULL,
    conference ENUM('AFC', 'NFC') NOT NULL,
    division VARCHAR(50) NOT NULL,
    logo_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================================
--  Table : player
--  Description : Liste des joueurs appartenant à une équipe
-- =============================================================
CREATE TABLE player (
    id_player INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    position ENUM('QB','RB','WR','TE','K','DEF','OL','DL','LB','CB','S') NOT NULL,
    number INT NOT NULL CHECK (number BETWEEN 1 AND 99),
    height_cm INT DEFAULT NULL,
    weight_kg INT DEFAULT NULL,
    birth_date DATE DEFAULT NULL,
    id_team INT,
    FOREIGN KEY (id_team) REFERENCES team(id_team) ON DELETE SET NULL ON UPDATE CASCADE
);

-- =============================================================
--  Table : stats
--  Description : Statistiques de match par joueur
-- =============================================================
CREATE TABLE stats (
    id_stat INT AUTO_INCREMENT PRIMARY KEY,
    id_player INT NOT NULL,
    game_date DATE NOT NULL,
    opponent VARCHAR(100) NOT NULL,
    passing_yards INT DEFAULT 0,
    rushing_yards INT DEFAULT 0,
    receiving_yards INT DEFAULT 0,
    touchdowns INT DEFAULT 0,
    interceptions INT DEFAULT 0,
    fumbles INT DEFAULT 0,
    tackles INT DEFAULT 0,
    sacks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_player) REFERENCES player(id_player) ON DELETE CASCADE ON UPDATE CASCADE
);

-- =============================================================
--  Table d’exemple (optionnelle) : utilisateur admin
--  Permet de gérer l’authentification si besoin
-- =============================================================
CREATE TABLE IF NOT EXISTS admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================================
--  Insertion de quelques données d’exemple
-- =============================================================

INSERT INTO team (name, city, conference, division)
VALUES
('Kansas City Chiefs', 'Kansas City', 'AFC', 'West'),
('Dallas Cowboys', 'Dallas', 'NFC', 'East'),
('Buffalo Bills', 'Buffalo', 'AFC', 'East');

INSERT INTO player (first_name, last_name, position, number, id_team)
VALUES
('Patrick', 'Mahomes', 'QB', 15, 1),
('Travis', 'Kelce', 'TE', 87, 1),
('Josh', 'Allen', 'QB', 17, 3);

INSERT INTO stats (id_player, game_date, opponent, passing_yards, touchdowns)
VALUES
(1, '2024-11-03', 'Buffalo Bills', 356, 3),
(3, '2024-11-03', 'Kansas City Chiefs', 280, 2);
