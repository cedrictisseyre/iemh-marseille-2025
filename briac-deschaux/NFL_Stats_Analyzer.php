<?php
declare(strict_types=1);

// Afficher les erreurs en dev
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php'; // e(), csrf_token(), validate_csrf()

// page par défaut
$page = $_GET['page'] ?? 'joueurs';

// --- Helper : vérifie existence d'une colonne dans la table stats
function column_exists(PDO $pdo, string $col): bool {
    $stmt = $pdo->prepare("
      SELECT COUNT(*) FROM information_schema.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stats' AND COLUMN_NAME = :col
    ");
    $stmt->execute([':col' => $col]);
    return (bool)$stmt->fetchColumn();
}

// renvoie premier nom de colonne existant parmi candidats
function first_existing_column(PDO $pdo, array $candidates): ?string {
    foreach ($candidates as $c) {
        if (column_exists($pdo, $c)) return $c;
    }
    return null;
}

// FR -> EN mapping candidates (on résout au runtime)
$stat_map_candidates = [
    'yards_passe'      => ['passing_yards','yards_passe','passing_yards'],
    'td_passe'         => ['passing_tds','td_passe','passing_tds'],
    'interceptions'    => ['interceptions','interceptions'],
    'yards_course'     => ['rushing_yards','yards_course','rushing_yards'],
    'td_course'        => ['rushing_tds','td_course','rushing_tds'],
    'receptions'       => ['receptions','receptions'],
    'yards_reception'  => ['receiving_yards','yards_reception','receiving_yards'],
    'td_reception'     => ['receiving_tds','td_reception','receiving_tds'],
    'plaquages'        => ['tackles','plaquages','tackles'],
    'sacks'            => ['sacks','sacks'],
    'interceptions_def'=> ['interceptions_def','interceptions_def'],
];

// construire une table de résolution statique pour la page (clé display => colonne réelle ou null)
$stat_column_map = [];
foreach ($stat_map_candidates as $display => $cands) {
    $stat_column_map[$display] = first_existing_column($pdo, $cands);
}

// --- Charger positions (table 'position' si existante, sinon fallback unique list depuis player.poste)
$positions = [];
try {
    $has_position_table = (bool)$pdo->query("SHOW TABLES LIKE 'position'")->fetchColumn();
    if ($has_position_table) {
        $positions = $pdo->query("SELECT id, code, libelle FROM position ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $rows = $pdo->query("SELECT DISTINCT poste FROM player WHERE poste IS NOT NULL AND poste <> '' ORDER BY poste")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($rows as $r) $positions[] = ['code' => $r];
    }
} catch (Throwable $e) {
    $positions = [];
}

// --- Charger teams groupées (conference -> division)
$teams_grouped = [];
$teams_flat = [];
try {
    $rows = $pdo->query("SELECT id_team, nom_team, conference, division FROM team ORDER BY conference, division, nom_team")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $t) {
        $conf = $t['conference'] ?? 'Autre';
        $div  = $t['division'] ?? 'Autre';
        $teams_grouped[$conf][$div][] = $t;
        $teams_flat[] = $t;
    }
} catch (Throwable $e) {
    $teams_grouped = []; $teams_flat = [];
}

// fallback e() si helpers absent
if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

// helper : rendu select des teams groupées
function render_team_select_grouped(array $teams_grouped, string $name='id_team', $selected=null, $required=true): string {
    $req = $required ? 'required' : '';
    $html = "<select name=\"".e($name)."\" $req>";
    $html .= "<option value=\"\">-- Sélectionner une équipe --</option>";
    foreach ($teams_grouped as $conf => $divs) {
        // label pour conference
        $html .= "<optgroup label=\"".e($conf)."\">";
        foreach ($divs as $div => $teams) {
            // show division prefix within option label
            foreach ($teams as $t) {
                $sel = ((string)$selected === (string)$t['id_team']) ? 'selected' : '';
                $label = ($div ? $div . ' - ' : '') . $t['nom_team'];
                $html .= "<option value=\"".e((string)$t['id_team'])."\" $sel>".e($label)."</option>";
            }
        }
        $html .= "</optgroup>";
    }
    $html .= "</select>";
    return $html;
}

// -------------------- HTML --------------------
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>NFL Stats Analyzer</title>
<link rel="stylesheet" href="css/style_page.css">
<style>
.container { max-width:1100px; margin:1.5em auto; padding:1em; background:#fff; border-radius:10px; }
.menu { display:flex; gap:0.6em; margin-bottom:1em; }
.menu a { text-decoration:none; padding:0.5em 0.8em; border-radius:8px; background:#e2e8f0; color:#0a2463; font-weight:600; }
.menu a.active { background:#ef4444; color:#fff; }
.table-responsive { overflow-x:auto; }
table { border-collapse:collapse; width:100%; min-width:480px; }
th, td { border:1px solid #e6eef8; padding:8px; text-align:left; }
th { background:#f8fafc; position:sticky; top:0; cursor:pointer; }
.filters { display:flex; gap:0.6em; flex-wrap:wrap; align-items:center; margin-bottom:0.8em; }
.card { background:#f8fafc; border:1px solid #e2e8f0; padding:1em; border-radius:10px; margin-bottom:1em; }
@media (max-width:800px) { .container { margin:0.6em; } table { min-width:360px; } }
</style>
</head>
<body>
<div class="container">
    <header><img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo" style="width:140px;"><h1>NFL STATS ANALYZER</h1></header>

    <nav class="menu" role="navigation">
        <?php foreach (['joueurs'=>'Joueurs','stats'=>'Statistiques','ranking'=>'Classement'] as $k=>$lab): ?>
            <a href="?page=<?= e($k) ?>" class="<?= ($page===$k)?'active':'' ?>"><?= e($lab) ?></a>
        <?php endforeach; ?>
    </nav>

    <main>
    <?php if ($page === 'joueurs'): ?>

        <section class="card"><h2>Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="display:flex;gap:0.6em;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>

                    <!-- SELECT poste uniquement -->
                    <select name="position_id" required>
                        <option value="">-- Poste --</option>
                        <?php foreach ($positions as $pos): ?>
                            <?php if (isset($pos['id'])): ?>
                                <option value="<?= e((string)$pos['id']) ?>"><?= e($pos['libelle'].' ('.$pos['code'].')') ?></option>
                            <?php else: ?>
                                <option value="<?= e((string)$pos['code']) ?>"><?= e($pos['code']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <input type="number" name="age" placeholder="Âge" min="16" max="60">
                    <input type="number" name="taille_cm" placeholder="Taille (cm)" min="140" max="230">
                    <input type="number" name="poids_kg" placeholder="Poids (kg)" min="50" max="200">
                    <input type="number" name="annee_debut" placeholder="Année début" min="1900" max="<?= date('Y') ?>">

                    <?= render_team_select_grouped($teams_grouped, 'id_team', null, true) ?>
                </div>
                <div style="margin-top:0.6em;"><button type="submit">Ajouter le joueur</button></div>
            </form>
        </section>

        <section>
            <h2>Liste des joueurs</h2>
            <div class="card">
                <form method="get" style="display:flex;gap:0.6em;align-items:center;flex-wrap:wrap;">
                    <input type="hidden" name="page" value="joueurs">
                    <input type="search" name="q" placeholder="Rechercher nom/prénom" value="<?= e((string)($_GET['q'] ?? '')) ?>">
                    <select name="team_filter">
                        <option value="">Toutes équipes</option>
                        <?php foreach ($teams_flat as $t): ?>
                            <option value="<?= e((string)$t['id_team']) ?>" <?= (isset($_GET['team_filter']) && $_GET['team_filter'] == $t['id_team']) ? 'selected' : '' ?>><?= e($t['nom_team']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="position_filter">
                        <option value="">Tous postes</option>
                        <?php foreach ($positions as $pos): $val = $pos['id'] ?? $pos['code']; ?>
                            <option value="<?= e((string)$val) ?>" <?= (isset($_GET['position_filter']) && (string)$_GET['position_filter'] === (string)$val) ? 'selected' : '' ?>><?= e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code']) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit">Filtrer</button>
                </form>

                <div style="margin-top:0.8em;">
                    <?php
                    // Construction sécurisée du WHERE et des paramètres (éviter paramètres répétés)
                    $where = []; $params = [];
                    if (!empty($_GET['q'])) {
                        // utiliser deux paramètres distincts : :q_nom et :q_prenom
                        $where[] = '(p.nom LIKE :q_nom OR p.prenom LIKE :q_prenom)';
                        $qval = '%'.$_GET['q'].'%';
                        $params[':q_nom'] = $qval;
                        $params[':q_prenom'] = $qval;
                    }
                    if (!empty($_GET['team_filter'])) {
                        $where[] = 'p.id_team = :team';
                        $params[':team'] = (int)$_GET['team_filter'];
                    }
                    if (!empty($_GET['position_filter'])) {
                        $pf = $_GET['position_filter'];
                        if (ctype_digit((string)$pf)) {
                            $posid = (int)$pf;
                            // tenter de récupérer le code correspondant dans position
                            try {
                                $stp = $pdo->prepare('SELECT code FROM position WHERE id = ?');
                                $stp->execute([$posid]);
                                $poscode = $stp->fetchColumn() ?: null;
                            } catch (Throwable $e) { $poscode = null; }

                            if ($poscode) {
                                // utiliser noms de paramètres distincts
                                $where[] = '(p.position_id = :posid_filter OR COALESCE(pos.code, p.poste) = :poscode_filter)';
                                $params[':posid_filter'] = $posid;
                                $params[':poscode_filter'] = $poscode;
                            } else {
                                $where[] = 'p.position_id = :posid_filter';
                                $params[':posid_filter'] = $posid;
                            }
                        } else {
                            $where[] = 'COALESCE(pos.code, p.poste) = :poscode_exact';
                            $params[':poscode_exact'] = (string)$pf;
                        }
                    }

                    $sql = "SELECT p.*, t.nom_team, pos.code AS pos_code, pos.libelle AS pos_lib
                            FROM player p
                            LEFT JOIN team t ON p.id_team = t.id_team
                            LEFT JOIN position pos ON p.position_id = pos.id";
                    if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
                    $sql .= ' ORDER BY p.nom LIMIT 500';

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($params);

                    echo '<div class="grid">';
                    while ($pl = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $experience = $pl['annee_debut'] ? (date('Y') - (int)$pl['annee_debut']) : 'N/A';
                        $position = $pl['pos_lib'] ?: ($pl['pos_code'] ?: ($pl['poste'] ?? '—'));
                        echo '<div class="card">';
                        echo '<h3>'.e($pl['prenom']).' '.e($pl['nom']).'</h3>';
                        echo '<p><strong>Poste:</strong> '.e($position).'</p>';
                        echo '<p><strong>Équipe:</strong> '.e($pl['nom_team'] ?? '—').'</p>';
                        echo '<p>Âge: '.e((string)$pl['age']).' ans</p>';
                        echo '<p>Taille: '.e((string)$pl['taille_cm']).' cm - Poids: '.e((string)$pl['poids_kg']).' kg</p>';
                        echo '<p>Expérience: '.e((string)$experience).' ans</p>';
                        echo '</div>';
                    }
                    echo '</div>';
                    ?>
                </div>
            </div>
        </section>

    <?php elseif ($page === 'stats'): ?>

        <section>
            <h2>Statistiques par groupe de postes</h2>

            <?php
            // groupes et colonnes d'affichage (display_keys)
            $groups = [
                'QB' => ['label'=>'Quarterbacks', 'codes'=>['QB'], 'cols'=> ['prenom','nom','nom_team','saison','yards_passe','td_passe','interceptions','yards_course','td_course']],
                'WR' => ['label'=>'Wide Receivers', 'codes'=>['WR'], 'cols'=> ['prenom','nom','nom_team','saison','receptions','yards_reception','td_reception']],
                'RB' => ['label'=>'Running Backs', 'codes'=>['RB'], 'cols'=> ['prenom','nom','nom_team','saison','yards_course','td_course','receptions']],
                'DB' => ['label'=>'Defensive Backs', 'codes'=>['CB','S'], 'cols'=> ['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']],
                'LB' => ['label'=>'Linebackers', 'codes'=>['LB'], 'cols'=> ['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']],
            ];

            foreach ($groups as $gcode => $ginfo) {
                echo "<h3>".e($ginfo['label'])."</h3>";

                // construire SELECT en mappant display keys vers colonnes DB existantes
                $select_parts = [];
                $display_cols = [];
                foreach ($ginfo['cols'] as $disp) {
                    if (in_array($disp, ['prenom','nom','nom_team','saison'])) {
                        if ($disp === 'prenom' || $disp === 'nom') { $select_parts[] = "p.".$disp." AS ".$disp; $display_cols[]=$disp; }
                        if ($disp === 'nom_team') { $select_parts[] = "t.nom_team AS nom_team"; $display_cols[]='nom_team'; }
                        if ($disp === 'saison') { $select_parts[] = "s.saison AS saison"; $display_cols[]='saison'; }
                        continue;
                    }
                    // stat -> check mapping
                    $dbcol = $stat_column_map[$disp] ?? null;
                    if ($dbcol) {
                        $select_parts[] = "s.`".str_replace("`","",$dbcol)."` AS `".$disp."`";
                    } else {
                        $select_parts[] = "NULL AS `".$disp."`";
                    }
                    $display_cols[] = $disp;
                }

                // placeholders pour IN(...)
                $phs = implode(',', array_fill(0, count($ginfo['codes']), '?'));
                $sql = "SELECT ".implode(',', $select_parts)." FROM stats s
                        JOIN player p ON p.id_player = s.id_player
                        LEFT JOIN team t ON t.id_team = p.id_team
                        LEFT JOIN position pos ON pos.id = p.position_id
                        WHERE COALESCE(pos.code, p.poste) IN ($phs)
                        ORDER BY s.saison DESC, p.nom";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($ginfo['codes']);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$rows) {
                    echo "<div class='card'><p>Aucune donnée disponible pour ce groupe.</p></div>";
                    continue;
                }

                // affichage
                echo "<div class='table-responsive'><table class='sortable'><thead><tr>";
                foreach ($display_cols as $dc) echo "<th>".e(ucfirst(str_replace('_',' ',$dc)))."</th>";
                echo "</tr></thead><tbody>";
                foreach ($rows as $r) {
                    echo "<tr>";
                    foreach ($display_cols as $dc) echo "<td>".e((string)($r[$dc] ?? ''))."</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            }
            ?>

        </section>

    <?php elseif ($page === 'ranking'): ?>

        <section>
            <h2>Classements (TD / Plaquages)</h2>

            <div class="card">
                <form method="get" class="filters">
                    <input type="hidden" name="page" value="ranking">
                    <label>Saison
                        <select name="saison">
                            <?php
                            $saisons = $pdo->query("SELECT DISTINCT saison FROM stats ORDER BY saison DESC")->fetchAll(PDO::FETCH_COLUMN);
                            foreach ($saisons as $s) {
                                $sel = (isset($_GET['saison']) && $_GET['saison']==$s) ? 'selected' : '';
                                echo "<option value='".e($s)."' $sel>".e($s)."</option>";
                            }
                            ?>
                        </select>
                    </label>

                    <label>Conférence
                        <select name="conference">
                            <option value="">Toutes</option>
                            <?php foreach (array_keys($teams_grouped) as $conf): $sel=(isset($_GET['conference'])&&$_GET['conference']==$conf)?'selected':''; ?>
                                <option value="<?= e($conf) ?>" <?= $sel ?>><?= e($conf) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Poste
                        <select name="position_filter">
                            <option value="">Tous</option>
                            <?php foreach ($positions as $pos): $val = $pos['id'] ?? $pos['code']; ?>
                                <option value="<?= e((string)$val) ?>" <?= (isset($_GET['position_filter']) && (string)$_GET['position_filter'] === (string)$val) ? 'selected' : '' ?>><?= e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Métrique
                        <select name="metric">
                            <option value="td" <?= (($_GET['metric'] ?? '')==='td') ? 'selected' : '' ?>>Total TD</option>
                            <option value="plaquages" <?= (($_GET['metric'] ?? '')==='plaquages') ? 'selected' : '' ?>>Plaquages</option>
                        </select>
                    </label>

                    <button type="submit">Afficher</button>
                </form>
            </div>

            <?php
            // déterminer colonnes disponibles (TD parts & tackles/yards)
            $col_passing_tds = first_existing_column($pdo, ['passing_tds','td_passe','passing_tds']);
            $col_rushing_tds = first_existing_column($pdo, ['rushing_tds','td_course','rushing_tds']);
            $col_receiving_tds = first_existing_column($pdo, ['receiving_tds','td_reception','receiving_tds']);

            $col_tackles = first_existing_column($pdo, ['tackles','plaquages','tackles']);

            $col_passing_yards = first_existing_column($pdo, ['passing_yards','yards_passe','passing_yards']);
            $col_rushing_yards = first_existing_column($pdo, ['rushing_yards','yards_course','rushing_yards']);
            $col_receiving_yards = first_existing_column($pdo, ['receiving_yards','yards_reception','receiving_yards']);

            $total_td_parts = [];
            if ($col_passing_tds) $total_td_parts[] = "COALESCE(s.`$col_passing_tds`,0)";
            if ($col_rushing_tds) $total_td_parts[] = "COALESCE(s.`$col_rushing_tds`,0)";
            if ($col_receiving_tds) $total_td_parts[] = "COALESCE(s.`$col_receiving_tds`,0)";
            $total_td_expr = $total_td_parts ? '('.implode(' + ', $total_td_parts).')' : '0';

            $total_plaquages_expr = $col_tackles ? "COALESCE(s.`$col_tackles`,0)" : '0';

            $yards_parts = [];
            if ($col_passing_yards) $yards_parts[] = "COALESCE(s.`$col_passing_yards`,0)";
            if ($col_rushing_yards) $yards_parts[] = "COALESCE(s.`$col_rushing_yards`,0)";
            if ($col_receiving_yards) $yards_parts[] = "COALESCE(s.`$col_receiving_yards`,0)";
            $total_yards_expr = $yards_parts ? '('.implode(' + ', $yards_parts).')' : '0';

            // filtres
            $saison = $_GET['saison'] ?? date('Y');
            $conference = $_GET['conference'] ?? '';
            $position_filter = $_GET['position_filter'] ?? '';
            $metric = $_GET['metric'] ?? 'td';

            // Construction SQL
            $sql = "SELECT p.prenom, p.nom, COALESCE(pos.code,p.poste) as pos_code, t.nom_team, t.conference,
                           $total_td_expr AS total_td,
                           $total_plaquages_expr AS total_plaquages,
                           $total_yards_expr AS total_yards
                    FROM player p
                    LEFT JOIN team t ON t.id_team = p.id_team
                    LEFT JOIN position pos ON pos.id = p.position_id
                    LEFT JOIN stats s ON s.id_player = p.id_player AND s.saison = :saison
                    WHERE 1=1";
            $params = [':saison' => $saison];
            if ($conference) { $sql .= " AND t.conference = :conf"; $params[':conf'] = $conference; }
            if ($position_filter) {
                if (ctype_digit((string)$position_filter)) {
                    $posid = (int)$position_filter;
                    // try to get pos code
                    try {
                        $ps = $pdo->prepare('SELECT code FROM position WHERE id = ?');
                        $ps->execute([$posid]);
                        $pcode = $ps->fetchColumn() ?: null;
                    } catch (Throwable $e) { $pcode = null; }
                    if ($pcode) {
                        $sql .= " AND (p.position_id = :pf_posid OR COALESCE(pos.code,p.poste) = :pf_poscode)";
                        $params[':pf_posid'] = $posid;
                        $params[':pf_poscode'] = $pcode;
                    } else {
                        $sql .= " AND p.position_id = :pf_posid";
                        $params[':pf_posid'] = $posid;
                    }
                } else {
                    $sql .= " AND COALESCE(pos.code,p.poste) = :pf_poscode";
                    $params[':pf_poscode'] = (string)$position_filter;
                }
            }

            if ($metric === 'td') {
                $sql .= " ORDER BY total_td DESC, total_yards DESC";
            } else {
                $sql .= " ORDER BY total_plaquages DESC, total_yards DESC";
            }
            $sql .= " LIMIT 500";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                echo "<div class='card'><p>Aucun résultat pour ces filtres.</p></div>";
            } else {
                if (!$conference) {
                    $grouped = [];
                    foreach ($rows as $r) $grouped[$r['conference'] ?? 'Autre'][] = $r;
                    foreach ($grouped as $conf => $groupRows) {
                        echo "<h3>".e($conf)."</h3>";
                        echo "<div class='table-responsive'><table class='sortable'><thead><tr><th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>Total TD</th><th>Plaquages</th><th>Total Yards</th></tr></thead><tbody>";
                        $i = 1;
                        foreach ($groupRows as $r) {
                            echo "<tr><td>".e((string)$i)."</td><td>".e($r['prenom'].' '.$r['nom'])."</td><td>".e($r['pos_code'])."</td><td>".e($r['nom_team'] ?? '—')."</td><td>".e((string)$r['total_td'])."</td><td>".e((string)$r['total_plaquages'])."</td><td>".e((string)$r['total_yards'])."</td></tr>";
                            $i++;
                        }
                        echo "</tbody></table></div>";
                    }
                } else {
                    echo "<div class='table-responsive'><table class='sortable'><thead><tr><th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>Total TD</th><th>Plaquages</th><th>Total Yards</th></tr></thead><tbody>";
                    $i = 1;
                    foreach ($rows as $r) {
                        echo "<tr><td>".e((string)$i)."</td><td>".e($r['prenom'].' '.$r['nom'])."</td><td>".e($r['pos_code'])."</td><td>".e($r['nom_team'] ?? '—')."</td><td>".e((string)$r['total_td'])."</td><td>".e((string)$r['total_plaquages'])."</td><td>".e((string)$r['total_yards'])."</td></tr>";
                        $i++;
                    }
                    echo "</tbody></table></div>";
                }
            }
            ?>

        </section>

    <?php else: ?>
        <p>Page inconnue.</p>
    <?php endif; ?>
    </main>
</div>

<footer style="text-align:center;margin-top:1em;"><small>&copy; 2025 NFL Stats Analyzer - Projet académique</small></footer>

<script>
// tri simple côté client pour toutes les tables .sortable
function sortTable(table, col) {
    const tbody = table.tBodies[0];
    const rows = Array.from(tbody.rows);
    const asc = table.asc = !table.asc;
    rows.sort((a,b) => {
        let A = a.cells[col].innerText.trim();
        let B = b.cells[col].innerText.trim();
        if (!isNaN(A) && !isNaN(B)) { A = Number(A); B = Number(B); }
        if (A === B) return 0;
        return (A > B ? 1 : -1) * (asc ? 1 : -1);
    });
    rows.forEach(r => tbody.appendChild(r));
}
document.querySelectorAll('table.sortable').forEach(t => {
    t.querySelectorAll('th').forEach((th,i) => th.addEventListener('click', ()=> sortTable(t,i)));
});
</script>
</body>
</html>

