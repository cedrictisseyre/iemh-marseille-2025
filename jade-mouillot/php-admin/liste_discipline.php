<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'db_connect.php';

$sql = "SELECT d.id, d.nom
        FROM discipline d";
$discipline = $pdo->query($sql)->fetchAll();
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Nom</th>
    </tr>
    <?php foreach ($discipline as $d): ?>
    <tr>
        <td><?= $s['id'] ?></td>
        <td><?= htmlspecialchars($s['nom']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>
