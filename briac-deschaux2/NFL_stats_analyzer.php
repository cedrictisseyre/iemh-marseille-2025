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
        $class = ($active === $key) ? 'active shiny-button' : 'shiny-button';
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

    <!-- HEADER avec True Focus -->
    <div class="header focus-container">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1 class="focus-word" id="mainTitle">NFL STATS ANALYZER</h1>
        <canvas class="focus-frame"></canvas>
    </div>

    <!-- NAV MENU -->
    <?php nav($page); ?>

    <main>
    <?php if ($page === 'joueurs') : ?>
        <!-- Formulaire d'ajout joueur -->
        <div class="card magic-bento">
            <h2>Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php" class="player-form">
                <input type="text" name="prenom" placeholder="Prénom" required class="autocomplete-input" data-type="player">
                <input type="text" name="nom" placeholder="Nom" required class="autocomplete-input" data-type="player">
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
                <button type="submit" class="shiny-button">Ajouter le joueur</button>
            </form>
        </div>

        <!-- Recherche joueurs -->
        <div class="card magic-bento">
            <h2>Recherche joueur</h2>
            <form method="get" class="player-search">
                <input type="hidden" name="page" value="joueurs">
                <input type="text" name="recherche" placeholder="Nom ou prénom" class="autocomplete-input" data-type="search">
                <div class="autocomplete-suggestions"></div>
                <button type="submit" class="shiny-button">Rechercher</button>
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
                echo "<div class='card magic-bento scroll-animate player-card'>
                        <h3>{$pl['prenom']} {$pl['nom']}</h3>
                        <p><strong>Poste:</strong> {$pl['poste']}</p>
                        <p><strong>Équipe:</strong> <img src='{$pl['logo_url']}' alt='' style='width:30px;height:30px;vertical-align:middle;'> {$pl['nom_team']}</p>
                        <p><strong>Âge:</strong> {$pl['age']} ans</p>
                        <p><strong>Taille:</strong> {$pl['taille_cm']} cm - <strong>Poids:</strong> {$pl['poids_kg']} kg</p>
                        <p><strong>Expérience:</strong> {$experience} ans</p>
                      </div>";
            }
            ?>
        </div>

    <?php elseif ($page === 'stats') : 
        $saison = date('Y'); ?>
        <!-- Formulaire stats -->
        <div class="card magic-bento">
            <h2>Ajouter des statistiques (Saison <?= $saison ?>)</h2>
            <form method="post" action="services/add_stats.php" class="stats-form">
                <input type="text" name="player_name" placeholder="Nom du joueur" class="autocomplete-input" data-type="stats">
                <div class="autocomplete-suggestions"></div>
                <!-- Tous les champs stats -->
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
                <input type="number" name="field_goals_made" placeholder="Field Goals marqués" min="0">
                <input type="number" name="field_goals_attempted" placeholder="Field Goals tentés" min="0">
                <input type="number" name="extra_points_made" placeholder="Extra Points marqués" min="0">
                <input type="number" name="extra_points_attempted" placeholder="Extra Points tentés" min="0">
                <input type="number" name="punts" placeholder="Punts" min="0">
                <input type="number" name="punt_yards" placeholder="Yards punts" min="0">
                <input type="number" name="longest_punt" placeholder="Plus long punt" min="0">
                <input type="number" name="inside_20" placeholder="Punts inside 20" min="0">
                <button type="submit" class="shiny-button">Ajouter les stats</button>
            </form>
        </div>

        <!-- Recherche stats -->
        <div class="card magic-bento">
            <h2>Recherche stats joueur</h2>
            <form method="get" class="stats-search">
                <input type="hidden" name="page" value="stats">
                <input type="text" name="recherche" placeholder="Nom ou prénom" class="autocomplete-input" data-type="search">
                <div class="autocomplete-suggestions"></div>
                <button type="submit" class="shiny-button">Rechercher</button>
            </form>
       
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

            $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste, t.nom_team, t.logo_url 
                                   FROM stats s 
                                   JOIN player p ON s.id_player = p.id_player 
                                   JOIN team t ON p.id_team = t.id_team
                                   $where
                                   ORDER BY p.nom");
            $stmt->execute($params);

            $has_stats = false;
            while ($st = $stmt->fetch()) {
                $has_stats = true;
                echo "<div class='card magic-bento scroll-animate stats-card'>
                        <h3><img src='{$st['logo_url']}' alt='' style='width:30px;height:30px;vertical-align:middle;margin-right:5px;'> 
                        {$st['prenom']} {$st['nom']} ({$st['poste']})</h3>";
                foreach ($st as $key => $val) {
                    if (in_array($key, ['id_stat','id_player','prenom','nom','poste','saison','nom_team','logo_url'])) continue;
                    if ($val !== null && $val != 0) {
                        $label = ucfirst(str_replace("_", " ", $key));
                        echo "<p><strong>{$label}:</strong> {$val}</p>";
                    }
                }
                echo "</div>";
            }
            if (!$has_stats) echo "<p>Aucune statistique disponible pour cette saison.</p>";
            ?>
        </div>

    <?php elseif ($page === 'classement') : 
        $saison = date('Y');
        $filtre_poste = $_GET['poste'] ?? '';
        $filtre_team = $_GET['team'] ?? '';
        ?>

        <!-- Filtres -->
        <div class="card magic-bento">
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

                <button type="submit" class="shiny-button">Filtrer</button>
            </form>
        </div>

        <?php
        // --- Classement TDs ---
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
        $conf_data = $stmt_conf->fetchAll();

        if (count($conf_data) > 0) {
            echo "<h2>Classement par conférence (Total TDs)</h2>";
            $conf = '';
            foreach ($conf_data as $row) {
                if ($row['conference'] !== $conf) {
                    if ($conf !== '') echo '</ol>';
                    $conf = $row['conference'];
                    echo "<h3>{$conf}</h3><ol>";
                }
                echo "<li>{$row['prenom']} {$row['nom']} ({$row['poste']}) - {$row['total_tds']} TDs</li>";
            }
            echo '</ol>';
        }

        // --- Classement Plaquages ---
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
        $div_data = $stmt_div->fetchAll();

        if (count($div_data) > 0) {
            echo "<h2>Classement par division (Plaquages)</h2>";
            $div = '';
            foreach ($div_data as $row) {
                if ($row['division'] !== $div) {
                    if ($div !== '') echo '</ol>';
                    $div = $row['division'];
                    echo "<h3>{$div}</h3><ol>";
                }
                echo "<li>{$row['prenom']} {$row['nom']} ({$row['poste']}) - {$row['total_plaquages']} plaquages</li>";
            }
            echo '</ol>';
        }
        ?>

    <?php endif; ?>
    </main>
</div>

<!-- Modal overlay pour cartes joueurs/stats -->
<div class="card-modal-overlay" id="cardModalOverlay">
    <div class="card-modal" id="cardModalContent"></div>
</div>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>

<script src="js/autocomplete.js"></script>
<script src="js/true-focus.js"></script>
<script>
// SCROLL ANIMATION, MAGIC BENTO & MODAL ZOOM
document.addEventListener('DOMContentLoaded', function() {
    // Scroll animation
    const elements = document.querySelectorAll('.scroll-animate');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => { if(entry.isIntersecting) entry.target.classList.add('show'); });
    }, { threshold: 0.1 });
    elements.forEach(el => observer.observe(el));

    // Modal zoom pour cartes joueurs et stats seulement
    const overlay = document.getElementById('cardModalOverlay');
    const modalContent = document.getElementById('cardModalContent');
    document.querySelectorAll('.player-card, .stats-card').forEach(card => {
        card.addEventListener('click', function() {
            modalContent.innerHTML = card.innerHTML;
            overlay.style.display = 'flex';
            setTimeout(() => modalContent.classList.add('show'), 10);
        });
    });
    overlay.addEventListener('click', function(e) {
        if(e.target === overlay) {
            modalContent.classList.remove('show');
            setTimeout(() => overlay.style.display = 'none', 300);
        }
    });

    // True Focus animation
    initTrueFocus('#mainTitle');
});
</script>
</body>
</html>
