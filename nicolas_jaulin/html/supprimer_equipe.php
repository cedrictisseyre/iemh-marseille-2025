<?php
require_once 'db_connect.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM Equipes WHERE id_equipe = ?');
    $stmt->execute([$id]);
}
header('Location: liste_equipes.php');
exit;
