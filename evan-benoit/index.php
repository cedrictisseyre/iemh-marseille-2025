<?php
require_once 'connect.php';

try {
    // Récupération des séances avec infos clients et coachs
    $sql = "
        SELECT 
            s.id,
            s.date_seance,
            s.type_seance,
            CONCAT(c.prenom, ' ', c.nom) AS client,
            co.prenom AS coach,
            co.specialite
        FROM seances s
        JOIN clients c ON s.id_client = c.id
        JOIN coachs co ON s.id_coach = co.id
        ORDER BY s.date_seance;
    ";

    $stmt = $conn->query($sql);
    $seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erreur SQL : ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning des séances</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f7f7f7;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            border-collapse: collapse;
            margin: 20px auto;
            width: 80%;
            background: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px 15px;
            text-align: center;
        }
        th {
            background-color: #efefef;
        }
    </style>
</head>
<body>
    <h1>Planning des séances de coaching</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Type de séance</th>
            <th>Client</th>
            <th>Coach</th>
            <th>Spécialité</th>
        </tr>
        <?php foreach ($seances as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['id']) ?></td>
            <td><?= htmlspecialchars($s['date_seance']) ?></td>
            <td><?= htmlspecialchars($s['type_seance']) ?></td>
            <td><?= htmlspecialchars($s['client']) ?></td>
            <td><?= htmlspecialchars($s['coach']) ?></td>
            <td><?= htmlspecialchars($s['specialite']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
