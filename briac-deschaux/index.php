<?php
include 'database_connexion.php'; // Connexion PDO

$page = $_GET['page'] ?? 'joueurs';

function nav($active) {
    $tabs = [
        'joueurs' => 'Joueurs',
        'stats' => 'Statistiques',
        'classement' => 'Classement'
    ];
    echo '<div class="menu">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page=$key' class='$class'>$label</a>";
    }
    echo '</div>';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="style_page.css">
</head>
<body>
<div class="container">
    <h1>NFL Stats Analyzer</h1>
    <?php nav($page); ?>

    <main>
    <?php
    if ($page === 'joueurs') {
     // --- Formulaire d'ajout joueur (envoie vers add_player.php) ---
echo '<div class="card"><h2>Ajouter un joueur</h2>
<form method="post" action="add_player.php">
    <input type="text" name="prenom" placeholder="Prénom" required>
    <input type="text" name="nom" placeholder="Nom" required>

    <label for="poste">Poste</label>
    <select name="poste" required>
        <optgroup label="Offense">
            <option value="QB">Quarterback (QB)</option>
            <option value="RB">Running Back (RB)</option>
            <option value="FB">Fullback (FB)</option>
            <option value="WR">Wide Receiver (WR)</option>
            <option value="TE">Tight End (TE)</option>
            <option value="LT">Left Tackle (LT)</option>
            <option value="LG">Left Guard (LG)</option>
            <option value="C">Center (C)</option>
            <option value="RG">Right Guard (RG)</option>
            <option value="RT">Right Tackle (RT)</option>
        </optgroup>
        <optgroup label="Defense">
            <option value="DE">Defensive End (DE)</option>
            <option value="DT">Defensive Tackle (DT)</option>
            <option value="NT">Nose Tackle (NT)</option>
            <option value="OLB">Outside Linebacker (OLB)</option>
            <option value="ILB">Inside Linebacker (ILB)</option>
            <option value="MLB">Middle Linebacker (MLB)</option>
            <option value="CB">Cornerback (CB)</option>
            <option value="FS">Free Safety (FS)</option>
            <option value="SS">Strong Safety (SS)</option>
        </optgroup>
        <optgroup label="Special Teams">
            <option value="K">Kicker (K)</option>
            <option value="P">Punter (P)</option>
            <option value="LS">Long Snapper (LS)</option>
            <option value="KR">Kick Returner (KR)</option>
            <option value="PR">Punt Returner (PR)</option>
            <option value="H">Holder (H)</option>
            <option value="G">Gunner (G)</option>
        </optgroup>
    </select>

    <input type="number" name="age" placeholder="Âge" required>
    <input type="number" name="taille_cm" placeholder="Taille (cm)" required>
    <input type="number" name="poids_kg" placeholder="Poids (kg)" required>
    <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)" required>

    <select name="id_team" required>
        <option value="">Sélectionner une équipe</option>';
        $teams = $pdo->query("SELECT id_team, nom_team FROM team ORDER BY nom_team")->fetchAll();
        foreach ($teams as $t) {
            echo "<option value='{$t['id_team']}'>{$t['nom_team']}</option>";
        }
echo '</select>
    <button type="submit">Ajouter le joueur</button>
</form></div>';


        // --- Affichage de la liste des joueurs ---
        echo '<h2>Liste des joueurs</h2><div class="grid">';
        $stmt = $pdo->query("SELECT p.*, t.nom_team FROM player p JOIN team t ON p.id_team = t.id_team ORDER BY p.nom");
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
    elseif ($page === 'stats') {
        $saison = date('Y');
        // --- Formulaire d'ajout stats (envoie vers add_stats.php) ---
        echo '<div class="card"><h2>Ajouter des statistiques (Saison '.$saison.')</h2>
        <form method="post" action="add_stats.php">
            <select name="id_player" required>
                <option value="">Sélectionner un joueur</option>';
                $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom")->fetchAll();
                foreach ($players as $p) {
                    echo "<option value='{$p['id_player']}'>{$p['prenom']} {$p['nom']}</option>";
                }
        echo '</select>
            <input type="number" name="yards_passe" placeholder="Yards passés" min="0">
            <input type="number" name="td_passe" placeholder="TD passés" min="0">
            <input type="number" name="interceptions" placeholder="Interceptions" min="0">
            <input type="number" name="yards_course" placeholder="Yards course" min="0">
            <input type="number" name="td_course" placeholder="TD course" min="0">
            <input type="number" name="receptions" placeholder="Réceptions" min="0">
            <input type="number" name="yards_reception" placeholder="Yards réception" min="0">
            <input type="number" name="td_reception" placeholder="TD réception" min="0">
            <input type="number" name="plaquages" placeholder="Plaquages" min="0">
            <input type="number" step="0.1" name="sacks" placeholder="Sacks" min="0">
            <input type="number" name="interceptions_def" placeholder="Interceptions déf" min="0">
            <input type="number" name="fg_reussis" placeholder="FG réussis" min="0">
            <input type="number" name="punts" placeholder="Punts" min="0">
            <button type="submit">Ajouter les stats</button>
        </form></div>';

        // --- Affichage des stats de la saison ---
        $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste FROM stats s JOIN player p ON s.id_player = p.id_player WHERE s.saison = ? ORDER BY p.nom");
        $stmt->execute([$saison]);
        echo "<h2>Statistiques $saison</h2><div class='grid'>";
        while ($st = $stmt->fetch()) {
            echo "<div class='card'>
                    <h3>{$st['prenom']} {$st['nom']} ({$st['poste']})</h3>
                    <p>Yds Passe: {$st['yards_passe']} | TD: {$st['td_passe']} | INT: {$st['interceptions']}</p>
                    <p>Rush: {$st['yards_course']} yds / {$st['td_course']} TD</p>
                    <p>Réceptions: {$st['receptions']} - {$st['yards_reception']} yds / {$st['td_reception']} TD</p>
                    <p>Plaquages: {$st['plaquages']} | Sacks: {$st['sacks']} | INT Def: {$st['interceptions_def']}</p>
                    <p>FG: {$st['fg_reussis']} | Punts: {$st['punts']}</p>
                  </div>";
        }
        echo '</div>';
    }
    elseif ($page === 'classement') {
        echo '<h2>Classement par conférence (TD total)</h2>';
        $sql = "SELECT p.nom, p.prenom, t.conference, 
                       (COALESCE(s.td_passe,0) + COALESCE(s.td_course,0) + COALESCE(s.td_reception,0)) as total_td
                FROM player p
                JOIN team t ON p.id_team = t.id_team
                LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ?
                ORDER BY t.conference, total_td DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([date('Y')]);

        $conf = '';
        while ($row = $stmt->fetch()) {
            if ($row['conference'] !== $conf) {
                if ($conf !== '') echo '</ol>';
                $conf = $row['conference'];
                echo "<h3>{$conf}</h3><ol>";
            }
            echo "<li>{$row['prenom']} {$row['nom']} - {$row['total_td']} TD</li>";
        }
        if ($conf !== '') echo '</ol>';
    }
    ?>
    </main>
</div>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>
</body>
</html>
