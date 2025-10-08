<?php
require_once '../../config/db_connect.php';

// Récupérer sportifs et courses
$sportifs = $pdo->query("SELECT id, nom FROM sportif")->fetchAll();
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();

// AJOUT PARTICIPATION
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ajouter'])){
    $sportif_id = $_POST['id_sportif'];
    $course_id = $_POST['id_course'];
    $resultat = $_POST['resultat'];
    $pdo->prepare("INSERT INTO participation (sportif_id, course_id, date_participation, resultat) VALUES (?, ?, CURDATE(), ?)")
        ->execute([$sportif_id, $course_id, $resultat]);
    echo "<p style='color:green'>Participation ajoutée !</p>";
}

// SUPPRESSION PARTICIPATION
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['supprimer_participation'])){
    $id_part = (int)$_POST['supprimer_participation'];
    $pdo->prepare("DELETE FROM participation WHERE id=?")->execute([$id_part]);
    echo "<p style='color:red'>Participation supprimée !</p>";
}

// FILTRE
$filtre_sportif = $_GET['sportif'] ?? '';
$where = '';
$params = [];
if($filtre_sportif){
    $where = 'WHERE p.sportif_id=?';
    $params[] = $filtre_sportif;
}

$sql = "SELECT p.id, s.nom AS sportif, c.nom AS course, p.resultat, p.date_participation
FROM participation p
JOIN sportif s ON p.sportif_id=s.id
JOIN course c ON p.course_id=c.id
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
label, select, input, button { display:block; margin-top:5px; width:100%; }
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
<a href="gestion_discipline.php">Disciplines</a>
<a href="gestion_participation.php"><b>Participations</b></a>
</div>

<div class="container">
<h1>Gestion des participations</h1>

<h2>Ajouter une participation</h2>
<form method="post">
    <label>Sportif:
        <select name="id_sportif" required>
        <?php foreach($sportifs as $s): ?>
            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom']) ?></option>
        <?php endforeach; ?>
        </select>
    </label>

    <label>Course:
        <select name="id_course" required>
        <?php foreach($courses as $c): ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
        <?php endforeach; ?>
        </select>
    </label>

    <label>Résultat: <input type="text" name="resultat" placeholder="ex: 1h23, 2ème..." required></label>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<h2>Liste des participations</h2>
<form method="get" style="margin-bottom:15px;">
    <label>Filtrer par sportif:
        <select name="sportif" onchange="this.form.submit()">
            <option value="">Tous</option>
            <?php foreach($sportifs as $s): ?>
                <option value="<?= $s['id'] ?>" <?= ($filtre_sportif==$s['id'])?'selected':''?>><?= htmlspecialchars($s['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit">Filtrer</button></noscript>
    </label>
</form>

<table>
<tr><th>Sportif</th><th>Course</th><th>Date</th><th>Résultat</th><th>Supprimer</th></tr>
<?php foreach($participations as $p): ?>
<tr>
<td><?= htmlspecialchars($p['sportif']) ?></td>
<td><?= htmlspecialchars($p['course']) ?></td>
<td><?= htmlspecialchars($p['date_participation']) ?></td>
<td><?= htmlspecialchars($p['resultat']) ?></td>
<td>
<form method="post" class="inline" onsubmit="return confirm('Supprimer cette participation ?');">
<input type="hidden" name="supprimer_participation" value="<?= $p['id'] ?>">
<button type="submit">Supprimer</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
