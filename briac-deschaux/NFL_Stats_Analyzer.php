<?php
declare(strict_types=1);

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

// page
$page = $_GET['page'] ?? 'joueurs';

// Récupérer listes utilitaires
// positions et teams pour formulaires
$positions = $pdo->query('SELECT id, code, libelle FROM position ORDER BY libelle')->fetchAll();
$teams = $pdo->query('SELECT id_team, nom_team FROM team ORDER BY nom_team')->fetchAll();

function nav(string $active) {
    $tabs = [
        'joueurs'   => 'Joueurs',
        'stats'     => 'Statistiques',
        'classement'=> 'Classement'
    ];
    echo '<div class="menu">';
    foreach ($tabs as $key => $label) {
        $class = ($active === $key) ? 'active' : '';
        echo "<a href='?page={$key}' class='{$class}'>" . e($label) . "</a>";
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
    <div class="header" role="banner">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1 id="page-title">NFL STATS ANALYZER</h1>
    </div>

    <!-- NAV MENU -->
    <?php nav($page); ?>

    <main role="main" aria-labelledby="page-title">
        <?php if ($page === 'joueurs') : ?>

            <div class="card" aria-labelledby="ajout-joueur">
                <h2 id="ajout-joueur">Ajouter un joueur</h2>
                <form method="post" action="services/add_player.php" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>

                    <!-- Sélecteur position (préférable au champ texte) -->
                    <select name="position_id" >
                        <option value="">Sélectionner un poste (optionnel)</option>
                        <?php foreach ($positions as $pos): ?>
                            <option value="<?= e((string)$pos['id']) ?>"><?= e($pos['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- fallback texte (legacy) -->
                    <input type="text" name="poste" placeholder="Poste (texte, si non listé)">

                    <input type="number" name="age" placeholder="Âge" min="16" max="60">
                    <input type="number" name="taille_cm" placeholder="Taille (cm)" min="140" max="230">
                    <input type="number" name="poids_kg" placeholder="Poids (kg)" min="50" max="200">
                    <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)" min="1900" max="<?= date('Y') ?>">
                    <!-- Sélecteur équipe -->
                    <select name="id_team" required>
                        <option value="">Sélectionner une équipe</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= e((string)$t['id_team']) ?>"><?= e($t['nom_team']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Ajouter le joueur</button>
                </form>
            </div>

            <!-- Barre recherche / filtres -->
            <div class="card">
                <h2>Rechercher / Filtrer</h2>
                <form method="get" action="">
                    <input type="hidden" name="page" value="joueurs">
                    <input type="search" name="q" placeholder="Rechercher nom ou prénom" value="<?= e($_GET['q'] ?? '') ?>">
                    <select name="team_filter">
                        <option value="">Toutes équipes</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= e((string)$t['id_team']) ?>" <?= (isset($_GET['team_filter']) && $_GET['team_filter'] == $t['id_team']) ? 'selected' : '' ?>><?= e($t['nom_team']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="position_filter">
                        <option value="">Tous postes</option>
                        <?php foreach ($positions as $pos): ?>
                            <option value="<?= e((string)$pos['id']) ?>" <?= (isset($_GET['position_filter']) && $_GET['position_filter'] == $pos['id']) ? 'selected' : '' ?>><?= e($pos['libelle']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Appliquer</button>
                </form>
            </div>

            <!-- Liste des joueurs -->
            <h2>Liste des joueurs</h2>
            <div class="grid">
                <?php
                // Construction dynamique de la requête selon filtres
                $params = [];
                $where = [];
                if (!empty($_GET['q'])) {
                    $where[] = '(p.nom LIKE :q OR p.prenom LIKE :q)';
                    $params[':q'] = '%' . $_GET['q'] . '%';
                }
                if (!empty($_GET['team_filter'])) {
                    $where[] = 'p.id_team = :team';
                    $params[':team'] = (int)$_GET['team_filter'];
                }
                if (!empty($_GET['position_filter'])) {
                    $where[] = 'p.position_id = :pos';
                    $params[':pos'] = (int)$_GET['position_filter'];
                }

                $sql = "SELECT p.*, t.nom_team, pos.libelle AS position_lib
                        FROM player p
                        LEFT JOIN team t ON p.id_team = t.id_team
                        LEFT JOIN position pos ON p.position_id = pos.id";
                if ($where) {
                    $sql .= ' WHERE ' . implode(' AND ', $where);
                }
                $sql .= ' ORDER BY p.nom LIMIT 200'; // pagination simple / protection
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                while ($pl = $stmt->fetch()) {
                    $experience = ($pl['annee_debut']) ? (date('Y') - (int)$pl['annee_debut']) : 'N/A';
                    $position = $pl['position_lib'] ?: $pl['poste'] ?: '—';
                    echo "<div class='card'>
                        <h3>" . e($pl['prenom']) . " " . e($pl['nom']) . "</h3>
                        <p><strong>Poste:</strong> " . e($position) . "</p>
                        <p><strong>Équipe:</strong> " . e($pl['nom_team'] ?? '—') . "</p>
                        <p>Âge: " . e((string)$pl['age']) . " ans</p>
                        <p>Taille: " . e((string)$pl['taille_cm']) . " cm - Poids: " . e((string)$pl['poids_kg']) . " kg</p>
                        <p>Expérience: " . e((string)$experience) . " ans</p>
                    </div>";
                }
                ?>
            </div>

        <?php elseif ($page === 'stats') : 
            $saison = (int) ($_GET['saison'] ?? date('Y'));
        ?>
            <div class="card">
                <h2>Ajouter des statistiques (Saison <?= e((string)$saison) ?>)</h2>
                <form method="post" action="services/add_stats.php">
                    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <select name="id_player" required>
                        <option value="">Sélectionner un joueur</option>
                        <?php
                        $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom")->fetchAll();
                        foreach ($players as $p) {
                            echo "<option value='" . e((string)$p['id_player']) . "'>" . e($p['prenom'] . ' ' . $p['nom']) . "</option>";
                        }
                        ?>
                    </select>

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
                </form>
            </div>

            <h2>Statistiques <?= e((string)$saison) ?></h2>
            <div class="grid">
                <?php
                $stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste 
                                       FROM stats s 
                                       JOIN player p ON s.id_player = p.id_player 
                                       WHERE s.saison = ? 
                                       ORDER BY p.nom");
                $stmt->execute([$saison]);

                // On affiche propres labels et on escape les valeurs
                $exclude = ['id_stat','id_player','prenom','nom','poste','saison'];
                while ($st = $stmt->fetch()) {
                    echo "<div class='card'>
                        <h3>" . e($st['prenom']) . " " . e($st['nom']) . " (" . e($st['poste']) . ")</h3>";

                    foreach ($st as $key => $val) {
                        if (in_array($key, $exclude, true)) continue;
                        if ($val === null || $val === '' || $val == 0) continue;
                        $label = ucfirst(str_replace('_', ' ', $key));
                        echo "<p>" . e($label) . ": " . e((string)$val) . "</p>";
                    }
                    echo "</div>";
                }
                ?>
            </div>

        <?php elseif ($page === 'classement') : ?>
            <h2>Classement par conférence (Total TD)</h2>
            <?php
            $sql_conf = "SELECT p.prenom, p.nom, p.poste, t.conference,
                           (COALESCE(s.td_passe,0) + COALESCE(s.td_course,0) + COALESCE(s.td_reception,0)) as total_td
                    FROM player p 
                    JOIN team t ON p.id_team = t.id_team 
                    LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ? 
                    ORDER BY t.conference, total_td DESC";
            $stmt_conf = $pdo->prepare($sql_conf);
            $stmt_conf->execute([date('Y')]);

            $conf = '';
            while ($row = $stmt_conf->fetch()) {
                if ($row['conference'] !== $conf) {
                    if ($conf !== '') echo '</ol>';
                    $conf = $row['conference'];
                    echo "<h3>" . e($conf) . "</h3><ol>";
                }
                echo "<li>" . e($row['prenom']) . " " . e($row['nom']) . " (" . e($row['poste']) . ") - " . e((string)$row['total_td']) . " TD</li>";
            }
            if ($conf !== '') echo '</ol>';
            ?>

            <h2>Classement par division (Plaquages)</h2>
            <?php
            $sql_div = "SELECT p.nom, p.prenom, p.poste, t.division,
                           COALESCE(s.plaquages,0) as total_plaquages
                    FROM player p 
                    JOIN team t ON p.id_team = t.id_team 
                    LEFT JOIN stats s ON p.id_player = s.id_player AND s.saison = ? 
                    ORDER BY t.division, total_plaquages DESC";
            $stmt_div = $pdo->prepare($sql_div);
            $stmt_div->execute([date('Y')]);

            $div = '';
            while ($row = $stmt_div->fetch()) {
                if ($row['division'] !== $div) {
                    if ($div !== '') echo '</ol>';
                    $div = $row['division'];
                    echo "<h3>" . e($div) . "</h3><ol>";
                }
                echo "<li>" . e($row['prenom']) . " " . e($row['nom']) . " (" . e($row['poste']) . ") - " . e((string)$row['total_plaquages']) . " plaquages</li>";
            }
            if ($div !== '') echo '</ol>';
            ?>
        <?php endif; ?>
    </main>
</div>

<footer role="contentinfo">
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>
</body>
</html>
