<?php
declare(strict_types=1);

// DEBUG: afficher les erreurs (en dev seulement)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php'; // e(), csrf_token(), validate_csrf() si présent

// page choisie
$page = $_GET['page'] ?? 'joueurs';

/**
 * Utilitaire : vérifie si une colonne existe dans la table 'stats'
 */
function column_exists(PDO $pdo, string $col): bool {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'stats' AND COLUMN_NAME = :col");
    $stmt->execute([':col' => $col]);
    return (bool) $stmt->fetchColumn();
}

/**
 * Donne le premier nom de colonne existant parmi un tableau d'options.
 * Retourne null s'il n'en existe aucun.
 */
function first_existing_column(PDO $pdo, array $candidates): ?string {
    foreach ($candidates as $c) {
        if (column_exists($pdo, $c)) return $c;
    }
    return null;
}

/**
 * Résolution des colonnes : map des noms d'affichage (fr) vers colonnes possibles (en)
 * On utilise first_existing_column() pour choisir ce qui existe en BDD.
 */
$stat_map_candidates = [
    // mapping: 'display_key' => [candidate_column_names_in_db_in_preference_order]
    'yards_passe'      => ['passing_yards', 'yards_passe', 'yards_passe'],   // QB
    'td_passe'         => ['passing_tds', 'td_passe', 'passing_tds'],
    'interceptions'    => ['interceptions', 'interceptions'],               // pour QB (INT thrown)
    'yards_course'     => ['rushing_yards', 'yards_course'],
    'td_course'        => ['rushing_tds', 'td_course'],
    'receptions'       => ['receptions', 'receptions'],
    'yards_reception'  => ['receiving_yards', 'yards_reception', 'receiving_yards'],
    'td_reception'     => ['receiving_tds', 'td_reception', 'receiving_tds'],
    'plaquages'        => ['tackles', 'plaquages', 'tackles'],              // DB uses 'tackles'
    'sacks'            => ['sacks', 'sacks'],
    'interceptions_def'=> ['interceptions_def','interceptions_def'],
    // add others if needed (fg_reussis -> field_goals ...), keep generic fallback
];

/**
 * For convenience, build an associative map 'display_key' => actual_db_column_or_null
 */
$stat_column_map = [];
foreach ($stat_map_candidates as $display => $candidates) {
    $stat_column_map[$display] = first_existing_column($pdo, $candidates); // string|null
}

/**
 * Load positions: prefer table 'position' if exists, else distinct p.poste.
 * We'll return array of ['id'=>..., 'code'=>..., 'libelle'=>...] OR ['code'=> 'QB'] fallback
 */
$positions = [];
try {
    $has_position_table = (bool)$pdo->query("SHOW TABLES LIKE 'position'")->fetchColumn();
    if ($has_position_table) {
        $positions = $pdo->query("SELECT id, code, libelle FROM position ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $rows = $pdo->query("SELECT DISTINCT poste FROM player WHERE poste IS NOT NULL AND poste != '' ORDER BY poste")->fetchAll(PDO::FETCH_COLUMN);
        foreach ($rows as $r) $positions[] = ['code' => $r];
    }
} catch (PDOException $e) {
    $positions = []; // fallback
}

/**
 * Load teams (grouped by conference->division)
 */
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
} catch (PDOException $e) {
    // silent fallback
}

/**
 * helper e() already provided by services/helpers.php (if not, define minimal)
 */
if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

/**
 * Helper: render team select grouped by conference/division
 */
function render_team_select_grouped(array $teams_grouped, string $name='id_team', $selected=null, $required=true): string {
    $req = $required ? 'required' : '';
    $html = "<select name=\"".e($name)."\" $req>";
    $html .= "<option value=\"\">-- Sélectionner une équipe --</option>";
    foreach ($teams_grouped as $conf => $divs) {
        $html .= "<optgroup label=\"".e($conf)."\">";
        foreach ($divs as $div => $teams) {
            // Many browsers don't support nested optgroup — we present division as prefix in option label
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

/**
 * Start HTML
 */
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>NFL Stats Analyzer</title>
<link rel="stylesheet" href="css/style_page.css">
<style>
/* small responsive tweaks */
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
    <header class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="Logo NFL" style="width:160px;">
        <h1>NFL STATS ANALYZER</h1>
    </header>

    <!-- Navigation -->
    <nav class="menu" role="navigation" aria-label="Menu principal">
        <?php foreach (['joueurs'=>'Joueurs','stats'=>'Statistiques','ranking'=>'Classement'] as $k=>$lab): ?>
            <a href="?page=<?= e($k) ?>" class="<?= ($page===$k)?'active':'' ?>"><?= e($lab) ?></a>
        <?php endforeach; ?>
    </nav>

    <main>
    <?php if ($page === 'joueurs'): ?>

        <section class="card" aria-labelledby="ajout-joueur"><h2 id="ajout-joueur">Ajouter un joueur</h2>
            <form method="post" action="services/add_player.php">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div style="display:flex;gap:0.6em;flex-wrap:wrap;align-items:center;">
                    <input type="text" name="prenom" placeholder="Prénom" required>
                    <input type="text" name="nom" placeholder="Nom" required>

                    <!-- SELECT poste uniquement -->
                    <select name="position_id" required>
                        <option value="">-- Poste --</option>
                        <?php foreach ($positions as $pos): ?>
                            <?php if (isset($pos['id'])): /* position table */ ?>
                                <option value="<?= e((string)$pos['id']) ?>"><?= e($pos['libelle'] . ' (' . $pos['code'] . ')' ) ?></option>
                            <?php else: /* fallback list of codes */ ?>
                                <option value="<?= e($pos['code']) ?>"><?= e($pos['code']) ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>

                    <input type="number" name="age" placeholder="Âge" min="16" max="60">
                    <input type="number" name="taille_cm" placeholder="Taille (cm)" min="140" max="230">
                    <input type="number" name="poids_kg" placeholder="Poids (kg)" min="50" max="200">
                    <input type="number" name="annee_debut" placeholder="Année début" min="1900" max="<?= date('Y') ?>">

                    <!-- team select grouped -->
                    <?= render_team_select_grouped($teams_grouped, 'id_team', null, true) ?>
                </div>
                <div style="margin-top:0.6em;">
                    <button type="submit">Ajouter le joueur</button>
                </div>
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
                            <option value="<?= e((string)$val) ?>" <?= (isset($_GET['position_filter']) && $_GET['position_filter'] == $val) ? 'selected' : '' ?>><?= e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Filtrer</button>
                </form>

                <div style="margin-top:0.8em;">
                    <?php
                        // Build query with optional filters
                        $where = []; $params = [];
                        if (!empty($_GET['q'])) { $where[] = '(p.nom LIKE :q OR p.prenom LIKE :q)'; $params[':q'] = '%'.$_GET['q'].'%'; }
                        if (!empty($_GET['team_filter'])) { $where[] = 'p.id_team = :team'; $params[':team'] = (int)$_GET['team_filter']; }
                        if (!empty($_GET['position_filter'])) {
                            // try numeric id or code
                            if (ctype_digit((string)$_GET['position_filter'])) {
                                $where[] = '(p.position_id = :posid OR p.poste = :poscode)';
                                $params[':posid'] = (int)$_GET['position_filter'];
                                $params[':poscode'] = (string)$_GET['position_filter'];
                            } else {
                                $where[] = 'COALESCE(pos.code, p.poste) = :poscode';
                                $params[':poscode'] = (string)$_GET['position_filter'];
                            }
                        }
                        $sql = "SELECT p.*, t.nom_team, pos.code AS pos_code, pos.libelle AS pos_lib FROM player p LEFT JOIN team t ON p.id_team = t.id_team LEFT JOIN position pos ON p.position_id = pos.id";
                        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
                        $sql .= ' ORDER BY p.nom LIMIT 500';
                        $stmt = $pdo->prepare($sql);
                        $stmt->execute($params);
                    ?>
                    <div class="grid">
                    <?php while ($pl = $stmt->fetch(PDO::FETCH_ASSOC)): 
                        $experience = $pl['annee_debut'] ? (date('Y') - (int)$pl['annee_debut']) : 'N/A';
                        $position = $pl['pos_lib'] ?: ($pl['pos_code'] ?: $pl['poste'] ?: '—');
                    ?>
                        <div class="card">
                            <h3><?= e($pl['prenom'].' '.$pl['nom']) ?></h3>
                            <p><strong>Poste:</strong> <?= e($position) ?></p>
                            <p><strong>Équipe:</strong> <?= e($pl['nom_team'] ?? '—') ?></p>
                            <p>Âge: <?= e((string)$pl['age']) ?> ans</p>
                            <p>Taille: <?= e((string)$pl['taille_cm']) ?> cm - Poids: <?= e((string)$pl['poids_kg']) ?> kg</p>
                            <p>Expérience: <?= e((string)$experience) ?> ans</p>
                        </div>
                    <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </section>

    <?php elseif ($page === 'stats'): ?>

        <section>
            <h2>Statistiques par groupe de postes</h2>
            <div class="card"><p>Les colonnes affichées sont automatiquement mappées sur les noms réels de ta base de données.</p></div>

            <?php
            // Define groups and which DISPLAY columns we want (display_key) in FR.
            $groups = [
                'QB' => ['label'=>'Quarterbacks', 'codes'=>['QB'], 'cols'=> ['prenom','nom','nom_team','saison','yards_passe','td_passe','interceptions','yards_course','td_course']],
                'WR' => ['label'=>'Wide Receivers', 'codes'=>['WR'], 'cols'=> ['prenom','nom','nom_team','saison','receptions','yards_reception','td_reception']],
                'RB' => ['label'=>'Running Backs', 'codes'=>['RB'], 'cols'=> ['prenom','nom','nom_team','saison','yards_course','td_course','receptions']],
                'DB' => ['label'=>'Defensive Backs', 'codes'=>['CB','S'], 'cols'=> ['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']],
                'LB' => ['label'=>'Linebackers', 'codes'=>['LB'], 'cols'=> ['prenom','nom','nom_team','saison','plaquages','sacks','interceptions_def']],
            ];

            foreach ($groups as $gcode => $ginfo) {
                echo "<h3>".e($ginfo['label'])."</h3>";

                // figure out which DB columns exist for each display col and build SELECT list with aliases
                $select_parts = [];
                $display_cols = []; // list of display labels to show (FR key => label)
                foreach ($ginfo['cols'] as $display_key) {
                    if (in_array($display_key, ['prenom','nom','nom_team','saison'])) {
                        // source comes from player/team/stats.saison
                        if ($display_key === 'prenom' || $display_key === 'nom' ) {
                            // from player table
                            $select_parts[] = "p.".$display_key." AS ". $display_key;
                            $display_cols[] = $display_key;
                        } elseif ($display_key === 'nom_team') {
                            $select_parts[] = "t.nom_team AS nom_team";
                            $display_cols[] = 'nom_team';
                        } elseif ($display_key === 'saison') {
                            $select_parts[] = "s.saison AS saison";
                            $display_cols[] = 'saison';
                        }
                        continue;
                    }
                    // non-basic stat -> map to db column
                    $dbcol = $stat_column_map[$display_key] ?? null;
                    if ($dbcol) {
                        $select_parts[] = "s.`".str_replace("`","",$dbcol)."` AS `".$display_key."`";
                        $display_cols[] = $display_key;
                    } else {
                        // column missing: still show a blank column (NULL)
                        $select_parts[] = "NULL AS `".$display_key."`";
                        $display_cols[] = $display_key;
                    }
                }

                // Build SQL: select from stats join player/team, WHERE COALESCE(pos.code, p.poste) IN (codes)
                $placeholders = implode(',', array_fill(0, count($ginfo['codes']), '?'));
                $sql = "SELECT ".implode(',', $select_parts)." FROM stats s
                        JOIN player p ON p.id_player = s.id_player
                        LEFT JOIN team t ON t.id_team = p.id_team
                        LEFT JOIN position pos ON pos.id = p.position_id
                        WHERE COALESCE(pos.code, p.poste) IN ($placeholders)
                        ORDER BY s.saison DESC, p.nom";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($ginfo['codes']);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!$rows) {
                    echo "<div class='card'><p>Aucune donnée disponible pour ce groupe.</p></div>";
                    continue;
                }

                // render table
                echo "<div class='table-responsive'><table class='sortable'><thead><tr>";
                foreach ($display_cols as $dc) {
                    echo "<th>".e(ucfirst(str_replace('_',' ',$dc)))."</th>";
                }
                echo "</tr></thead><tbody>";
                foreach ($rows as $r) {
                    echo "<tr>";
                    foreach ($display_cols as $dc) {
                        $val = $r[$dc] ?? '';
                        echo "<td>".e((string)$val)."</td>";
                    }
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
                <form method="get" class="filters" aria-label="Filtres classement">
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
                            <?php foreach (array_keys($teams_grouped) as $conf): $sel=(isset($_GET['conference'])&&$_GET['conference']==$conf)?'selected':''; ?>
                                <option value="<?= e($conf) ?>" <?= $sel ?>><?= e($conf) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <label>Poste
                        <select name="position_filter">
                            <option value="">Tous</option>
                            <?php foreach ($positions as $pos): $val = $pos['id'] ?? $pos['code']; ?>
                                <option value="<?= e((string)$val) ?>"><?= e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code']) ?></option>
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
            // Determine columns existing for TD and tackles
            $col_passing_tds = first_existing_column($pdo, ['passing_tds','td_passe','td_passe']);
            $col_rushing_tds = first_existing_column($pdo, ['rushing_tds','td_course','td_course']);
            $col_receiving_tds = first_existing_column($pdo, ['receiving_tds','td_reception','receiving_tds']);

            $col_tackles = first_existing_column($pdo, ['tackles','plaquages','tackles']);

            // get filters
            $saison = $_GET['saison'] ?? date('Y');
            $conference = $_GET['conference'] ?? '';
            $position_filter = $_GET['position_filter'] ?? '';
            $metric = $_GET['metric'] ?? 'td';

            // Build SELECT with computed total_td and total_plaquages using existing db columns
            $select_extra = [];
            $total_td_expr_parts = [];
            if ($col_passing_tds) $total_td_expr_parts[] = "COALESCE(s.`$col_passing_tds`,0)";
            if ($col_rushing_tds) $total_td_expr_parts[] = "COALESCE(s.`$col_rushing_tds`,0)";
            if ($col_receiving_tds) $total_td_expr_parts[] = "COALESCE(s.`$col_receiving_tds`,0)";

            $total_td_expr = $total_td_expr_parts ? '(' . implode(' + ', $total_td_expr_parts) . ')' : '0';

            $total_plaquages_expr = $col_tackles ? "COALESCE(s.`$col_tackles`,0)" : '0';

            // total_yards for tie-breaker (try to find any yards columns)
            $col_passing_yards = first_existing_column($pdo, ['passing_yards','yards_passe','passing_yards']);
            $col_rushing_yards = first_existing_column($pdo, ['rushing_yards','yards_course','rushing_yards']);
            $col_receiving_yards = first_existing_column($pdo, ['receiving_yards','yards_reception','receiving_yards']);
            $yards_parts = [];
            if ($col_passing_yards) $yards_parts[] = "COALESCE(s.`$col_passing_yards`,0)";
            if ($col_rushing_yards) $yards_parts[] = "COALESCE(s.`$col_rushing_yards`,0)";
            if ($col_receiving_yards) $yards_parts[] = "COALESCE(s.`$col_receiving_yards`,0)";
            $total_yards_expr = $yards_parts ? '(' . implode(' + ', $yards_parts) . ')' : '0';

            // Build SQL
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
                // numeric id or code
                if (ctype_digit((string)$position_filter)) {
                    $sql .= " AND (pos.id = :posid OR p.poste = :poscode)";
                    $params[':posid'] = (int)$position_filter;
                    $params[':poscode'] = (string)$position_filter;
                } else {
                    $sql .= " AND COALESCE(pos.code,p.poste) = :poscode";
                    $params[':poscode'] = (string)$position_filter;
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
                // If no conference filter, group per conference
                if (!$conference) {
                    $grouped = [];
                    foreach ($rows as $r) { $conf = $r['conference'] ?? 'Autre'; $grouped[$conf][] = $r; }
                    foreach ($grouped as $conf => $grows) {
                        echo "<h3>".e($conf)."</h3>";
                        echo "<div class='table-responsive'><table class='sortable'><thead><tr>";
                        echo "<th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>Total TD</th><th>Plaquages</th><th>Total Yards</th>";
                        echo "</tr></thead><tbody>";
                        $i = 1;
                        foreach ($grows as $r) {
                            echo "<tr>";
                            echo "<td>".e((string)$i)."</td>";
                            echo "<td>".e($r['prenom'].' '.$r['nom'])."</td>";
                            echo "<td>".e($r['pos_code'])."</td>";
                            echo "<td>".e($r['nom_team'] ?? '—')."</td>";
                            echo "<td>".e((string)$r['total_td'])."</td>";
                            echo "<td>".e((string)$r['total_plaquages'])."</td>";
                            echo "<td>".e((string)$r['total_yards'])."</td>";
                            echo "</tr>";
                            $i++;
                        }
                        echo "</tbody></table></div>";
                    }
                } else {
                    echo "<div class='table-responsive'><table class='sortable'><thead><tr>";
                    echo "<th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>Total TD</th><th>Plaquages</th><th>Total Yards</th>";
                    echo "</tr></thead><tbody>";
                    $i = 1;
                    foreach ($rows as $r) {
                        echo "<tr>";
                        echo "<td>".e((string)$i)."</td>";
                        echo "<td>".e($r['prenom'].' '.$r['nom'])."</td>";
                        echo "<td>".e($r['pos_code'])."</td>";
                        echo "<td>".e($r['nom_team'] ?? '—')."</td>";
                        echo "<td>".e((string)$r['total_td'])."</td>";
                        echo "<td>".e((string)$r['total_plaquages'])."</td>";
                        echo "<td>".e((string)$r['total_yards'])."</td>";
                        echo "</tr>";
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
// Small client-side sorter for tables
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
