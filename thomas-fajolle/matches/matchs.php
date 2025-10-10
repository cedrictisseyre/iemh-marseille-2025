<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../includes/header.php';

$sql = "SELECT m.*, 
           ht.nom AS home_team, 
           at.nom AS away_team, 
           c.nom AS competition 
    FROM matches m
    JOIN teams ht ON m.home_team_id = ht.id
    JOIN teams at ON m.away_team_id = at.id
    JOIN competitions c ON m.competition_id = c.id
    ORDER BY m.date_match DESC";
$stmt = $pdo->query($sql);
$matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour générer le nom du fichier logo d'équipe
function logo_filename($nomEquipe) {
    $nom = strtolower($nomEquipe);
    $nom = iconv('UTF-8', 'ASCII//TRANSLIT', $nom); // retire les accents
    $nom = preg_replace('/[^a-z0-9 ]/', '', $nom); // retire caractères spéciaux
    $nom = str_replace(' ', '-', $nom);
    return $nom . '.png';
}

// Fonction pour colorer le score
function score_class($home, $away) {
    if ($home > $away) return 'score-win';
    if ($home < $away) return 'score-lose';
    return 'score-draw';
}
?>

<h2>Résultats des matchs</h2>
<?php if (isset($_GET['simule']) && $_GET['simule'] == '1'): ?>
    <div style="padding:10px;background:#e6ffed;border:1px solid #b7eb8f;color:#135200;margin-bottom:12px;">
        La saison a été resimulée avec succès.
    </div>
<?php endif; ?>
<form method="post" action="simuler_saison.php" style="margin:8px 0 16px;">
    <button type="submit" style="padding:8px 12px;">Resimuler une saison de Ligue 1</button>
    <span style="color:#666;margin-left:8px;">Remplace tous les matchs de « Ligue 1 » par une nouvelle simulation aléatoire.</span>
  </form>
<p class="nb-equipes">Nombre de matchs : <strong><?= count($matchs) ?></strong></p>
<input type="text" id="searchMatch" placeholder="Rechercher par équipe, compétition..." class="search-input" onkeyup="filtrerMatchs()">

<?php
// Regrouper par "journée" = week-end (samedi/dimanche) selon la date du samedi de la semaine correspondante
$byJournee = [];
foreach ($matchs as $m) {
        $dt = new DateTime($m['date_match']);
        $weekStart = (clone $dt)->modify('Saturday this week')->format('Y-m-d');
        $byJournee[$weekStart][] = $m;
}

// Trier les journées (plus récentes d'abord, car liste DESC)
krsort($byJournee);
?>

<?php foreach ($byJournee as $journee => $items): ?>
<h3 style="margin-top:18px;">Journée du weekend du <?= htmlspecialchars((new DateTime($journee))->format('d/m/Y')) ?></h3>
<div class="table-responsive">
    <table class="equipes-table">
        <thead>
            <tr><th>Date</th><th>Compétition</th><th>Domicile</th><th>Score</th><th>Extérieur</th></tr>
        </thead>
        <tbody class="journee" data-journee="<?= htmlspecialchars($journee) ?>">
        <?php foreach ($items as $m): ?>
            <?php $logoHome = logo_filename($m['home_team']); ?>
            <?php $logoAway = logo_filename($m['away_team']); ?>
            <tr>
                <td><?= htmlspecialchars($m['date_match']) ?></td>
                <td><?= htmlspecialchars($m['competition']) ?></td>
                <td><img src="../teams/logos equipes/<?= $logoHome ?>" alt="Logo <?= htmlspecialchars($m['home_team']) ?>" class="logo-equipe"> <?= htmlspecialchars($m['home_team']) ?></td>
                <td class="<?= score_class($m['home_score'], $m['away_score']) ?>">
                    <?= $m['home_score'] . ' - ' . $m['away_score'] ?>
                </td>
                <td><img src="../teams/logos equipes/<?= $logoAway ?>" alt="Logo <?= htmlspecialchars($m['away_team']) ?>" class="logo-equipe"> <?= htmlspecialchars($m['away_team']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endforeach; ?>
<script>
function filtrerMatchs() {
    var input = document.getElementById('searchMatch');
    var filter = input.value.toLowerCase();
    var bodies = document.querySelectorAll('tbody.journee');
    bodies.forEach(function(body) {
        var trs = body.getElementsByTagName('tr');
        var anyVisible = false;
        for (var i = 0; i < trs.length; i++) {
            var home = trs[i].getElementsByTagName('td')[2].textContent.toLowerCase();
            var away = trs[i].getElementsByTagName('td')[4].textContent.toLowerCase();
            var comp = trs[i].getElementsByTagName('td')[1].textContent.toLowerCase();
            if (home.indexOf(filter) > -1 || away.indexOf(filter) > -1 || comp.indexOf(filter) > -1) {
                trs[i].style.display = '';
                anyVisible = true;
            } else {
                trs[i].style.display = 'none';
            }
        }
        // cacher le bloc journée si aucun match visible
        var section = body.closest('div.table-responsive');
        if (section) {
            section.style.display = anyVisible ? '' : 'none';
            var title = section.previousElementSibling;
            if (title && title.tagName === 'H3') {
                title.style.display = anyVisible ? '' : 'none';
            }
        }
    });
}
</script>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>