<?php
// pages/joueurs.php
declare(strict_types=1);
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// messages éventuels
$added = $_GET['added'] ?? '';
$error = $_GET['error'] ?? '';
?>
<!-- Ajouter joueur -->
<div class="card">
    <h2>Ajouter un joueur</h2>
    <?php if ($added === '1'): ?>
        <p style="color:green;">Joueur ajouté avec succès.</p>
    <?php elseif ($error === 'doublon'): ?>
        <p style="color:orange;">Doublon détecté (nom/prénom/équipe).</p>
    <?php elseif ($error !== ''): ?>
        <p style="color:red;">Erreur lors de l'ajout.</p>
    <?php endif; ?>

    <form method="post" action="services/add_player.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="text" name="nom" placeholder="Nom" required>

        <select name="poste" required>
            <option value="">Sélectionner un poste</option>
            <?php
            // liste des positions (table 'position')
            try {
                $pos = $pdo->query("SELECT code, libelle FROM `position` ORDER BY libelle")->fetchAll();
                foreach ($pos as $p) {
                    echo "<option value=\"" . htmlspecialchars($p['code']) . "\">" . htmlspecialchars($p['libelle']) . " (" . htmlspecialchars($p['code']) . ")</option>";
                }
            } catch (Exception $e) {
                echo "<option value=''>Erreur chargement positions</option>";
            }
            ?>
        </select>

        <input type="number" name="age" placeholder="Âge">
        <input type="number" name="taille_cm" placeholder="Taille (cm)">
        <input type="number" name="poids_kg" placeholder="Poids (kg)">
        <input type="number" name="annee_debut" placeholder="Année début (ex: 2019)">

        <select name="id_team" required>
            <option value="">Sélectionner une équipe</option>
            <?php
            $teams = $pdo->query("SELECT id_team, nom_team, conference FROM team ORDER BY conference, nom_team")->fetchAll();
            $current_conf = "";
            foreach ($teams as $t) {
                if ($t['conference'] !== $current_conf) {
                    if ($current_conf !== "") echo "</optgroup>";
                    $current_conf = $t['conference'];
                    echo "<optgroup label='" . htmlspecialchars($current_conf) . "'>";
                }
                echo "<option value='" . (int)$t['id_team'] . "'>" . htmlspecialchars($t['nom_team']) . "</option>";
            }
            if ($current_conf !== "") echo "</optgroup>";
            ?>
        </select>

        <button type="submit">Ajouter le joueur</button>
    </form>
</div>

<!-- Recherche joueurs -->
<div class="card">
    <h2>Recherche joueur</h2>
    <form method="get">
        <input type="hidden" name="page" value="joueurs">
        <input type="text" name="recherche" placeholder="Nom ou prénom">
        <button type="submit">Rechercher</button>
    </form>
</div>

<!-- Liste des joueurs -->
<h2>Liste des joueurs</h2>
<div class="grid">
    <?php
    $where = "";
    $params = [];
    if (!empty($_GET['recherche'])) {
        $search = "%" . $_GET['recherche'] . "%";
        $where = "WHERE p.nom LIKE ? OR p.prenom LIKE ? OR CONCAT(p.prenom,' ',p.nom) LIKE ? OR CONCAT(p.nom,' ',p.prenom) LIKE ?";
        $params = [$search, $search, $search, $search];
    }
    $stmt = $pdo->prepare("SELECT p.*, t.nom_team, t.logo_url 
                           FROM player p 
                           JOIN team t ON p.id_team = t.id_team 
                           $where
                           ORDER BY p.nom");
    $stmt->execute($params);
    while ($pl = $stmt->fetch()) {
        $experience = (int)date('Y') - (int)($pl['annee_debut'] ?? date('Y'));
        echo "<div class='card'>
                <h3>" . htmlspecialchars($pl['prenom']) . " " . htmlspecialchars($pl['nom']) . "</h3>
                <p><strong>Poste:</strong> " . htmlspecialchars($pl['poste']) . "</p>
                <p><strong>Équipe:</strong> <img src='" . htmlspecialchars($pl['logo_url']) . "' alt='' style='width:30px;height:30px;vertical-align:middle;'> " . htmlspecialchars($pl['nom_team']) . "</p>
                <p><strong>Âge:</strong> " . htmlspecialchars((string)($pl['age'] ?? '')) . " ans</p>
                <p><strong>Taille:</strong> " . htmlspecialchars((string)($pl['taille_cm'] ?? '')) . " cm - <strong>Poids:</strong> " . htmlspecialchars((string)($pl['poids_kg'] ?? '')) . " kg</p>
                <p><strong>Expérience:</strong> " . htmlspecialchars((string)$experience) . " ans</p>
              </div>";
    }
    ?>
</div>
