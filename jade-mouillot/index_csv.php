<?php
// resultats.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$csvFile = __DIR__ . "/resultats.csv";
$delimiter = ";"; // adapte si ton CSV utilise ","

$participants = [];

if (($handle = fopen($csvFile, "r")) !== false) {
    $header = fgetcsv($handle, 1000, $delimiter, '"', '\\');
    $header = array_map(fn($h) => trim(utf8_encode($h)), $header);

    while (($row = fgetcsv($handle, 1000, $delimiter, '"', '\\')) !== false) {
        if (count($row) === count($header)) {
            $participants[] = array_combine($header, $row);
        }
    }
    fclose($handle);
}

// Filtres depuis GET
$selectedCat = $_GET['cat'] ?? "";
$searchName  = trim($_GET['name'] ?? "");

// Liste unique des catégories
$cats = array_unique(array_column($participants, "Cat."));
sort($cats);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>Résultats CSV - Filtres</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
select, input, button { padding: 6px; margin-top: 6px; }
table { border-collapse: collapse; margin-top: 15px; width: 100%; }
td, th { border: 1px solid #ddd; padding: 8px; }
th { background: #f2f2f2; }
</style>
</head>
<body>

<h1>Résultats sportifs</h1>

<form method="get">
    <label for="cat">Catégorie :</label>
    <select name="cat" id="cat">
        <option value="">-- Toutes --</option>
        <?php foreach ($cats as $cat): ?>
            <option value="<?= htmlspecialchars($cat) ?>" <?= $cat===$selectedCat ? "selected":"" ?>>
                <?= htmlspecialchars($cat) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="name">Nom / Prénom contient :</label>
    <input type="text" name="name" id="name" value="<?= htmlspecialchars($searchName) ?>">

    <button type="submit">Filtrer</button>
</form>

<h2>
<?php if ($selectedCat || $searchName): ?>
    Résultats filtrés
<?php else: ?>
    Tous les résultats
<?php endif; ?>
</h2>

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
        <?php
        $ok = true;
        if ($selectedCat !== "" && $p["Cat."] !== $selectedCat) $ok = false;
        if ($searchName !== "" && stripos($p["Prénom NOM"], $searchName) === false) $ok = false;
        ?>
        <?php if ($ok): ?>
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
