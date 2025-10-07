<?php
declare(strict_types=1);

// DEV: afficher erreurs
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/config/database_connexion.php';
require_once __DIR__ . '/services/helpers.php'; // e(), csrf_token(), validate_csrf(), app_log()

$page = $_GET['page'] ?? 'joueurs';

// helper safe echo
if (!function_exists('e')) {
    function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
}

// helper: fetch positions (position table or fallback)
function load_positions(PDO $pdo): array {
    try {
        if ($pdo->query("SHOW TABLES LIKE 'position'")->fetchColumn()) {
            return $pdo->query("SELECT id, code, libelle FROM position ORDER BY libelle")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $rows = $pdo->query("SELECT DISTINCT poste FROM player WHERE poste IS NOT NULL AND poste <> '' ORDER BY poste")->fetchAll(PDO::FETCH_COLUMN);
            $res = [];
            foreach ($rows as $r) $res[] = ['code'=>$r];
            return $res;
        }
    } catch (Throwable $e) {
        return [];
    }
}

// helper: load teams grouped by conference/division (for SELECT grouping)
function load_teams_grouped(PDO $pdo): array {
    $out = [];
    try {
        // sort by conference, division, nom_team
        $rows = $pdo->query("SELECT id_team, nom_team, logo_url, conference, division FROM team ORDER BY conference, division, nom_team")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $t) {
            $conf = $t['conference'] ?? 'Autre';
            $div = $t['division'] ?? 'Autre';
            $out[$conf][$div][] = $t;
        }
    } catch (Throwable $e) {
        // empty
    }
    return $out;
}

// render team select grouped by conference -> division (teams sorted in SQL already)
function render_team_select_grouped(array $teams_grouped, string $name='team', $selected=null, bool $includeEmpty=true): string {
    $html = "<select name=\"".e($name)."\">";
    if ($includeEmpty) $html .= "<option value=\"\">-- Toutes --</option>";
    foreach ($teams_grouped as $conf => $divs) {
        $html .= "<optgroup label=\"".e($conf)."\">";
        foreach ($divs as $div => $teams) {
            foreach ($teams as $t) {
                $sel = ((string)$selected === (string)$t['id_team']) ? 'selected' : '';
                $label = ($div ? "[$div] " : '') . $t['nom_team'];
                $html .= "<option value=\"".e($t['id_team'])."\" $sel>".e($label)."</option>";
            }
        }
        $html .= "</optgroup>";
    }
    $html .= "</select>";
    return $html;
}

$positions = load_positions($pdo);
$teams_grouped = load_teams_grouped($pdo);

// --- HTML output ---
?><!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>NFL Stats Analyzer</title>
<link rel="stylesheet" href="css/style_page.css">
<style>
/* local tweaks */
.header-logo { width: 280px; } /* slightly bigger header logo */
.team-logo { height: 40px; vertical-align: middle; margin-right: 8px; } /* x2 size (was 20px) */
.grid { display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:1em; }
.card { background:#f8fafc; border:1px solid #e2e8f0; padding:1em; border-radius:10px; }
.table-responsive { overflow-x:auto; }
table { border-collapse:collapse; width:100%; min-width:480px; }
th,td { border:1px solid #e6eef8; padding:8px; text-align:left; }
th { background:#f8fafc; position:sticky; top:0; cursor:pointer; }
.filters { display:flex; gap:0.6em; flex-wrap:wrap; margin-bottom:0.8em; align-items:center; }
</style>
</head>
<body>
<div class="container">
    <header class="header">
        <img src="https://logos-world.net/wp-content/uploads/2021/09/NFL-Logo.png" alt="NFL" class="header-logo">
        <h1>NFL STATS ANALYZER</h1>
    </header>

    <div class="menu" role="navigation">
        <a href="?page=joueurs" class="<?= $page==='joueurs'?'active':'' ?>">Joueurs</a>
        <a href="?page=stats" class="<?= $page==='stats'?'active':'' ?>">Statistiques</a>
        <a href="?page=classement" class="<?= $page==='classement'?'active':'' ?>">Classement</a>
    </div>

    <main>
<?php
// -------------------- PAGE JOUEURS --------------------
if ($page === 'joueurs'):
    // filters: q (name), position_filter, team_filter
    $q = trim((string)($_GET['q'] ?? ''));
    $position_filter = (string)($_GET['position_filter'] ?? '');
    $team_filter = (string)($_GET['team_filter'] ?? '');
    ?>
    <section class="card">
        <h2>Ajouter un joueur</h2>
        <form method="post" action="services/add_player.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <div style="display:flex; gap:0.6em; flex-wrap:wrap; align-items:center;">
                <input type="text" name="prenom" placeholder="Prénom" required>
                <input type="text" name="nom" placeholder="Nom" required>

                <select name="position_id" required>
                    <option value="">-- Poste --</option>
                    <?php foreach ($positions as $pos): ?>
                        <?php if (isset($pos['id'])): ?>
                            <option value="<?= e($pos['id']) ?>"><?= e($pos['libelle'].' ('.$pos['code'].')') ?></option>
                        <?php else: ?>
                            <option value="<?= e($pos['code']) ?>"><?= e($pos['code']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>

                <?= render_team_select_grouped($teams_grouped, 'id_team', null, true) ?>

                <input type="number" name="age" placeholder="Âge" min="16" max="60">
                <input type="number" name="taille_cm" placeholder="Taille (cm)" min="140" max="230">
                <input type="number" name="poids_kg" placeholder="Poids (kg)" min="50" max="200">
                <input type="number" name="annee_debut" placeholder="Année début" min="1900" max="<?= date('Y') ?>">
                <button type="submit">Ajouter le joueur</button>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>Rechercher / Filtrer les joueurs</h2>
        <form method="get" class="filters" aria-label="Filtres joueurs">
            <input type="hidden" name="page" value="joueurs">
            <input type="search" name="q" placeholder="Nom et/ou Prénom" value="<?= e($q) ?>">
            <select name="position_filter">
                <option value="">Tous postes</option>
                <?php foreach ($positions as $pos): $val = $pos['id'] ?? $pos['code']; ?>
                    <option value="<?= e((string)$val) ?>" <?= ($position_filter === (string)$val) ? 'selected' : '' ?>>
                        <?= e(isset($pos['libelle']) ? ($pos['libelle'].' ('.$pos['code'].')') : $pos['code']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?= render_team_select_grouped($teams_grouped, 'team_filter', $team_filter, true) ?>

            <button type="submit">Rechercher</button>
        </form>

        <?php
        // Build WHERE and params, support name searches:
        $where = [];
        $params = [];

        if ($q !== '') {
            // Split by whitespace — support "prenom nom", "nom prenom" and single token
            $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) >= 2) {
                // try both orders: prenom LIKE first AND nom LIKE second OR prenom LIKE second AND nom LIKE first
                $where[] = '((p.prenom LIKE :p_first AND p.nom LIKE :p_second) OR (p.prenom LIKE :p_second AND p.nom LIKE :p_first) OR CONCAT(p.prenom, " ", p.nom) LIKE :qfull OR CONCAT(p.nom, " ", p.prenom) LIKE :qfull)';
                $params[':p_first'] = '%'.$parts[0].'%';
                $params[':p_second'] = '%'.$parts[1].'%';
                $params[':qfull'] = '%'.$q.'%';
            } else {
                $where[] = '(p.prenom LIKE :q_single OR p.nom LIKE :q_single OR CONCAT(p.prenom, " ", p.nom) LIKE :q_single OR CONCAT(p.nom, " ", p.prenom) LIKE :q_single)';
                $params[':q_single'] = '%'.$q.'%';
            }
        }

        if ($position_filter !== '') {
            if (ctype_digit((string)$position_filter)) {
                $where[] = '(p.position_id = :posid OR COALESCE(pos.code, p.poste) = :poscode)';
                $params[':posid'] = (int)$position_filter;
                // attempt to fetch code to compare if needed
                try {
                    $st = $pdo->prepare('SELECT code FROM position WHERE id = ?');
                    $st->execute([(int)$position_filter]);
                    $codeFound = $st->fetchColumn();
                    $params[':poscode'] = $codeFound ?: (string)$position_filter;
                } catch (Throwable $e) { $params[':poscode'] = (string)$position_filter; }
            } else {
                $where[] = 'COALESCE(pos.code, p.poste) = :poscode';
                $params[':poscode'] = (string)$position_filter;
            }
        }

        if ($team_filter !== '') {
            $where[] = 'p.id_team = :team';
            $params[':team'] = (int)$team_filter;
        }

        $sql = "SELECT p.*, t.nom_team, t.logo_url, COALESCE(pos.code, p.poste) AS pos_code, pos.libelle AS pos_lib
                FROM player p
                LEFT JOIN team t ON p.id_team = t.id_team
                LEFT JOIN position pos ON p.position_id = pos.id";
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY p.nom LIMIT 1000';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo '<div class="grid">';
        while ($pl = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $experience = $pl['annee_debut'] ? (date('Y') - (int)$pl['annee_debut']) : 'N/A';
            $positionLabel = $pl['pos_lib'] ?: ($pl['pos_code'] ?: ($pl['poste'] ?? '—'));
            echo '<div class="card">';
            echo '<h3>'.e($pl['prenom']).' '.e($pl['nom']).'</h3>';
            echo '<p><strong>Poste:</strong> '.e($positionLabel).'</p>';
            echo '<p><strong>Équipe:</strong> ';
            if (!empty($pl['logo_url'])) echo '<img src="'.e($pl['logo_url']).'" class="team-logo" alt="">';
            echo e($pl['nom_team'] ?? '—').'</p>';
            echo '<p>Âge: '.e((string)$pl['age']).' ans</p>';
            echo '<p>Taille: '.e((string)$pl['taille_cm']).' cm - Poids: '.e((string)$pl['poids_kg']).' kg</p>';
            echo '<p>Expérience: '.e((string)$experience).' ans</p>';
            echo '</div>';
        }
        echo '</div>';
        ?>
    </section>

<?php
// -------------------- PAGE STATISTIQUES --------------------
elseif ($page === 'stats'):
    $q = trim((string)($_GET['q'] ?? ''));
    $position_filter = (string)($_GET['position_filter'] ?? '');
    $team_filter = (string)($_GET['team_filter'] ?? '');
    $saison_filter = (int)($_GET['saison'] ?? date('Y'));
    ?>
    <section class="card">
        <h2>Ajouter des statistiques (Saison <?= e($saison_filter) ?>)</h2>
        <form method="post" action="services/add_stats.php">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <select name="id_player" required>
                <option value="">Sélectionner un joueur</option>
                <?php
                // list players ordered by nom
                $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($players as $p) {
                    echo "<option value=\"".e($p['id_player'])."\">".e($p['prenom'].' '.$p['nom'])."</option>";
                }
                ?>
            </select>

            <input type="number" name="passing_yards" placeholder="passing_yards" min="0">
            <input type="number" name="passing_tds" placeholder="passing_tds" min="0">
            <input type="number" name="interceptions" placeholder="interceptions" min="0">
            <input type="number" name="rushing_yards" placeholder="rushing_yards" min="0">
            <input type="number" name="rushing_tds" placeholder="rushing_tds" min="0">
            <input type="number" name="receptions" placeholder="receptions" min="0">
            <input type="number" name="receiving_yards" placeholder="receiving_yards" min="0">
            <input type="number" name="receiving_tds" placeholder="receiving_tds" min="0">
            <input type="number" name="tackles" placeholder="tackles" min="0">
            <input type="number" step="0.1" name="sacks" placeholder="sacks" min="0">
            <input type="number" name="interceptions_def" placeholder="interceptions_def" min="0">
            <button type="submit">Ajouter les stats</button>
        </form>
    </section>

    <section class="card">
        <h2>Recherche / Filtre (Statistiques)</h2>
        <form method="get" class="filters" aria-label="Filtres statistiques">
            <input type="hidden" name="page" value="stats">
            <input type="search" name="q" placeholder="Nom et/ou Prénom" value="<?= e($q) ?>">
            <select name="position_filter">
                <option value="">Tous postes</option>
                <?php foreach ($positions as $pos): $val = $pos['id'] ?? $pos['code']; ?>
                    <option value="<?= e((string)$val) ?>" <?= ($position_filter === (string)$val) ? 'selected' : '' ?>>
                        <?= e(isset($pos['libelle'])?($pos['libelle'].' ('.$pos['code'].')'):$pos['code']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <?= render_team_select_grouped($teams_grouped, 'team_filter', $team_filter, true) ?>

            <label>Saison</label>
            <select name="saison">
                <?php
                $saisons = $pdo->query("SELECT DISTINCT saison FROM stats ORDER BY saison DESC")->fetchAll(PDO::FETCH_COLUMN);
                if (!$saisons) $saisons = [date('Y')];
                foreach ($saisons as $s) {
                    $sel = ((string)$s === (string)$saison_filter) ? 'selected' : '';
                    echo "<option value=\"".e($s)."\" $sel>".e($s)."</option>";
                }
                ?>
            </select>

            <button type="submit">Filtrer</button>
        </form>

        <?php
        // Build query
        $where = ['s.saison = :saison'];
        $params = [':saison' => $saison_filter];

        if ($q !== '') {
            $parts = preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY);
            if (count($parts) >= 2) {
                $where[] = '((p.prenom LIKE :p_first AND p.nom LIKE :p_second) OR (p.prenom LIKE :p_second AND p.nom LIKE :p_first) OR CONCAT(p.prenom," ",p.nom) LIKE :qfull)';
                $params[':p_first'] = '%'.$parts[0].'%';
                $params[':p_second'] = '%'.$parts[1].'%';
                $params[':qfull'] = '%'.$q.'%';
            } else {
                $where[] = '(p.prenom LIKE :q OR p.nom LIKE :q OR CONCAT(p.prenom," ",p.nom) LIKE :q)';
                $params[':q'] = '%'.$q.'%';
            }
        }

        if ($position_filter !== '') {
            if (ctype_digit((string)$position_filter)) {
                $where[] = '(p.position_id = :posid OR COALESCE(pos.code,p.poste) = :poscode)';
                $params[':posid'] = (int)$position_filter;
                try {
                    $st = $pdo->prepare('SELECT code FROM position WHERE id = ?');
                    $st->execute([(int)$position_filter]);
                    $codeFound = $st->fetchColumn();
                    $params[':poscode'] = $codeFound ?: (string)$position_filter;
                } catch (Throwable $e) { $params[':poscode'] = (string)$position_filter; }
            } else {
                $where[] = 'COALESCE(pos.code,p.poste) = :poscode';
                $params[':poscode'] = (string)$position_filter;
            }
        }

        if ($team_filter !== '') {
            $where[] = 'p.id_team = :team';
            $params[':team'] = (int)$team_filter;
        }

        $sql = "SELECT s.*, p.prenom, p.nom, p.poste, t.nom_team, t.logo_url
                FROM stats s
                JOIN player p ON p.id_player = s.id_player
                LEFT JOIN team t ON t.id_team = p.id_team
                LEFT JOIN position pos ON pos.id = p.position_id
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.nom, s.saison DESC
                LIMIT 1000";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            echo "<p>Aucune statistique trouvée pour ces filtres.</p>";
        } else {
            echo "<div class=\"table-responsive\"><table class=\"sortable\"><thead><tr>
                    <th>Joueur</th><th>Équipe</th><th>Saison</th>
                    <th>passing_yards</th><th>passing_tds</th>
                    <th>rushing_yards</th><th>rushing_tds</th>
                    <th>receptions</th><th>receiving_yards</th><th>receiving_tds</th>
                    <th>tackles</th><th>sacks</th><th>interceptions_def</th>
                </tr></thead><tbody>";
            foreach ($rows as $r) {
                echo "<tr>";
                echo "<td>".e($r['prenom'].' '.$r['nom'])."</td>";
                echo "<td>";
                if (!empty($r['logo_url'])) echo '<img src="'.e($r['logo_url']).'" class="team-logo" alt="">';
                echo e($r['nom_team'] ?? '—')."</td>";
                echo "<td>".e($r['saison'])."</td>";
                echo "<td>".e((string)($r['passing_yards'] ?? ''))."</td>";
                echo "<td>".e((string)($r['passing_tds'] ?? ''))."</td>";
                echo "<td>".e((string)($r['rushing_yards'] ?? ''))."</td>";
                echo "<td>".e((string)($r['rushing_tds'] ?? ''))."</td>";
                echo "<td>".e((string)($r['receptions'] ?? ''))."</td>";
                echo "<td>".e((string)($r['receiving_yards'] ?? ''))."</td>";
                echo "<td>".e((string)($r['receiving_tds'] ?? ''))."</td>";
                echo "<td>".e((string)($r['tackles'] ?? ''))."</td>";
                echo "<td>".e((string)($r['sacks'] ?? ''))."</td>";
                echo "<td>".e((string)($r['interceptions_def'] ?? ''))."</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        }
        ?>
    </section>

<?php
// -------------------- PAGE CLASSEMENT --------------------
elseif ($page === 'classement'):
    // filters: saison, poste, team, conference, metric (td | tackles)
    $saison = (int)($_GET['saison'] ?? date('Y'));
    $filtre_poste = (string)($_GET['poste'] ?? '');
    $filtre_team = (string)($_GET['team'] ?? '');
    $filtre_conf = (string)($_GET['conference'] ?? '');
    $metric = (string)($_GET['metric'] ?? 'td'); // default td

    // render filter form
    ?>
    <section class="card">
        <h2>Filtres Classement</h2>
        <form method="get" class="filters" aria-label="Filtres classement">
            <input type="hidden" name="page" value="classement">
            <label>Saison</label>
            <select name="saison">
                <?php
                $saisons = $pdo->query("SELECT DISTINCT saison FROM stats ORDER BY saison DESC")->fetchAll(PDO::FETCH_COLUMN);
                if (!$saisons) $saisons = [date('Y')];
                foreach ($saisons as $s) {
                    $sel = ((string)$s === (string)$saison) ? 'selected' : '';
                    echo "<option value=\"".e($s)."\" $sel>".e($s)."</option>";
                }
                ?>
            </select>

            <label>Conférence</label>
            <select name="conference">
                <option value="">Toutes</option>
                <?php foreach ($teams_grouped as $confName => $divs): $sel = ($filtre_conf === $confName) ? 'selected' : ''; ?>
                    <option value="<?= e($confName) ?>" <?= $sel ?>><?= e($confName) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Poste</label>
            <select name="poste">
                <option value="">Tous</option>
                <?php foreach ($positions as $p): $val = $p['id'] ?? $p['code']; $sel = ($filtre_poste === (string)$val) ? 'selected' : ''; ?>
                    <option value="<?= e((string)$val) ?>" <?= $sel ?>><?= e($p['libelle'] ?? $p['code']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Équipe</label>
            <?= render_team_select_grouped($teams_grouped, 'team', $filtre_team, true) ?>

            <label>Métrique</label>
            <select name="metric">
                <option value="td" <?= $metric==='td' ? 'selected' : '' ?>>Total TDs</option>
                <option value="tackles" <?= $metric==='tackles' ? 'selected' : '' ?>>Plaquages</option>
            </select>

            <button type="submit">Appliquer</button>
        </form>
    </section>

    <section class="card">
        <h2>Classement (Saison <?= e($saison) ?>) - <?= $metric === 'td' ? 'Total TDs' : 'Plaquages' ?></h2>

        <?php
        // build base query (we group by player)
        // compute total_tds and total_tackles
        $sql = "SELECT p.id_player, p.prenom, p.nom, COALESCE(pos.code,p.poste) AS pos_code, t.nom_team, t.logo_url, t.conference, t.division,
                       COALESCE(SUM(s.passing_tds),0) AS sum_passing_tds,
                       COALESCE(SUM(s.rushing_tds),0) AS sum_rushing_tds,
                       COALESCE(SUM(s.receiving_tds),0) AS sum_receiving_tds,
                       COALESCE(SUM(s.tackles),0) AS sum_tackles
                FROM player p
                LEFT JOIN team t ON p.id_team = t.id_team
                LEFT JOIN position pos ON pos.id = p.position_id
                LEFT JOIN stats s ON s.id_player = p.id_player AND s.saison = :saison
                WHERE 1=1";

        $params = [':saison' => $saison];

        if ($filtre_conf !== '') { $sql .= " AND t.conference = :conf"; $params[':conf'] = $filtre_conf; }
        if ($filtre_team !== '') { $sql .= " AND p.id_team = :team"; $params[':team'] = (int)$filtre_team; }
        if ($filtre_poste !== '') {
            if (ctype_digit((string)$filtre_poste)) {
                // try match by id or code
                $sql .= " AND (p.position_id = :posid OR COALESCE(pos.code,p.poste) = :poscode)";
                $params[':posid'] = (int)$filtre_poste;
                try {
                    $ps = $pdo->prepare('SELECT code FROM position WHERE id = ?');
                    $ps->execute([(int)$filtre_poste]);
                    $pcode = $ps->fetchColumn() ?: (string)$filtre_poste;
                } catch (Throwable $e) { $pcode = (string)$filtre_poste; }
                $params[':poscode'] = $pcode;
            } else {
                $sql .= " AND COALESCE(pos.code,p.poste) = :poscode";
                $params[':poscode'] = $filtre_poste;
            }
        }

        $sql .= " GROUP BY p.id_player, p.prenom, p.nom, pos_code, t.nom_team, t.logo_url, t.conference, t.division";

        if ($metric === 'td') {
            // compute total_tds
            $sql .= " ORDER BY (COALESCE(SUM(s.passing_tds),0) + COALESCE(SUM(s.rushing_tds),0) + COALESCE(SUM(s.receiving_tds),0)) DESC";
        } else {
            $sql .= " ORDER BY COALESCE(SUM(s.tackles),0) DESC";
        }

        $sql .= " LIMIT 500";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            echo "<p>Aucun résultat pour ces filtres.</p>";
        } else {
            // Group by conference/division display
            if ($filtre_conf === '') {
                // group by conference (then division optionally)
                $grouped = [];
                foreach ($rows as $r) {
                    $conf = $r['conference'] ?? 'Autre';
                    $div = $r['division'] ?? 'Autre';
                    $grouped[$conf][$div][] = $r;
                }
                foreach ($grouped as $confName => $divs) {
                    echo "<h3>".e($confName)."</h3>";
                    foreach ($divs as $divName => $playersInDiv) {
                        echo "<h4>".e($divName)."</h4>";
                        echo "<div class='table-responsive'><table class='sortable'><thead><tr><th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>TDs</th><th>Plaquages</th></tr></thead><tbody>";
                        $i = 1;
                        foreach ($playersInDiv as $pr) {
                            $total_tds = ((int)$pr['sum_passing_tds'] + (int)$pr['sum_rushing_tds'] + (int)$pr['sum_receiving_tds']);
                            $total_tackles = (int)$pr['sum_tackles'];
                            // display only non-zero values
                            $td_display = $total_tds > 0 ? e((string)$total_tds) : '';
                            $tackles_display = $total_tackles > 0 ? e((string)$total_tackles) : '';
                            echo "<tr>";
                            echo "<td>".e((string)$i)."</td>";
                            echo "<td>".e($pr['prenom'].' '.$pr['nom'])."</td>";
                            echo "<td>".e($pr['pos_code'])."</td>";
                            echo "<td>";
                            if (!empty($pr['logo_url'])) echo '<img src="'.e($pr['logo_url']).'" class="team-logo" alt="">';
                            echo e($pr['nom_team'] ?? '—')."</td>";
                            echo "<td>".$td_display."</td>";
                            echo "<td>".$tackles_display."</td>";
                            echo "</tr>";
                            $i++;
                        }
                        echo "</tbody></table></div>";
                    }
                }
            } else {
                // filtered to a conference: just one table
                echo "<div class='table-responsive'><table class='sortable'><thead><tr><th>Rang</th><th>Joueur</th><th>Poste</th><th>Équipe</th><th>TDs</th><th>Plaquages</th></tr></thead><tbody>";
                $i = 1;
                foreach ($rows as $pr) {
                    $total_tds = ((int)$pr['sum_passing_tds'] + (int)$pr['sum_rushing_tds'] + (int)$pr['sum_receiving_tds']);
                    $total_tackles = (int)$pr['sum_tackles'];
                    $td_display = $total_tds > 0 ? e((string)$total_tds) : '';
                    $tackles_display = $total_tackles > 0 ? e((string)$total_tackles) : '';
                    echo "<tr><td>".e((string)$i)."</td><td>".e($pr['prenom'].' '.$pr['nom'])."</td><td>".e($pr['pos_code'])."</td><td>";
                    if (!empty($pr['logo_url'])) echo '<img src="'.e($pr['logo_url']).'" class="team-logo" alt="">';
                    echo e($pr['nom_team'] ?? '—')."</td><td>".$td_display."</td><td>".$tackles_display."</td></tr>";
                    $i++;
                }
                echo "</tbody></table></div>";
            }
        }
        ?>
    </section>

<?php
else:
    echo "<p>Page inconnue.</p>";
endif;
?>
    </main>
</div>

<footer>
    <p>&copy; 2025 NFL Stats Analyzer - Projet académique</p>
</footer>

<script>
// simple client-side sortable for .sortable tables
document.querySelectorAll('table.sortable').forEach(t => {
    t.querySelectorAll('th').forEach((th, idx) => {
        th.addEventListener('click', () => {
            const tbody = t.tBodies[0];
            const rows = Array.from(tbody.rows);
            const asc = th.asc = !th.asc;
            rows.sort((a,b) => {
                let A = a.cells[idx].innerText.trim();
                let B = b.cells[idx].innerText.trim();
                const nA = parseFloat(A.replace(/,/g,'')), nB = parseFloat(B.replace(/,/g,''));
                if (!isNaN(nA) && !isNaN(nB)) { A = nA; B = nB; }
                if (A === B) return 0;
                return (A > B ? 1 : -1) * (asc ? 1 : -1);
            });
            rows.forEach(r => tbody.appendChild(r));
        });
    });
});
</script>
</body>
</html>
