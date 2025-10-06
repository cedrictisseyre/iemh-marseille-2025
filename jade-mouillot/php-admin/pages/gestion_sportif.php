<?php
require_once '../../config/db_connect.php';


$clubs = $pdo->query("SELECT id, nom FROM club")->fetchAll();
$courses = $pdo->query("SELECT id, nom FROM course")->fetchAll();
$disciplines = $pdo->query("SELECT id, nom FROM discipline")->fetchAll();


// Ajout d'un sportif avec club (club_membership)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $nom = $_POST['nom'];
    $id_course = $_POST['id_course'];
    $id_discipline = $_POST['id_discipline'];
    $id_club = $_POST['id_club'];
    // Insérer le sportif
    $stmt = $pdo->prepare("INSERT INTO sportif (nom, id_course, id_discipline) VALUES (?, ?, ?)");
    $stmt->execute([$nom, $id_course, $id_discipline]);
    $sportif_id = $pdo->lastInsertId();
    // Créer l'adhésion club active
    $stmt2 = $pdo->prepare("INSERT INTO club_membership (sportif_id, club_id, start_date, end_date) VALUES (?, ?, CURDATE(), NULL)");
    $stmt2->execute([$sportif_id, $id_club]);
    echo "<p style='color:green'>Sportif ajouté avec club !</p>";
}


// Filtres et recherche (club via club_membership actif)
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
    <div class="nav">
        <a href="gestion_sportif.php"><b>Sportif</b></a>
        <a href="gestion_club.php">Club</a>
        <a href="gestion_course.php">Course</a>
        <a href="gestion_discipline.php">Discipline</a>
        <a href="gestion_participation.php">Participation</a>
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
            <select name="id_club" required>
                <?php foreach ($clubs as $club): ?>
                    <option value="<?= $club['id'] ?>"><?= htmlspecialchars($club['nom']) ?></option>
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
            <th>Historique clubs</th>
        </tr>
        <?php foreach ($sportifs as $s): ?>
        <tr>
            <td><?= $s['id'] ?></td>
            <td><?= htmlspecialchars($s['nom']) ?></td>
            <td><?= htmlspecialchars($s['club']) ?></td>
            <td><?= htmlspecialchars($s['course']) ?></td>
            <td><?= htmlspecialchars($s['discipline']) ?></td>
            <td>
                <form method="get" style="display:inline">
                    <input type="hidden" name="historique" value="<?= $s['id'] ?>">
                    <button type="submit" style="padding:2px 8px; font-size:0.9em;">Voir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php
    // Affichage de l'historique des clubs si demandé
    if (isset($_GET['historique']) && ctype_digit($_GET['historique'])) {
        $sportif_id = (int)$_GET['historique'];
        // Traitement du changement de club
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['changer_club']) && isset($_POST['nouveau_club'])) {
            $nouveau_club = (int)$_POST['nouveau_club'];
            // Clôturer l'adhésion courante (date et heure)
            $stmt = $pdo->prepare("UPDATE club_membership SET end_date = NOW() WHERE sportif_id = ? AND end_date IS NULL");
            $stmt->execute([$sportif_id]);
            // Créer la nouvelle adhésion (date et heure)
            $stmt = $pdo->prepare("INSERT INTO club_membership (sportif_id, club_id, start_date, end_date) VALUES (?, ?, NOW(), NULL)");
            $stmt->execute([$sportif_id, $nouveau_club]);
            echo "<p style='color:green'>Changement de club effectué !</p>";
            // Rafraîchir la liste des sportifs (pour afficher le club actuel)
            // Recharger la page sans paramètre POST
            echo '<script>window.location.href = "gestion_sportif.php?historique=' . $sportif_id . '";</script>';
            exit;
        }
    $sql = "SELECT cm.start_date, cm.end_date, c.nom AS club_nom
        FROM club_membership cm
        JOIN club c ON cm.club_id = c.id
        WHERE cm.sportif_id = ?
        ORDER BY cm.start_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$sportif_id]);
        $historique = $stmt->fetchAll();
        echo '<h3>Historique des clubs du sportif #' . $sportif_id . '</h3>';
        if ($historique) {
            echo '<table><tr><th>Club</th><th>Date début</th><th>Date fin</th></tr>';
            foreach ($historique as $h) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($h['club_nom']) . '</td>';
                echo '<td>' . htmlspecialchars($h['start_date']) . '</td>';
                echo '<td>' . ($h['end_date'] ? htmlspecialchars($h['end_date']) : '<b>En cours</b>') . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo '<p>Aucun historique de club pour ce sportif.</p>';
        }
        // Formulaire de changement de club (toujours affiché)
        $stmt = $pdo->prepare("SELECT club_id FROM club_membership WHERE sportif_id = ? AND end_date IS NULL");
        $stmt->execute([$sportif_id]);
        $adhesion_actuelle = $stmt->fetchColumn();
        echo '<h4>Changer de club</h4>';
        echo '<form method="post">';
        echo '<select name="nouveau_club" required>';
        foreach ($clubs as $club) {
            if ($club['id'] != $adhesion_actuelle) {
                echo '<option value="' . $club['id'] . '">' . htmlspecialchars($club['nom']) . '</option>';
            }
        }
        echo '</select> ';
        echo '<button type="submit" name="changer_club">Changer</button>';
        echo '</form>';
    }
    ?>
</div>
</body>
</html>
