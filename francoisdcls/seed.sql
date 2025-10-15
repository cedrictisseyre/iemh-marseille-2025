-- Seed minimal pour tests locaux

INSERT INTO ecuries (nom, pays, annee_creation) VALUES
('Scuderia Example', 'Italie', 1929),
('Team Demo', 'France', 2001);

INSERT INTO pilotes (prenom, nom, annee_naissance, nationalite, photo, ecurie_id) VALUES
('Jean', 'Dupont', 1987, 'France', NULL, 2),
('Mario', 'Rossi', 1990, 'Italie', NULL, 1);

INSERT INTO participations (pilote_id, annee, points, ecurie_id, position_finale) VALUES
(1, 2022, 12, 2, 8),
(2, 2022, 45, 1, 2);
