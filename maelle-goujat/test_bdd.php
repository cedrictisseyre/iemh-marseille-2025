<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion PDO
$host = "127.0.0.1";
$dbname = "maelle_goujat";
$user = "root";
$pass = "INNnsk40374";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p>✅ Connexion réussie !</p>";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupération des joueurs
$stmt = $conn->query("SELECT * FROM joueurs");
$joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($joueurs) {
    echo "<table border='1' cellpadding='5'><tr>
        <th>Nom</th><th>Poste</th><th>DC</th><th>Squat</th><th>Soulevé de terre</th>
    </tr>";
    foreach ($joueurs as $j) {
        echo "<tr>
            <td>{$j['nom']}</td>
            <td>{$j['poste']}</td>
            <td>{$j['dc']}</td>
            <td>{$j['squat']}</td>
            <td>{$j['terre']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Aucun joueur trouvé.</p>";
}
?>
