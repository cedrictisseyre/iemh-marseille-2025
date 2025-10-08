<?php
require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

$players = $pdo->query('SELECT * FROM player')->fetchAll(PDO::FETCH_ASSOC);
$stats = $pdo->query('SELECT * FROM stats')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="css/style_page.css">
    <style>
        body { font-family: Arial, sans-serif; background: #fafafa; color: #333; margin: 0; padding: 0; }
        .tabs { display: flex; justify-content: center; margin: 20px 0; }
        .tab { padding: 10px 20px; background: #ddd; margin: 0 5px; border-radius: 6px; cursor: pointer; transition: 0.3s; }
        .tab.active { background: #0077cc; color: white; }
        .tab:hover { background: #005fa3; color: white; }
        .section { display: none; padding: 20px; }
        .section.active { display: block; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ccc; text-align: center; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1 style="text-align:center;">🏈 NFL Stats Analyzer</h1>

    <div class="tabs">
        <div class="tab active" data-tab="offense">Offense</div>
        <div class="tab" data-tab="defense">Defense</div>
        <div class="tab" data-tab="special">Special Teams</div>
    </div>

    <!-- OFFENSE -->
    <div id="offense" class="section active">
        <h2>Offensive Stats</h2>
        <table>
            <tr>
                <th>Joueur</th>
                <th>Yards Passe</th><th>TD Passe</th><th>INT</th>
                <th>Yards Course</th><th>TD Course</th>
                <th>Réceptions</th><th>Yards Réception</th><th>TD Réception</th>
            </tr>
            <?php foreach ($stats as $st): ?>
                <?php
                    $player = array_filter($players, fn($p) => $p['id_player'] == $st['id_player']);
                    $player = reset($player);
                ?>
                <tr>
                    <td><?= htmlspecialchars($player['prenom'].' '.$player['nom']) ?></td>
                    <td><?= $st['yards_passe'] ?></td>
                    <td><?= $st['td_passe'] ?></td>
                    <td><?= $st['interceptions'] ?></td>
                    <td><?= $st['yards_course'] ?></td>
                    <td><?= $st['td_course'] ?></td>
                    <td><?= $st['receptions'] ?></td>
                    <td><?= $st['yards_reception'] ?></td>
                    <td><?= $st['td_reception'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- DEFENSE -->
    <div id="defense" class="section">
        <h2>Defensive Stats</h2>
        <table>
            <tr>
                <th>Joueur</th>
                <th>Plaquages</th><th>Sacks</th><th>Interceptions</th>
            </tr>
            <?php foreach ($stats as $st): ?>
                <?php
                    $player = array_filter($players, fn($p) => $p['id_player'] == $st['id_player']);
                    $player = reset($player);
                ?>
                <tr>
                    <td><?= htmlspecialchars($player['prenom'].' '.$player['nom']) ?></td>
                    <td><?= $st['plaquages'] ?></td>
                    <td><?= $st['sacks'] ?></td>
                    <td><?= $st['interceptions_def'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- SPECIAL TEAMS -->
    <div id="special" class="section">
        <h2>Special Teams Stats (Kickers & Punters)</h2>
        <table>
            <tr>
                <th>Joueur</th>
                <th>FG Made</th><th>FG Attempted</th>
                <th>XP Made</th><th>XP Attempted</th>
                <th>Punts</th><th>Punt Yards</th><th>Longest Punt</th><th>Inside 20</th>
            </tr>
            <?php foreach ($stats as $st): ?>
                <?php
                    $player = array_filter($players, fn($p) => $p['id_player'] == $st['id_player']);
                    $player = reset($player);
                ?>
                <tr>
                    <td><?= htmlspecialchars($player['prenom'].' '.$player['nom']) ?></td>
                    <td><?= $st['field_goals_made'] ?></td>
                    <td><?= $st['field_goals_attempted'] ?></td>
                    <td><?= $st['extra_points_made'] ?></td>
                    <td><?= $st['extra_points_attempted'] ?></td>
                    <td><?= $st['punts'] ?></td>
                    <td><?= $st['punt_yards'] ?></td>
                    <td><?= $st['longest_punt'] ?></td>
                    <td><?= $st['inside_20'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        const tabs = document.querySelectorAll('.tab');
        const sections = document.querySelectorAll('.section');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                sections.forEach(s => s.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
    </script>
</body>
</html>
