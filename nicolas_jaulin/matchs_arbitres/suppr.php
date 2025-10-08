<?php
require_once '../db_connect.php';
$id_match = $_GET['id_match'] ?? '';
$id_arbitre = $_GET['id_arbitre'] ?? '';
if ($id_match && $id_arbitre) {
    $stmt = $pdo->prepare('DELETE FROM Matchs_Arbitres WHERE id_match = ? AND id_arbitre = ?');
    $stmt->execute([$id_match, $id_arbitre]);
}
header('Location: ../matchs_arbitres/liste.php');
exit;
