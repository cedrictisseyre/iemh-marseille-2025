<?php
require_once '../../config/db_connect.php';

// Récupération des clubs, courses et disciplines
$clubs = $pdo->query("SELECT id, nom FROM club")->fetchAll();
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();
$disciplines = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();

// --- AJOUT D'UN SPORTIF ---
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

// --- SUPPRESSION D'UN SPORTIF ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supprimer'])) {
    $id = $_POST['id'];
    // Supprimer les adhésions club
    $pdo->prepare("DELETE FROM club_membership WHERE sportif_id = ?")->execute([$id]);
    // Supprimer le sportif
    $pdo->prepare("DELETE FROM sportif WHERE id = ?")->execute([$id]);
    echo "<p style='color:red'>Sportif supprimé !</p>";
}

// --- MODIFICATION DU NOM D'UN SPORTIF ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_nom'])) {
    $id = $_POST['id'];
    $nouveau_nom = $_POST['nouveau_nom'];
    $stmt = $pdo->prepare("UPDATE sportif SET nom = ? WHERE id = ?");
    $stmt->execute([$nouveau_nom, $id]);
    echo "<p style='color:green'>Nom du sportif modifié !</p>";
}

// --- FILTRAGE ET RECHERCHE ---
$where = [];
$params = [];
if (!empty($_GET['club'])) {
    $where[] = 'cm.club_id = ? AND cm.end_date IS NULL';
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
body { font-family: Arial, sans-serif; background: #f4f4f4; }
.container { max-width: 900px; margin: 40px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
h1 { color: #2c3e50; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
th { background: #2980b9; color: #fff; }
tr:nth-child(even) { background: #f9f9f9; }
label { display: block; margin-top: 10px; }
input, select { padding: 6px; width: 100%; }
button { margin-top: 10px; padding: 6px 12px; background: #2980b9; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
button:hover { background: #1abc9c; }
.delete-btn { background: #e74c3c; }
.delete-btn:hover { background: #c0392b; }
.nav { margin-bottom: 20px; }
.nav a { margin-right: 15px; color: #2980b9; text-decoration: none; font-weight: bold; }
.nav a:hover { text-decoration: underline; }
.form-section { margin-bottom: 30px; }
</style>
</head>
<body>
<div class="container">
<div class="nav">
    <a href="gestion_sportif.php"><b>Sportif</b></a>
    <a href="gestion_club.php">Club</a>
    <a href="gestion_course.php">Course</a>
    <a href="gestion_discipline.php">Discipline</a>
    <a href="gestion_participation.php">Participation</a>
</div>

<h1>Gestion des sportifs</h1>

<h2>Ajouter un sportif</h2>
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
    <th>Actions</th>
</tr>
<?php foreach ($sportifs as $s): ?>
<tr>
    <td><?= $s['id'] ?></td>
    <td>
        <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $s['id'] ?>">
            <input type="text" name="nouveau_nom" value="<?= htmlspecialchars($s['nom']) ?>" style="width:90px;">
            <button type="submit" name="modifier_nom">Modifier</button>
        </form>
    </td>
    <td><?= htmlspecialchars($s['club']) ?></td>
    <td><?= htmlspecialchars($s['course']) ?></td>
    <td><?= htmlspecialchars($s['discipline']) ?></td>
    <td>
        <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $s['id'] ?>">
            <button type="submit" name="supprimer" class="delete-btn" onclick="return confirm('Supprimer ce sportif ?')">Supprimer</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</table>
</div>
</body>
</html>
