<?php
require_once '../../config/db_connect.php';

// Récupérer les listes
$clubs = $pdo->query("SELECT id, nom FROM club")->fetchAll();
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();
$disciplines = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();

// AJOUT SPORTIF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $id_course = $_POST['id_course'];
    $id_discipline = $_POST['id_discipline'];
    $id_club = $_POST['id_club'];

    $stmt = $pdo->prepare("INSERT INTO sportif (nom, id_course, id_discipline) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $id_course, $id_discipline]);
    $sportif_id = $pdo->lastInsertId();

    $stmt2 = $pdo->prepare("INSERT INTO club_membership (sportif_id, club_id, start_date, end_date) VALUES (?, ?, CURDATE(), NULL)");
    $stmt2->execute([$sportif_id, $id_club]);

    echo "<p style='color:green'>Sportif ajouté avec club !</p>";
}

// SUPPRESSION SPORTIF
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer_sportif'])) {
    $id_sportif = (int)$_POST['supprimer_sportif'];
    $pdo->prepare("DELETE FROM club_membership WHERE sportif_id = ?")->execute([$id_sportif]);
    $pdo->prepare("DELETE FROM participation WHERE sportif_id = ?")->execute([$id_sportif]);
    $pdo->prepare("DELETE FROM sportif WHERE id = ?")->execute([$id_sportif]);
    echo "<p style='color:red'>Sportif supprimé !</p>";
}

// CHANGEMENT DE CLUB
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_club'])) {
    $sportif_id = (int)$_POST['sportif_id'];
    $nouveau_club = (int)$_POST['nouveau_club'];
    // Clôturer l’adhésion actuelle avec date+heure précise
    $pdo->prepare("UPDATE club_membership SET end_date = NOW() WHERE sportif_id = ? AND end_date IS NULL")->execute([$sportif_id]);
    // Ajouter la nouvelle adhésion avec date+heure précise
    $pdo->prepare("INSERT INTO club_membership (sportif_id, club_id, start_date, end_date) VALUES (?, ?, NOW(), NULL)")->execute([$sportif_id, $nouveau_club]);
    echo "<p style='color:green'>Club changé avec succès !</p>";
}

// FILTRE
$where = [];
$params = [];
if (!empty($_GET['club'])) { $where[] = 'cm.club_id = ? AND cm.end_date IS NULL'; $params[] = $_GET['club']; }
if (!empty($_GET['course'])) { $where[] = 's.id_course = ?'; $params[] = $_GET['course']; }
if (!empty($_GET['discipline'])) { $where[] = 's.id_discipline = ?'; $params[] = $_GET['discipline']; }
if (!empty($_GET['search'])) { $where[] = 's.nom LIKE ?'; $params[] = '%' . $_GET['search'] . '%'; }

$sql = "SELECT s.id, s.nom, c.nom AS club, co.nom AS course, d.nom AS discipline
        FROM sportif s
        LEFT JOIN club_membership cm ON cm.sportif_id = s.id AND cm.end_date IS NULL
        LEFT JOIN club c ON cm.club_id = c.id
        LEFT JOIN course co ON s.id_course = co.id
        LEFT JOIN discipline d ON s.id_discipline = d.id";

if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sportifs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Gestion des sportifs</title>
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

<div class="container">
<h1>Gestion des sportifs</h1>

<h2>Ajouter un sportif</h2>
<form method="post">
<label>Nom: <input type="text" name="nom" required></label>
<label>Course:
<select name="id_course" required>
<?php foreach($courses as $c): ?>
<option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
<?php endforeach; ?>
</select></label>

<label>Discipline:
<select name="id_discipline" required>
<?php foreach($disciplines as $d): ?>
<option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['nom']) ?></option>
<?php endforeach; ?>
</select></label>

<label>Club:
<select name="id_club" required>
<?php foreach($clubs as $cl): ?>
<option value="<?= $cl['id'] ?>"><?= htmlspecialchars($cl['nom']) ?></option>
<?php endforeach; ?>
</select></label>

<button type="submit" name="ajouter">Ajouter</button>
</form>

<h2>Liste des sportifs</h2>
<form method="get" style="margin-bottom:15px;">
<input type="text" name="search" placeholder="Rechercher un nom..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
<select name="club">
<option value="">Tous les clubs</option>
<?php foreach($clubs as $cl): ?>
<option value="<?= $cl['id'] ?>" <?= (isset($_GET['club']) && $_GET['club']==$cl['id'])?'selected':''?>><?= htmlspecialchars($cl['nom']) ?></option>
<?php endforeach; ?>
</select>
<select name="course">
<option value="">Toutes les courses</option>
<?php foreach($courses as $c): ?>
<option value="<?= $c['id'] ?>" <?= (isset($_GET['course']) && $_GET['course']==$c['id'])?'selected':''?>><?= htmlspecialchars($c['nom']) ?></option>
<?php endforeach; ?>
</select>
<select name="discipline">
<option value="">Toutes les disciplines</option>
<?php foreach($disciplines as $d): ?>
<option value="<?= $d['id'] ?>" <?= (isset($_GET['discipline']) && $_GET['discipline']==$d['id'])?'selected':''?>><?= htmlspecialchars($d['nom']) ?></option>
<?php endforeach; ?>
</select>
<button type="submit">Filtrer</button>
<a href="gestion_sportif.php" style="text-decoration:underline; color:#2980b9;">Réinitialiser</a>
</form>

<table>
<tr>
<th>ID</th><th>Nom</th><th>Club</th><th>Course</th><th>Discipline</th><th>Historique</th><th>Changer Club</th><th>Supprimer</th>
</tr>
<?php foreach($sportifs as $s): ?>
<tr>
<td><?= $s['id'] ?></td>
<td><?= htmlspecialchars($s['nom']) ?></td>
<td><?= htmlspecialchars($s['club']) ?></td>
<td><?= htmlspecialchars($s['course']) ?></td>
<td><?= htmlspecialchars($s['discipline']) ?></td>
<td>
<form method="get" class="inline"><input type="hidden" name="historique" value="<?= $s['id'] ?>">
<button type="submit">Voir</button></form>
</td>
<td>
<form method="post" class="inline">
<input type="hidden" name="sportif_id" value="<?= $s['id'] ?>">
<select name="nouveau_club" required>
<?php foreach($clubs as $cl){
if($cl['nom'] != $s['club']) echo '<option value="'.$cl['id'].'">'.htmlspecialchars($cl['nom']).'</option>';
} ?>
</select>
<button type="submit" name="changer_club">Changer</button>
</form>
</td>
<td>
<form method="post" class="inline" onsubmit="return confirm('Supprimer ce sportif ?');">
<input type="hidden" name="supprimer_sportif" value="<?= $s['id'] ?>">
<button type="submit">Supprimer</button>
</form>
</td>
</tr>
<?php endforeach; ?>
</table>

<?php
// Historique clubs
if(isset($_GET['historique']) && ctype_digit($_GET['historique'])){
    $sportif_id = (int)$_GET['historique'];
    $stmt = $pdo->prepare("SELECT cm.start_date, cm.end_date, c.nom AS club_nom
        FROM club_membership cm JOIN club c ON cm.club_id=c.id
        WHERE cm.sportif_id=? ORDER BY cm.start_date DESC");
    $stmt->execute([$sportif_id]);
    $historique = $stmt->fetchAll();
    echo '<h3>Historique du sportif #'.$sportif_id.'</h3>';
    if($historique){
        echo '<table><tr><th>Club</th><th>Date début</th><th>Date fin</th></tr>';
        foreach($historique as $h){
            echo '<tr><td>'.htmlspecialchars($h['club_nom']).'</td><td>'.$h['start_date'].'</td><td>'.($h['end_date']??'<b>En cours</b>').'</td></tr>';
        }
        echo '</table>';
    } else echo '<p>Aucun historique de club.</p>';
}
?>
</div>
</body>
</html>
