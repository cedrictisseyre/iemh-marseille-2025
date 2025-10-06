<?php
$host = '195.15.235.20';       // IP fournie par ton prof
$dbname = 'thomas_fajolle';    // Nom de ta base
$username = 'root';            // Login fourni
$password = 'INNnsk40374';    // Mot de passe fourni
$port = 3306;                  // Port MySQL par défaut

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connexion réussie"; // Décommenter juste pour tester
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>
