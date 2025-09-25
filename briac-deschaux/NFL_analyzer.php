<?php
include 'database_connexion.php';

$page = $_GET['page'] ?? 'joueurs';

function nav($active) {
    $tabs = [
        'joueurs' => 'Joueurs',
        'equipes' => 'Équipes',
        'stats' => 'Statistiques'
    ];
    echo '<nav class="menu">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a>";
    }
    echo '</nav>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Manager</title>
    <link rel="stylesheet" href="style-page.css">
</head>
<body>
<header>
    <div class="logo-title">
        <img src="nfl-logo.png" alt="NFL Logo" class="logo">
        <h1>NFL Manager</h1>
    </div>
    <?php nav($page); ?>
</header>

<main>
    <?php
    if ($page === 'joueurs') {
        $stmt = $pdo->query("SELECT p.*, t.nom_team FROM player p JOIN team t ON p.id_team = t.id_team");
        echo '<h2>Joueurs titulaires</h2><div class="grid">';
        while ($pl = $stmt->fetch()) {
            $experience = date('Y') - $pl['annee_debut'];
            echo "<div class='card'>
                    <h3>{$pl['prenom']} {$pl['nom']}</h3>
                    <p><strong>Poste:</strong> {$pl['poste']}</p>
                    <p><strong>Équipe:</strong> {$pl['nom_team']}</p>
                    <p>Âge: {$pl['age']} ans</p>
                    <p>Taille: {$pl['taille_cm']} cm - Poids: {$pl['poids_kg']} kg</p>
                    <p>Expérience: {$experience} ans</p>
                  </div>";
        }
        echo '</div>';
    }
    elseif ($page === 'equipes') {
        $stmt = $pdo->query("SELECT * FROM team ORDER BY conference, division, nom_team");
        echo '<h2>Équipes NFL</h2><div class="grid">';
        while ($team = $stmt->fetch()) {
            echo "<div class='card'>
                    <h3>{$team['nom_team']}</h3>
                    <p>{$team['ville']}</p>
                    <p>{$team['conference']} - {$team['division']}</p>
                  </div>";
        }
        echo '</div>';
    }
    elseif ($page === 'stats') {
        $saison = date('Y');
        $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste FROM stats s JOIN player p ON s.id_player = p.id_player WHERE s.saison = ?");
        $stmt->execute([$saison]);
        echo "<h2>Statistiques $saison</h2><div class='grid'>";
        while ($st = $stmt->fetch()) {
            echo "<div class='card'>
                    <h3>{$st['prenom']} {$st['nom']} ({$st['poste']})</h3>
                    <p>Yards Passes: {$st['yards_passe']} | TD: {$st['td_passe']} | INT: {$st['interceptions']}</p>
                    <p>Rush: {$st['yards_course']} yds / {$st['td_course']} TD</p>
                    <p>Réceptions: {$st['receptions']} - {$st['yards_reception']} yds / {$st['td_reception']} TD</p>
                    <p>Tackles: {$st['plaquages']} | Sacks: {$st['sacks']} | INT Def: {$st['interceptions_def']}</p>
                    <p>FG: {$st['fg_reussis']} | Punts: {$st['punts']}</p>
                  </div>";
        }
        echo '</div>';
    }
    ?>
</main>

<footer>
    <p>&copy; 2025 NFL Manager - Données inspirées de NFL.com & Pro-Football-Reference</p>
</footer>
</body>
</html>
