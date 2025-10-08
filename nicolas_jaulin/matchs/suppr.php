<?php
require_once '../db_connect.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM Matchs WHERE id_match = ?');
    $stmt->execute([$id]);
}
header('Location: ../matchs/liste.php');
exit;
