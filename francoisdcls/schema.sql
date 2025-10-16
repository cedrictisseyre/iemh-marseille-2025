-- Minimal schema compatible with SQLite and MySQL (pilotes, ecuries, participations)

CREATE TABLE IF NOT EXISTS ecuries (
  ecurie_id INTEGER PRIMARY KEY AUTOINCREMENT,
  nom_ecuries VARCHAR(255) NOT NULL,
  siege VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS pilotes (
  pilote_id INTEGER PRIMARY KEY AUTOINCREMENT,
  prenom VARCHAR(100) NOT NULL,
  nom VARCHAR(100) NOT NULL,
  nationalite VARCHAR(100),
  photo VARCHAR(512)
);

CREATE TABLE IF NOT EXISTS participations (
  participation_id INTEGER PRIMARY KEY AUTOINCREMENT,
  annee INTEGER NOT NULL,
  pilote_id INTEGER NOT NULL,
  ecurie_id INTEGER NOT NULL,
  FOREIGN KEY(pilote_id) REFERENCES pilotes(pilote_id),
  FOREIGN KEY(ecurie_id) REFERENCES ecuries(ecurie_id)
);
