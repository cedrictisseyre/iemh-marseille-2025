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

    <!-- HEADER -->
    <div class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <!-- True Focus Animation -->
        <div id="mainTitle" class="focus-container">
            <span class="focus-word active">NFL</span>
            <span class="focus-word">STATS</span>
            <span class="focus-word">ANALYZER</span>
            <div class="focus-frame">
                <span class="corner top-left"></span>
                <span class="corner top-right"></span>
                <span class="corner bottom-left"></span>
                <span class="corner bottom-right"></span>
            </div>
        </div>
    </div>

    <!-- NAV MENU -->
    <?php nav($page); ?>

    <main>
    <?php if ($page === 'joueurs') : ?>
        <!-- Formulaire d'ajout joueur -->
        <div class="card magic-bento">
            <h2>Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php" autocomplete="off">
                <input type="text" name="prenom" id="prenom" placeholder="Prénom" required>
                <input type="text" name="nom" id="nom" placeholder="Nom" required>
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
            <form method="get" autocomplete="off">
                <input type="hidden" name="page" value="joueurs">
                <input type="text" name="recherche" id="searchJoueur" placeholder="Nom ou prénom">
                <div id="suggestionsJoueur" class="autocomplete-suggestions"></div>
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
            <form method="post" action="services/add_stats.php" autocomplete="off">
                <input type="text" name="player_name" id="playerNameStats" placeholder="Nom ou prénom joueur" required>
                <input type="hidden" name="id_player" id="idPlayerStats">
                <div id="suggestionsStats" class="autocomplete-suggestions"></div>
                <!-- Champs stats -->
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
                <input type="number" name="fg_reussis" placeholder="Field Goals marqués" min="0">
                <input type="number" name="punts" placeholder="Punts" min="0">
                <button type="submit" class="shiny-button">Ajouter les stats</button>
            </form>
        </div>

        <!-- Recherche stats -->
        <div class="card magic-bento">
            <h2>Recherche stats joueur</h2>
            <form method="get" autocomplete="off">
                <input type="hidden" name="page" value="stats">
                <input type="text" name="recherche" id="searchStats" placeholder="Nom ou prénom">
                <div id="suggestionsStatsSearch" class="autocomplete-suggestions"></div>
                <button type="submit" class="shiny-button">Rechercher</button>
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
                echo "<div class='card magic-bento scroll-animate stat-card'>
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
                   COALESCE(SUM(s.td_passe),0) + COALESCE(SUM(s.td_course),0) + COALESCE(SUM(s.td_reception),0) AS total_tds
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
                   COALESCE(SUM(s.plaquages),0) AS total_plaquages
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
    endif; ?>
    </main>
</div>

<!-- Modal overlay pour zoom cartes -->
<div class="card-modal-overlay" id="cardModalOverlay">
    <div class="card-modal" id="cardModalContent"></div>
</div>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>

<!-- SCRIPTS JS -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll animation
    const elements = document.querySelectorAll('.scroll-animate');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => { if(entry.isIntersecting) entry.target.classList.add('show'); });
    }, { threshold: 0.1 });
    elements.forEach(el => observer.observe(el));

    // Modal zoom uniquement sur cartes joueur/stat
    const overlay = document.getElementById('cardModalOverlay');
    const modalContent = document.getElementById('cardModalContent');

    document.querySelectorAll('.player-card, .stat-card').forEach(card => {
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

    // --- Autocomplétion joueurs ---
    const searchInputs = [
        { inputId: 'searchJoueur', suggestionsId: 'suggestionsJoueur' },
        { inputId: 'searchStats', suggestionsId: 'suggestionsStatsSearch' },
        { inputId: 'playerNameStats', suggestionsId: 'suggestionsStats' }
    ];

    searchInputs.forEach(({inputId, suggestionsId}) => {
        const input = document.getElementById(inputId);
        const suggestionBox = document.getElementById(suggestionsId);

        input.addEventListener('input', function() {
            const val = this.value.trim();
            if (!val) { suggestionBox.innerHTML = ''; return; }

            fetch('services/search_player.php?q=' + encodeURIComponent(val))
                .then(res => res.json())
                .then(data => {
                    suggestionBox.innerHTML = '';
                    data.forEach(p => {
                        const div = document.createElement('div');
                        div.classList.add('suggestion-item');
                        div.textContent = p.prenom + ' ' + p.nom;
                        div.dataset.id = p.id_player;
                        div.addEventListener('click', function() {
                            input.value = this.textContent;
                            const hiddenId = document.getElementById('idPlayerStats');
                            if(hiddenId) hiddenId.value = this.dataset.id;
                            suggestionBox.innerHTML = '';
                        });
                        suggestionBox.appendChild(div);
                    });
                });
        });
    });
});
</script>
</body>
</html>
