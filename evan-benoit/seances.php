<?php
require 'connexion.php';

// Ajouter séance
if (isset($_POST['ajouter'])) {
    $date_seance = $_POST['date_seance'];
    $type_seance = $_POST['type_seance'];
    $id_client = $_POST['id_client'];
    $id_coach = $_POST['id_coach'];
    $stmt = $conn->prepare("INSERT INTO seances (date_seance, type_seance, id_client, id_coach) VALUES (?, ?, ?, ?)");
    $stmt->execute([$date_seance, $type_seance, $id_client, $id_coach]);
}

// Supprimer séance
if (isset($_GET['supprimer'])) {
    $id = $_GET['supprimer'];
    $stmt = $conn->prepare("DELETE FROM seances WHERE id = ?");
    $stmt->execute([$id]);
}

$seances = $conn->query("
    SELECT s.id, s.date_seance, s.type_seance, c.prenom AS client_prenom, co.prenom AS coach_prenom
    FROM seances s
    JOIN clients c ON s.id_client = c.id
    JOIN coachs co ON s.id_coach = co.id
")->fetchAll(PDO::FETCH_ASSOC);

$clients = $conn->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
$coachs = $conn->query("SELECT * FROM coachs")->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Séances</h2>

<form method="POST">
    Date: <input type="date" name="date_seance" required>
    Type: <input type="text" name="type_seance" required>
    Client: 
    <select name="id_client" required>
        <?php foreach ($clients as $client): ?>
            <option value="<?= $client['id'] ?>"><?= $client['prenom'] . " " . $client['nom'] ?></option>
        <?php endforeach; ?>
    </select>
    Coach: 
    <select name="id_coach" required>
        <?php foreach ($coachs as $coach): ?>
            <option value="<?= $coach['id'] ?>"><?= $coach['prenom'] ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<table border="1">
    <tr><th>ID</th><th>Date</th><th>Type</th><th>Client</th><th>Coach</th><th>Actions</th></tr>
    <?php foreach ($seances as $seance): ?>
        <tr>
            <td><?= $seance['id'] ?></td>
            <td><?= $seance['date_seance'] ?></td>
            <td><?= $seance['type_seance'] ?></td>
            <td><?= $seance['client_prenom'] ?></td>
            <td><?= $seance['coach_prenom'] ?></td>
            <td>
                <a href="seances.php?supprimer=<?= $seance['id'] ?>">Supprimer</a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
