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
    <button id="toggle-dark" aria-label="Activer/désactiver le mode sombre" style="position:absolute;top:1em;right:3.5em;z-index:10;">🌙</button>
    <button id="toggle-access" aria-label="Activer/désactiver le mode accessibilité forte" style="position:absolute;top:1em;right:1em;z-index:10;">🦾</button>
        <button id="toggle-dark" aria-label="Activer/désactiver le mode sombre" style="position:absolute;top:1em;right:3.5em;z-index:10;">🌙</button>
        <button id="toggle-access" aria-label="Activer/désactiver le mode accessibilité forte" style="position:absolute;top:1em;right:1em;z-index:10;">🦾</button>
}
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
        <form id="search-joueurs-form" style="margin-bottom:1em;display:flex;gap:0.5em;align-items:center;flex-wrap:wrap;">
            <input type="text" id="search-joueurs" placeholder="Rechercher un joueur..." aria-label="Rechercher un joueur">
            <select id="filter-poste" aria-label="Filtrer par poste">
                <option value="">Tous postes</option>
                <option value="Pilier">Pilier</option>
                <option value="Talonneur">Talonneur</option>
                <option value="2e ligne">2e ligne</option>
                <option value="3e ligne">3e ligne</option>
                <option value="Demi de mêlée">Demi de mêlée</option>
                <option value="Demi d'ouverture">Demi d'ouverture</option>
                <option value="Centre">Centre</option>
                <option value="Ailier">Ailier</option>
                <option value="Arrière">Arrière</option>
            </select>
            <select id="filter-equipe" aria-label="Filtrer par équipe">
                <option value="">Toutes équipes</option>
                <?php foreach ($equipes as $equipe): ?>
                <option value="<?= htmlspecialchars($equipe['id_equipe']) ?>"><?= htmlspecialchars($equipe['nom_equipe']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="export-joueurs-csv">Export CSV</button>
            <button type="button" id="export-joueurs-json">Export JSON</button>
            <button type="button" id="delete-joueurs-multi" style="background:#ef4444;">Supprimer sélection</button>
        </form>
        <?php if (!empty($erreur)) : ?>
            <div class="error">Erreur : <?= htmlspecialchars($erreur) ?></div>
        <?php endif; ?>
        <?php if (count($joueurs) > 0): ?>
        <form id="form-joueurs-multi" method="post" action="joueurs/supprimer_joueurs_multi.php">
        <table id="table-joueurs">
            <thead>
            <tr>
                <th><input type="checkbox" id="check-all-joueurs" aria-label="Tout sélectionner"></th>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Poste</th>
                <th>ID équipe</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($joueurs as $joueur): ?>
            <tr>
                <td><input type="checkbox" name="ids[]" value="<?= htmlspecialchars($joueur['id_joueur']) ?>" class="check-joueur" aria-label="Sélectionner joueur"></td>
                <td><a href="joueurs/fiche_joueur.php?id=<?= urlencode($joueur['id_joueur']) ?>" title="Voir fiche joueur"><?= htmlspecialchars($joueur['id_joueur']) ?></a>
                  <button class="fav-btn" data-type="joueur" data-id="<?= htmlspecialchars($joueur['id_joueur']) ?>" aria-label="Ajouter/retirer des favoris" title="Favori">★</button>
                </td>
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
            </tbody>
        </table>

	</form>
	<?php else: ?>
		<p>Aucun joueur trouvé.</p>
	<?php endif; ?>

        <!-- Première occurrence de la liste des équipes supprimée, seule la version avancée reste -->
        <!-- Fin affichage équipes -->
<script>
// Accessibilité forte (contraste élevé, police dyslexique)
const accessBtn = document.getElementById('toggle-access');
accessBtn.onclick = function() {
    document.body.classList.toggle('access-high');
    localStorage.setItem('access', document.body.classList.contains('access-high'));
};
if (localStorage.getItem('access') === 'true') {
    document.body.classList.add('access-high');
}
// Raccourcis clavier
document.addEventListener('keydown', function(e) {
    if (e.altKey && !e.shiftKey && !e.ctrlKey) {
        switch (e.key.toLowerCase()) {
            case 'a': window.location.href = 'index.php'; break; // Alt+A : Accueil
            case 'd': window.location.href = 'dashboard.php'; break; // Alt+D : Dashboard
            case 'm': document.getElementById('toggle-dark').click(); break; // Alt+M : Mode sombre
            case 'j': document.getElementById('search-joueurs').focus(); break; // Alt+J : Recherche joueurs
            case 'e': document.getElementById('search-equipes').focus(); break; // Alt+E : Recherche équipes
        }
    }
});
// Sélection/désélection tout joueurs
document.getElementById('check-all-joueurs').addEventListener('change', function() {
    document.querySelectorAll('.check-joueur').forEach(cb => cb.checked = this.checked);
});
// Bouton suppression multiple joueurs
document.getElementById('delete-joueurs-multi').onclick = function() {
    const checked = document.querySelectorAll('.check-joueur:checked');
    if (checked.length === 0) { showNotif('Aucun joueur sélectionné'); return; }
    if (confirm('Supprimer les joueurs sélectionnés ?')) {
        document.getElementById('form-joueurs-multi').submit();
    }
};
// Sélection/désélection tout équipes
document.getElementById('check-all-equipes').addEventListener('change', function() {
    document.querySelectorAll('.check-equipe').forEach(cb => cb.checked = this.checked);
});
// Bouton suppression multiple équipes
document.getElementById('delete-equipes-multi').onclick = function() {
    const checked = document.querySelectorAll('.check-equipe:checked');
    if (checked.length === 0) { showNotif('Aucune équipe sélectionnée'); return; }
    if (confirm('Supprimer les équipes sélectionnées ?')) {
        document.getElementById('form-equipes-multi').submit();
    }
};
        // Recherche avancée joueurs
        document.getElementById('filter-poste').addEventListener('change', filterJoueurs);
        document.getElementById('filter-equipe').addEventListener('change', filterJoueurs);
        function filterJoueurs() {
            const poste = document.getElementById('filter-poste').value.toLowerCase();
            const equipe = document.getElementById('filter-equipe').value;
            const search = document.getElementById('search-joueurs').value.toLowerCase();
            document.querySelectorAll('#table-joueurs tbody tr').forEach(row => {
                const tds = row.children;
                const matchPoste = !poste || tds[4].textContent.toLowerCase().includes(poste);
                const matchEquipe = !equipe || (tds[5].querySelector('a') && tds[5].querySelector('a').textContent === equipe) || (tds[5].textContent === equipe);
                const matchSearch = Array.from(tds).some(td => td.textContent.toLowerCase().includes(search));
                row.style.display = (matchPoste && matchEquipe && matchSearch) ? '' : 'none';
            });
        }
        document.getElementById('search-joueurs').addEventListener('input', filterJoueurs);
        // Recherche avancée équipes
        document.getElementById('filter-ville').addEventListener('change', filterEquipes);
        function filterEquipes() {
            const ville = document.getElementById('filter-ville').value.toLowerCase();
            const search = document.getElementById('search-equipes').value.toLowerCase();
            document.querySelectorAll('#table-equipes tbody tr').forEach(row => {
                const tds = row.children;
                const matchVille = !ville || tds[3].textContent.toLowerCase().includes(ville);
                const matchSearch = Array.from(tds).some(td => td.textContent.toLowerCase().includes(search));
                row.style.display = (matchVille && matchSearch) ? '' : 'none';
            });
        }
        document.getElementById('search-equipes').addEventListener('input', filterEquipes);
</script>

        <h2>Liste des équipes</h2>
        <form id="search-equipes-form" style="margin-bottom:1em;display:flex;gap:0.5em;align-items:center;flex-wrap:wrap;">
            <input type="text" id="search-equipes" placeholder="Rechercher une équipe..." aria-label="Rechercher une équipe">
            <select id="filter-ville" aria-label="Filtrer par ville">
                <option value="">Toutes villes</option>
                <?php
                $villes = array_unique(array_map(function($e) { return $e['ville']; }, $equipes));
                sort($villes);
                foreach ($villes as $ville): ?>
                <option value="<?= htmlspecialchars($ville) ?>"><?= htmlspecialchars($ville) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="button" id="export-equipes-csv">Export CSV</button>
            <button type="button" id="export-equipes-json">Export JSON</button>
            <button type="button" id="delete-equipes-multi" style="background:#ef4444;">Supprimer sélection</button>
        </form>
        <?php if (count($equipes) > 0): ?>
        <form id="form-equipes-multi" method="post" action="equipes/supprimer_equipes_multi.php">
        <table id="table-equipes">
            <thead>
            <tr>
                <th><input type="checkbox" id="check-all-equipes" aria-label="Tout sélectionner"></th>
                <th>ID</th>
                <th>Nom</th>
                <th>Ville</th>
                <th>Pays</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($equipes as $equipe): ?>
            <tr>
                <td><input type="checkbox" name="ids[]" value="<?= htmlspecialchars($equipe['id_equipe']) ?>" class="check-equipe" aria-label="Sélectionner équipe"></td>
                <td><a href="equipes/fiche_equipe.php?id=<?= urlencode($equipe['id_equipe']) ?>" title="Voir fiche équipe"><?= htmlspecialchars($equipe['id_equipe']) ?></a>
                  <button class="fav-btn" data-type="equipe" data-id="<?= htmlspecialchars($equipe['id_equipe']) ?>" aria-label="Ajouter/retirer des favoris" title="Favori">★</button>
                </td>
                <td><?= htmlspecialchars($equipe['nom_equipe']) ?></td>
                <td><?= htmlspecialchars($equipe['ville']) ?></td>
                <td><?= htmlspecialchars($equipe['pays']) ?></td>
                <td>
                    <a href="equipes/modifier_equipe.php?id=<?= urlencode($equipe['id_equipe']) ?>" class="btn-modifier">Modifier</a>
                    <a href="equipes/supprimer_equipe.php?id=<?= urlencode($equipe['id_equipe']) ?>" class="btn-supprimer" onclick="return confirm('Supprimer cette équipe ?');">Supprimer</a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </form>
        <?php else: ?>
            <p>Aucune équipe trouvée.</p>
        <?php endif; ?>
        <!-- JS de recherche avancée déplacé dans le <script> final, plus d'affichage parasite -->
        <?php if (count($equipes) > 0): ?>
                <form id="form-joueurs-multi" method="post" action="joueurs/supprimer_joueurs_multi.php">
                <table id="table-joueurs">
                        <thead>
                        <tr>
                                <th><input type="checkbox" id="check-all-joueurs" aria-label="Tout sélectionner"></th>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Poste</th>
                                <th>ID équipe</th>
                                <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($joueurs as $joueur): ?>
                        <tr>
                                <td><input type="checkbox" name="ids[]" value="<?= htmlspecialchars($joueur['id_joueur']) ?>" class="check-joueur" aria-label="Sélectionner joueur"></td>
                                <td><a href="joueurs/fiche_joueur.php?id=<?= urlencode($joueur['id_joueur']) ?>" title="Voir fiche joueur"><?= htmlspecialchars($joueur['id_joueur']) ?></a>
                                    <button class="fav-btn" data-type="joueur" data-id="<?= htmlspecialchars($joueur['id_joueur']) ?>" aria-label="Ajouter/retirer des favoris" title="Favori">★</button>
                                </td>
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
                        </tbody>
                </table>
                </form>
// Sélection/désélection tout joueurs
document.getElementById('check-all-joueurs').addEventListener('change', function() {
    document.querySelectorAll('.check-joueur').forEach(cb => cb.checked = this.checked);
});
// Bouton suppression multiple joueurs
document.getElementById('delete-joueurs-multi').onclick = function() {
    const checked = document.querySelectorAll('.check-joueur:checked');
    if (checked.length === 0) { showNotif('Aucun joueur sélectionné'); return; }
    if (confirm('Supprimer les joueurs sélectionnés ?')) {
        document.getElementById('form-joueurs-multi').submit();
    }
};
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
<div id="notif" aria-live="polite" style="position:fixed;top:1em;left:50%;transform:translateX(-50%);z-index:1000;display:none;background:#0ea5e9;color:#fff;padding:0.7em 1.5em;border-radius:6px;font-size:1.1em;box-shadow:0 2px 8px #0002;"></div>
// Notification globale pour actions CRUD (succès/erreur via query string)
const params = new URLSearchParams(window.location.search);
if (params.has('msg')) {
    let txt = '';
    switch(params.get('msg')) {
        case 'ajout_joueur': txt = 'Joueur ajouté avec succès !'; break;
        case 'modif_joueur': txt = 'Joueur modifié avec succès !'; break;
        case 'suppression_joueur': txt = 'Joueur supprimé.'; break;
        case 'ajout_equipe': txt = 'Équipe ajoutée avec succès !'; break;
        case 'modif_equipe': txt = 'Équipe modifiée avec succès !'; break;
        case 'suppression_equipe': txt = 'Équipe supprimée.'; break;
        case 'erreur': txt = 'Une erreur est survenue.'; break;
    }
    if (txt) showNotif(txt);
}
<script>
// Favoris (localStorage)
function updateFavButtons() {
    const favs = JSON.parse(localStorage.getItem('favs')||'{}');
    document.querySelectorAll('.fav-btn').forEach(btn => {
        const key = btn.dataset.type + '-' + btn.dataset.id;
        btn.classList.toggle('fav-active', !!favs[key]);
        btn.setAttribute('aria-pressed', !!favs[key]);
    });
}
function showNotif(msg) {
    const notif = document.getElementById('notif');
    notif.textContent = msg;
    notif.style.display = 'block';
    setTimeout(()=>{notif.style.display='none';}, 1800);
}
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('fav-btn')) {
        const btn = e.target;
        const key = btn.dataset.type + '-' + btn.dataset.id;
        let favs = JSON.parse(localStorage.getItem('favs')||'{}');
        if (favs[key]) {
            delete favs[key];
            showNotif('Retiré des favoris');
        } else {
            favs[key] = true;
            showNotif('Ajouté aux favoris');
        }
        localStorage.setItem('favs', JSON.stringify(favs));
        updateFavButtons();
    }
});
updateFavButtons();
// Style favoris
const styleFav = document.createElement('style');
styleFav.textContent = `.fav-btn{background:none;border:none;color:#64748b;font-size:1.1em;cursor:pointer;vertical-align:middle;transition:color 0.2s;}.fav-btn.fav-active{color:#facc15;text-shadow:0 0 2px #fbbf24;}.fav-btn:focus{outline:2px solid #facc15;}`;
document.head.appendChild(styleFav);
// Pagination et tri dynamique
function makeTableSortableAndPaginated(tableId, rowsPerPage = 10) {
    const table = document.getElementById(tableId);
    if (!table) return;
    const tbody = table.querySelector('tbody') || table;
    let rows = Array.from(tbody.querySelectorAll('tr'));
    let currentPage = 1;
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    // Tri
    table.querySelectorAll('th').forEach((th, idx) => {
        th.style.cursor = 'pointer';
        th.title = 'Trier';
        th.onclick = function() {
            const asc = !th.classList.contains('asc');
            table.querySelectorAll('th').forEach(h => h.classList.remove('asc', 'desc'));
            th.classList.add(asc ? 'asc' : 'desc');
            rows.sort((a, b) => {
                let va = a.children[idx].textContent.trim().toLowerCase();
                let vb = b.children[idx].textContent.trim().toLowerCase();
                if (!isNaN(va) && !isNaN(vb)) { va = +va; vb = +vb; }
                if (va < vb) return asc ? -1 : 1;
                if (va > vb) return asc ? 1 : -1;
                return 0;
            });
            renderPage(currentPage);
        };
    });
    // Pagination
    function renderPage(page) {
        tbody.innerHTML = '';
        const start = (page - 1) * rowsPerPage;
        const end = Math.min(start + rowsPerPage, totalRows);
        for (let i = start; i < end; i++) tbody.appendChild(rows[i]);
        renderPagination(page);
    }
    function renderPagination(page) {
        let pag = document.getElementById(tableId + '-pagination');
        if (!pag) {
            pag = document.createElement('div');
            pag.id = tableId + '-pagination';
            pag.className = 'pagination';
            table.parentNode.appendChild(pag);
        }
        pag.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.disabled = (i === page);
            btn.onclick = () => { currentPage = i; renderPage(i); };
            pag.appendChild(btn);
        }
    }
    renderPage(currentPage);
}
makeTableSortableAndPaginated('table-joueurs', 10);
makeTableSortableAndPaginated('table-equipes', 10);
// Style pagination
const style = document.createElement('style');
style.textContent = `.pagination{margin:1em 0;text-align:center;}.pagination button{background:#2563eb;color:#fff;border:none;padding:0.4em 0.8em;margin:0 0.2em;border-radius:4px;cursor:pointer;}.pagination button[disabled]{background:#64748b;cursor:default;}`;
document.head.appendChild(style);
// Mode sombre
const darkBtn = document.getElementById('toggle-dark');
darkBtn.onclick = function() {
    document.body.classList.toggle('dark');
    localStorage.setItem('darkmode', document.body.classList.contains('dark'));
};
if (localStorage.getItem('darkmode') === 'true') {
    document.body.classList.add('dark');
}
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