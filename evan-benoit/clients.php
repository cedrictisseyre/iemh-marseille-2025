<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>

<?php
include 'header.html';
require 'connexion.php';

// --- Suppression d’un client ---
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->execute([':id' => $id_delete]);
    echo "<div class='alert alert-danger text-center'>❌ Client supprimé avec succès.</div>";
}

// --- Ajout d’un client ---
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

// --- Récupération des clients ---
$clients = $conn->query("SELECT * FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<section class="section bg-white">
  <div class="container">
    <h2>👥 Gestion des Clients</h2>
    <p class="text-center mb-5">
      Gérez les informations de vos clients : ajoutez, modifiez ou supprimez leurs profils pour un suivi personnalisé.
    </p>

    <!-- Formulaire d’ajout -->
    <div class="card card-custom shadow-sm p-4 mb-5">
      <h4 class="text-primary mb-3">➕ Ajouter un client</h4>
      <form method="POST" class="row g-3">
        <input type="hidden" name="add" value="1">
        <div class="col-md-4">
          <label class="form-label">Prénom :</label>
          <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Nom :</label>
          <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Âge :</label>
          <input type="number" name="age" min="10" class="form-control" required>
        </div>
        <div class="col-12 text-center mt-3">
          <button type="submit" class="btn btn-primary px-4 py-2">Ajouter le client</button>
        </div>
      </form>
    </div>

    <!-- Liste des clients -->
    <div class="card shadow-sm p-4">
      <h4 class="text-dark mb-4">📋 Liste des clients</h4>

      <?php if (count($clients) > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped align-middle text-center">
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
                    <a href="modifier_client.php?id=<?= $client['id'] ?>" class="btn btn-warning btn-sm">✏️ Modifier</a>
                    <a href="?delete=<?= $client['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce client ?');">🗑 Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted text-center fst-italic">Aucun client enregistré pour le moment.</p>
      <?php endif; ?>
    </div>

    <div class="text-center mt-5">
      <a href="index.php" class="btn btn-outline-dark px-4 py-2">⬅️ Retour à l'accueil</a>
    </div>
  </div>
</section>

<?php include 'footer.html'; ?>
