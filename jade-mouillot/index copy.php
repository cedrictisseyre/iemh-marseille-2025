<?php
// -----------------------------
// Debug & sécurité
// -----------------------------
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// -----------------------------
// Configuration
// -----------------------------
$csvFile = 'data_csv.csv'; // Chemin vers ton fichier CSV
$typeRecherche = isset($_POST['type']) ? trim($_POST['type']) : '';
$communeRecherche = isset($_POST['commune']) ? trim($_POST['commune']) : '';

// -----------------------------
// Lire le CSV de façon sécurisée
// -----------------------------
$equipements = [];

if (!file_exists($csvFile)) {
    die("Erreur : le fichier CSV '$csvFile' est introuvable.");
}

if (($handle = fopen($csvFile, 'r')) !== false) {
    $header = fgetcsv($handle, 1000, ';', '"', '\\'); // ajout du paramètre $escape
    if ($header === false) {
        die("Erreur : le CSV est vide ou mal formé.");
    }

    while (($row = fgetcsv($handle, 1000, ';', '"', '\\')) !== false) {
        if (count($row) === count($header)) {
            $equipement = array_combine($header, $row);
            $equipements[] = $equipement;
        }
    }
    fclose($handle);
}

// -----------------------------
// Lister types et communes
// -----------------------------
$types = [];
$communes = [];
foreach ($equipements as $e) {
    if (isset($e['type_equipement'])) $types[$e['type_equipement']] = true;
    if (isset($e['commune'])) $communes[$e['commune']] = true;
}
$types = array_keys($types);
sort($types);
$communes = array_keys($communes);
sort($communes);

// -----------------------------
// Filtrer selon le formulaire
// -----------------------------
$results = [];
foreach ($equipements as $e) {
    $matchType = $typeRecherche === '' || (isset($e['type_equipement']) && stripos($e['type_equipement'], $typeRecherche) !== false);
    $matchCommune = $communeRecherche === '' || (isset($e['commune']) && stripos($e['commune'], $communeRecherche) !== false);
    if ($matchType && $matchCommune) {
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
        body { font-family: Arial; max-width: 900px; margin: auto; padding: 20px; }
        input, button { margin: 5px; padding: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h2>Recherche Équipements Sportifs</h2>

    <form method="post" action="">
        Type d'équipement : 
        <select name="type">
            <option value="">-- Tous --</option>
            <?php foreach ($types as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= $t==$typeRecherche?'selected':'' ?>><?= htmlspecialchars($t) ?></option>
            <?php endforeach; ?>
        </select>

        Commune : 
        <select name="commune">
            <option value="">-- Toutes --</option>
            <?php foreach ($communes as $c): ?>
                <option value="<?= htmlspecialchars($c) ?>" <?= $c==$communeRecherche?'selected':'' ?>><?= htmlspecialchars($c) ?></option>
            <?php endforeach; ?>
        </select>

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
                    <td><?= htmlspecialchars($r['nom'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['type_equipement'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['adresse'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['code_postal'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['commune'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['region'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>Aucun équipement trouvé pour ces critères.</p>
    <?php endif; ?>
</body>
</html>
