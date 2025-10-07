-- Table des coureurs
CREATE TABLE IF NOT EXISTS runners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(50) NOT NULL,
    birth DATE NOT NULL,
    team VARCHAR(100) NOT NULL,
    info TEXT
);

-- Table des résultats
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stage VARCHAR(100) NOT NULL,
    runner VARCHAR(100) NOT NULL,
    rank INT NOT NULL,
    time VARCHAR(20) NOT NULL
);
