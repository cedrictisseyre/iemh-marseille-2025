<?php
require_once 'db_connect.php';
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $pdo->prepare('DELETE FROM Joueurs WHERE id_joueur = ?');
    $stmt->execute([$id]);
}
header('Location: liste_joueurs.php');
exit;
