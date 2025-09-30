<?php
// Header include: meta, styles
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>L'annuaire du karaté</title>
    <link rel="stylesheet" href="../css/style-onglets.css">
    <style>
        .site-title{display:flex;align-items:center;gap:0.6rem}
        .site-icon{width:38px;height:38px;flex:0 0 38px}
    <?php
    // Header include: meta, styles
    ?><!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>L'annuaire du karaté</title>
        <link rel="stylesheet" href="../css/style-onglets.css">
        <style>
            .site-title{display:flex;align-items:center;gap:0.6rem}
            .site-icon{width:44px;height:44px;flex:0 0 44px}
        </style>
    </head>
    <body>
    <div class="container">
        <h1 class="site-title">
            <!-- Icône karaté simple (silhouette stylisée) -->
            <svg class="site-icon" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img">
                <rect width="64" height="64" rx="8" fill="#dc2626"/>
                <g transform="translate(8,6) scale(0.9)" fill="#fff">
                    <!-- tête -->
                    <circle cx="18" cy="8" r="5"/>
                    <!-- corps -->
                    <path d="M18 14 C18 18, 18 26, 18 30" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <!-- bras -->
                    <path d="M18 18 L8 22" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                    <!-- jambe avant (coup de pied) -->
                    <path d="M18 30 L30 22" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                    <!-- jambe arrière -->
                    <path d="M18 30 L12 40" stroke="#fff" stroke-width="2" stroke-linecap="round"/>
                </g>
            </svg>
            L'annuaire du karaté
        </h1>

    <?php
    require_once __DIR__ . '/../helpers.php'; // activation de helpers.php pour esc(), formatName(), etc.
    ?>
