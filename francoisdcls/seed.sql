-- Minimal seed data for local testing
INSERT INTO ecuries (nom_ecuries, siege) VALUES ('SmokeEcurie', 'France');
INSERT INTO ecuries (nom_ecuries, siege) VALUES ('Test Team', 'UK');

INSERT INTO pilotes (prenom, nom, nationalite, photo) VALUES ('Jean', 'Dupont', 'France', 'https://example.org/photo1.png');
INSERT INTO pilotes (prenom, nom, nationalite, photo) VALUES ('Ana', 'Silva', 'Portugal', 'https://example.org/photo2.png');

INSERT INTO participations (annee, pilote_id, ecurie_id) VALUES (2020, 1, 1);

