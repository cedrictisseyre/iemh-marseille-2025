-- Structure recommandée pour la table runners
CREATE TABLE IF NOT EXISTS runners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    country VARCHAR(100),
    birth DATE DEFAULT NULL,
    team VARCHAR(255) DEFAULT NULL,
    info TEXT DEFAULT NULL
);
