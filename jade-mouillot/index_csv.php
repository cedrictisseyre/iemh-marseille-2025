<?php
// resultats.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Nom du fichier CSV
$csvFile = __DIR__ . "/TRIATHLON DES COLLINES 2024.csv";
$delimiter = ";"; // ou "," selon ton fichier

$participants = [];

// Lecture CSV
if (($handle = fopen($csvFile, "r")) !== false) {
    // Lecture de l'en-tête (avec échappement explicit)
    $header = fgetcsv($handle, 1000, $delimiter, '"', '\\');

    // Normaliser les en-têtes (corriger encodage si besoin)
    $header = array_map(function($h) {
        return trim(utf8_encode($h)); // au cas où encodage ISO-8859-1
    }, $header);

    // Charger toutes les lignes
    while (($row = fgetcsv($handle, 1000, $delimiter, '"', '\\')) !== false) {
        if (count($row) === count($header)) {
            $participants[] = array_combine($header, $row);
        }
    }
    fclose($handle);
}

// Récupération catégorie depuis GET
$selectedCat = $_GET['cat'] ?? "";

// Extraire toutes les catégories disponibles
$cats = array_unique(array_column($participants, "Cat."));
sort($cats);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Résultats CSV - Filtre Catégorie</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
select, button { padding: 6px; margin-top: 6px; }
table { border-collapse: collapse; margin-top: 15px; width: 100%; }
td, th { border: 1px solid #ddd; padding: 8px; }
th { background: #f2f2f2; }
</style>
</head>
<body>

<h1>Résultats sportifs</h1>

<form method="get">
    <label for="cat">Choisir une catégorie :</label>
    <select name="cat" id="cat">
        <option value="">-- Toutes --</option>
        <?php foreach ($cats as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $cat===$selectedCat ? "selected":"" ?>>
                <?= htmlspecialchars($cat) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Filtrer</button>
</form>

<?php if ($selectedCat): ?>
    <h2>Résultats pour la catégorie : <?= htmlspecialchars($selectedCat) ?></h2>
<?php else: ?>
    <h2>Résultats pour toutes les catégories</h2>
<?php endif; ?>

<table>
    <tr>
        <th>Place</th>
        <th>Dossard</th>
        <th>Nom</th>
        <th>Sexe</th>
        <th>Cat.</th>
        <th>Club</th>
        <th>Temps</th>
        <th>Ecart</th>
    </tr>
    <?php foreach ($participants as $p): ?>
        <?php if ($selectedCat === "" || $p["Cat."] === $selectedCat): ?>
        <tr>
            <td><?= htmlspecialchars($p["Place"]) ?></td>
            <td><?= htmlspecialchars($p["Doss."]) ?></td>
            <td><?= htmlspecialchars($p["Prénom NOM"]) ?></td>
            <td><?= htmlspecialchars($p["M/F"]) ?></td>
            <td><?= htmlspecialchars($p["Cat."]) ?></td>
            <td><?= htmlspecialchars($p["Club"]) ?></td>
            <td><?= htmlspecialchars($p["Temps"]) ?></td>
            <td><?= htmlspecialchars($p["Ecart"]) ?></td>
        </tr>
        <?php endif; ?>
    <?php endforeach; ?>
</table>

</body>
</html>
