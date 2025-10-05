<?php
require 'connexion.php';

// Ajouter coach
if (isset($_POST['ajouter'])) {
    $prenom = $_POST['prenom'];
    $specialite = $_POST['specialite'];
    $stmt = $conn->prepare("INSERT INTO coachs (prenom, specialite) VALUES (?, ?)");
    $stmt->execute([$prenom, $specialite]);
}

// Supprimer coach
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM coachs WHERE id = ?");
    $stmt->execute([$id]);
}

// Modifier coach
if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $prenom = $_POST['prenom'];
    $specialite = $_POST['specialite'];
    $stmt = $conn->prepare("UPDATE coachs SET prenom=?, specialite=? WHERE id=?");
    $stmt->execute([$prenom, $specialite, $id]);
}

$coachs = $conn->query("SELECT * FROM coachs")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Coachs</h2>

<form method="POST">
    Prénom: <input type="text" name="prenom" required>
    Spécialité: <input type="text" name="specialite" required>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<table border="1">
    <tr><th>ID</th><th>Prénom</th><th>Spécialité</th><th>Actions</th></tr>
    <?php foreach ($coachs as $coach): ?>
        <tr>
            <td><?= $coach['id'] ?></td>
            <td><?= $coach['prenom'] ?></td>
            <td><?= $coach['specialite'] ?></td>
            <td>
                <a href="coachs.php?supprimer=<?= $coach['id'] ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
