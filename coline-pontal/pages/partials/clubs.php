<?php
// Section Clubs
$db_ok = isset($pdo) && $pdo !== null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_club'])) {
    if ($db_ok) {
        $stmt = $pdo->prepare("INSERT INTO club (nom_club, ville, pays, date_creation) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom'], $_POST['ville'], $_POST['pays'], $_POST['date_creation']
        ]);
        echo '<p class="success">Club ajouté !</p>';
    } else {
        echo '<p class="success" style="color:#b91c1c;">Impossible : pas de connexion DB.</p>';
    }
}

$stmt = $db_ok ? $pdo->query("SELECT * FROM club") : null;
echo '<h2 class="title-with-icons"><svg class="small-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 2l3 6h6l-5 4 2 6-6-3-6 3 2-6-5-4h6z" fill="currentColor"/></svg>Liste des clubs<svg class="small-icon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 2l3 6h6l-5 4 2 6-6-3-6 3 2-6-5-4h6z" fill="currentColor"/></svg></h2><ul class="list">';
if ($stmt) {
    while ($club = $stmt->fetch()) {
        echo "<li><a href='gestion-karate.php?page=clubs&club_id={$club['id_club']}'><strong>" . htmlspecialchars($club['nom_club']) . "</strong></a> (" . htmlspecialchars($club['ville']) . ", " . htmlspecialchars($club['pays']) . ")</li>";
    }
} else {
    echo "<li class='meta'>Aucun club listé (connexion DB manquante).</li>";
}
echo '</ul>';

if (isset($_GET['club_id'])) {
    $cid = intval($_GET['club_id']);
    $stmt = $pdo->prepare("SELECT * FROM club WHERE id_club = ?");
    $stmt->execute([$cid]);
    $club = $stmt->fetch();
    if ($club) {
        echo "<h3>Karateka du club : " . htmlspecialchars($club['nom_club']) . "</h3>";
        if ($db_ok) {
            $stmt2 = $pdo->prepare("SELECT * FROM karateka WHERE id_club = ?");
            $stmt2->execute([$cid]);
            echo '<ul class="list">';
            while ($k = $stmt2->fetch()) {
                echo "<li><strong>" . htmlspecialchars($k['prenom'] . ' ' . $k['nom']) . "</strong> (<span class='meta'>" . htmlspecialchars($k['grade']) . "</span>)</li>";
            }
            echo '</ul>';
        } else {
            echo '<p class="meta">Impossible d\'afficher les karateka : connexion DB manquante.</p>';
        }
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
