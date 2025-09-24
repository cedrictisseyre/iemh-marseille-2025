<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion MySQL avec mysqli
$host = "127.0.0.1"; // ou localhost
$user = "root";
$pass = "INNnsk40374";
$dbname = "maelle_goujat";

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
echo "<p>✅ Connexion réussie !</p>";

// Récupération des joueurs
$sql = "SELECT * FROM joueurs";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'><tr>
        <th>Nom</th><th>Poste</th><th>DC</th><th>Squat</th><th>Soulevé de terre</th>
    </tr>";
    while ($joueur = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$joueur['nom']}</td>
            <td>{$joueur['poste']}</td>
            <td>{$joueur['dc']}</td>
            <td>{$joueur['squat']}</td>
            <td>{$joueur['terre']}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Aucun joueur trouvé.</p>";
}

$conn->close();
?>
