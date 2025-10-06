<?php
require 'connexion.php';

// Ajouter masse
if (isset($_POST['ajouter'])) {
    $id_client = $_POST['id_client'];
    $date_mesure = $_POST['date_mesure'];
    $masse = $_POST['masse'];
    $stmt = $conn->prepare("INSERT INTO suivi_masse (id_client, date_mesure, masse) VALUES (?, ?, ?)");
    $stmt->execute([$id_client, $date_mesure, $masse]);
}

// Supprimer masse
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM suivi_masse WHERE id = ?");
    $stmt->execute([$id]);
}

$masse = $conn->query("
    SELECT m.id, m.date_mesure, m.masse, c.prenom, c.nom
    FROM suivi_masse m
    JOIN clients c ON m.id_client = c.id
")->fetchAll(PDO::FETCH_ASSOC);

$clients = $conn->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Suivi Masse</h2>

<form method="POST">
    Date: <input type="date" name="date_mesure" required>
    Masse (kg): <input type="number" step="0.1" name="masse" required>
    Client:
    <select name="id_client" required>
        <?php foreach ($clients as $client): ?>
            <option value="<?= $client['id'] ?>"><?= $client['prenom'] . " " . $client['nom'] ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<table border="1">
    <tr><th>ID</th><th>Date</th><th>Masse</th><th>Client</th><th>Actions</th></tr>
    <?php foreach ($masse as $m): ?>
        <tr>
            <td><?= $m['id'] ?></td>
            <td><?= $m['date_mesure'] ?></td>
            <td><?= $m['masse'] ?> kg</td>
            <td><?= $m['prenom'] . " " . $m['nom'] ?></td>
            <td>
                <a href="masse.php?supprimer=<?= $m['id'] ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
