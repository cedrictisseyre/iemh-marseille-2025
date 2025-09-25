<?php
// Paramètres de connexion
$host = "localhost";
$user = "root";
$pass = "INNnsk40374";
$db   = "";

// Connexion à MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête SQL
$sql = "SELECT id, nom, poste, score_dc, score_squat, score_sdt FROM Joueurs";
$result = $conn->query($sql);

// Affichage
echo "<h1>Liste des Joueurs</h1>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='8'>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Poste</th>
                <th>Développé Couché</th>
                <th>Squat</th>
                <th>Soulevé de Terre</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['nom']}</td>
                <td>{$row['poste']}</td>
                <td>{$row['score_dc']}</td>
                <td>{$row['score_squat']}</td>
                <td>{$row['score_sdt']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "Aucun joueur trouvé.";
}

$conn->close();
?>
