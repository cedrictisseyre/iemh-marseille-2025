-- Structure minimale pour la base coline_pontal
-- Tables principales : club, championnat, karateka, participation
CREATE TABLE IF NOT EXISTS club (
  id_club INT AUTO_INCREMENT PRIMARY KEY,
  nom_club VARCHAR(255) NOT NULL,
  ville VARCHAR(100),
  pays VARCHAR(100),
  date_creation DATE
);

CREATE TABLE IF NOT EXISTS championnat (
  id_championnat INT AUTO_INCREMENT PRIMARY KEY,
  nom_championnat VARCHAR(255) NOT NULL,
  lieu VARCHAR(255),
  date_championnat DATE,
  type VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS karateka (
  id_karateka INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100),
  prenom VARCHAR(100),
  date_naissance DATE,
  sexe CHAR(1),
  grade VARCHAR(50),
  id_club INT
);

CREATE TABLE IF NOT EXISTS participation (
  id_participation INT AUTO_INCREMENT PRIMARY KEY,
  id_karateka INT,
  id_championnat INT,
  epreuve VARCHAR(50),
  sexe VARCHAR(10),
  equipe VARCHAR(50),
  categorie VARCHAR(50),
  resultat VARCHAR(50)
);
