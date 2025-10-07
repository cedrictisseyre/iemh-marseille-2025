<?php
require_once __DIR__ . '/../config.php';

// Détermination de la page active
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ligue 1 - Base de données</title>
    <link rel="stylesheet" href="<?= base_path() ?>assets/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-container">
        <div class="logo-title">
            <img src="<?= base_path() ?>assets/ligue1_logo.png" alt="Logo Ligue 1" class="logo">
            <h1>Ligue 1</h1>
        </div>
        <nav class="navbar">
            <a href="<?= base_path() ?>index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Accueil</a>
            <a href="<?= base_path() ?>teams/teams.php" class="<?= $currentPage === 'teams.php' ? 'active' : '' ?>">Équipes</a>
            <a href="<?= base_path() ?>players/players.php" class="<?= $currentPage === 'players.php' ? 'active' : '' ?>">Joueurs</a>
            <a href="<?= base_path() ?>matches/matchs.php" class="<?= $currentPage === 'matchs.php' ? 'active' : '' ?>">Matchs</a>
            <a href="<?= base_path() ?>standings/standings.php" class="<?= $currentPage === 'standings.php' ? 'active' : '' ?>">Classement</a>
        </nav>
    </div>
</header>
<main>
