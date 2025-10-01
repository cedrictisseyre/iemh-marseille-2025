<?php
require_once '../../config/db_connect.php';
// Page de démonstration d'appel à une API sportive externe (TheSportsDB)
$athlete = isset($_GET['athlete']) ? trim($_GET['athlete']) : 'Lionel Messi';
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
        echo '<h2>Résultats pour : ' . htmlspecialchars($athlete) . '</h2>';
        echo '<ul style="list-style:none;padding:0;">';
        foreach ($data['player'] as $player) {
            echo '<li style="margin-bottom:20px;">';
            echo '<strong>' . htmlspecialchars($player['strPlayer']) . '</strong><br>';
            echo 'Nationalité : ' . htmlspecialchars($player['strNationality']) . '<br>';
            echo 'Club : ' . htmlspecialchars($player['strTeam']) . '<br>';
            if (!empty($player['strThumb'])) {
                echo '<img src="' . htmlspecialchars($player['strThumb']) . '" width="120">';
            }
            echo '</li>';
        }
        echo '</ul>';
    } elseif ($athlete) {
        echo '<p>Aucun joueur trouvé pour "' . htmlspecialchars($athlete) . '".</p>';
        echo '<p>Essayez un autre nom ou une orthographe différente.</p>';
    }
    ?>
</div>
</body>
</html>
