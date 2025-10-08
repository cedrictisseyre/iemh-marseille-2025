<?php
require_once '../db_connect.php';
$id_stat = $_GET['id_stat'] ?? '';
if ($id_stat) {
    $stmt = $pdo->prepare('DELETE FROM Statistiques_Joueur WHERE id_stat = ?');
    $stmt->execute([$id_stat]);
}
header('Location: ../statistiques/liste.php');
exit;
