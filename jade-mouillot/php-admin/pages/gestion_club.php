<?php
require_once '../../config/db_connect.php';

// AJOUT CLUB
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $pdo->prepare("INSERT INTO club (nom) VALUES (?)")->execute([$nom]);
    echo "<p style='color:green'>Club ajouté !</p>";
}

// SUPPRESSION CLUB
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_club'])) {
    $id_club = (int)$_POST['supprimer_club'];
    // Supprimer les memberships liés
    $pdo->prepare("DELETE FROM club_membership WHERE club_id = ?")->execute([$id_club]);
    $pdo->prepare("DELETE FROM club WHERE id = ?")->execute([$id_club]);
    echo "<p style='color:red'>Club supprimé !</p>";
}

// LISTE DES CLUBS
$clubs = $pdo->query("SELECT id, nom FROM club")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des clubs</title>
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; margin:0; padding:0; display:flex; }
.nav { width: 200px; background:#fff; padding:20px; box-shadow:2px 0 6px rgba(0,0,0,0.1); height:100vh; position:fixed; top:0; left:0; }
.nav a { display:block; margin-bottom:15px; color:#2980b9; text-decoration:none; font-weight:bold; }
.nav a:hover { text-decoration:underline; }
.container { margin-left:220px; padding:20px; flex:1; }
h1,h2,h3,h4 { text-align:left; color:#2c3e50;}
table { width:100%; border-collapse: collapse; margin-top:10px; text-align:left;}
th,td { border:1px solid #ccc; padding:6px; }
th { background:#2980b9; color:#fff; }
tr:nth-child(even) { background:#f9f9f9; }
label, input, select, button { display:block; margin-top:5px; width:100%; }
button { padding:8px; background:#2980b9; color:#fff; border:none; border-radius:4px; cursor:pointer;}
button:hover { background:#1abc9c; }
form.inline { display:inline-block; margin:0; padding:0; }
</style>
</head>
<body>
<div class="nav">
<a href="gestion_sportif.php"><b>Sportifs</b></a>
<a href="gestion_club.php">Clubs</a>
<a href="gestion_course.php">Courses</a>
<a href="gestion_discipline.php">Disciplines</a>
<a href="gestion_participation.php">Participations</a>
</div>

<h1>Gestion des clubs</h1>

<h2>Ajouter un club</h2>
<form method="post">
    <label>Nom: <input type="text" name="nom" required></label>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<h2>Liste des clubs</h2>
<table>
<tr><th>ID</th><th>Nom</th><th>Supprimer</th></tr>
<?php foreach($clubs as $c): ?>
<tr>
<td><?= $c['id'] ?></td>
<td><?= htmlspecialchars($c['nom']) ?></td>
<td>
<form method="post" style="display:inline" onsubmit="return confirm('Supprimer ce club ?');">
<input type="hidden" name="supprimer_club" value="<?= $c['id'] ?>">
<button type="submit">Supprimer</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
