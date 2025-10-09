<?php
require_once 'connexion.php';
include 'header.php';

// Suppression
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->execute([':id' => $id_delete]);
    echo "<div class='alert alert-danger text-center'>❌ Client supprimé avec succès.</div>";
}

// Ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $age = intval($_POST['age']);

    if ($prenom && $nom && $age > 0) {
        $stmt = $conn->prepare("INSERT INTO clients (prenom, nom, age) VALUES (:prenom, :nom, :age)");
        $stmt->execute([':prenom' => $prenom, ':nom' => $nom, ':age' => $age]);
        echo "<div class='alert alert-success text-center'>✅ Client ajouté avec succès.</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Tous les champs doivent être remplis correctement.</div>";
    }
}

$clients = $conn->query("SELECT * FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>👥 Gestion des Clients</h1>
</div>

<!-- Formulaire d'ajout -->
<div class="card shadow-sm mb-5">
    <div class="card-header bg-primary text-white">➕ Ajouter un client</div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="add" value="1">
            <div class="col-md-4">
                <label>Prénom :</label>
                <input type="text" name="prenom" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Nom :</label>
                <input type="text" name="nom" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Âge :</label>
                <input type="number" name="age" class="form-control" required>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success px-4">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">📋 Liste des clients</div>
    <div class="card-body">
        <table class="table table-striped text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Âge</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= $client['id'] ?></td>
                    <td><?= htmlspecialchars($client['prenom']) ?></td>
                    <td><?= htmlspecialchars($client['nom']) ?></td>
                    <td><?= $client['age'] ?></td>
                    <td>
                        <a href="modif_client.php?id=<?= $client['id'] ?>" class="btn btn-warning btn-sm">✏️ Modifier</a>
                        <a href="?delete=<?= $client['id'] ?>" onclick="return confirm('Supprimer ce client ?')" class="btn btn-danger btn-sm">🗑 Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
