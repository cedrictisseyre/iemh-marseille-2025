<?php
// Section Championnats / Tournois
$db_ok = isset($pdo) && $pdo !== null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_champ'])) {
    if ($db_ok) {
        $stmt = $pdo->prepare("INSERT INTO championnat (nom_championnat, lieu, date_championnat, type) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom'], $_POST['lieu'], $_POST['date'], $_POST['type']
        ]);
        echo '<p class="success">Tournoi ajouté !</p>';
    } else {
        echo '<p class="success" style="color:#b91c1c;">Impossible : pas de connexion DB.</p>';
    }
}

$stmt = $db_ok ? $pdo->query("SELECT * FROM championnat") : null;
echo '<h2 class="title-with-icons"><svg class="small-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 3h12v2a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V3z" fill="currentColor"/><path d="M8 13a4 4 0 0 0 8 0v-1h1a1 1 0 0 1 1 1v3a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-3a1 1 0 0 1 1-1h1v1z" fill="currentColor"/></svg>Liste des tournois<svg class="small-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M6 3h12v2a3 3 0 0 1-3 3h-6a3 3 0 0 1-3-3V3z" fill="currentColor"/><path d="M8 13a4 4 0 0 0 8 0v-1h1a1 1 0 0 1 1 1v3a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2v-3a1 1 0 0 1 1-1h1v1z" fill="currentColor"/></svg></h2><ul class="list">';
if ($stmt) {
    while ($ch = $stmt->fetch()) {
        echo "<li><a href='gestion-karate.php?page=championnats&championnat_id={$ch['id_championnat']}'><strong>" . htmlspecialchars($ch['nom_championnat']) . "</strong></a> (" . htmlspecialchars($ch['lieu']) . ", " . htmlspecialchars($ch['date_championnat']) . ", " . htmlspecialchars($ch['type']) . ")</li>";
    }
} else {
    echo "<li class='meta'>Aucun tournoi listé (connexion DB manquante).</li>";
}
echo '</ul>';

if (isset($_GET['championnat_id'])) {
    $chid = intval($_GET['championnat_id']);
    $stmt = $pdo->prepare("SELECT * FROM championnat WHERE id_championnat = ?");
    $stmt->execute([$chid]);
    $champ = $stmt->fetch();
    if ($champ) {
        echo "<h3>Participants au championnat : " . htmlspecialchars($champ['nom_championnat']) . "</h3>";
        $stmt2 = $pdo->prepare("SELECT p.*, k.nom, k.prenom, k.grade, c.nom_club FROM participation p JOIN karateka k ON p.id_karateka = k.id_karateka JOIN club c ON k.id_club = c.id_club WHERE p.id_championnat = ?");
        $stmt2->execute([$chid]);
        echo '<ul class="list">';
        while ($part = $stmt2->fetch()) {
            echo "<li><div><strong>" . htmlspecialchars($part['prenom'] . ' ' . $part['nom']) . "</strong> (<span class='meta'>" . htmlspecialchars($part['nom_club']) . "</span>)</div><div class='meta'>Épreuve: " . htmlspecialchars($part['epreuve']) . " | Sexe: " . htmlspecialchars($part['sexe']) . " | Équipe: " . htmlspecialchars($part['equipe']) . " | Catégorie: " . htmlspecialchars($part['categorie']) . " | Résultat: " . htmlspecialchars($part['resultat']) . "</div></li>";
        }
        echo '</ul>';
        echo '<p><a href="gestion-karate.php?page=championnats">Retour à la liste des tournois</a></p>';
    } else {
        echo '<p>Championnat introuvable.</p>';
    }
}

?>

<h3 class="add-title">Ajouter un tournoi</h3>
<form method="post" aria-label="Ajouter un tournoi">
    <input type="hidden" name="add_champ" value="1">
    <label for="nom_championnat">Nom du tournoi :</label>
    <input type="text" id="nom_championnat" name="nom" placeholder="Nom du tournoi" required>
    <label for="lieu">Lieu :</label>
    <input type="text" id="lieu" name="lieu" placeholder="Lieu" required>
    <label for="date_championnat">Date :</label>
    <input type="date" id="date_championnat" name="date" required>
    <label for="type">Type :</label>
    <select id="type" name="type" required>
        <option value="Régional">Régional</option>
        <option value="National">National</option>
        <option value="International">International</option>
    </select>
    <button type="submit">Ajouter</button>
</form>
