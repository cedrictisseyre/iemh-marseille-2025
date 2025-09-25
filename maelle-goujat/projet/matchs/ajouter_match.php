<?php
require_once '../connexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date_match'] ?? '';
    $lieu = $_POST['lieu'] ?? null;
    $equipe_dom = $_POST['equipe_dom'] ?? null;
    $equipe_ext = $_POST['equipe_ext'] ?? null;
    $score_dom = $_POST['score_dom'] ?? 0;
    $score_ext = $_POST['score_ext'] ?? 0;
    $sql = 'INSERT INTO matchs (date_match, lieu, equipe_dom, equipe_ext, score_dom, score_ext) VALUES (?, ?, ?, ?, ?, ?)';
    $stmt = $conn->prepare($sql);
    $stmt->execute([$date, $lieu, $equipe_dom, $equipe_ext, $score_dom, $score_ext]);
    echo 'Match ajouté !';
}
?>
