<?php
require_once '../connexion.php';
$sql = "SELECT * FROM team";
$stmt = $conn->query($sql);
echo "<table border='1'><tr><th>ID</th><th>Nom</th><th>Catégorie</th></tr>";
while ($row = $stmt->fetch()) {
    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['category']}</td></tr>";
}
echo "</table>";
