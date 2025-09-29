<?php
// Page de démonstration d'appel à une API sportive externe (TheSportsDB)
$athlete = isset($_GET['athlete']) ? $_GET['athlete'] : 'Lionel Messi';
$url = 'https://www.thesportsdb.com/api/v1/json/1/searchplayers.php?p=' . urlencode($athlete);

$response = @file_get_contents($url);
$data = $response ? json_decode($response, true) : null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche d'athlète via API</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; }
        form { margin-bottom: 20px; }
        input[type=text] { padding: 8px; width: 70%; }
        button { padding: 8px 16px; background: #2980b9; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1abc9c; }
        img { margin-top: 10px; border-radius: 8px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Recherche d'athlète (API TheSportsDB)</h1>
    <form method="get">
        <input type="text" name="athlete" placeholder="Nom de l'athlète" value="<?= htmlspecialchars($athlete) ?>">
        <button type="submit">Rechercher</button>
    </form>
    <?php
    if ($data && !empty($data['player'])) {
        $player = $data['player'][0];
        echo '<h2>' . htmlspecialchars($player['strPlayer']) . '</h2>';
        echo '<p>Nationalité : ' . htmlspecialchars($player['strNationality']) . '</p>';
        echo '<p>Club : ' . htmlspecialchars($player['strTeam']) . '</p>';
        if (!empty($player['strThumb'])) {
            echo '<img src="' . htmlspecialchars($player['strThumb']) . '" width="150">';
        }
    } elseif ($athlete) {
        echo '<p>Aucun joueur trouvé.</p>';
    }
    ?>
</div>
</body>
</html>
