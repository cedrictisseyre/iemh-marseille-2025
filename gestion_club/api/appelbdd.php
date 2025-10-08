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
