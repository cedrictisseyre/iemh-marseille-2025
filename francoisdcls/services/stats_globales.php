<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
// Nombre total de pilotes
$nb_pilotes = $pdo->query("SELECT COUNT(*) FROM pilotes")->fetchColumn();
// Nombre total d'écuries
$nb_ecuries = $pdo->query("SELECT COUNT(*) FROM ecuries")->fetchColumn();
// Nombre total de championnats
$nb_champ = $pdo->query("SELECT COUNT(*) FROM championnats")->fetchColumn();
// Nombre total de participations
$nb_particip = $pdo->query("SELECT COUNT(*) FROM participations")->fetchColumn();
echo json_encode([
  'nb_pilotes' => $nb_pilotes,
  'nb_ecuries' => $nb_ecuries,
  'nb_championnats' => $nb_champ,
  'nb_participations' => $nb_particip
]);
