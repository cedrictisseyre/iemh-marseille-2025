<?php
require_once '../db_connect.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM Arbitres WHERE id_arbitre = ?');
    $stmt->execute([$id]);
}
header('Location: ../arbitres/liste.php');
exit;
