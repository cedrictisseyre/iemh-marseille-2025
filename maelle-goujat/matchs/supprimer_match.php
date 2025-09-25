<?php
require_once __DIR__ . '/../connexion.php';
if (isset($_GET['id_match'])) {
    $id = $_GET['id_match'];
    $sql = 'DELETE FROM matchs WHERE id_match = ?';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    echo 'Match supprimé !';
}
?>
