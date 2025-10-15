<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$sql = "SELECT p.pilote_id, p.nom, p.prenom FROM pilotes p WHERE p.pilote_id IN (SELECT pilote_id FROM championnats) ORDER BY p.nom, p.prenom";
$stmt = $pdo->query($sql);
$champions = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pid = $row['pilote_id'];
    $sql2 = "SELECT COUNT(*) as nb FROM championnats WHERE pilote_id = ?";
    $stmt2 = $pdo->prepare($sql2);
    $stmt2->execute([$pid]);
    $row['nb_victoires'] = $stmt2->fetchColumn();
    $sql3 = "SELECT annee FROM championnats WHERE pilote_id = ? ORDER BY annee";
    $stmt3 = $pdo->prepare($sql3);
    $stmt3->execute([$pid]);
    $annees_victoires = $stmt3->fetchAll(PDO::FETCH_COLUMN);
    $row['annees_victoires'] = $annees_victoires;
    $sql4 = "SELECT COUNT(DISTINCT annee) as nb FROM participations WHERE pilote_id = ?";
    $stmt4 = $pdo->prepare($sql4);
    $stmt4->execute([$pid]);
    $row['nb_participations'] = $stmt4->fetchColumn();
    $sql5 = "SELECT DISTINCT annee FROM participations WHERE pilote_id = ? ORDER BY annee";
    $stmt5 = $pdo->prepare($sql5);
    $stmt5->execute([$pid]);
    $annees_participations = $stmt5->fetchAll(PDO::FETCH_COLUMN);
    $row['annees_participations'] = $annees_participations;
    $row['photo'] = isset($row['photo']) ? $row['photo'] : '';
    $champions[] = $row;
}
echo json_encode($champions);
