<?php
require_once 'connexion.php';
include 'header.php';

// -------------------------------------------------------
// 🔴 SUPPRESSION D’UNE MESURE
// -------------------------------------------------------
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM suivi_masse WHERE id = :id");
    $stmt->execute([':id' => $id_delete]);
    echo "<div class='alert alert-danger text-center'>❌ Mesure supprimée avec succès.</div>";
}

// -------------------------------------------------------
// 🟢 AJOUT D’UNE MESURE
// -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $id_client = intval($_POST['id_client']);
    $date_mesure = trim($_POST['date_mesure']);
    $masse = floatval($_POST['masse']);

    if ($id_client > 0 && !empty($date_mesure) && $masse > 0) {
        $stmt = $conn->prepare("
            INSERT INTO suivi_masse (id_client, date_mesure, masse)
            VALUES (:id_client, :date_mesure, :masse)
        ");
        $stmt->execute([
            ':id_client' => $id_client,
            ':date_mesure' => $date_mesure,
            ':masse' => $masse
        ]);
        echo "<div class='alert alert-success text-center'>✅ Mesure ajoutée avec succès.</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Tous les champs doivent être remplis correctement.</div>";
    }
}

// -------------------------------------------------------
// ✏️ MODIFICATION D’UNE MESURE
// -------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $id_client = intval($_POST['id_client']);
    $date_mesure = trim($_POST['date_mesure']);
    $masse = floatval($_POST['masse']);

    if ($id > 0 && $id_client > 0 && !empty($date_mesure) && $masse > 0) {
        $stmt = $conn->prepare("
            UPDATE suivi_masse 
            SET id_client = :id_client, date_mesure = :date_mesure, masse = :masse
            WHERE id = :id
        ");
        $stmt->execute([
            ':id' => $id,
            ':id_client' => $id_client,
            ':date_mesure' => $date_mesure,
            ':masse' => $masse
        ]);
        echo "<div class='alert alert-info text-center'>✏️ Mesure modifiée avec succès.</div>";
    } else {
        echo "<div class='alert alert-warning text-center'>⚠️ Données invalides pour la modification.</div>";
    }
}

// -------------------------------------------------------
// 📋 RÉCUPÉRATION DES DONNÉES
// -------------------------------------------------------
$clients = $conn->query("SELECT id, prenom, nom FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
$sql = "
    SELECT sm.id, sm.date_mesure, sm.masse, c.prenom, c.nom, sm.id_client
    FROM suivi_masse sm
    JOIN clients c ON sm.id_client = c.id
    ORDER BY sm.date_mesure DESC
";
$mesures = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="text-center my-4">
    <h1>📊 Suivi de la Masse Corporelle</h1>
</div>

<!-- 🟢 Formulaire d’ajout -->
<div class="card shadow-sm mb-5">
    <div class="card-header bg-primary text-white">➕ Ajouter une mesure</div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <input type="hidden" name="add" value="1">
            <div class="col-md-4">
                <label>Client :</label>
                <select name="id_client" class="form-control" required>
                    <option value="">-- Choisir un client --</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label>Date :</label>
                <input type="date" name="date_mesure" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Masse (kg) :</label>
                <input type="number" step="0.1" name="masse" class="form-control" required>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success px-4">Ajouter</button>
            </div>
        </form>
    </div>
</div>

<!-- 📋 Tableau des mesures -->
<div class="card shadow-sm">
    <div class="card-header bg-dark text-white">📅 Historique des mesures</div>
    <div class="card-body">
        <table class="table table-striped text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Client</th>
                    <th>Masse (kg)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mesures as $m): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td><?= $m['date_mesure'] ?></td>
                    <td><?= htmlspecialchars($m['prenom'].' '.$m['nom']) ?></td>
                    <td><?= $m['masse'] ?></td>
                    <td>
                        <a href="?edit=<?= $m['id'] ?>" class="btn btn-warning btn-sm">✏️ Modifier</a>
                        <a href="?delete=<?= $m['id'] ?>" onclick="return confirm('Supprimer cette mesure ?')" class="btn btn-danger btn-sm">🗑 Supprimer</a>
                    </td>
                </tr>

                <?php if (isset($_GET['edit']) && $_GET['edit'] == $m['id']): ?>
                <tr>
                    <td colspan="5">
                        <form method="POST" class="row g-2 justify-content-center">
                            <input type="hidden" name="update" value="1">
                            <input type="hidden" name="id" value="<?= $m['id'] ?>">
                            <div class="col-md-3">
                                <select name="id_client" class="form-control" required>
                                    <?php foreach ($clients as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $m['id_client'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="date_mesure" value="<?= $m['date_mesure'] ?>" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" step="0.1" name="masse" value="<?= $m['masse'] ?>" class="form-control" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info w-100">💾 Enregistrer</button>
                            </div>
                        </form>
                    </td>
                </tr>
                <?php endif; ?>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>
