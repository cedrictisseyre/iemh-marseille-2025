<?php
require_once __DIR__ . '/../evaluation_etudiant.php';
$score = getGlobalScore(__DIR__ . '/..');
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Évaluation projet</title>
  <link rel="stylesheet" href="/francoisdcls/assets/style.css">
</head>
<body>
  <div class="container">
    <h1>Évaluation automatique</h1>
    <p>Score global (auto) : <strong id="score"><?= $score ?></strong>/100</p>
    <p>Consignes : cette évaluation est indicative.</p>
    <a href="/francoisdcls/site_f1.php">Retour au site</a>
  </div>
</body>
</html>
