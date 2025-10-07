<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../includes/header.php';

$sql = "SELECT s.*, t.nom, t.id 
        FROM standings s
        JOIN teams t ON s.team_id = t.id
        ORDER BY s.points DESC, s.goal_difference DESC";
$stmt = $pdo->query($sql);
$classement = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour générer le nom du fichier logo d'équipe
function logo_filename($nomEquipe) {
    $nom = strtolower($nomEquipe);
    $nom = iconv('UTF-8', 'ASCII//TRANSLIT', $nom); // retire les accents
    $nom = preg_replace('/[^a-z0-9 ]/', '', $nom); // retire caractères spéciaux
    $nom = str_replace(' ', '-', $nom);
    return $nom . '.png';
}

// Exemple d'évolution (à remplacer par une vraie donnée si dispo)
function evolution_icon($evolution) {
    if ($evolution > 0) return '<span class="evo-up">▲</span>';
    if ($evolution < 0) return '<span class="evo-down">▼</span>';
    return '<span class="evo-stable">●</span>';
}

// Mise en avant des qualifiés (exemple : 1-2 Ligue des Champions, 3 Europa, 18-20 relégation)
function qualif_class($pos) {
    if ($pos == 1 || $pos == 2) return 'qualif-champions';
    if ($pos == 3) return 'qualif-europa';
    if ($pos >= 18) return 'relagation';
    return '';
}
?>

<h2>Classement de la Ligue 1</h2>
<p class="nb-equipes">Nombre d'équipes : <strong><?= count($classement) ?></strong></p>
<input type="text" id="searchClassement" placeholder="Rechercher une équipe..." class="search-input" onkeyup="filtrerClassement()">
<div class="table-responsive">
<table class="equipes-table">
    <thead>
        <tr><th>#</th><th>Logo</th><th>Équipe</th><th>Pts</th><th>J</th><th>G</th><th>N</th><th>P</th><th>BP</th><th>BC</th><th>Diff</th><th>Évo</th></tr>
    </thead>
    <tbody id="classementBody">
    <?php $pos = 1; foreach ($classement as $c): ?>
        <?php $logo = logo_filename($c['nom']); ?>
        <?php $evolution = 0; // À remplacer par la vraie donnée d'évolution si dispo ?>
        <tr class="<?= qualif_class($pos) ?>">
            <td><?= $pos ?></td>
            <td><img src="../teams/logos equipes/<?= $logo ?>" alt="Logo <?= htmlspecialchars($c['nom']) ?>" class="logo-equipe"></td>
            <td><?= htmlspecialchars($c['nom']) ?></td>
            <td><?= $c['points'] ?></td>
            <td><?= $c['played'] ?></td>
            <td><?= $c['won'] ?></td>
            <td><?= $c['draw'] ?></td>
            <td><?= $c['lost'] ?></td>
            <td><?= $c['goals_for'] ?></td>
            <td><?= $c['goals_against'] ?></td>
            <td><?= $c['goal_difference'] ?></td>
            <td><?= evolution_icon($evolution) ?></td>
        </tr>
    <?php $pos++; endforeach; ?>
    </tbody>
</table>
</div>
<script>
function filtrerClassement() {
    var input = document.getElementById('searchClassement');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('classementBody');
    var trs = table.getElementsByTagName('tr');
    for (var i = 0; i < trs.length; i++) {
        var equipe = trs[i].getElementsByTagName('td')[2].textContent.toLowerCase();
        if (equipe.indexOf(filter) > -1) {
            trs[i].style.display = '';
        } else {
            trs[i].style.display = 'none';
        }
    }
}
</script>
<?php
require_once __DIR__ . '/../includes/footer.php';
?>