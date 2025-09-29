<?php
require_once '../db_connect.php';

$clubs = $pdo->query("SELECT id, nom FROM club")->fetchAll();
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();
$disciplines = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $id_course = $_POST['id_course'];
    $id_discipline = $_POST['id_discipline'];
    $id_club = $_POST['id_club'];
    $stmt = $pdo->prepare("INSERT INTO sportif (nom, id_course, id_discipline, id_club) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nom, $id_course, $id_discipline, $id_club]);
    echo "<p style='color:green'>Sportif ajouté !</p>";
}

$sql = "SELECT s.id, s.nom, c.nom AS club, co.nom AS course, d.nom AS discipline
        FROM sportif s
        LEFT JOIN club c ON s.id_club = c.id
        LEFT JOIN course co ON s.id_course = co.id
        LEFT JOIN discipline d ON s.id_discipline = d.id";
$sportifs = $pdo->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sportifs</title>
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
        <a href="sportif.php">Sportif</a>
        <a href="club.php">Club</a>
        <a href="course.php">Course</a>
        <a href="discipline.php">Discipline</a>
    </div>
    <h1>Ajouter un sportif</h1>
    <form method="post" class="form-section">
        <label>Nom : <input type="text" name="nom" required></label>
        <label>Course :
            <select name="id_course" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Discipline :
            <select name="id_discipline" required>
                <?php foreach ($disciplines as $discipline): ?>
                    <option value="<?= $discipline['id'] ?>"><?= htmlspecialchars($discipline['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Club :
            <select name="id_club" required>
                <?php foreach ($clubs as $club): ?>
                    <option value="<?= $club['id'] ?>"><?= htmlspecialchars($club['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des sportifs</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Club</th>
            <th>Course</th>
            <th>Discipline</th>
        </tr>
        <?php foreach ($sportifs as $s): ?>
        <tr>
            <td><?= $s['id'] ?></td>
            <td><?= htmlspecialchars($s['nom']) ?></td>
            <td><?= htmlspecialchars($s['club']) ?></td>
            <td><?= htmlspecialchars($s['course']) ?></td>
            <td><?= htmlspecialchars($s['discipline']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
