<?php
require_once '../../config/db_connect.php';

// AJOUT DISCIPLINE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $pdo->prepare("INSERT INTO discipline (nom) VALUES (?)")->execute([$nom]);
    echo "<p style='color:green'>Discipline ajoutée !</p>";
}

// SUPPRESSION DISCIPLINE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_discipline'])) {
    $id_discipline = (int)$_POST['supprimer_discipline'];
    $pdo->prepare("UPDATE sportif SET id_discipline=NULL WHERE id_discipline = ?")->execute([$id_discipline]);
    $pdo->prepare("DELETE FROM discipline WHERE id = ?")->execute([$id_discipline]);
    echo "<p style='color:red'>Discipline supprimée !</p>";
}

// LISTE DES DISCIPLINES
$disciplines = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des disciplines</title>
<style>
body { font-family: Arial, sans-serif; background:#f4f4f4; margin:0; padding:0; display:flex; }
.nav { width:200px; background:#fff; padding:20px; box-shadow:2px 0 6px rgba(0,0,0,0.1); height:100vh; position:fixed; top:0; left:0; }
.nav a { display:block; margin-bottom:15px; color:#2980b9; text-decoration:none; font-weight:bold; }
.nav a:hover { text-decoration:underline; }
.container { margin-left:220px; padding:20px; flex:1; }
h1,h2,h3,h4 { text-align:left; color:#2c3e50;}
table { width:100%; border-collapse: collapse; margin-top:10px; text-align:left;}
th,td { border:1px solid #ccc; padding:6px; }
th { background:#2980b9; color:#fff; }
tr:nth-child(even) { background:#f9f9f9; }
label, input, button { display:block; margin-top:5px; width:100%; }
button { padding:8px; background:#2980b9; color:#fff; border:none; border-radius:4px; cursor:pointer; }
button:hover { background:#1abc9c; }
form.inline { display:inline-block; margin:0; padding:0; }
</style>
</head>
<body>
<div class="nav">
<a href="gestion_sportif.php">Sportifs</a>
<a href="gestion_club.php">Clubs</a>
<a href="gestion_course.php">Courses</a>
<a href="gestion_discipline.php"><b>Disciplines</b></a>
<a href="gestion_participation.php">Participations</a>
</div>

<div class="container">
<h1>Gestion des disciplines</h1>

<h2>Ajouter une discipline</h2>
<form method="post">
    <label>Nom: <input type="text" name="nom" required></label>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<h2>Liste des disciplines</h2>
<table>
<tr><th>ID</th><th>Nom</th><th>Supprimer</th></tr>
<?php foreach($disciplines as $d): ?>
<tr>
<td><?= $d['id'] ?></td>
<td><?= htmlspecialchars($d['nom']) ?></td>
<td>
<form method="post" class="inline" onsubmit="return confirm('Supprimer cette discipline ?');">
<input type="hidden" name="supprimer_discipline" value="<?= $d['id'] ?>">
<button type="submit">Supprimer</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
