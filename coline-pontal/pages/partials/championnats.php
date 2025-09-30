<?php
// Section Championnats / Tournois
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_champ'])) {
    $stmt = $pdo->prepare("INSERT INTO championnat (nom_championnat, lieu, date_championnat, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nom'], $_POST['lieu'], $_POST['date'], $_POST['type']
    ]);
    echo '<p class="success">Tournoi ajouté !</p>';
}

$stmt = $pdo->query("SELECT * FROM championnat");
echo '<h2>Liste des tournois</h2><ul class="list">';
while ($ch = $stmt->fetch()) {
    echo "<li><a href='gestion-karate.php?page=championnats&championnat_id={$ch['id_championnat']}'><strong>" . htmlspecialchars($ch['nom_championnat']) . "</strong></a> (" . htmlspecialchars($ch['lieu']) . ", " . htmlspecialchars($ch['date_championnat']) . ", " . htmlspecialchars($ch['type']) . ")</li>";
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
            echo "<li><span style='font-size:1.3em; margin-right:0.5em;'>🥋</span><strong>" . htmlspecialchars($part['prenom'] . ' ' . $part['nom']) . "</strong> (" . htmlspecialchars($part['nom_club']) . ")<br>Épreuve : " . htmlspecialchars($part['epreuve']) . " | Sexe : " . htmlspecialchars($part['sexe']) . " | Équipe : " . htmlspecialchars($part['equipe']) . "<br>Catégorie : " . htmlspecialchars($part['categorie']) . " | Résultat : " . htmlspecialchars($part['resultat']) . "</li>";
        }
        echo '</ul>';
        echo '<p><a href="gestion-karate.php?page=championnats">Retour à la liste des tournois</a></p>';
    } else {
        echo '<p>Championnat introuvable.</p>';
    }
}

?>

<h3>Ajouter un tournoi</h3>
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
