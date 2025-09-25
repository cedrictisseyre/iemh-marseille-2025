<?php
$host = "195.15.235.20";
$user = "root"; // Ton utilisateur MySQL
$pass = "INNnsk40374";     // Ton mot de passe MySQL
$dbname = "briac_deschaux"; // Le nom de ta base

// Connexion
$conn = new mysqli($host, $user, $pass, $briac_deschaux);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour récupérer les Vainqueurs
$sql = "SELECT e.NOM_evenement, c.NOM_combattants AS Vainqueurs
        FROM Vainqueurs v
        JOIN Evenement e ON v.ID_evenement = e.ID_evenement
        JOIN Combattants c ON v.ID_combattants = c.ID_combattants";

$result = $conn->query($sql);

echo "<h1>Résultats UFC</h1>";

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr><th>Evenement</th><th>Vainqueurs</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["NOM_evenement"]. "</td><td>" . $row["Vainqueurs"]. "</td></tr>";
    }
    echo "</table>";
} else {
    echo "Aucun résultat trouvé";
}

$conn->close();
?>