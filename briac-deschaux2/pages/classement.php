<?php
// pages/classement.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$saison = date('Y');
$filtre_poste = $_GET['poste'] ?? '';
$filtre_team = $_GET['team'] ?? '';
?>
<div class="card">
    <h2>Filtres Classement</h2>
    <form method="get">
        <input type="hidden" name="page" value="classement">

        <label>Poste :</label>
        <select name="poste">
            <option value="">Tous</option>
            <?php
            $positions = $pdo->query("SELECT code, libelle FROM `position` ORDER BY libelle")->fetchAll();
            foreach ($positions as $p) {
                $sel = ($filtre_poste === $p['code']) ? "selected" : "";
                echo "<option value='" . htmlspecialchars($p['code']) . "' $sel>" . htmlspecialchars($p['libelle']) . " (" . htmlspecialchars($p['code']) . ")</option>";
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
                    echo "<optgroup label='" . htmlspecialchars($current_conf) . "'>";
                }
                $sel = ($filtre_team == $t['id_team']) ? "selected" : "";
                echo "<option value='" . (int)$t['id_team'] . "' $sel>" . htmlspecialchars($t['nom_team']) . "</option>";
            }
            if ($current_conf !== "") echo "</optgroup>";
            ?>
        </select>

        <button type="submit">Filtrer</button>
    </form>
</div>

<?php
// Classement TDs par conférence
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
            echo "<h3>" . htmlspecialchars($conf) . "</h3><ol>";
        }
        echo "<li>" . htmlspecialchars($row['prenom'] . ' ' . $row['nom'] . " ({$row['poste']})") . " - " . (int)$row['total_tds'] . " TDs</li>";
    }
    echo '</ol>';
}

// Classement plaquages par division
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
            echo "<h3>" . htmlspecialchars($div) . "</h3><ol>";
        }
        echo "<li>" . htmlspecialchars($row['prenom'] . ' ' . $row['nom'] . " ({$row['poste']})") . " - " . (int)$row['total_plaquages'] . " plaquages</li>";
    }
    echo '</ol>';
}
?>
