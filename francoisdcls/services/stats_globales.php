<?php

header('Content-Type: application/json');
require_once __DIR__ . '/../database/bdd_formule1.php';
$pdoLocal = get_pdo();
if (!$pdoLocal) {
    http_response_code(500);
    echo json_encode(['error' => 'Database unavailable']);
    exit;
}
// Nombre total de pilotes
$nb_pilotes = $pdoLocal->query("SELECT COUNT(*) FROM pilotes")->fetchColumn();
// Nombre total d'écuries
$nb_ecuries = $pdoLocal->query("SELECT COUNT(*) FROM ecuries")->fetchColumn();
// Nombre total de championnats
$nb_champ = $pdoLocal->query("SELECT COUNT(*) FROM championnats")->fetchColumn();
// Nombre total de participations
$nb_particip = $pdoLocal->query("SELECT COUNT(*) FROM participations")->fetchColumn();
echo json_encode([
  'nb_pilotes' => $nb_pilotes,
  'nb_ecuries' => $nb_ecuries,
  'nb_championnats' => $nb_champ,
  'nb_participations' => $nb_particip
]);
