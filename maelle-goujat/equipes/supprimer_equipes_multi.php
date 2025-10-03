<?php
session_start();
require_once __DIR__ . '/../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['ids']) && is_array($_POST['ids'])) {
    $ids = array_filter($_POST['ids'], 'is_numeric');
    if (count($ids) > 0) {
        $in = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $conn->prepare("DELETE FROM equipes WHERE id_equipe IN ($in)");
        $stmt->execute($ids);
        header('Location: ../index.php?msg=suppression_equipe');
        exit;
    }
}
header('Location: ../index.php?msg=erreur');
exit;
