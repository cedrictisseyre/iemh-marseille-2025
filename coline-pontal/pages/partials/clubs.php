<?php
// Section Clubs
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_club'])) {
    $stmt = $pdo->prepare("INSERT INTO club (nom_club, ville, pays, date_creation) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        $_POST['nom'], $_POST['ville'], $_POST['pays'], $_POST['date_creation']
    ]);
    echo '<p class="success">Club ajouté !</p>';
}

$stmt = $pdo->query("SELECT * FROM club");
echo '<h2>Liste des clubs</h2><ul class="list">';
while ($club = $stmt->fetch()) {
    echo "<li><a href='gestion-karate.php?page=clubs&club_id={$club['id_club']}'><strong>" . htmlspecialchars($club['nom_club']) . "</strong></a> (" . htmlspecialchars($club['ville']) . ", " . htmlspecialchars($club['pays']) . ")</li>";
}
echo '</ul>';

if (isset($_GET['club_id'])) {
    $cid = intval($_GET['club_id']);
    $stmt = $pdo->prepare("SELECT * FROM club WHERE id_club = ?");
    $stmt->execute([$cid]);
    $club = $stmt->fetch();
    if ($club) {
        echo "<h3>Karateka du club : " . htmlspecialchars($club['nom_club']) . "</h3>";
        $stmt2 = $pdo->prepare("SELECT * FROM karateka WHERE id_club = ?");
        $stmt2->execute([$cid]);
        echo '<ul class="list">';
        while ($k = $stmt2->fetch()) {
            echo "<li><span style='font-size:1.3em; margin-right:0.5em;'>🥋</span><strong>" . htmlspecialchars($k['prenom'] . ' ' . $k['nom']) . "</strong> (" . htmlspecialchars($k['grade']) . ")</li>";
        }
        echo '</ul>';
        echo '<p><a href="gestion-karate.php?page=clubs">Retour à la liste des clubs</a></p>';
    } else {
        echo '<p>Club introuvable.</p>';
    }
}

?>

<h3>Ajouter un club</h3>
<form method="post" aria-label="Ajouter un club">
    <input type="hidden" name="add_club" value="1">
    <label for="nom_club">Nom du club :</label>
    <input type="text" id="nom_club" name="nom" placeholder="Nom du club" required>
    <label for="ville">Ville :</label>
    <input type="text" id="ville" name="ville" placeholder="Ville" required>
    <label for="pays">Pays :</label>
    <input type="text" id="pays" name="pays" placeholder="Pays" required>
    <label for="date_creation">Date de création :</label>
    <input type="date" id="date_creation" name="date_creation" required>
    <button type="submit">Ajouter</button>
</form>
