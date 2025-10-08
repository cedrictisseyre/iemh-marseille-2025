<?php
require 'connexion.php';

// Ajouter client
if (isset($_POST['ajouter'])) {
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $age = $_POST['age'];
    $stmt = $conn->prepare("INSERT INTO clients (prenom, nom, age) VALUES (?, ?, ?)");
    $stmt->execute([$prenom, $nom, $age]);
}

// Supprimer client
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
}

// Modifier client
if (isset($_POST['modifier'])) {
    $id = $_POST['id'];
    $prenom = $_POST['prenom'];
    $nom = $_POST['nom'];
    $age = $_POST['age'];
    $stmt = $conn->prepare("UPDATE clients SET prenom=?, nom=?, age=? WHERE id=?");
    $stmt->execute([$prenom, $nom, $age, $id]);
}

// Récupérer clients
$clients = $conn->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Clients</h2>
<form method="POST">
    Prénom: <input type="text" name="prenom" required>
    Nom: <input type="text" name="nom" required>
    Age: <input type="number" name="age" required>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<table border="1">
    <tr><th>ID</th><th>Prénom</th><th>Nom</th><th>Age</th><th>Actions</th></tr>
    <?php foreach ($clients as $client): ?>
        <tr>
            <td><?= $client['id'] ?></td>
            <td><?= $client['prenom'] ?></td>
            <td><?= $client['nom'] ?></td>
            <td><?= $client['age'] ?></td>
            <td>
                <a href="clients.php?supprimer=<?= $client['id'] ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
