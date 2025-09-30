<?php
// Header include: meta, styles
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>L'annuaire du karaté</title>
    <link rel="stylesheet" href="css/style-onglets.css">
    <style>
        /* Centrer le titre et aligner proprement */
        .site-title{display:flex;align-items:center;justify-content:center;gap:0.6rem;text-align:center}
    </style>
</head>
<body>
<div class="container">
    <h1 class="site-title">L'annuaire du karaté</h1>
    <?php
    require_once __DIR__ . '/../helpers.php'; // activation de helpers.php pour esc(), formatName(), etc.
    ?>
