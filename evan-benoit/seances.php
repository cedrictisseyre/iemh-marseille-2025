<?php
require_once 'connexion.php';
include 'header.php';

// Ajout
if (isset($_POST['ajouter'])) {
    $date_seance = $_POST['date_seance'];
    $type_seance = $_POST['type_seance'];
    $id_client = $_POST['id_client'];
    $id_coach = $_POST['id_coach'];
    $stmt = $conn->prepare("INSERT INTO seances (date_seance, type_seance, id_client, id_coach) VALUES (?, ?, ?, ?)");
    $stmt->execute([$date_seance, $type_seance, $id_client, $id_coach]);
}

// Suppression
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
    ORDER BY s.date_seance DESC
")->fetchAll(PDO::FETCH_ASSOC);

$clients = $conn->query("SELECT * FROM clients")->fetchAll(PDO::FETCH_ASSOC);
$coachs = $conn->query("SELECT * FROM coachs")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>📅 Gestion des Séances</h1>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-header bg-success text-white">➕ Ajouter une séance</div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <div class="col-md-3">
                <label>Date :</label>
                <input type="date" name="date_seance" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Type :</label>
                <input type="text" name="type_seance" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label>Client :</label>
                <select name="id_client" class="form-select" required>
                    <?php foreach ($clients as $client): ?>
                        <option value="<?= $client['id'] ?>"><?= $client['prenom'] . " " . $client['nom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Coach :</label>
                <select name="id_coach" class="form-select" required>
                    <?php foreach ($coachs as $coach): ?>
                        <option value="<?= $coach['id'] ?>"><?= $coach['prenom'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 text-center">
                <button type="submit" name="ajouter" class="btn btn-success px-4">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">📋 Liste des séances</div>
    <div class="card-body">
        <table class="table table-striped text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Client</th>
                    <th>Coach</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($seances as $s): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= $s['date_seance'] ?></td>
                    <td><?= htmlspecialchars($s['type_seance']) ?></td>
                    <td><?= htmlspecialchars($s['client_prenom']) ?></td>
                    <td><?= htmlspecialchars($s['coach_prenom']) ?></td>
                    <td>
                        <a href="?supprimer=<?= $s['id'] ?>" onclick="return confirm('Supprimer cette séance ?')" class="btn btn-danger btn-sm">🗑</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
