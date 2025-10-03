<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/connexion.php';
$joueurs = $equipes = $matchs = $stats = [];
$erreur = '';
try {
    $sql = 'SELECT * FROM joueurs';
    $stmt = $conn->query($sql);
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM equipes';
    $stmt = $conn->query($sql);
    $equipes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM matchs';
    $stmt = $conn->query($sql);
    $matchs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sql = 'SELECT * FROM stats_joueurs';
    $stmt = $conn->query($sql);
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erreur = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Page d'accueil - Maëlle GOUJAT</title>
    <link rel="stylesheet" href="style-accueil.css">
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur la page de Maëlle GOUJAT</h1>
        <p>Cette page est générée en PHP pour le projet du mastère spécialisé IHME Marseille 2025.</p>
        <div class="pub">
            <strong>Publicité :</strong> <br>
            Découvrez le calendrier de la compétition élite 1 féminine pour la saison 2025-2026 !<br>
            <a href="https://competitions.ffr.fr/competitions/elite-1-feminine/calendrier.html" target="_blank">En savoir plus</a>
        </div>
        <p>Accès rapide :</p>
        <ul>
            <li><a href="joueurs/ajouter_joueur.php">Ajouter un joueur</a></li>
            <li><a href="joueurs/lire_joueurs.php">Liste des joueurs (JSON)</a></li>
            <li><a href="equipes/ajouter_equipe.php">Ajouter une équipe</a></li>
            <li><a href="dashboard.php">Tableau de bord</a></li>
        </ul>
        <h2>Liste des joueurs</h2>
        <form id="search-joueurs-form" style="margin-bottom:1em;">
            <input type="text" id="search-joueurs" placeholder="Rechercher un joueur..." aria-label="Rechercher un joueur">
            <button type="button" id="export-joueurs-csv">Export CSV</button>
            <button type="button" id="export-joueurs-json">Export JSON</button>
        </form>
        <?php if (!empty($erreur)) : ?>
            <div class="error">Erreur : <?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <?php if (count($joueurs) > 0): ?>
    <table id="table-joueurs">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Poste</th>
                <th>ID équipe</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($joueurs as $joueur): ?>
            <tr>
                                <td><a href="joueurs/fiche_joueur.php?id=<?= urlencode($joueur['id_joueur']) ?>" title="Voir fiche joueur"><?= htmlspecialchars($joueur['id_joueur']) ?></a></td>
                                <td><?= htmlspecialchars($joueur['nom']) ?></td>
                                <td><?= htmlspecialchars($joueur['prenom']) ?></td>
                                <td><?= htmlspecialchars($joueur['poste']) ?></td>
                                <td>
                                    <?php if ($joueur['id_equipe']): ?>
                                        <a href="equipes/fiche_equipe.php?id=<?= urlencode($joueur['id_equipe']) ?>" title="Voir fiche équipe">
                                            <?= htmlspecialchars($joueur['id_equipe']) ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                <td>
                    <a href="joueurs/modifier_joueur.php?id=<?= urlencode($joueur['id_joueur']) ?>" class="btn-modifier">Modifier</a>
                    <a href="joueurs/supprimer_joueur.php?id=<?= urlencode($joueur['id_joueur']) ?>" class="btn-supprimer" onclick="return confirm('Supprimer ce joueur ?');">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucun joueur trouvé.</p>
        <?php endif; ?>

        <h2>Liste des équipes</h2>
        <form id="search-equipes-form" style="margin-bottom:1em;">
            <input type="text" id="search-equipes" placeholder="Rechercher une équipe..." aria-label="Rechercher une équipe">
            <button type="button" id="export-equipes-csv">Export CSV</button>
            <button type="button" id="export-equipes-json">Export JSON</button>
        </form>
        <?php if (count($equipes) > 0): ?>
    <table id="table-equipes">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Ville</th>
                <th>Pays</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($equipes as $equipe): ?>
            <tr>
                <td><a href="equipes/fiche_equipe.php?id=<?= urlencode($equipe['id_equipe']) ?>" title="Voir fiche équipe"><?= htmlspecialchars($equipe['id_equipe']) ?></a></td>
                <td><?= htmlspecialchars($equipe['nom_equipe']) ?></td>
                <td><?= htmlspecialchars($equipe['ville']) ?></td>
                <td><?= htmlspecialchars($equipe['pays']) ?></td>
                <td>
                    <a href="equipes/modifier_equipe.php?id=<?= urlencode($equipe['id_equipe']) ?>" class="btn-modifier">Modifier</a>
                    <a href="equipes/supprimer_equipe.php?id=<?= urlencode($equipe['id_equipe']) ?>" class="btn-supprimer" onclick="return confirm('Supprimer cette équipe ?');">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucune équipe trouvée.</p>
        <?php endif; ?>

        <h2>Liste des matchs</h2>
        <?php if (count($matchs) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Lieu</th>
                <th>Équipe dom.</th>
                <th>Équipe ext.</th>
                <th>Score dom.</th>
                <th>Score ext.</th>
            </tr>
            <?php foreach ($matchs as $match): ?>
            <tr>
                <td><?= htmlspecialchars($match['id_match']) ?></td>
                <td><?= htmlspecialchars($match['date_match']) ?></td>
                <td><?= htmlspecialchars($match['lieu']) ?></td>
                <td><?= htmlspecialchars($match['equipe_dom']) ?></td>
                <td><?= htmlspecialchars($match['equipe_ext']) ?></td>
                <td><?= htmlspecialchars($match['score_dom']) ?></td>
                <td><?= htmlspecialchars($match['score_ext']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucun match trouvé.</p>
        <?php endif; ?>

        <h2>Statistiques des joueurs</h2>
        <?php if (count($stats) > 0): ?>
        <table>
            <tr>
                <th>ID match</th>
                <th>ID joueur</th>
                <th>Essais</th>
                <th>Transformations</th>
                <th>Pénalités</th>
                <th>Cartons jaunes</th>
                <th>Cartons rouges</th>
            </tr>
            <?php foreach ($stats as $stat): ?>
            <tr>
                <td><?= htmlspecialchars($stat['id_match']) ?></td>
                <td><?= htmlspecialchars($stat['id_joueur']) ?></td>
                <td><?= htmlspecialchars($stat['essais']) ?></td>
                <td><?= htmlspecialchars($stat['transformations']) ?></td>
                <td><?= htmlspecialchars($stat['penalites']) ?></td>
                <td><?= htmlspecialchars($stat['cartons_jaunes']) ?></td>
                <td><?= htmlspecialchars($stat['cartons_rouges']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <?php else: ?>
            <p>Aucune statistique trouvée.</p>
        <?php endif; ?>
    </div>
<script>
// Filtrage joueurs
document.getElementById('search-joueurs').addEventListener('input', function() {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll('#table-joueurs tbody tr');
    rows.forEach(row => {
        row.style.display = Array.from(row.children).some(td => td.textContent.toLowerCase().includes(value)) ? '' : 'none';
    });
});
// Filtrage équipes
document.getElementById('search-equipes').addEventListener('input', function() {
    const value = this.value.toLowerCase();
    const rows = document.querySelectorAll('#table-equipes tbody tr');
    rows.forEach(row => {
        row.style.display = Array.from(row.children).some(td => td.textContent.toLowerCase().includes(value)) ? '' : 'none';
    });
});
// Export CSV générique
function exportTableToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    let csv = '';
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        let rowData = Array.from(row.children).map(td => '"' + td.textContent.replace(/"/g, '""') + '"').join(',');
        csv += rowData + '\n';
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
// Export JSON générique
function exportTableToJSON(tableId, filename) {
    const table = document.getElementById(tableId);
    const headers = Array.from(table.querySelectorAll('tr')[0].children).map(th => th.textContent);
    const data = [];
    table.querySelectorAll('tbody tr').forEach(row => {
        if (row.style.display === 'none') return;
        const obj = {};
        Array.from(row.children).forEach((td, i) => {
            obj[headers[i]] = td.textContent;
        });
        data.push(obj);
    });
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
// Boutons export joueurs
document.getElementById('export-joueurs-csv').onclick = function() {
    exportTableToCSV('table-joueurs', 'joueurs.csv');
};
document.getElementById('export-joueurs-json').onclick = function() {
    exportTableToJSON('table-joueurs', 'joueurs.json');
};
// Boutons export équipes
document.getElementById('export-equipes-csv').onclick = function() {
    exportTableToCSV('table-equipes', 'equipes.csv');
};
document.getElementById('export-equipes-json').onclick = function() {
    exportTableToJSON('table-equipes', 'equipes.json');
};
</script>
</body>
</html>