<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php';

// page selection
$page = $_GET['page'] ?? 'joueurs';

// Load positions (from position table if exists, else fallback to distinct player.poste)
try {
    $positions = [];
    $hasPosTable = false;
    $res = $pdo->query("SHOW TABLES LIKE 'position'")->fetchAll();
    if (count($res) > 0) {
        $hasPosTable = true;
        $positions = $pdo->query("SELECT id, code, libelle FROM position ORDER BY libelle")->fetchAll();
    } else {
        $positions = array_map(function($r){ return ['code'=>$r]; }, $pdo->query("SELECT DISTINCT poste FROM player WHERE poste IS NOT NULL AND poste!='' ORDER BY poste")->fetchAll(PDO::FETCH_COLUMN));
    }
} catch (PDOException $e) {
    $positions = [];
}

// Load teams grouped by conference and division
$teams_grouped = [];
try {
    $stmt = $pdo->query("SELECT id_team, nom_team, conference, division FROM team ORDER BY conference, division, nom_team");
    $teams = $stmt->fetchAll();
    foreach ($teams as $t) {
        $conf = $t['conference'] ?? 'Autre';
        $div = $t['division'] ?? 'Autre';
        if (!isset($teams_grouped[$conf])) $teams_grouped[$conf] = [];
        if (!isset($teams_grouped[$conf][$div])) $teams_grouped[$conf][$div] = [];
        $teams_grouped[$conf][$div][] = $t;
    }
} catch (PDOException $e) {
    $teams_grouped = [];
}

// helper: build select options for teams grouped
function render_team_select(array $teams_grouped, $name = 'id_team', $required = true, $selected = null) {
    $req = $required ? 'required' : '';
    $html = "<select name=\"" . htmlspecialchars($name) . "\" $req>";
    $html .= "<option value=\"\">-- Sélectionner une équipe --</option>";
    foreach ($teams_grouped as $conf => $divs) {
        $html .= "<optgroup label=\"" . e($conf) . "\">";
        foreach ($divs as $div => $teams) {
            $html .= "<optgroup label=\"  " . e($div) . "\">"; // nested optgroup for division (some browsers don’t support nested optgroup but many do; fallback still readable)
            foreach ($teams as $t) {
                $sel = ((string)$selected === (string)$t['id_team']) ? 'selected' : '';
                $html .= "<option value=\"" . e((string)$t['id_team']) . "\" $sel>" . e($t['nom_team']) . "</option>";
            }
            $html .= "</optgroup>";
        }
        $html .= "</optgroup>";
    }
    $html .= "</select>";
    return $html;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>NFL Stats Analyzer</title>
    <link rel="stylesheet" href="css/style_page.css">
    <style>
        /* Responsive tables */
        .table-responsive { overflow-x:auto; width:100%; }
        table { width:100%; border-collapse: collapse; min-width: 600px; }
        th, td { padding: 0.5rem; border: 1px solid #e2e8f0; }
        th { cursor: pointer; background:#f1f5f9; position: sticky; top:0; }
        .filters { display:flex; gap:0.6em; flex-wrap:wrap; align-items:center; }
        .filters select, .filters input { padding:0.4em; border-radius:6px; border:1px solid #cbd5e1; }
        @media (max-width:800px) {
            .card { padding:0.8em; }
            table { min-width: 480px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </div>

    <?php
    // navigation
    echo '<div class="menu">';
    $tabs = ['joueurs'=>'Joueurs','stats'=>'Statistiques','ranking'=>'Classement'];
    foreach ($tabs as $k=>$lab) {
        $class = ($page === $k) ? 'active' : '';
        echo "<a href='?page=".e($k)."' class='$class'>".e($lab)."</a>";
    }
    echo '</div>';
    ?>

    <main>

    <?php if ($page === 'joueurs') : ?>

        <div class="card">
            <h2>Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="display:flex;gap:0.6em;flex-wrap:wrap;">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>
                    <!-- position select -->
                    <select name="position_id">
                        <option value="">-- Poste (optionnel) --</option>
                        <?php foreach ($positions as $pos): ?>
                            <?php if (isset($pos['id'])): ?>
                                <option value="<?= e((string)$pos['id']) ?>"><?= e($pos['libelle'] . ' (' . $pos['code'] . ')') ?></option>
                            <?php else: ?>
                                <option value="<?= e($pos['code']) ?>"><?= e($pos['code']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <!-- fallback legacy poste text (hidden but left for compatibility) -->
                    <input type="text" name="poste" placeholder="Poste (texte)" style="min-width:160px;">

                    <input type="number" name="age" placeholder="Âge" min="16" max="60">
                    <input type="number" name="taille_cm" placeholder="Taille (cm)" min="140" max="230">
                    <input type="number" name="poids_kg" placeholder="Poids (kg)" min="50" max="200">
                    <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)" min="1900" max="<?= date('Y') ?>">

                    <!-- team select grouped by conference/division -->
                    <?= render_team_select($teams_grouped, 'id_team', true) ?>
                </div>

                <div style="margin-top:0.6em;">
                    <button type="submit">Ajouter le joueur</button>
                </div>
            </form>
        </div>

        <h2>Liste des joueurs</h2>
        <div class="card">
            <div style="margin-bottom:0.6em;">
                <form method="get" style="display:flex;gap:0.6em;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="page" value="joueurs">
                    <input type="search" name="q" placeholder="Rechercher nom/prénom" value="<?= e((string)($_GET['q'] ?? '')) ?>">
                    <select name="team_filter">
                        <option value="">Toutes équipes</option>
                        <?php foreach ($teams as $t): ?>
                            <option value="<?= e((string)$t['id_team']) ?>" <?= (isset($_GET['team_filter']) && $_GET['team_filter'] == $t['id_team']) ? 'selected' : '' ?>><?= e($t['nom_team']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="position_filter">
                        <option value="">Tous postes</option>
                        <?php foreach ($positions as $pos): ?>
                            <?php $val = $pos['id'] ?? $pos['code']; ?>
                            <option value="<?= e((string)$val) ?>" <?= (isset($_GET['position_filter']) && $_GET['position_filter'] == $val) ? 'selected' : '' ?>><?= e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Filtrer</button>
                </form>
            </div>

            <div class="grid">
                <?php
                $params = [];
                $where = [];
                if (!empty($_GET['q'])) { $where[] = '(p.nom LIKE :q OR p.prenom LIKE :q)'; $params[':q'] = '%'.$_GET['q'].'%'; }
                if (!empty($_GET['team_filter'])) { $where[] = 'p.id_team = :team'; $params[':team'] = (int)$_GET['team_filter']; }
                if (!empty($_GET['position_filter'])) { $where[] = ' (p.position_id = :pos OR p.poste = :pos_text)'; $params[':pos'] = (int)$_GET['position_filter']; $params[':pos_text'] = $_GET['position_filter']; }
                $sql = "SELECT p.*, t.nom_team, pos.libelle as position_lib, pos.code as position_code FROM player p LEFT JOIN team t ON p.id_team = t.id_team LEFT JOIN position pos ON p.position_id = pos.id";
                if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
                $sql .= ' ORDER BY p.nom LIMIT 500';
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                while ($pl = $stmt->fetch()) {
                    $experience = $pl['annee_debut'] ? (date('Y') - (int)$pl['annee_debut']) : 'N/A';
                    $position = $pl['position_lib'] ?: $pl['poste'] ?: '—';
                    echo "<div class='card'>
";
                    echo "<h3>" . e($pl['prenom']) . " " . e($pl['nom']) . "</h3>
";
                    echo "<p><strong>Poste:</strong> " . e($position) . "</p>
";
                    echo "<p><strong>Équipe:</strong> " . e($pl['nom_team'] ?? '—') . "</p>
";
                    echo "<p>Âge: " . e((string)$pl['age']) . " ans</p>
";
                    echo "<p>Taille: " . e((string)$pl['taille_cm']) . " cm - Poids: " . e((string)$pl['poids_kg']) . " kg</p>
";
                    echo "<p>Expérience: " . e((string)$experience) . " ans</p>
";
                    echo "</div>
";
                }
                ?>
            </div>
        </div>

    <?php elseif ($page === 'stats') : ?>

        <h2>Statistiques par poste</h2>
        <div class="card">
            <p>Affichage des statistiques adaptées au poste. Les tableaux sont responsives.</p>
        </div>

        <?php
        // Define groups: QB, WR, RB, DB (CB+S), LB
        $groups = [
            'QB' => ['label'=>'Quarterbacks','codes'=>['QB']],'WR' => ['label'=>'Wide Receivers','codes'=>['WR']],'RB'=>['label'=>'Running Backs','codes'=>['RB']],
            'DB'=>['label'=>'Defensive Backs','codes'=>['CB','S']],'LB'=>['label'=>'Linebackers','codes'=>['LB']]
        ];

        foreach ($groups as $gcode => $ginfo) {
            $labels = $ginfo['label'];
            $codes = $ginfo['codes'];
            echo "<h3>".e($labels)."</h3>";

            // Build placeholder for codes in SQL
            $placeholders = implode(',', array_fill(0, count($codes), '?'));
            // Query stats joined with player and team, use position code if exists else p.poste
            $sql = "SELECT p.prenom, p.nom, COALESCE(pos.code, p.poste) as pos_code, t.nom_team, s.*
                    FROM stats s
                    JOIN player p ON p.id_player = s.id_player
                    LEFT JOIN team t ON t.id_team = p.id_team
                    LEFT JOIN position pos ON pos.id = p.position_id
                    WHERE (COALESCE(pos.code, p.poste) IN ($placeholders))
                    ORDER BY s.saison DESC, p.nom";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($codes);
            $rows = $stmt->fetchAll();

            if (count($rows) === 0) {
                echo "<div class=\"card\"><p>Aucune donnée pour ce groupe.</p></div>";
                continue;
            }

            // decide columns per group
            switch ($gcode) {
                case 'QB': $cols = ['prenom','nom','nom_team','saison','yards_passe','td_passe','interceptions','yards_course','td_course']; break;
                case 'WR': $cols = ['prenom','nom','nom_team','saison','receptions','yards_reception','td_reception']; break;
                case 'RB': $cols = ['prenom','nom','nom_team','saison','yards_course','td_course','receptions']; break;
                case 'DB': $cols = ['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']; break;
                case 'LB': $cols = ['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']; break;
                default: $cols = ['prenom','nom','nom_team','saison'];
            }

            echo "<div class=\"table-responsive\"><table class=\"sortable\"><thead><tr>";
            foreach ($cols as $c) { echo "<th>".e(ucfirst(str_replace('_',' ',$c)))."</th>"; }
            echo "</tr></thead><tbody>";
            foreach ($rows as $r) {
                echo "<tr>";
                foreach ($cols as $c) {
                    $val = $r[$c] ?? '';
                    echo "<td>" . e((string)$val) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        }
        ?>

    <?php elseif ($page === 'ranking') : ?>

        <h2>Classements</h2>
        <div class="card">
            <form method="get" class="filters">
                <input type="hidden" name="page" value="ranking">
                <label>Saison
                    <select name="saison">
                        <?php
                        $saisons = $pdo->query("SELECT DISTINCT saison FROM stats ORDER BY saison DESC")->fetchAll(PDO::FETCH_COLUMN);
                        foreach ($saisons as $s) {
                            $sel = (isset($_GET['saison']) && $_GET['saison']==$s) ? 'selected' : '';
                            echo "<option value=\"".e((string)$s)."\" $sel>".e((string)$s)."</option>";
                        }
                        ?>
                    </select>
                </label>

                <label>Conférence
                    <select name="conference">
                        <option value="">Toutes</option>
                        <?php foreach (array_keys($teams_grouped) as $conf) { $sel = (isset($_GET['conference']) && $_GET['conference']==$conf)?'selected':''; echo "<option value=\"".e($conf)."\" $sel>".e($conf)."</option>"; } ?>
                    </select>
                </label>

                <label>Poste
                    <select name="position_filter">
                        <option value="">Tous</option>
                        <?php foreach ($positions as $pos) { $val = $pos['id'] ?? $pos['code']; echo "<option value=\"".e($val)."\">".e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code'])."</option>"; } ?>
                    </select>
                </label>

                <label>Métrique
                    <select name="metric">
                        <option value="td">Total TD</option>
                        <option value="plaquages">Plaquages</option>
                    </select>
                </label>

                <button type="submit">Afficher</button>
            </form>
        </div>

        <?php
        $saison = $_GET['saison'] ?? date('Y');
        $conference = $_GET['conference'] ?? '';
        $position_filter = $_GET['position_filter'] ?? '';
        $metric = $_GET['metric'] ?? 'td';

        // Build SQL: we want classement par conférence (AFC/NFC), then top players
        $sql = "SELECT p.prenom, p.nom, COALESCE(pos.code,p.poste) as pos_code, t.nom_team, t.conference,
                       (COALESCE(s.td_passe,0)+COALESCE(s.td_course,0)+COALESCE(s.td_reception,0)) as total_td,
                       COALESCE(s.plaquages,0) as total_plaquages,
                       (COALESCE(s.yards_passe,0)+COALESCE(s.yards_course,0)+COALESCE(s.yards_reception,0)) as total_yards
                FROM player p
                LEFT JOIN team t ON t.id_team = p.id_team
                LEFT JOIN stats s ON s.id_player = p.id_player AND s.saison = :saison
                LEFT JOIN position pos ON pos.id = p.position_id
                WHERE 1=1";
        $params = [':saison'=>$saison];
        if ($conference !== '') { $sql .= ' AND t.conference = :conf'; $params[':conf'] = $conference; }
        if ($position_filter !== '') { $sql .= ' AND (pos.id = :posid OR p.poste = :posid_text)'; $params[':posid'] = $position_filter; $params[':posid_text'] = $position_filter; }

        // choose order
        if ($metric === 'td') {
            $sql .= ' ORDER BY total_td DESC, total_yards DESC';
        } else {
            $sql .= ' ORDER BY total_plaquages DESC, total_yards DESC';
        }
        $sql .= ' LIMIT 200';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rank = $stmt->fetchAll();

        // Display per conference groups
        if (!$conference) {
            // group by conference
            $grouped = [];
            foreach ($rank as $r) {
                $conf = $r['conference'] ?? 'Autre';
                $grouped[$conf][] = $r;
            }
            foreach ($grouped as $conf => $rows) {
                echo "<h3>".e($conf)."</h3>";
                echo "<div class=\"table-responsive\"><table class=\"sortable\"><thead><tr><th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>TD</th><th>Plaquages</th><th>Yards</th></tr></thead><tbody>";
                $i=1;
                foreach ($rows as $r) {
                    echo "<tr><td>".e((string)$i)."</td><td>".e($r['prenom'].' '.$r['nom'])."</td><td>".e($r['pos_code'])."</td><td>".e($r['nom_team'] ?? '—')."</td><td>".e((string)$r['total_td'])."</td><td>".e((string)$r['total_plaquages'])."</td><td>".e((string)$r['total_yards'])."</td></tr>";
                    $i++;
                }
                echo "</tbody></table></div>";
            }
        } else {
            // single conference
            echo "<div class=\"table-responsive\"><table class=\"sortable\"><thead><tr><th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>TD</th><th>Plaquages</th><th>Yards</th></tr></thead><tbody>";
            $i=1;
            foreach ($rank as $r) {
                echo "<tr><td>".e((string)$i)."</td><td>".e($r['prenom'].' '.$r['nom'])."</td><td>".e($r['pos_code'])."</td><td>".e($r['nom_team'] ?? '—')."</td><td>".e((string)$r['total_td'])."</td><td>".e((string)$r['total_plaquages'])."</td><td>".e((string)$r['total_yards'])."</td></tr>";
                $i++;
            }
            echo "</tbody></table></div>";
        }
        ?>

    <?php endif; ?>

    </main>
</div>
<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>

<script>
// simple sortable tables
function sortTable(table, col) {
  const tbody = table.tBodies[0];
  const rows = Array.from(tbody.rows);
  const asc = table.asc = !table.asc;
  rows.sort((a,b) => {
    let A = a.cells[col].innerText.trim();
    let B = b.cells[col].innerText.trim();
    if (!isNaN(A) && !isNaN(B)) { A = Number(A); B = Number(B); }
    return A > B ? (asc?1:-1) : (A < B ? (asc?-1:1) : 0);
  });
  rows.forEach(r => tbody.appendChild(r));
}

document.querySelectorAll('table.sortable').forEach(table => {
  table.querySelectorAll('th').forEach((th, i) => th.addEventListener('click', ()=> sortTable(table, i)));
});
</script>
</body>
</html>

