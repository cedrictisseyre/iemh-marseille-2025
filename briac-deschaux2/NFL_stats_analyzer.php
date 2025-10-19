<?php
// NFL_stats_analyzer.php
// Fichier principal autonome — toutes les fonctionnalités incluses.
// Nécessite : config/database_connexion.php (PDO $pdo)

include __DIR__ . '/config/database_connexion.php';

// Page active
$page = $_GET['page'] ?? 'joueurs';

// Récupération de paramètres globaux (recherche, filtres)
$recherche = $_GET['recherche'] ?? '';
$filtre_poste = $_GET['poste'] ?? '';
$filtre_team = $_GET['team'] ?? '';

// Fonction nav
function nav($active) {
    $tabs = [
        'joueurs' => 'Joueurs',
        'stats' => 'Statistiques',
        'classement' => 'Classement'
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
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="css/style_page.css">
</head>
<body>
<div class="container">

    <!-- HEADER -->
    <div class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </div>

    <!-- NAV -->
    <?php nav($page); ?>

    <main>
    <?php if ($page === 'joueurs') : ?>

        <!-- Ajouter un joueur (formulaire) -->
        <div class="card magic-bento no-zoom">
            <h2>Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php">
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="text" name="nom" placeholder="Nom" required>
                <select name="poste" required>
                    <option value="">Sélectionner un poste</option>
                    <?php
                    $pos = $pdo->query("SELECT code, libelle FROM position ORDER BY libelle")->fetchAll();
                    foreach ($pos as $p) {
                        echo "<option value='".htmlspecialchars($p['code'], ENT_QUOTES)."'>".htmlspecialchars($p['libelle'], ENT_QUOTES)." ({$p['code']})</option>";
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
                            echo "<optgroup label='".htmlspecialchars($current_conf, ENT_QUOTES)."'>";
                        }
                        echo "<option value='".(int)$t['id_team']."'>".htmlspecialchars($t['nom_team'], ENT_QUOTES)."</option>";
                    }
                    if ($current_conf !== "") echo "</optgroup>";
                    ?>
                </select>
                <button type="submit" class="shiny-button">Ajouter le joueur</button>
            </form>
        </div>

        <!-- Recherche joueurs -->
        <div class="card magic-bento no-zoom">
            <h2>Recherche joueur</h2>
            <form method="get" action="">
                <input type="hidden" name="page" value="joueurs">
                <div class="autocomplete-container">
                    <input type="text" name="recherche" placeholder="Nom ou prénom" class="autocomplete-player-search" value="<?php echo htmlspecialchars($recherche, ENT_QUOTES); ?>">
                    <div class="autocomplete-suggestions"></div>
                </div>
                <button type="submit" class="shiny-button">Rechercher</button>
            </form>
        </div>

        <!-- Liste des joueurs -->
        <h2>Liste des joueurs</h2>
        <div class="grid">
            <?php
            $where = "";
            $params = [];
            if (!empty($recherche)) {
                $search = "%$recherche%";
                $where = "WHERE p.nom LIKE ? OR p.prenom LIKE ? OR CONCAT(p.prenom,' ',p.nom) LIKE ? OR CONCAT(p.nom,' ',p.prenom) LIKE ?";
                $params = [$search, $search, $search, $search];
            }
            $sql = "SELECT p.*, t.nom_team, t.logo_url 
                    FROM player p 
                    JOIN team t ON p.id_team = t.id_team 
                    $where
                    ORDER BY p.nom";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            while ($pl = $stmt->fetch()) {
                $experience = date('Y') - (int)$pl['annee_debut'];
                $prenom = htmlspecialchars($pl['prenom'], ENT_QUOTES);
                $nom = htmlspecialchars($pl['nom'], ENT_QUOTES);
                $poste = htmlspecialchars($pl['poste'], ENT_QUOTES);
                $nom_team = htmlspecialchars($pl['nom_team'], ENT_QUOTES);
                $logo = htmlspecialchars($pl['logo_url'] ?? '', ENT_QUOTES);

                echo "<div class='card magic-bento zoomable scroll-animate'>
                        <h3>{$prenom} {$nom}</h3>
                        <p><strong>Poste:</strong> {$poste}</p>
                        <p><strong>Équipe:</strong> <img src='{$logo}' alt='' style='width:30px;height:30px;vertical-align:middle;'> {$nom_team}</p>
                        <p><strong>Âge:</strong> ".((int)$pl['age'])." ans</p>
                        <p><strong>Taille:</strong> ".((int)$pl['taille_cm'])." cm - <strong>Poids:</strong> ".((int)$pl['poids_kg'])." kg</p>
                        <p><strong>Expérience:</strong> {$experience} ans</p>
                      </div>";
            }
            ?>
        </div>

    <?php elseif ($page === 'stats') : 
        $saison = date('Y'); ?>

        <!-- Formulaire ajout stats -->
        <div class="card magic-bento no-zoom">
            <h2>Ajouter des statistiques (Saison <?= $saison ?>)</h2>
            <form method="post" action="services/add_stats.php">
                <!-- Select filtrable pour choisir le joueur -->
                <div class="filterable-select">
                    <input type="text" id="playerFilter" placeholder="Filtrer par nom ou prénom...">
                    <select name="id_player" id="playerSelect" required size="6">
                        <option value="">-- Sélectionner un joueur --</option>
                        <?php
                        $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom, prenom")->fetchAll();
                        foreach ($players as $pl) {
                            $label = htmlspecialchars($pl['prenom'] . ' ' . $pl['nom'], ENT_QUOTES);
                            echo "<option value='".(int)$pl['id_player']."'>{$label}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Champs statistiques (tous conservés) -->
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
        <div class="card magic-bento no-zoom">
            <h2>Recherche stats joueur</h2>
            <form method="get" action="">
                <input type="hidden" name="page" value="stats">
                <div class="autocomplete-container">
                    <input type="text" name="recherche" placeholder="Nom ou prénom" class="autocomplete-player-search" value="<?php echo htmlspecialchars($recherche, ENT_QUOTES); ?>">
                    <div class="autocomplete-suggestions"></div>
                </div>
                <button type="submit" class="shiny-button">Rechercher</button>
            </form>
        </div>

        <!-- Affichage stats -->
        <h2>Statistiques <?= $saison ?></h2>
        <div class="grid">
            <?php
            $where = "WHERE s.saison = ?";
            $params = [$saison];
            if (!empty($recherche)) {
                $search = "%$recherche%";
                $where .= " AND (p.nom LIKE ? OR p.prenom LIKE ? OR CONCAT(p.prenom,' ',p.nom) LIKE ? OR CONCAT(p.nom,' ',p.prenom) LIKE ?)";
                $params = array_merge($params, [$search, $search, $search, $search]);
            }

            $sql = "SELECT s.*, p.prenom, p.nom, p.poste, t.nom_team, t.logo_url
                    FROM stats s
                    JOIN player p ON s.id_player = p.id_player
                    JOIN team t ON p.id_team = t.id_team
                    $where
                    ORDER BY p.nom";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $has_stats = false;
            while ($st = $stmt->fetch()) {
                $has_stats = true;
                $prenom = htmlspecialchars($st['prenom'], ENT_QUOTES);
                $nom = htmlspecialchars($st['nom'], ENT_QUOTES);
                $poste = htmlspecialchars($st['poste'], ENT_QUOTES);
                $logo = htmlspecialchars($st['logo_url'] ?? '', ENT_QUOTES);

                echo "<div class='card magic-bento zoomable scroll-animate'>
                        <h3><img src='{$logo}' alt='' style='width:30px;height:30px;vertical-align:middle;margin-right:5px;'> {$prenom} {$nom} ({$poste})</h3>";
                foreach ($st as $key => $val) {
                    if (in_array($key, ['id_stat','id_player','prenom','nom','poste','saison','nom_team','logo_url'])) continue;
                    if ($val !== null && $val != 0) {
                        $label = ucfirst(str_replace('_', ' ', $key));
                        echo "<p><strong>{$label}:</strong> ".htmlspecialchars($val, ENT_QUOTES)."</p>";
                    }
                }
                echo "</div>";
            }
            if (!$has_stats) echo "<p>Aucune statistique disponible pour cette saison.</p>";
            ?>
        </div>

    <?php elseif ($page === 'classement') : ?>
        <!-- Page classement : intégrée directement, par conférence / division -->
        <h2>Classement (Totaux saison <?= date('Y') ?>)</h2>

        <!-- Filtres -->
        <div class="card magic-bento no-zoom">
            <h2>Filtres Classement</h2>
            <form method="get" action="">
                <input type="hidden" name="page" value="classement">
                <label>Poste :</label>
                <select name="poste">
                    <option value="">Tous</option>
                    <?php
                    $positions = $pdo->query("SELECT code, libelle FROM position ORDER BY libelle")->fetchAll();
                    foreach ($positions as $p) {
                        $sel = ($filtre_poste === $p['code']) ? "selected" : "";
                        echo "<option value='".htmlspecialchars($p['code'], ENT_QUOTES)."' $sel>".htmlspecialchars($p['libelle'], ENT_QUOTES)." ({$p['code']})</option>";
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
                            echo "<optgroup label='".htmlspecialchars($current_conf, ENT_QUOTES)."'>";
                        }
                        $sel = ($filtre_team == $t['id_team']) ? "selected" : "";
                        echo "<option value='".(int)$t['id_team']."' $sel>".htmlspecialchars($t['nom_team'], ENT_QUOTES)."</option>";
                    }
                    if ($current_conf !== "") echo "</optgroup>";
                    ?>
                </select>

                <button type="submit" class="shiny-button">Filtrer</button>
            </form>
        </div>

        <?php
        // --- Classement TDs par conférence (like original) ---
        $saison = date('Y');
        $sql_conf = "
            SELECT p.prenom, p.nom, p.poste, t.conference,
                   (COALESCE(SUM(s.passing_tds),0) + COALESCE(SUM(s.rushing_tds),0) + COALESCE(SUM(s.receiving_tds),0)) AS total_tds
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
            echo "<div class='grid'>";
            foreach ($conf_data as $row) {
                if ($row['conference'] !== $conf) {
                    $conf = $row['conference'];
                    echo "</div>";
                    echo "<h3 class='conference-title'>".htmlspecialchars($conf, ENT_QUOTES)."</h3>";
                    echo "<div class='grid'>";
                }
                $prenom = htmlspecialchars($row['prenom'], ENT_QUOTES);
                $nom = htmlspecialchars($row['nom'], ENT_QUOTES);
                $poste = htmlspecialchars($row['poste'], ENT_QUOTES);
                $tds = (int)$row['total_tds'];

                echo "<div class='card magic-bento no-zoom scroll-animate'>
                        <h4>{$prenom} {$nom} ({$poste})</h4>
                        <p><strong>Total TDs:</strong> {$tds}</p>
                      </div>";
            }
            echo "</div>";
        } else {
            echo "<p>Aucun leader TD trouvé pour la saison.</p>";
        }

        // --- Classement Plaquages par division ---
        $sql_div = "
            SELECT p.prenom, p.nom, p.poste, t.division,
                   COALESCE(SUM(s.tackles),0) AS total_plaquages
            FROM player p
            JOIN team t ON p.id_team = t.id_team
            LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = :saison2
            WHERE 1=1";

        $params2 = [':saison2' => $saison];
        if ($filtre_poste !== '') { $sql_div .= " AND p.poste = :poste2"; $params2[':poste2'] = $filtre_poste; }
        if ($filtre_team !== '') { $sql_div .= " AND p.id_team = :team2"; $params2[':team2'] = $filtre_team; }

        $sql_div .= " GROUP BY p.id_player, p.prenom, p.nom, p.poste, t.division
                      HAVING total_plaquages > 0
                      ORDER BY t.division, total_plaquages DESC";

        $stmt_div = $pdo->prepare($sql_div);
        $stmt_div->execute($params2);
        $div_data = $stmt_div->fetchAll();

        if (count($div_data) > 0) {
            echo "<h2>Classement par division (Plaquages)</h2>";
            $div = '';
            echo "<div class='grid'>";
            foreach ($div_data as $row) {
                if ($row['division'] !== $div) {
                    $div = $row['division'];
                    echo "</div>";
                    echo "<h3 class='conference-title'>".htmlspecialchars($div, ENT_QUOTES)."</h3>";
                    echo "<div class='grid'>";
                }
                $prenom = htmlspecialchars($row['prenom'], ENT_QUOTES);
                $nom = htmlspecialchars($row['nom'], ENT_QUOTES);
                $poste = htmlspecialchars($row['poste'], ENT_QUOTES);
                $plaquages = (int)$row['total_plaquages'];

                echo "<div class='card magic-bento no-zoom scroll-animate'>
                        <h4>{$prenom} {$nom} ({$poste})</h4>
                        <p><strong>Plaquages:</strong> {$plaquages}</p>
                      </div>";
            }
            echo "</div>";
        } else {
            echo "<p>Aucun leader plaquages trouvé pour la saison.</p>";
        }

    endif; ?>
    </main>
</div>

<!-- Modal overlay pour zoom (uniquement pour .zoomable) -->
<div class="card-modal-overlay" id="cardModalOverlay" aria-hidden="true">
    <div class="card-modal" id="cardModalContent" role="dialog" aria-modal="true"></div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> NFL Stats Analyzer - Projet académique</p>
</footer>

<script>
// JS : scroll animation + modal zoom (uniquement .zoomable) + autocomplétion + select filtrable
document.addEventListener('DOMContentLoaded', function() {
    // Scroll animation
    const elements = document.querySelectorAll('.scroll-animate');
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => { if(entry.isIntersecting) entry.target.classList.add('show'); });
    }, { threshold: 0.1 });
    elements.forEach(el => observer.observe(el));

    // Modal zoom uniquement pour .zoomable
    const overlay = document.getElementById('cardModalOverlay');
    const modalContent = document.getElementById('cardModalContent');

    document.querySelectorAll('.card.magic-bento.zoomable').forEach(card => {
        card.addEventListener('click', function() {
            modalContent.innerHTML = card.innerHTML;
            overlay.style.display = 'flex';
            overlay.setAttribute('aria-hidden', 'false');
            setTimeout(() => modalContent.classList.add('show'), 10);
        });
    });

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) {
            modalContent.classList.remove('show');
            overlay.setAttribute('aria-hidden', 'true');
            setTimeout(() => overlay.style.display = 'none', 300);
        }
    });

    // --- Autocomplétion (fetch vers services/player_search.php) ---
    const autocompleteInputs = document.querySelectorAll('.autocomplete-player, .autocomplete-player-search');
    autocompleteInputs.forEach(input => {
        const suggestionsContainer = input.parentElement.querySelector('.autocomplete-suggestions');
        if (!suggestionsContainer) return;

        input.addEventListener('input', function() {
            const val = this.value.trim();
            if (val.length < 1) {
                suggestionsContainer.innerHTML = '';
                return;
            }
            fetch('services/player_search.php?q=' + encodeURIComponent(val))
                .then(res => res.json())
                .then(data => {
                    suggestionsContainer.innerHTML = '';
                    data.forEach(player => {
                        const div = document.createElement('div');
                        div.textContent = player.prenom + ' ' + player.nom;
                        div.dataset.id = player.id_player;
                        div.classList.add('suggestion-item');
                        div.addEventListener('click', () => {
                            input.value = player.prenom + ' ' + player.nom;
                            // si besoin d'un champ hidden pour id_player, on peut le gérer dans le formulaire cible
                            suggestionsContainer.innerHTML = '';
                        });
                        suggestionsContainer.appendChild(div);
                    });
                }).catch(err => {
                    // silently ignore or optionally show an error entry
                    suggestionsContainer.innerHTML = '';
                });
        });

        document.addEventListener('click', (e) => {
            if (!input.contains(e.target) && !suggestionsContainer.contains(e.target)) {
                suggestionsContainer.innerHTML = '';
            }
        });
    });

    // --- Filtrage du select des joueurs (stats) ---
    const playerFilter = document.getElementById('playerFilter');
    const playerSelect = document.getElementById('playerSelect');

    if (playerFilter && playerSelect) {
        playerFilter.addEventListener('input', function() {
            const filter = this.value.trim().toLowerCase();
            Array.from(playerSelect.options).forEach(opt => {
                // Toujours afficher l'option vide
                if (opt.value === "") { opt.style.display = ""; return; }
                const text = opt.text.toLowerCase();
                opt.style.display = (text.includes(filter)) ? "" : "none";
            });
            // si après filtrage il n'y a plus d'options visibles, on peut éventuellement montrer un message (omitted)
        });

        // Optionnel : double-clic pour sélectionner rapidement
        playerSelect.addEventListener('dblclick', function() {
            if (this.selectedIndex >= 0) {
                // nothing special, select will be submitted by the form
            }
        });
    }
});
</script>
</body>
</html>
