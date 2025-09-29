-- Création des tables minimales et insertion de données d'exemple pour pantheon_pilotes.php

CREATE TABLE IF NOT EXISTS pilotes (
    pilote_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(50),
    prénom VARCHAR(50),
    photo VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS championnats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pilote_id INT,
    annee INT,
    FOREIGN KEY (pilote_id) REFERENCES pilotes(pilote_id)
);

CREATE TABLE IF NOT EXISTS participations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    pilote_id INT,
    annee INT,
    FOREIGN KEY (pilote_id) REFERENCES pilotes(pilote_id)
);

-- Insertion de pilotes
INSERT INTO pilotes (nom, prénom, photo) VALUES
('Hamilton', 'Lewis', 'https://upload.wikimedia.org/wikipedia/commons/8/81/Lewis_Hamilton_2016_Malaysia_2.jpg'),
('Verstappen', 'Max', 'https://upload.wikimedia.org/wikipedia/commons/6/6e/Max_Verstappen_2017_Malaysia_3.jpg');

-- Insertion de championnats
INSERT INTO championnats (pilote_id, annee) VALUES
(1, 2008), (1, 2014), (1, 2015), (1, 2017), (1, 2018), (1, 2019), (1, 2020),
(2, 2021), (2, 2022), (2, 2023);

-- Insertion de participations
INSERT INTO participations (pilote_id, annee) VALUES
(1, 2007), (1, 2008), (1, 2009), (1, 2010), (1, 2011), (1, 2012), (1, 2013), (1, 2014), (1, 2015), (1, 2016), (1, 2017), (1, 2018), (1, 2019), (1, 2020), (1, 2021), (1, 2022), (1, 2023),
(2, 2015), (2, 2016), (2, 2017), (2, 2018), (2, 2019), (2, 2020), (2, 2021), (2, 2022), (2, 2023);
