<?php
require_once '../../config/db_connect.php';

// --- AJOUT D'UN CLUB ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $stmt = $pdo->prepare("INSERT INTO club (nom) VALUES (?)");
    $stmt->execute([$nom]);
    echo "<p style='color:green'>Club ajouté !</p>";
}

// --- SUPPRESSION D'UN CLUB ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM club WHERE id = ?");
    $stmt->execute([$id]);
    echo "<p style='color:red'>Club supprimé !</p>";
}

// --- RÉCUPÉRATION DES CLUBS ---
$sql = "SELECT c.id, c.nom FROM club c";
$clubs = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des clubs</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #2980b9; color: #fff; }
        tr:nth-child(even) { background: #f9f9f9; }
        .form-section { margin-bottom: 30px; }
        label { display: block; margin-top: 10px; }
        input, select { padding: 6px; width: 100%; }
        button { margin-top: 5px; padding: 6px 12px; background: #2980b9; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1abc9c; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #2980b9; text-decoration: none; font-weight: bold; }
        .nav a:hover { text-decoration: underline; }
        .delete-btn { background: #e74c3c; }
        .delete-btn:hover { background: #c0392b; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="gestion_sportif.php">Sportif</a>
        <a href="gestion_club.php"><b>Club</b></a>
        <a href="gestion_course.php">Course</a>
        <a href="gestion_discipline.php">Discipline</a>
        <a href="gestion_participation.php">Participation</a>
    </div>

    <h1>Gestion des clubs</h1>

    <h2>Ajouter un club</h2>
    <form method="post" class="form-section">
        <label>Nom : <input type="text" name="nom" required></label>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des clubs</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($clubs as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nom']) ?></td>
            <td>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $c['id'] ?>">
                    <button type="submit" name="supprimer" class="delete-btn" onclick="return confirm('Supprimer ce club ?')">Supprimer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
