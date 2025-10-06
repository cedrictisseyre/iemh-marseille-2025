<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    require_once 'connexion.php'; // Connexion à la base de données
} catch (Exception $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

include 'header.php';

//////////////////////////////////////
// 1️⃣ SUPPRESSION D'UNE SÉANCE
//////////////////////////////////////
if (isset($_GET['delete'])) {
    $id_delete = intval($_GET['delete']);
    if ($id_delete > 0) {
        $sql_delete = "DELETE FROM seances WHERE id = :id";
        $stmt = $conn->prepare($sql_delete);
        if ($stmt->execute([':id' => $id_delete])) {
            echo "<p style='color: red; text-align: center;'>❌ Séance supprimée avec succès.</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>⚠️ Erreur lors de la suppression.</p>";
        }
    }
}

//////////////////////////////////////
// 2️⃣ AJOUT D'UNE SÉANCE
//////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $date_seance = trim($_POST['date_seance']);
    $type_seance = trim($_POST['type_seance']);
    $id_client = intval($_POST['id_client']);
    $id_coach = intval($_POST['id_coach']);

    if (!empty($date_seance) && !empty($type_seance) && $id_client > 0 && $id_coach > 0) {
        $sql_insert = "INSERT INTO seances (date_seance, type_seance, id_client, id_coach)
                       VALUES (:date_seance, :type_seance, :id_client, :id_coach)";
        $stmt_insert = $conn->prepare($sql_insert);
        if ($stmt_insert->execute([
            ':date_seance' => $date_seance,
            ':type_seance' => $type_seance,
            ':id_client' => $id_client,
            ':id_coach' => $id_coach
        ])) {
            echo "<p style='color:green; text-align:center;'>✅ Séance ajoutée avec succès !</p>";
        } else {
            echo "<p style='color:red; text-align:center;'>⚠️ Erreur lors de l'ajout.</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>⚠️ Tous les champs doivent être remplis.</p>";
    }
}

//////////////////////////////////////
// 3️⃣ MODIFICATION D'UNE SÉANCE
//////////////////////////////////////
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id_update = intval($_POST['id']);
    $date_seance = trim($_POST['date_seance']);
    $type_seance = trim($_POST['type_seance']);
    $id_client = intval($_POST['id_client']);
    $id_coach = intval($_POST['id_coach']);

    if ($id_update > 0 && !empty($date_seance) && !empty($type_seance) && $id_client > 0 && $id_coach > 0) {
        $sql_update = "UPDATE seances 
                       SET date_seance = :date_seance,
                           type_seance = :type_seance,
                           id_client = :id_client,
                           id_coach = :id_coach
                       WHERE id = :id";
        $stmt_update = $conn->prepare($sql_update);
        if ($stmt_update->execute([
            ':date_seance' => $date_seance,
            ':type_seance' => $type_seance,
            ':id_client' => $id_client,
            ':id_coach' => $id_coach,
            ':id' => $id_update
        ])) {
            echo "<p style='color:orange; text-align:center;'>✏️ Séance modifiée avec succès.</p>";
        } else {
            echo "<p style='color:red; text-align:center;'>⚠️ Erreur lors de la modification.</p>";
        }
    } else {
        echo "<p style='color:red; text-align:center;'>⚠️ Tous les champs doivent être remplis pour la modification.</p>";
    }
}

//////////////////////////////////////
// 4️⃣ RECUPÉRATION DES DONNÉES
//////////////////////////////////////
try {
    $clients = $conn->query("SELECT id, prenom, nom FROM clients ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);
    $coachs = $conn->query("SELECT id, prenom, specialite FROM coachs ORDER BY prenom")->fetchAll(PDO::FETCH_ASSOC);

    $sql = "
        SELECT 
            s.id,
            s.date_seance,
            s.type_seance,
            CONCAT(c.prenom, ' ', c.nom) AS client,
            co.prenom AS coach,
            co.specialite,
            s.id_client,
            s.id_coach
        FROM seances s
        JOIN clients c ON s.id_client = c.id
        JOIN coachs co ON s.id_coach = co.id
        ORDER BY s.date_seance;
    ";
    $seances = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Erreur récupération données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Base Coaching Sportif</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f7f7f7; }
        h1, h2 { text-align: center; }
        form { background: white; padding: 20px; border-radius: 10px; width: 50%; margin: 20px auto; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 5px; }
        button { margin-top: 15px; background-color: #008CBA; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #005f73; }
        table { border-collapse: collapse; margin: 40px auto; width: 90%; background: white; }
        th, td { border: 1px solid #ccc; padding: 10px 15px; text-align: center; }
        th { background-color: #efefef; }
        .delete-btn { color: white; background-color: #d9534f; padding: 5px 10px; border-radius: 5px; text-decoration: none; }
        .delete-btn:hover { background-color: #c9302c; }
        .edit-form { background-color: #fff7e6; padding: 10px; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>🏋️ Base de données Coaching Sportif</h1>

    <h2>➕ Ajouter une séance</h2>
    <form method="POST">
        <input type="hidden" name="add" value="1">
        <label>Date :</label>
        <input type="date" name="date_seance" required>
        <label>Type de séance :</label>
        <input type="text" name="type_seance" required>
        <label>Client :</label>
        <select name="id_client" required>
            <option value="">-- Choisir un client --</option>
            <?php foreach ($clients as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Coach :</label>
        <select name="id_coach" required>
            <option value="">-- Choisir un coach --</option>
            <?php foreach ($coachs as $co): ?>
                <option value="<?= $co['id'] ?>"><?= htmlspecialchars($co['prenom']) ?> (<?= htmlspecialchars($co['specialite']) ?>)</option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Ajouter</button>
    </form>

    <h2>📅 Séances enregistrées</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Type</th>
            <th>Client</th>
            <th>Coach</th>
            <th>Spécialité</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($seances as $s): ?>
        <tr>
            <td><?= $s['id'] ?></td>
            <td><?= $s['date_seance'] ?></td>
            <td><?= htmlspecialchars($s['type_seance']) ?></td>
            <td><?= htmlspecialchars($s['client']) ?></td>
            <td><?= htmlspecialchars($s['coach']) ?></td>
            <td><?= htmlspecialchars($s['specialite']) ?></td>
            <td>
                <a class="delete-btn" href="?delete=<?= $s['id'] ?>" onclick="return confirm('Supprimer cette séance ?')">Supprimer</a>
                <a href="?edit=<?= $s['id'] ?>">Modifier</a>
            </td>
        </tr>
        <?php if (isset($_GET['edit']) && $_GET['edit'] == $s['id']): ?>
        <tr>
            <td colspan="7">
                <form method="POST" class="edit-form">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" value="<?= $s['id'] ?>">
                    <label>Date :</label>
                    <input type="date" name="date_seance" value="<?= $s['date_seance'] ?>" required>
                    <label>Type :</label>
                    <input type="text" name="type_seance" value="<?= htmlspecialchars($s['type_seance']) ?>" required>
                    <label>Client :</label>
                    <select name="id_client" required>
                        <?php foreach ($clients as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $s['id_client'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Coach :</label>
                    <select name="id_coach" required>
                        <?php foreach ($coachs as $co): ?>
                            <option value="<?= $co['id'] ?>" <?= $co['id'] == $s['id_coach'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($co['prenom']) ?> (<?= htmlspecialchars($co['specialite']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Enregistrer</button>
                </form>
            </td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>
    </table>

    <?php include 'footer.php'; ?>
</body>
</html>
