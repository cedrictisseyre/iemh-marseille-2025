<?php
require_once '../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $stmt = $pdo->prepare("INSERT INTO club (nom) VALUES (?)");
    $stmt->execute([$nom]);
    echo "<p style='color:green'>Club ajouté !</p>";
}

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
        button { margin-top: 15px; padding: 10px 20px; background: #2980b9; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1abc9c; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; color: #2980b9; text-decoration: none; font-weight: bold; }
        .nav a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="gestion_sportif.php">Sportif</a>
        <a href="gestion_club.php">Club</a>
        <a href="gestion_course.php">Course</a>
        <a href="gestion_discipline.php">Discipline</a>
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
        </tr>
        <?php foreach ($clubs as $c): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><?= htmlspecialchars($c['nom']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
