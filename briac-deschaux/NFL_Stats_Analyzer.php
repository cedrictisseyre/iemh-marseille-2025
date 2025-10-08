<?php
include __DIR__ . '/config/database_connexion.php';

// Page active
$page = $_GET['page'] ?? 'joueurs';

// Fonction pour générer le menu
function nav($active) {
    $tabs = [
        'joueurs' => 'Joueurs',
        'stats' => 'Statistiques',
        'classement'=> 'Classement'
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
    <link rel="stylesheet" href="css/style_page.css">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </div>

    <!-- NAV MENU -->
    <?php nav($page); ?>

    <main>
    <?php if ($page === 'joueurs') : ?>
        <!-- Formulaire d'ajout joueur -->
        <div class="card">
            <h2>Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php">
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="text" name="nom" placeholder="Nom" required>

                <!-- Select poste depuis table position -->
                <select name="poste" required>
                    <option value="">Sélectionner un poste</option>
                    <?php
                    $pos = $pdo->query("SELECT code, libelle FROM position ORDER BY libelle")->fetchAll();
                    foreach ($pos as $p) {
                        echo "<option value='{$p['code']}'>{$p['libelle']} ({$p['code']})</option>";
                    }
                    ?>
                </select>

                <input type="number" name="age" placeholder="Âge" required>
                <input type="number" name="taille_cm" placeholder="Taille (cm)" required>
                <input type="number" name="poids_kg" placeholder="Poids (kg)" required>
                <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)" required>

                <!-- Select équipe par conférence -->
                <select name="id_team" required>
                    <option value="">Sélectionner une équipe</option>
                    <?php
                    $teams = $pdo->query("SELECT id_team, nom_team, conference FROM team ORDER BY conference, nom_team")->fetchAll();
                    $current_conf = "";
                    foreach ($teams as $t) {
                        if ($t['conference'] !== $current_conf) {
                            if ($current_conf !== "") echo "</optgroup>";
                            $current_conf = $t['conference'];
                            echo "<optgroup label='{$current_conf}'>";
                        }
                        echo "<option value='{$t['id_team']}'>{$t['nom_team']}</option>";
                    }
                    if ($current_conf !== "") echo "</optgroup>";
                    ?>
                </select>

                <button type="submit">Ajouter le joueur</button>
            </form>
        </div>

        <!-- Recherche joueurs -->
        <div class="card">
            <h2>Recherche joueur</h2>
            <form method="get">
                <input type="hidden" name="page" value="joueurs">
                <input type="text" name="recherche" placeholder="Nom ou prénom">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <!-- Liste des joueurs -->
        <h2>Liste des joueurs</h2>
        <div class="grid">
            <?php
            $where = "";
            $params = [];
            if (!empty($_GET['recherche'])) {
                $search = "%" . $_GET['recherche'] . "%";
                $where = "WHERE p.nom LIKE ? OR p.prenom LIKE ? OR CONCAT(p.prenom,' ',p.nom) LIKE ? OR CONCAT(p.nom,' ',p.prenom) LIKE ?";
                $params = [$search, $search, $search, $search];
            }

            $stmt = $pdo->prepare("SELECT p.*, t.nom_team, t.logo_url 
                                 FROM player p 
                                 JOIN team t ON p.id_team = t.id_team 
                                 $where
                                 ORDER BY p.nom");
            $stmt->execute($params);
            while ($pl = $stmt->fetch()) {
                $experience = date('Y') - $pl['annee_debut'];
                echo "<div class='card'>
                        <h3>{$pl['prenom']} {$pl['nom']}</h3>
                        <p><strong>Poste:</strong> {$pl['poste']}</p>
                        <p><strong>Équipe:</strong> <img src='{$pl['logo_url']}' alt='' style='width:25px;height:25px;vertical-align:middle;'> {$pl['nom_team']}</p>
                        <p>Âge: {$pl['age']} ans</p>
                        <p>Taille: {$pl['taille_cm']} cm - Poids: {$pl['poids_kg']} kg</p>
                        <p>Expérience: {$experience} ans</p>
                      </div>";
            }
            ?>
        </div>

    <?php elseif ($page === 'stats') : 
        $saison = date('Y'); ?>
        <!-- Formulaire stats -->
        <div class="card">
            <h2>Ajouter des statistiques (Saison <?= $saison ?>)</h2>
            <form method="post" action="services/add_stats.php">
                <select name="id_player" required>
                    <option value="">Sélectionner un joueur</option>
                    <?php
                    $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom")->fetchAll();
                    foreach ($players as $p) {
                        echo "<option value='{$p['id_player']}'>{$p['prenom']} {$p['nom']}</option>";
                    }
                    ?>
                </select>
                <input type="number" name="passing_yards" placeholder="Yards passés" min="0">
                <input type="number" name="passing_tds" placeholder="TD passés" min="0">
                <input type="number" name="interceptions" placeholder="Interceptions" min="0">
                <input type="number" name="rushing_yards" placeholder="Yards course" min="0">
                <input type="number" name="rushing_tds" placeholder="TD course" min="0">
                <input type="number" name="receptions" placeholder="Réceptions" min="0">
                <input type="number" name="receiving_yards" placeholder="Yards réception" min="0">
                <input type="number" name="receiving_tds" placeholder="TD réception" min="0">
                <input type="number" name="tackles" placeholder="Plaquages" min="0">
                <input type="number" step="0.1" name="sacks" placeholder="Sacks" min="0">
                <input type="number" name="interceptions_def" placeholder="Interceptions déf" min="0">
                <button type="submit">Ajouter les stats</button>
            </form>
        </div>

        <!-- Recherche joueurs -->
        <div class="card">
            <h2>Recherche stats joueur</h2>
            <form method="get">
                <input type="hidden" name="page" value="stats">
                <input type="text" name="recherche" placeholder="Nom ou prénom">
                <button type="submit">Rechercher</button>
            </form>
        </div>

        <!-- Affichage stats -->
        <h2>Statistiques <?= $saison ?></h2>
        <div class="grid">
            <?php
            $where = "WHERE s.saison = ?";
            $params = [$saison];
            if (!empty($_GET['recherche'])) {
                $search = "%" . $_GET['recherche'] . "%";
                $where .= " AND (p.nom LIKE ? OR p.prenom LIKE ? OR CONCAT(p.prenom,' ',p.nom) LIKE ? OR CONCAT(p.nom,' ',p.prenom) LIKE ?)";
                $params = array_merge($params, [$search,$search,$search,$search]);
            }

            $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste 
                                   FROM stats s 
                                   JOIN player p ON s.id_player = p.id_player 
                                   $where
                                   ORDER BY p.nom");
            $stmt->execute($params);
            while ($st = $stmt->fetch()) {
                echo "<div class='card'><h3>{$st['prenom']} {$st['nom']} ({$st['poste']})</h3>";
                foreach ($st as $key => $val) {
                    if (in_array($key, ['id_stat','id_player','prenom','nom','poste','saison'])) continue;
                    if ($val !== null && $val != 0) {
                        $label = ucfirst(str_replace("_", " ", $key));
                        echo "<p>{$label}: {$val}</p>";
                    }
                }
                echo "</div>";
            }
            ?>
        </div>

    <?php elseif ($page === 'classement') : 
        $saison = date('Y');
        $filtre_poste = $_GET['poste'] ?? '';
        $filtre_team = $_GET['team'] ?? '';
        ?>

        <!-- Filtres -->
        <div class="card">
            <h2>Filtres Classement</h2>
            <form method="get">
                <input type="hidden" name="page" value="classement">

                <label>Poste :</label>
                <select name="poste">
                    <option value="">Tous</option>
                    <?php
                    $positions = $pdo->query("SELECT code, libelle FROM position ORDER BY libelle")->fetchAll();
                    foreach ($positions as $p) {
                        $sel = ($filtre_poste === $p['code']) ? "selected" : "";
                        echo "<option value='{$p['code']}' $sel>{$p['libelle']} ({$p['code']})</option>";
                    }
                    ?>
                </select>

                <label>Équipe :</label>
                <select name="team">
                    <option value="">Toutes</option>
                    <?php
                    $teams = $pdo->query("SELECT id_team, nom_team, conference FROM team ORDER BY conference, nom_team")->fetchAll();
                    $current_conf = "";
                    foreach ($teams as $t) {
                        if ($t['conference'] !== $current_conf) {
                            if ($current_conf !== "") echo "</optgroup>";
                            $current_conf = $t['conference'];
                            echo "<optgroup label='{$current_conf}'>";
                        }
                        $sel = ($filtre_team == $t['id_team']) ? "selected" : "";
                        echo "<option value='{$t['id_team']}' $sel>{$t['nom_team']}</option>";
                    }
                    if ($current_conf !== "") echo "</optgroup>";
                    ?>
                </select>

                <button type="submit">Filtrer</button>
            </form>
        </div>

        <!-- Classement par conférence (TDs) -->
        <h2>Classement par conférence (Total TDs)</h2>
        <?php
        $sql_conf = "
            SELECT p.prenom, p.nom, p.poste, t.conference,
                   COALESCE(SUM(s.passing_tds),0) + COALESCE(SUM(s.rushing_tds),0) + COALESCE(SUM(s.receiving_tds),0) AS total_tds
            FROM player p
            JOIN team t ON p.id_team = t.id_team
            LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = :saison
            WHERE 1=1";

        $params = [':saison' => $saison];
        if ($filtre_poste !== '') { $sql_conf .= " AND p.poste = :poste"; $params[':poste'] = $filtre_poste; }
        if ($filtre_team !== '') { $sql_conf .= " AND p.id_team = :team"; $params[':team'] = $filtre_team; }

        $sql_conf .= " GROUP BY p.id_player, p.prenom, p.nom, p.poste, t.conference
                       HAVING total_tds > 0
                       ORDER BY t.conference, total_tds DESC";

        $stmt_conf = $pdo->prepare($sql_conf);
        $stmt_conf->execute($params);

        $conf = '';
        while ($row = $stmt_conf->fetch()) {
            if ($row['conference'] !== $conf) {
                if ($conf !== '') echo '</ol>';
                $conf = $row['conference'];
                echo "<h3>{$conf}</h3><ol>";
            }
            echo "<li>{$row['prenom']} {$row['nom']} ({$row['poste']}) - {$row['total_tds']} TDs</li>";
        }
        if ($conf !== '') echo '</ol>';
        ?>

        <!-- Classement par division (Plaquages) -->
        <h2>Classement par division (Plaquages)</h2>
        <?php
        $sql_div = "
            SELECT p.prenom, p.nom, p.poste, t.division,
                   COALESCE(SUM(s.tackles),0) AS total_plaquages
            FROM player p
            JOIN team t ON p.id_team = t.id_team
            LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = :saison
            WHERE 1=1";

        $params = [':saison' => $saison];
        if ($filtre_poste !== '') { $sql_div .= " AND p.poste = :poste"; $params[':poste'] = $filtre_poste; }
        if ($filtre_team !== '') { $sql_div .= " AND p.id_team = :team"; $params[':team'] = $filtre_team; }

        $sql_div .= " GROUP BY p.id_player, p.prenom, p.nom, p.poste, t.division
                      HAVING total_plaquages > 0
                      ORDER BY t.division, total_plaquages DESC";

        $stmt_div = $pdo->prepare($sql_div);
        $stmt_div->execute($params);

        $div = '';
        while ($row = $stmt_div->fetch()) {
            if ($row['division'] !== $div) {
                if ($div !== '') echo '</ol>';
                $div = $row['division'];
                echo "<h3>{$div}</h3><ol>";
            }
            echo "<li>{$row['prenom']} {$row['nom']} ({$row['poste']}) - {$row['total_plaquages']} plaquages</li>";
        }
        if ($div !== '') echo '</ol>';
        ?>

    <?php endif; ?>
    </main>
</div>
<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>
</body>
</html>
