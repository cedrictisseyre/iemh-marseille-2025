<?php
// -----------------------------
// Configuration
// -----------------------------
$csvFile = 'data_csv.csv'; // Le chemin vers ton fichier CSV
$typeRecherche = isset($_POST['type']) ? trim($_POST['type']) : '';
$regionRecherche = isset($_POST['region']) ? trim($_POST['region']) : '';

// -----------------------------
// Lire le CSV
// -----------------------------
$equipements = [];
if (($handle = fopen($csvFile, 'r')) !== false) {
    $header = fgetcsv($handle, 1000, ';'); // Premier ligne = en-têtes
    while (($row = fgetcsv($handle, 1000, ';')) !== false) {
        $equipement = array_combine($header, $row);
        $equipements[] = $equipement;
    }
    fclose($handle);
}

// -----------------------------
// Filtrer les équipements
// -----------------------------
$results = [];
foreach ($equipements as $e) {
    $matchType = $typeRecherche === '' || stripos($e['type_equipement'], $typeRecherche) !== false;
    $matchRegion = $regionRecherche === '' || stripos($e['region'], $regionRecherche) !== false;
    if ($matchType && $matchRegion) {
        $results[] = $e;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche Équipements Sportifs</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: auto; padding: 20px; }
        input, button { margin: 5px; padding: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Recherche Équipements Sportifs</h2>

    <!-- Formulaire -->
    <form method="post" action="">
        Type d'équipement : <input type="text" name="type" value="<?= htmlspecialchars($typeRecherche) ?>" placeholder="ex: piscine"><br>
        Région : <input type="text" name="region" value="<?= htmlspecialchars($regionRecherche) ?>" placeholder="ex: Provence-Alpes-Côte d'Azur"><br>
        <button type="submit">Rechercher</button>
    </form>

    <?php if (!empty($results)): ?>
        <h3>Résultats :</h3>
        <table>
            <tr>
                <th>Nom</th>
                <th>Type</th>
                <th>Adresse</th>
                <th>Code postal</th>
                <th>Commune</th>
                <th>Région</th>
            </tr>
            <?php foreach ($results as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['nom']) ?></td>
                    <td><?= htmlspecialchars($r['type_equipement']) ?></td>
                    <td><?= htmlspecialchars($r['adresse']) ?></td>
                    <td><?= htmlspecialchars($r['code_postal']) ?></td>
                    <td><?= htmlspecialchars($r['commune']) ?></td>
                    <td><?= htmlspecialchars($r['region']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Aucun équipement trouvé pour ces critères.</p>
    <?php endif; ?>
</body>
</html>
