<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../includes/header.php';

$sql = "SELECT * FROM teams ORDER BY nom";
$stmt = $pdo->query($sql);
$equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fonction pour générer le nom du fichier logo
function logo_filename($nomEquipe) {
    $nom = strtolower($nomEquipe);
    $nom = iconv('UTF-8', 'ASCII//TRANSLIT', $nom); // retire les accents
    $nom = preg_replace('/[^a-z0-9 ]/', '', $nom); // retire caractères spéciaux
    $nom = str_replace(' ', '-', $nom);
    return $nom . '.png';
}
?>

<h2>Liste des équipes</h2>
<p class="nb-equipes">Nombre d'équipes : <strong><?= count($equipes) ?></strong></p>
<input type="text" id="searchEquipe" placeholder="Rechercher une équipe..." class="search-input" onkeyup="filtrerEquipes()">
<div class="table-responsive">
<table class="equipes-table">
    <thead>
        <tr><th>Logo</th><th>Nom</th><th>Ville</th><th>Stade</th><th>Entraîneur</th></tr>
    </thead>
    <tbody id="equipesBody">
    <?php foreach ($equipes as $e): ?>
        <?php $logo = logo_filename($e['nom']); ?>
        <tr>
            <td><img src="../teams/logos equipes/<?= $logo ?>" alt="Logo <?= htmlspecialchars($e['nom']) ?>" class="logo-equipe"></td>
            <td><?= htmlspecialchars($e['nom']) ?></td>
            <td><?= htmlspecialchars($e['ville']) ?></td>
            <td><?= htmlspecialchars($e['stade']) ?></td>
            <td><?= htmlspecialchars($e['entraineur']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<script>
function filtrerEquipes() {
    var input = document.getElementById('searchEquipe');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('equipesBody');
    var trs = table.getElementsByTagName('tr');
    for (var i = 0; i < trs.length; i++) {
        var nom = trs[i].getElementsByTagName('td')[1].textContent.toLowerCase();
        if (nom.indexOf(filter) > -1) {
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