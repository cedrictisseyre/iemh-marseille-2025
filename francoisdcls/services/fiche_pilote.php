<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql = "SELECT * FROM pilotes WHERE pilote_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$pilote = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pilote) { echo json_encode(['error'=>'Pilote introuvable']); exit; }
// Statistiques
$sql2 = "SELECT COUNT(*) FROM championnats WHERE pilote_id = ?";
$nb_titres = $pdo->prepare($sql2); $nb_titres->execute([$id]); $nb_titres = $nb_titres->fetchColumn();
$sql3 = "SELECT COUNT(DISTINCT annee) FROM participations WHERE pilote_id = ?";
$nb_particip = $pdo->prepare($sql3); $nb_particip->execute([$id]); $nb_particip = $nb_particip->fetchColumn();
$sql4 = "SELECT DISTINCT ecurie_id FROM participations WHERE pilote_id = ?";
$ecuries = $pdo->prepare($sql4); $ecuries->execute([$id]); $ecuries = $ecuries->fetchAll(PDO::FETCH_COLUMN);
$pilote['nb_titres'] = $nb_titres;
$pilote['nb_participations'] = $nb_particip;
$pilote['ecuries'] = $ecuries;
echo json_encode($pilote);
