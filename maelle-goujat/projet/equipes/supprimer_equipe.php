<?php
require_once '../connexion.php';
if (isset($_GET['id_equipe'])) {
    $id = $_GET['id_equipe'];
    $sql = 'DELETE FROM equipes WHERE id_equipe = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    echo 'Equipe supprimée !';
}
?>
