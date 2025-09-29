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

// Filtres et recherche
$where = [];
$params = [];
if (!empty($_GET['club'])) {
    $where[] = 's.id_club = ?';
    $params[] = $_GET['club'];
}
if (!empty($_GET['course'])) {
    $where[] = 's.id_course = ?';
    $params[] = $_GET['course'];
}
if (!empty($_GET['discipline'])) {
    $where[] = 's.id_discipline = ?';
    $params[] = $_GET['discipline'];
}
if (!empty($_GET['search'])) {
    $where[] = 's.nom LIKE ?';
    $params[] = '%' . $_GET['search'] . '%';
}
$sql = "SELECT s.id, s.nom, c.nom AS club, co.nom AS course, d.nom AS discipline
        FROM sportif s
        LEFT JOIN club c ON s.id_club = c.id
        LEFT JOIN course co ON s.id_course = co.id
        LEFT JOIN discipline d ON s.id_discipline = d.id";
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sportifs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang=\"fr\">
<head>
    <meta charset=\"UTF-8\">
    <title>Gestion des sportifs</title>
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
<div class=\"container\">
    <div class=\"nav\">
        <a href=\"gestion_sportif.php\">Sportif</a>
        <a href=\"gestion_club.php\">Club</a>
        <a href=\"gestion_course.php\">Course</a>
        <a href=\"gestion_discipline.php\">Discipline</a>
    </div>
    <h1>Gestion des sportifs</h1>
    <h2>Ajouter un sportif</h2>
    <form method=\"post\" class=\"form-section\">
        <label>Nom : <input type=\"text\" name=\"nom\" required></label>
        <label>Course :
            <select name=\"id_course\" required>
                <?php foreach ($courses as $course): ?>
                    <option value=\"<?= $course['id'] ?>\"><?= htmlspecialchars($course['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Discipline :
            <select name=\"id_discipline\" required>
                <?php foreach ($disciplines as $discipline): ?>
                    <option value=\"<?= $discipline['id'] ?>\"><?= htmlspecialchars($discipline['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Club :
            <select name=\"id_club\" required>
                <?php foreach ($clubs as $club): ?>
                    <option value=\"<?= $club['id'] ?>\"><?= htmlspecialchars($club['nom']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type=\"submit\" name=\"ajouter\">Ajouter</button>
    </form>

    <h2>Liste des sportifs</h2>
    <form method="get" style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
        <input type="text" name="search" placeholder="Rechercher un nom..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" style="flex:1;min-width:180px;">
        <select name="club">
            <option value="">Tous les clubs</option>
            <?php foreach ($clubs as $club): ?>
                <option value="<?= $club['id'] ?>" <?= (isset($_GET['club']) && $_GET['club'] == $club['id']) ? 'selected' : '' ?>><?= htmlspecialchars($club['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="course">
            <option value="">Toutes les courses</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= $course['id'] ?>" <?= (isset($_GET['course']) && $_GET['course'] == $course['id']) ? 'selected' : '' ?>><?= htmlspecialchars($course['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="discipline">
            <option value="">Toutes les disciplines</option>
            <?php foreach ($disciplines as $discipline): ?>
                <option value="<?= $discipline['id'] ?>" <?= (isset($_GET['discipline']) && $_GET['discipline'] == $discipline['id']) ? 'selected' : '' ?>><?= htmlspecialchars($discipline['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Filtrer</button>
        <a href="gestion_sportif.php" style="margin-left:10px; color:#2980b9; text-decoration:underline;">Réinitialiser</a>
    </form>
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
