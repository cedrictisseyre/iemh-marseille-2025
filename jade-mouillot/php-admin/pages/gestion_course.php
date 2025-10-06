<?php
require_once '../../config/db_connect.php';

// AJOUT COURSE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $pdo->prepare("INSERT INTO course (nom) VALUES (?)")->execute([$nom]);
    echo "<p style='color:green'>Course ajoutée !</p>";
}

// SUPPRESSION COURSE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_course'])) {
    $id_course = (int)$_POST['supprimer_course'];
    // Supprimer les sportifs associés
    $pdo->prepare("DELETE FROM sportif WHERE id_course = ?")->execute([$id_course]);
    $pdo->prepare("DELETE FROM participation WHERE course_id = ?")->execute([$id_course]);
    $pdo->prepare("DELETE FROM course WHERE id = ?")->execute([$id_course]);
    echo "<p style='color:red'>Course supprimée !</p>";
}

// LISTE DES COURSES
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des courses</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; }
.container { max-width:700px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
h1,h2 { text-align:left; color:#2c3e50; }
table { width:100%; border-collapse:collapse; margin-top:10px; text-align:left; }
th, td { border:1px solid #ccc; padding:6px; }
th { background:#2980b9; color:#fff; }
tr:nth-child(even) { background:#f9f9f9; }
label, input, button { display:block; margin-top:5px; width:100%; }
button { padding:8px; background:#2980b9; color:#fff; border:none; border-radius:4px; cursor:pointer; }
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
<a href="gestion_course.php"><b>Course</b></a>
<a href="gestion_discipline.php">Discipline</a>
<a href="gestion_participation.php">Participation</a>
</div>

<h1>Gestion des courses</h1>

<h2>Ajouter une course</h2>
<form method="post">
<label>Nom: <input type="text" name="nom" required></label>
<button type="submit" name="ajouter">Ajouter</button>
</form>

<h2>Liste des courses</h2>
<table>
<tr><th>ID</th><th>Nom</th><th>Supprimer</th></tr>
<?php foreach($courses as $c): ?>
<tr>
<td><?= $c['id'] ?></td>
<td><?= htmlspecialchars($c['nom']) ?></td>
<td>
<form method="post" style="display:inline" onsubmit="return confirm('Supprimer cette course ?');">
<input type="hidden" name="supprimer_course" value="<?= $c['id'] ?>">
<button type="submit">Supprimer</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
