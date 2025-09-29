<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db_connect.php';

$sql = "SELECT s.id, s.nom, c.nom AS club, co.nom AS course, d.nom AS discipline
        FROM sportif s
        LEFT JOIN club c ON s.id_club = c.id
        LEFT JOIN course co ON s.id_course = co.id
        LEFT JOIN discipline d ON s.id_discipline = d.id";
$sportifs = $pdo->query($sql)->fetchAll();
?>

<table border="1">
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
