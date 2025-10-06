<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ligue 1 - Base de données</title>
    <link rel="stylesheet" href="<?= base_path() ?>style.css">
</head>
<body>
<header>
    <h1>⚽ Ligue 1 - Projet Base de données</h1>
    <nav>
        <a href="<?= base_path() ?>index.php">Accueil</a>
        <a href="<?= base_path() ?>teams/equipes.php">Équipes</a>
        <a href="<?= base_path() ?>players/joueurs.php">Joueurs</a>
        <a href="<?= base_path() ?>matches/matchs.php">Matchs</a>
        <a href="<?= base_path() ?>standings/classement.php">Classement</a>
    </nav>
</header>
<main>
