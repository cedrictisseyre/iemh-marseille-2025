<?php
require_once 'connect.php';
include 'header.html';

// 🔹 Ajout d'une séance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $stmt = $conn->prepare("
        INSERT INTO seances (date_seance, type_seance, id_client, id_coach)
        VALUES (:date_seance, :type_seance, :id_client, :id_coach)
    ");
    $stmt->execute([
        ':date_seance' => $_POST['date_seance'],
        ':type_seance' => $_POST['type_seance'],
        ':id_client' => $_POST['id_client'],
        ':id_coach' => $_POST['id_coach']
    ]);
    echo "<div class='alert alert-success text-center'>✅ Séance ajoutée avec succès !</div>";
}

// 🔹 Suppression d'une séance
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $stmt = $conn->prepare("DELETE FROM seances WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo "<div class='alert alert-danger text-center'>🗑 Séance supprimée avec succès.</div>";
}

// 🔹 Récupération des données
$seances = $conn->query("
    SELECT s.id, s.date_seance, s.type_seance, c.prenom AS client_prenom, co.prenom AS coach_prenom
    FROM seances s
    JOIN clients c ON s.id_client = c.id
    JOIN coachs co ON s.id_coach = co.id
    ORDER BY s.date_seance DESC
")->fetchAll(PDO::FETCH_ASSOC);

$clients = $conn->query("SELECT id, prenom, nom FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
$coachs = $conn->query("SELECT id, prenom FROM coachs ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold text-primary">📅 Gestion des Séances</h1>
        <p class="text-muted fs-5">Planifie, visualise et gère toutes les séances d'entraînement de tes clients.</p>
    </div>

    <!-- 🟢 Formulaire d'ajout -->
    <div class="card shadow-sm border-0 mb-5">
        <div class="card-header bg-primary text-white fw-bold">➕ Ajouter une nouvelle séance</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <input type="hidden" name="ajouter" value="1">

                <div class="col-md-3">
                    <label class="form-label">Date :</label>
                    <input type="date" name="date_seance" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Type :</label>
                    <input type="text" name="type_seance" class="form-control" placeholder="Ex: Full body, Push, Legs..." required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Client :</label>
                    <select name="id_client" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['prenom'] . " " . $client['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Coach :</label>
                    <select name="id_coach" class="form-select" required>
                        <option value="">Sélectionner</option>
                        <?php foreach ($coachs as $coach): ?>
                            <option value="<?= $coach['id'] ?>"><?= htmlspecialchars($coach['prenom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-success px-5 py-2">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 📋 Tableau des séances -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark text-white fw-bold">📖 Liste des séances</div>
        <div class="card-body">
            <?php if (count($seances) > 0): ?>
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
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
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($s['date_seance']))) ?></td>
                            <td><span class="badge bg-info text-dark"><?= htmlspecialchars($s['type_seance']) ?></span></td>
                            <td><?= htmlspecialchars($s['client_prenom']) ?></td>
                            <td><?= htmlspecialchars($s['coach_prenom']) ?></td>
                            <td>
                                <a href="?supprimer=<?= $s['id'] ?>" onclick="return confirm('Supprimer cette séance ?')" class="btn btn-sm btn-danger">🗑 Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-muted mb-0">Aucune séance enregistrée pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="index.php" class="btn btn-secondary">🏠 Retour à l'accueil</a>
    </div>
</div>

<?php include 'footer.html'; ?>
