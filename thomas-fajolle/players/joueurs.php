<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connexion.php';
require_once __DIR__ . '/../includes/header.php';

$seasonFilter = $_GET['season'] ?? '2024-2025';
$useSeasonSchema = false; // deviendra true si les tables seasons / player_seasons existent

// Détection des tables seasons & player_seasons
try {
    $pdo->query("SELECT 1 FROM player_seasons LIMIT 1");
    $pdo->query("SELECT 1 FROM seasons LIMIT 1");
    $useSeasonSchema = true;
} catch (Throwable $e) {
    $useSeasonSchema = false; // on reste sur l'ancien mode
}
if ($useSeasonSchema) {
    $sql = "SELECT p.id, p.nom, p.prenom,
                   COALESCE(ps.position, p.poste) AS poste,
                   COALESCE(ps.shirt_number, p.numero) AS numero,
                   t.nom AS equipe,
                   p.nationalite
            FROM player_seasons ps
            JOIN players p ON ps.player_id = p.id
            JOIN teams t ON ps.team_id = t.id
            JOIN seasons s ON ps.season_id = s.id
            WHERE s.name = :season
            ORDER BY t.nom, p.nom";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':season' => $seasonFilter]);
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sql = "SELECT p.*, t.nom AS equipe FROM players p JOIN teams t ON p.team_id = t.id ORDER BY t.nom, p.nom";
    $stmt = $pdo->query($sql);
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour générer le nom du fichier logo d'équipe
function logo_filename($nomEquipe) {
    $nom = strtolower($nomEquipe);
    $nom = iconv('UTF-8', 'ASCII//TRANSLIT', $nom); // retire les accents
    $nom = preg_replace('/[^a-z0-9 ]/', '', $nom); // retire caractères spéciaux
    $nom = str_replace(' ', '-', $nom);
    return $nom . '.png';
}
?>

<h2>Liste des joueurs <?= $useSeasonSchema ? ' - Saison ' . htmlspecialchars($seasonFilter) : '' ?></h2>
<p class="nb-equipes">Nombre de joueurs : <strong><?= count($joueurs) ?></strong></p>
<?php if ($useSeasonSchema): ?>
<form method="get" style="margin-bottom:1rem;">
    <label>Saison : <input type="text" name="season" value="<?= htmlspecialchars($seasonFilter) ?>" placeholder="2024-2025"></label>
    <button type="submit">Filtrer</button>
    <a href="import_2024_2025.php" style="margin-left:1rem;">Importer 2024-2025</a>
</form>
<?php else: ?>
<p style="font-size:0.9rem;color:#666;">Astuce : crée la structure saisons via <code>players/import_2024_2025.php</code> pour activer le filtrage par saison.</p>
<?php endif; ?>
<input type="text" id="searchJoueur" placeholder="Rechercher un joueur..." class="search-input" onkeyup="filtrerJoueurs()">
<div class="table-responsive">
<table class="equipes-table">
    <thead>
        <tr><th>Logo équipe</th><th>Nom</th><th>Prénom</th><th>Poste</th><th>Numéro</th><th>Équipe</th><th>Nationalité</th></tr>
    </thead>
    <tbody id="joueursBody">
    <?php foreach ($joueurs as $j): ?>
        <?php $logo = logo_filename($j['equipe']); ?>
        <tr>
            <td><img src="../teams/logos equipes/<?= $logo ?>" alt="Logo <?= htmlspecialchars($j['equipe']) ?>" class="logo-equipe"></td>
            <td><?= htmlspecialchars($j['nom']) ?></td>
            <td><?= htmlspecialchars($j['prenom']) ?></td>
            <td><?= htmlspecialchars($j['poste']) ?></td>
            <td><?= htmlspecialchars($j['numero']) ?></td>
            <td><?= htmlspecialchars($j['equipe']) ?></td>
            <td><?= htmlspecialchars($j['nationalite']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<script>
function filtrerJoueurs() {
    var input = document.getElementById('searchJoueur');
    var filter = input.value.toLowerCase();
    var table = document.getElementById('joueursBody');
    var trs = table.getElementsByTagName('tr');
    for (var i = 0; i < trs.length; i++) {
        var nom = trs[i].getElementsByTagName('td')[1].textContent.toLowerCase();
        var prenom = trs[i].getElementsByTagName('td')[2].textContent.toLowerCase();
        if (nom.indexOf(filter) > -1 || prenom.indexOf(filter) > -1) {
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