<?php
$stmt = $conn->prepare("
    SELECT date_mesure, masse
    FROM suivi_masse
    WHERE id_client = :id_client
    ORDER BY date_mesure ASC
");
$stmt->execute(['id_client' => 1]); // Exemple : client id = 1

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "{$row['date_mesure']} - {$row['masse']} kg<br>";
}
?>
