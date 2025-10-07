-- Ajout du champ sexe à la table runners
ALTER TABLE runners ADD COLUMN gender VARCHAR(10) NOT NULL DEFAULT 'Homme';

-- Création des tables avec liaison
DROP TABLE IF EXISTS results;
DROP TABLE IF EXISTS runners;

CREATE TABLE runners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(50) NOT NULL,
    birth DATE NOT NULL,
    team VARCHAR(100) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    info TEXT
);

CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stage VARCHAR(100) NOT NULL,
    runner_id INT NOT NULL,
    rank INT NOT NULL,
    time VARCHAR(20) NOT NULL,
    FOREIGN KEY (runner_id) REFERENCES runners(id) ON DELETE CASCADE
);
