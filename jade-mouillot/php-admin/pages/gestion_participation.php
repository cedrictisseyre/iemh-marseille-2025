<?php
require_once '../db_connect.php';

// Récupérer les sportifs et les courses
$sportifs = $pdo->query("SELECT id, nom FROM sportif")->fetchAll();
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();

// Ajout d'une participation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $id_sportif = $_POST['id_sportif'];
    $id_course = $_POST['id_course'];
    $resultat = $_POST['resultat'];
    $stmt = $pdo->prepare("INSERT INTO participation (id_sportif, id_course, resultat) VALUES (?, ?, ?)");
    $stmt->execute([$id_sportif, $id_course, $resultat]);
    echo "<p style='color:green'>Participation ajoutée !</p>";
}

// Filtre par sportif pour l'historique
$filtre_sportif = isset($_GET['sportif']) ? $_GET['sportif'] : '';
$where = '';
$params = [];
if ($filtre_sportif) {
    $where = 'WHERE p.id_sportif = ?';
    $params[] = $filtre_sportif;
}

$sql = "SELECT p.id, s.nom AS sportif, c.nom AS course, p.resultat
        FROM participation p
        JOIN sportif s ON p.id_sportif = s.id
        JOIN course c ON p.id_course = c.id
        $where
        ORDER BY s.nom, c.nom";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$participations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des participations</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
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
        .filter-form { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    <div class="nav">
        <a href="gestion_sportif.php">Sportif</a>
        <a href="gestion_club.php">Club</a>
        <a href="gestion_course.php">Course</a>
        <a href="gestion_discipline.php">Discipline</a>
        <a href="gestion_participation.php"><b>Participation</b></a>
    </div>
    <h1>Gestion des participations</h1>
    <h2>Ajouter une participation</h2>
    <form method="post" class="form-section">
        <label>Sportif :
            <select name="id_sportif" required>
                <?php foreach ($sportifs as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Course :
            <select name="id_course" required>
                <?php foreach ($courses as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Résultat : <input type="text" name="resultat" placeholder="ex: 1h23, 2ème..." required></label>
        <button type="submit" name="ajouter">Ajouter</button>
    </form>

    <h2>Liste des participations</h2>
    <form method="get" class="filter-form">
        <label>Filtrer par sportif :
            <select name="sportif" onchange="this.form.submit()">
                <option value="">Tous les sportifs</option>
                <?php foreach ($sportifs as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= ($filtre_sportif == $s['id']) ? 'selected' : '' ?>><?= htmlspecialchars($s['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <noscript><button type="submit">Filtrer</button></noscript>
    </form>
    <table>
        <tr>
            <th>Sportif</th>
            <th>Course</th>
            <th>Résultat</th>
        </tr>
        <?php foreach ($participations as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['sportif']) ?></td>
            <td><?= htmlspecialchars($p['course']) ?></td>
            <td><?= htmlspecialchars($p['resultat']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
