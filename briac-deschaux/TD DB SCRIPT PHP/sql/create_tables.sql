-- Création / adaptation du schéma pour supporter import large de données

DROP TABLE IF EXISTS stats;
DROP TABLE IF EXISTS player;
DROP TABLE IF EXISTS team;

CREATE TABLE team (
  id_team BIGINT PRIMARY KEY,
  nom_team VARCHAR(200) NOT NULL,
  ville VARCHAR(200) DEFAULT NULL,
  conference VARCHAR(10) DEFAULT NULL,
  division VARCHAR(50) DEFAULT NULL,
  logo_url VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE player (
  id_player BIGINT PRIMARY KEY,
  prenom VARCHAR(150) NOT NULL,
  nom VARCHAR(150) NOT NULL,
  poste VARCHAR(50) DEFAULT NULL,
  age INT DEFAULT NULL,
  taille_cm INT DEFAULT NULL,
  poids_kg INT DEFAULT NULL,
  annee_debut INT DEFAULT NULL,
  id_team BIGINT,
  FOREIGN KEY (id_team) REFERENCES team(id_team) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE stats (
  id_stat BIGINT PRIMARY KEY AUTO_INCREMENT,
  id_player BIGINT NOT NULL,
  saison INT NOT NULL,
  passing_yards INT DEFAULT 0,
  passing_tds INT DEFAULT 0,
  interceptions INT DEFAULT 0,
  rushing_yards INT DEFAULT 0,
  rushing_tds INT DEFAULT 0,
  receptions INT DEFAULT 0,
  receiving_yards INT DEFAULT 0,
  receiving_tds INT DEFAULT 0,
  tackles INT DEFAULT 0,
  sacks DECIMAL(6,2) DEFAULT 0.0,
  interceptions_def INT DEFAULT 0,
  fg_made INT DEFAULT 0,
  punts INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_player) REFERENCES player(id_player) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY uniq_stat (id_player, saison)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
