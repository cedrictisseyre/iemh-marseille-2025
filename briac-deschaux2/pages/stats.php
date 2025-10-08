<?php 
// pages/stats.php
declare(strict_types=1); 
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
} 

$saison = date('Y'); 
$added = $_GET['added'] ?? ''; 
$error = $_GET['error'] ?? ''; 
?>

<div class="card">
    <h2>Ajouter des statistiques (Saison <?= htmlspecialchars($saison) ?>)</h2>
    <?php if ($added === '1'): ?>
        <p style="color:green;">Statistiques ajoutées.</p>
    <?php elseif ($error === 'duplicate'): ?>
        <p style="color:orange;">Statistiques déjà renseignées pour ce joueur & cette saison.</p>
    <?php elseif ($error !== ''): ?>
        <p style="color:red;">Erreur lors de l'ajout des statistiques.</p>
    <?php endif; ?>

    <form method="post" action="services/add_stats.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <select name="id_player" required>
            <option value="">Sélectionner un joueur</option>
            <?php 
            $players = $pdo->query("SELECT id_player, prenom, nom FROM player ORDER BY nom")->fetchAll();
            foreach ($players as $p) {
                echo "<option value='" . (int)$p['id_player'] . "'>" . htmlspecialchars($p['prenom'] . ' ' . $p['nom']) . "</option>";
            }
            ?>
        </select>

        <!-- Champs (noms identiques aux colonnes de la table stats) -->
        <input type="number" name="passing_yards" placeholder="Yards passés" min="0">
        <input type="number" name="passing_tds" placeholder="TD passés" min="0">
        <input type="number" name="interceptions" placeholder="Interceptions" min="0">
        <input type="number" name="rushing_yards" placeholder="Yards course" min="0">
        <input type="number" name="rushing_tds" placeholder="TD course" min="0">
        <input type="number" name="receptions" placeholder="Réceptions" min="0">
        <input type="number" name="receiving_yards" placeholder="Yards réception" min="0">
        <input type="number" name="receiving_tds" placeholder="TD réception" min="0">
        <input type="number" name="tackles" placeholder="Plaquages" min="0">
        <input type="number" step="0.1" name="sacks" placeholder="Sacks" min="0">
        <input type="number" name="interceptions_def" placeholder="Interceptions déf" min="0">

        <!-- Kickers / Punters -->
        <input type="number" name="field_goals_made" placeholder="Field Goals marqués" min="0">
        <input type="number" name="field_goals_attempted" placeholder="Field Goals tentés" min="0">
        <input type="number" name="extra_points_made" placeholder="Extra Points marqués" min="0">
        <input type="number" name="extra_points_attempted" placeholder="Extra Points tentés" min="0">
        <input type="number" name="punts" placeholder="Punts" min="0">
        <input type="number" name="punt_yards" placeholder="Yards punts" min="0">
        <input type="number" name="longest_punt" placeholder="Plus long punt" min="0">
        <input type="number" name="inside_20" placeholder="Punts inside 20" min="0">

        <button type="submit">Ajouter les stats</button>
    </form>
</div>

<!-- Recherche joueurs -->
<div class="card">
    <h2>Recherche stats joueur</h2>
    <form method="get">
        <input type="hidden" name="page" value="stats">
        <input type="text" name="recherche" placeholder="Nom ou prénom">
        <button type="submit">Rechercher</button>
    </form>
</div>

<!-- Affichage stats -->
<h2>Statistiques <?= htmlspecialchars((string)$saison) ?></h2>
<div class="grid">
<?php
$where = "WHERE s.saison = ?";
$params = [$saison];

if (!empty($_GET['recherche'])) {
    $search = "%" . $_GET['recherche'] . "%";
    $where .= " AND (p.nom LIKE ? OR p.prenom LIKE ? OR CONCAT(p.prenom,' ',p.nom) LIKE ? OR CONCAT(p.nom,' ',p.prenom) LIKE ?)";
    $params = array_merge($params, [$search,$search,$search,$search]);
}

$stmt = $pdo->prepare("SELECT s.*, p.prenom, p.nom, p.poste, t.nom_team, t.logo_url 
    FROM stats s 
    JOIN player p ON s.id_player = p.id_player 
    JOIN team t ON p.id_team = t.id_team 
    $where 
    ORDER BY p.nom");
$stmt->execute($params);

$has_stats = false;
while ($st = $stmt->fetch()) {
    $has_stats = true;
    echo "<div class='card'> 
            <h3><img src='" . htmlspecialchars($st['logo_url']) . "' alt='' style='width:30px;height:30px;vertical-align:middle;margin-right:5px;'> 
            " . htmlspecialchars($st['prenom'] . ' ' . $st['nom'] . " ({$st['poste']})") . "</h3>";

    // Affichage spécial Kicker
    if ($st['poste'] === 'K') {
        echo "<p><strong>Field Goals :</strong> " . (int)$st['field_goals_made'] . "/" . (int)$st['field_goals_attempted'] . "</p>";
        echo "<p><strong>Extra Points :</strong> " . (int)$st['extra_points_made'] . "/" . (int)$st['extra_points_attempted'] . "</p>";
    }
    // Affichage spécial Punter
    elseif ($st['poste'] === 'P') {
        echo "<p><strong>Punts :</strong> " . (int)$st['punts'] . " (Yards: " . (int)$st['punt_yards'] . 
            ", Longest: " . (int)$st['longest_punt'] . ", Inside 20: " . (int)$st['inside_20'] . ")</p>";
    }
    // Affichage générique pour tous les autres joueurs
    else {
        foreach ($st as $key => $val) {
            if (in_array($key, ['id_stat','id_player','prenom','nom','poste','saison','nom_team','logo_url',
                                'field_goals_made','field_goals_attempted','extra_points_made','extra_points_attempted',
                                'punts','punt_yards','longest_punt','inside_20'], true)) continue;
            if ($val !== null) {
                $label = ucfirst(str_replace("_", " ", $key));
                echo "<p><strong>" . htmlspecialchars($label) . ":</strong> " . htmlspecialchars((string)$val) . "</p>";
            }
        }
    }

    echo "</div>";
}
if (!$has_stats) {
    echo "<p>Aucune statistique disponible pour cette saison.</p>";
}
?>
</div>
