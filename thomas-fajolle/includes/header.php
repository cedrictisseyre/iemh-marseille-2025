<?php
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ligue 1 - Base de données</title>
    <!-- Chemin vers le CSS dans le dossier assets -->
    <link rel="stylesheet" href="<?= base_path() ?>assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-top">
        <img src="<?= base_path() ?>assets/logo-ligue1.png" alt="Logo Ligue 1" class="logo">
        <div class="header-title">
            <h1>Ligue 1 - Projet Base de données</h1>
            <p class="subtitle">Toute l'actualité, les équipes, les joueurs et les résultats du championnat de France</p>
        </div>
    </div>
    <nav class="main-nav">
        <a href="<?= base_path() ?>index.php"><span class="nav-icon">🏠</span> Accueil</a>
        <a href="<?= base_path() ?>teams/equipes.php"><span class="nav-icon">👥</span> Équipes</a>
        <a href="<?= base_path() ?>players/joueurs.php"><span class="nav-icon">🧑‍💼</span> Joueurs</a>
        <a href="<?= base_path() ?>matches/matchs.php"><span class="nav-icon">📅</span> Matchs</a>
        <a href="<?= base_path() ?>standings/classement.php"><span class="nav-icon">📊</span> Classement</a>
    </nav>
</header>
<main>
