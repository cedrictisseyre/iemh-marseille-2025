<?php
require_once base_path('../config.php'); // pour s'assurer que base_path() est disponible
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ligue 1 - Base de données</title>
    <link rel="stylesheet" href="<?= base_path('assets/style.css') ?>">
</head>
<body>
<header>
    <h1>⚽ Ligue 1 - Projet Base de données</h1>
    <nav>
        <a href="<?= base_path('index.php') ?>">Accueil</a>
        <a href="<?= base_path('tables/teams/equipes.php') ?>">Équipes</a>
        <a href="<?= base_path('tables/players/joueurs.php') ?>">Joueurs</a>
        <a href="<?= base_path('tables/matches/matchs.php') ?>">Matchs</a>
        <a href="<?= base_path('tables/standings/classement.php') ?>">Classement</a>
    </nav>
</header>
<main>
