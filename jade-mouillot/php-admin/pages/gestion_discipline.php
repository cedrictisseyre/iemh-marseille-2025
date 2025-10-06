<?php
require_once '../../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $stmt = $pdo->prepare("INSERT INTO discipline (nom) VALUES (?)");
    $stmt->execute([$nom]);
    echo "<p style='color:green'>Discipline ajoutée !</p>";
}

$disciplines = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Disciplines</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:20px; }
        .container { max-width:900px; margin:0 auto; background:#fff; padding:30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
        h1,h2 { color:#2c3e50; text-align:left; }
        table { width:100%; border-collapse:collapse; margin-top:20px; }
        th, td { border:1px solid #ccc; padding:8px; text-align:left; }
        th { background:#2980b9; color:#fff; }
        tr:nth-child(even){ background:#f9f9f9; }
        .form-section { margin-bottom:30px; }
        label { display:block; margin-top:10px; }
        input { padding:6px; width:100%; }
        button { margin-top:15px; padding:10px 20px; background:#2980b9; color:#fff; border:none; border-radius:4px; cursor:pointer; }
        button:hover { background:#1abc9c; }
        .nav { margin-bottom:20px; text-align:left; }
        .nav a { margin-right:15px; color:#2980b9; text-decoration:none; font-weight:bold; }
        .nav a:hover { text-decoration:underline; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="gestion_sportif.php">Sportif</a>
        <a href="gestion_club.php">Club</a>
        <a href="gestion_course.php">Course</a>
        <a href="gestion_discipline.php"><b>Discipline</b></a>
        <a href="gestion_participation.php">Participation</a>
    </div>
    <h1>Gestion des disciplines</h1>
    <h2>Ajouter une discipline</h2>
    <form method="post" class="form-section">
        <label>Nom : <input type="text" name="nom" required></label>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des disciplines</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
        </tr>
        <?php foreach($disciplines as $d): ?>
        <tr>
            <td><?= $d['id'] ?></td>
            <td><?= htmlspecialchars($d['nom']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
