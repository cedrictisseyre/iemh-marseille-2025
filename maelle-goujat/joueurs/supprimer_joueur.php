<?php
require_once __DIR__ . '/../connexion.php';
if (isset($_GET['id_joueur'])) {
    $id = $_GET['id_joueur'];
    $sql = 'DELETE FROM joueurs WHERE id_joueur = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    echo 'Joueur supprimé !';
}
?>
