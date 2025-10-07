-- Table des courses officielles
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO courses (name) VALUES
('Kobe Trail'),
('Jin Shan Ling Great Wall Trail'),
('Il Golfo dell''Isola Trail'),
('Zegama-Aizkorri'),
('Broken Arrow Skyrace'),
('Tepec Trail'),
('Salomon Pitz Alpine Glacier Trail'),
('Sierre-Zinal'),
('Ledro Sky Trentino Grand Finale');

-- Table des coureurs
CREATE TABLE IF NOT EXISTS runners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(50) NOT NULL,
    birth DATE NOT NULL,
    team VARCHAR(100) NOT NULL,
    gender VARCHAR(10) NOT NULL,
    info TEXT
);

-- Table des résultats
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL,
    runner_id INT NOT NULL,
    rank INT NOT NULL,
    time VARCHAR(20) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (runner_id) REFERENCES runners(id) ON DELETE CASCADE
);
