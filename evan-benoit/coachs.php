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

// --- Récupération des coachs ---
$coachs = $conn->query("SELECT * FROM coachs ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);

// --- Ajout d’un coach ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $prenom = trim($_POST['prenom']);
    $specialite = trim($_POST['specialite']);

    if ($prenom && $specialite) {
        $stmt = $conn->prepare("INSERT INTO coachs (prenom, specialite) VALUES (:prenom, :specialite)");
        $stmt->execute([':prenom' => $prenom, ':specialite' => $specialite]);
        echo "<div class='alert alert-success text-center'>✅ Coach ajouté avec succès.</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Tous les champs doivent être remplis correctement.</div>";
    }
}

// --- Suppression d’un coach ---
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM coachs WHERE id = :id");
    $stmt->execute([':id' => $id_delete]);
    echo "<div class='alert alert-danger text-center'>❌ Coach supprimé avec succès.</div>";
}
?>

<section class="section bg-white">
  <div class="container">
    <h2>💪 Gestion des Coachs</h2>
    <p class="text-center mb-5">Ajoutez, modifiez ou supprimez les coachs de votre équipe EB Coaching.</p>

    <!-- Formulaire d’ajout -->
    <div class="card shadow-sm p-4 mb-5">
      <h4 class="text-primary mb-3">➕ Ajouter un coach</h4>
      <form method="POST" class="row g-3">
        <input type="hidden" name="add" value="1">
        <div class="col-md-6">
          <label class="form-label">Prénom :</label>
          <input type="text" name="prenom" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Spécialité :</label>
          <input type="text" name="specialite" class="form-control" required>
        </div>
        <div class="col-12 text-center mt-3">
          <button type="submit" class="btn btn-primary px-4 py-2">Ajouter le coach</button>
        </div>
      </form>
    </div>

    <!-- Liste des coachs -->
    <div class="card shadow-sm p-4">
      <h4 class="text-dark mb-4">📋 Liste des coachs</h4>

      <?php if (count($coachs) > 0): ?>
        <div class="table-responsive">
          <table class="table table-striped align-middle text-center">
            <thead class="table-light">
              <tr>
                <th>ID</th>
                <th>Prénom</th>
                <th>Spécialité</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($coachs as $coach): ?>
                <tr>
                  <td><?= $coach['id'] ?></td>
                  <td><?= htmlspecialchars($coach['prenom']) ?></td>
                  <td><?= htmlspecialchars($coach['specialite']) ?></td>
                  <td>
                    <a href="?delete=<?= $coach['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce coach ?');">🗑 Supprimer</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted text-center fst-italic">Aucun coach enregistré pour le moment.</p>
      <?php endif; ?>
    </div>

    <div class="text-center mt-5">
      <a href="index.php" class="btn btn-outline-dark px-4 py-2">⬅️ Retour à l'accueil</a>
    </div>
  </div>
</section>

<?php include 'footer.html'; ?>
